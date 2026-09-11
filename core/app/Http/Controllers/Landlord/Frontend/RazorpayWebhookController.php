<?php
namespace App\Http\Controllers\Landlord\Frontend;

use App\Http\Controllers\Controller;
use App\Helpers\Payment\PaymentGatewayCredential;
use App\Models\PaymentLogs;
use App\Models\Tenant;
use App\Services\Payment\RazorpaySubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Mail\PlaceOrder;
use Illuminate\Support\Facades\Mail;

class RazorpayWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $event = $request->input('event');
        $isRecurring = RazorpaySubscriptionService::isGloballyEnabled();

        // Ignore subscription events if recurring is disabled
        if (!$isRecurring && str_starts_with($event, 'subscription.')) {
            Log::info('Ignoring subscription event because recurring is disabled', ['event' => $event]);
            return response()->json(['status' => 'ignored']);
        }

        $payload = $request->all();
        Log::info('Razorpay Webhook Received', ['event' => $event, 'timestamp' => now()]);

        // Verify webhook signature
        try {
            $razorpay = PaymentGatewayCredential::get_razorpay_credential();
            $signature = $request->header('X-Razorpay-Signature');
            $rawBody = $request->getContent();

            if (!$razorpay->verify_webhook_signature($rawBody, $signature)) {
                Log::warning('Invalid Razorpay webhook signature', ['event' => $event]);
                return response()->json(['status' => 'invalid_signature'], 401);
            }

            Log::info('Razorpay Webhook Event', ['event' => $event]);

            // Route to appropriate handler
            match($event) {
                // Payment Events
                'payment.authorized' => $this->handlePaymentAuthorized($payload),
                'payment.captured' => $this->handlePaymentCaptured($payload),
                'payment.failed' => $this->handlePaymentFailed($payload),

                // QR Code Events
                'qr_code.created' => $this->handleQrCodeCreated($payload),
                'qr_code.closed' => $this->handleQrCodeClosed($payload),
                'qr_code.paid' => $this->handleQrCodePaid($payload),

                // Subscription Events
                'subscription.authenticated' => $isRecurring ? Log::info('Subscription authenticated', ['subscription_id' => $payload['payload']['subscription']['entity']['id'] ?? null]) : null,
                'subscription.activated' => $isRecurring ? $this->handleSubscriptionActivated($payload) : null,
                'subscription.charged' => $isRecurring ? RazorpaySubscriptionService::handleSubscriptionCharged($payload) : null,
//                'subscription.pending' => $isRecurring ? RazorpaySubscriptionService::handleSubscriptionFailed($payload) : null,
                'subscription.pending' => $isRecurring ? Log::info('Subscription pending - no action'): null,
                'subscription.halted' => $isRecurring ? RazorpaySubscriptionService::handleSubscriptionFailed($payload) : null,
                'subscription.cancelled' => $isRecurring ? $this->handleSubscriptionCancelled($payload) : null,
                'subscription.completed' => $isRecurring ? Log::info('Subscription completed', ['subscription_id' => $payload['payload']['subscription']['entity']['id'] ?? null]) : null,

                default => Log::info('Unhandled Razorpay webhook event', ['event' => $event])
            };

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Razorpay webhook processing failed', [
                'event' => $event,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle payment.authorized event
     */
    private function handlePaymentAuthorized($payload): void
    {
        try {
            $payment = $payload['payload']['payment']['entity'] ?? null;

            if (!$payment) {
                Log::warning('No payment data in payment.authorized payload');
                return;
            }

            $paymentId = $payment['id'] ?? null;
            $notes = $payment['notes'] ?? [];
            $orderId = $notes['payment_log_id'] ?? null;

            Log::info('Processing payment.authorized', [
                'payment_id' => $paymentId,
                'order_id' => $orderId,
                'amount' => $payment['amount'] ?? null
            ]);

            if (!$orderId) {
                Log::warning('No order_id in payment notes', ['payment_id' => $paymentId]);
                return;
            }

            $this->processPaymentCompletion($orderId, $paymentId, $payment['amount'] ?? 0);

        } catch (\Exception $e) {
            Log::error('Failed to handle payment.authorized', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Handle payment.captured event
     */
    private function handlePaymentCaptured($payload): void
    {
        try {
            $payment = $payload['payload']['payment']['entity'] ?? null;

            if (!$payment) {
                Log::warning('No payment data in payment.captured payload');
                return;
            }

            $paymentId = $payment['id'] ?? null;
            $notes = $payment['notes'] ?? [];
            $orderId = $notes['payment_log_id'] ?? null;

            Log::info('Processing payment.captured', [
                'payment_id' => $paymentId,
                'order_id' => $orderId,
                'amount' => $payment['amount'] ?? null
            ]);

            if (!$orderId) {
                Log::warning('No order_id in payment notes', ['payment_id' => $paymentId]);
                return;
            }

            $this->processPaymentCompletion($orderId, $paymentId, $payment['amount'] ?? 0);

        } catch (\Exception $e) {
            Log::error('Failed to handle payment.captured', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Handle payment.failed event
     */
    private function handlePaymentFailed($payload): void
    {
        try {
            $payment = $payload['payload']['payment']['entity'] ?? null;

            if (!$payment) {
                Log::warning('No payment data in payment.failed payload');
                return;
            }

            $paymentId = $payment['id'] ?? null;
            $notes = $payment['notes'] ?? [];
            $orderId = $notes['payment_log_id'] ?? null;
            $errorCode = $payment['error_code'] ?? 'UNKNOWN';
            $errorDescription = $payment['error_description'] ?? 'Payment failed';
            $errorReason = $payment['error_reason'] ?? 'unknown';
            $email = $payment['email'] ?? null;
            $amount = $payment['amount'] ?? 0;

            Log::info('Payment Failed Event', [
                'payment_id' => $paymentId,
                'order_id' => $orderId,
                'error_code' => $errorCode,
                'error_description' => $errorDescription
            ]);

            // Find payment log
            $paymentLog = $orderId ? PaymentLogs::find($orderId) : null;

            if ($paymentLog && $paymentLog->payment_status === 'complete') {
                Log::warning('Ignoring payment.failed for already completed payment', ['payment_id' => $paymentId]);
                return;
            }

            if ($paymentLog) {
                // Update payment log with failure
                $paymentLog->update([
                    'status' => 'failed',
                    'payment_status' => 'failed',
                    'transaction_id' => $paymentId,
                    'failure_reason' => $errorDescription,
                    'error_code' => $errorCode
                ]);

                Log::info('Payment log updated with failure', [
                    'payment_log_id' => $paymentLog->id,
                    'error' => $errorDescription
                ]);

                // Send failure emails
//                $this->sendPaymentFailureEmail($paymentLog, $errorDescription, $errorReason);
//                $this->sendAdminFailureAlert($paymentLog, $errorDescription, $errorCode, $errorReason);
            } else {
                Log::warning('Payment log not found for failed payment', ['payment_id' => $paymentId]);

                // Still send email if we have it
                if ($email) {
//                    $this->sendPaymentFailureEmailDirect($email, $amount, $errorDescription, $paymentId);
                }
            }

        } catch (\Exception $e) {
            Log::error('Failed to handle payment.failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Handle qr_code.created event
     */
    private function handleQrCodeCreated($payload): void
    {
        try {
            $qrCode = $payload['payload']['qr_code']['entity'] ?? null;

            if (!$qrCode) {
                Log::warning('No QR code data in qr_code.created payload');
                return;
            }

            Log::info('QR Code Created', [
                'qr_code_id' => $qrCode['id'] ?? null,
                'status' => $qrCode['status'] ?? null,
                'amount' => $qrCode['amount'] ?? null
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to handle qr_code.created', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Handle qr_code.closed event
     */
    private function handleQrCodeClosed($payload): void
    {
        try {
            $qrCode = $payload['payload']['qr_code']['entity'] ?? null;

            if (!$qrCode) {
                Log::warning('No QR code data in qr_code.closed payload');
                return;
            }

            Log::info('QR Code Closed', [
                'qr_code_id' => $qrCode['id'] ?? null,
                'closed_reason' => $qrCode['closed_reason'] ?? null
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to handle qr_code.closed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Handle qr_code.paid event
     */
    private function handleQrCodePaid($payload): void
    {
        try {
            $qrCode = $payload['payload']['qr_code']['entity'] ?? null;
            $payment = $payload['payload']['payment']['entity'] ?? null;

            if (!$qrCode || !$payment) {
                Log::warning('Missing QR code or payment data in qr_code.paid payload');
                return;
            }

            $qrCodeId = $qrCode['id'];
            $paymentId = $payment['id'];
            $amount = $payment['amount'] ?? 0;
            $qrNotes = $qrCode['notes'] ?? [];
            $paymentNotes = $payment['notes'] ?? [];

            Log::info('QR Code Payment Received', [
                'qr_code_id' => $qrCodeId,
                'payment_id' => $paymentId,
                'amount' => $amount
            ]);

            // Extract payment log ID
            $paymentLogId = $qrNotes['payment_log_id'] ?? $paymentNotes['payment_log_id'] ?? null;
            $subscriptionId = $qrNotes['subscription_id'] ?? $paymentNotes['subscription_id'] ?? null;
            $tenantId = $qrNotes['tenant_id'] ?? $paymentNotes['tenant_id'] ?? null;

            // Process based on payment type
            if ($subscriptionId && $paymentLogId) {
                $this->handleQrSubscriptionPayment($paymentLogId, $subscriptionId, $paymentId, $amount);
            } elseif ($paymentLogId) {
                $this->processPaymentCompletion($paymentLogId, $paymentId, $amount);
            } elseif ($tenantId) {
                $this->handleQrTenantPayment($tenantId, $paymentId, $amount);
            } else {
                Log::warning('QR payment without associated order', ['qr_code_id' => $qrCodeId]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to handle qr_code.paid', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Handle QR subscription payment
     */
    private function handleQrSubscriptionPayment($paymentLogId, $subscriptionId, $paymentId, $amount): void
    {
        try {
            $paymentLog = PaymentLogs::find($paymentLogId);

            if (!$paymentLog) {
                Log::warning('Payment log not found for QR subscription', ['payment_log_id' => $paymentLogId]);
                return;
            }

            $paymentLog->update([
                'status' => 'complete',
                'payment_status' => 'complete',
                'transaction_id' => $paymentId,
                'subscription_status' => 'active'
            ]);

            Log::info('QR subscription payment processed', [
                'payment_log_id' => $paymentLogId,
                'payment_id' => $paymentId
            ]);

//            $this->sendPaymentEmails($paymentLog);

        } catch (\Exception $e) {
            Log::error('Failed to handle QR subscription payment', [
                'payment_log_id' => $paymentLogId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle QR payment by tenant ID
     */
    private function handleQrTenantPayment($tenantId, $paymentId, $amount): void
    {
        try {
            $paymentLog = PaymentLogs::where('tenant_id', $tenantId)
                ->where('payment_status', 'pending')
                ->orderByDesc('created_at')
                ->first();

            if (!$paymentLog) {
                Log::warning('No pending payment for QR tenant payment', ['tenant_id' => $tenantId]);
                return;
            }

            $this->processPaymentCompletion($paymentLog->id, $paymentId, $amount);

        } catch (\Exception $e) {
            Log::error('Failed to handle QR tenant payment', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle subscription.cancelled event
     */
    private function handleSubscriptionCancelled($payload): void
    {
        try {
            $subscription = $payload['payload']['subscription']['entity'] ?? null;

            if (!$subscription) {
                Log::warning('No subscription data in cancellation payload');
                return;
            }

            $subscriptionId = $subscription['id'];

            Log::info('Subscription Cancelled', [
                'subscription_id' => $subscriptionId,
                'status' => $subscription['status'] ?? null
            ]);

            // Update related payment logs
            PaymentLogs::where('razorpay_subscription_id', $subscriptionId)
                ->update(['subscription_status' => 'cancelled']);

            // Update related tenants
            DB::table('tenants')->where('razorpay_subscription_id', $subscriptionId)
                ->update([
                    'razorpay_subscription_id' => null,
                    'recurring_enabled' => 0
                ]);

            Log::info('Subscription cancellation processed successfully', [
                'subscription_id' => $subscriptionId
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to handle subscription cancellation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Handle subscription.activated event
     */
    private function handleSubscriptionActivated($payload): void
    {
        try {
            $subscription = $payload['payload']['subscription']['entity'] ?? null;

            if (!$subscription) {
                Log::warning('No subscription data in activation payload');
                return;
            }

            $subscriptionNotes = $subscription['notes'] ?? [];
            $paymentLogId = $subscriptionNotes['payment_log_id'] ?? null;
            $tenantId = $subscriptionNotes['tenant_id'] ?? null;

            Log::info('Subscription Activated', [
                'subscription_id' => $subscription['id'],
                'payment_log_id' => $paymentLogId,
                'tenant_id' => $tenantId
            ]);

            if (!$paymentLogId) {
                Log::warning('Payment log ID not in subscription notes');
                return;
            }

            $paymentLog = PaymentLogs::find($paymentLogId);
            if (!$paymentLog) {
                Log::warning('Payment log not found', ['payment_log_id' => $paymentLogId]);
                return;
            }

            // Update payment log - mark as complete for initial subscription payment
            $paymentLog->update([

                'razorpay_subscription_id' => $subscription['id'],
                'subscription_status' => 'active'
            ]);

            // Update or create tenant
            if ($tenantId) {
//                $tenant = Tenant::find($tenantId);
                $tenant = DB::table('tenants')->where('id', $tenantId)->first();
//                $tenant = Tenant::find($tenantId);

                if ($tenant) {
                    DB::table('tenants')->where('id', $tenantId)->update([
                        'user_id' => $paymentLog->user_id,
                        'theme_slug' => $paymentLog->theme_slug,
                        'razorpay_subscription_id' => $subscription['id'],
                        'recurring_enabled' => 1,
                        'renew_status' => ($tenant->renew_status ?? 0) + 1,
                        'start_date' => now()->format('Y-m-d H:i:s'),
                        'expire_date' => $paymentLog->expire_date,
                        'grace_period_end' => null
                    ]);

                    Log::info('Tenant updated', ['tenant_id' => $tenantId]);
                } else {
                    Tenant::create([
                        'id' => $tenantId,
                        'user_id' => $paymentLog->user_id,
                        'theme_slug' => $paymentLog->theme_slug,
                        'razorpay_subscription_id' => $subscription['id'],
                        'recurring_enabled' => 1,
                        'start_date' => now()->format('Y-m-d H:i:s'),
                        'expire_date' => $paymentLog->expire_date
                    ]);

                    Log::info('Tenant created', ['tenant_id' => $tenantId]);
                }
            }

//            $this->sendPaymentEmails($paymentLog);

        } catch (\Exception $e) {
            Log::error('Failed to handle subscription activation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Process payment completion - core logic for all successful payments
     */
    private function processPaymentCompletion($orderId, $paymentId, $amount): void
    {
        try {
            $paymentLog = PaymentLogs::find($orderId);

            if (!$paymentLog) {
                Log::warning('Payment log not found', ['order_id' => $orderId]);
                return;
            }

            // Update payment log
            $paymentLog->update([
                'status' => 'complete',
                'payment_status' => 'complete',
                'transaction_id' => $paymentId
            ]);

            Log::info('Payment completed', [
                'order_id' => $orderId,
                'transaction_id' => $paymentId,
                'tenant_id' => $paymentLog->tenant_id
            ]);

            // Send emails
//            $this->sendOrderMail($orderId);
//            $this->sendPaymentEmails($paymentLog);

            // Handle tenant creation/update
            $this->handleTenantProcessing($paymentLog);

        } catch (\Exception $e) {
            Log::error('Failed to process payment completion', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Handle tenant creation and credential email
     */
    private function handleTenantProcessing(PaymentLogs $paymentLog): void
    {
        try {
            $tenant = DB::table('tenants')->where('id', $paymentLog->tenant_id)->first();
//            $tenant = Tenant::find($paymentLog->tenant_id);

            if ($paymentLog->payment_status === 'complete') {
                if (!$tenant) {
                    // Create new tenant
                    $tenant = Tenant::create([
                        'id' => $paymentLog->tenant_id,
                        'theme_slug' => $paymentLog->theme_slug ?? 'hexfashion'
                    ]);

                    // Send credentials for new tenant
//                    $this->sendTenantCredentials($paymentLog);

                    Log::info('Tenant created', ['tenant_id' => $paymentLog->tenant_id]);
                } elseif ($paymentLog->is_renew == 0) {
                    // Send credentials for existing tenant (first purchase only)
//                    $this->sendTenantCredentials($paymentLog);
                }

                // Update tenant expiry
                if ($tenant) {
                    Log::info('Tenant ', [$tenant]);
                    $updateData = [
                        'renew_status' => ($tenant->renew_status ?? 0) + 1,
                        'is_renew' => 1,
                        'start_date' => now()->format('Y-m-d H:i:s'),
                        'expire_date' => $paymentLog->expire_date,
                        'renewal_payment_log_id' => $paymentLog->id,
                        'grace_period_end' => null
                    ];

                    // Update subscription-related fields if present in payment log
                    if ($paymentLog->razorpay_subscription_id) {
                        $updateData['razorpay_subscription_id'] = $paymentLog->razorpay_subscription_id;
                    }

                    if ($paymentLog->is_recurring) {
                        $updateData['recurring_enabled'] = 1;
                    } else {
                        // When recurring is disabled, explicitly set to 0
                        $updateData['recurring_enabled'] = 0;
                    }

                    DB::table('tenants')->where('id', $paymentLog->tenant_id)->update($updateData);

                    Log::info('Tenant updated', [
                        'tenant_id' => $paymentLog->tenant_id,
                        'razorpay_subscription_id' => $paymentLog->razorpay_subscription_id ?? 'none',
                        'recurring_enabled' => $paymentLog->is_recurring ? 1 : 0,
                        'updated_data'=> $updateData
                    ]);
                }
            }

        } catch (\Exception $e) {
            Log::error('Failed to handle tenant processing', [
                'tenant_id' => $paymentLog->tenant_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send tenant credentials email
     */
//    private function sendTenantCredentials(PaymentLogs $paymentLog): void
//    {
//        try {
//            $user = \App\Models\User::where('id', $paymentLog->user_id)->first();
//
//            if (!$user) {
//                Log::warning('User not found for tenant credentials', ['user_id' => $paymentLog->user_id]);
//                return;
//            }
//
//            $username = get_static_option('tenant_admin_default_username') ?? 'super_admin';
//            $password = get_static_option('tenant_admin_default_password') ?? '12345678';
//
////            Mail::to($user->email)->send(new \App\Mail\TenantCredentialMail($username, $password));
//
//            Log::info('Tenant credentials sent', [
//                'user_id' => $paymentLog->user_id,
//                'email' => $user->email
//            ]);
//
//        } catch (\Exception $e) {
//            Log::error('Failed to send tenant credentials', [
//                'user_id' => $paymentLog->user_id,
//                'error' => $e->getMessage()
//            ]);
//        }
//    }

    /**
     * Send order confirmation emails
     */
//    private function sendOrderMail($orderId): void
//    {
//        try {
//            $paymentLog = PaymentLogs::find($orderId);
//
//            if (!$paymentLog) {
//                return;
//            }
//
//            $order_mail = get_static_option('order_page_form_mail') ?: get_static_option('site_global_email');
//            $all_fields = [];
//            $all_attachment = [];
//
//            Mail::to($order_mail)->send(new PlaceOrder($all_fields, $all_attachment, $paymentLog, "admin", 'regular'));
//            Mail::to($paymentLog->email)->send(new PlaceOrder($all_fields, $all_attachment, $paymentLog, 'user', 'regular'));
//
//            Log::info('Order emails sent', ['order_id' => $orderId]);
//
//        } catch (\Exception $e) {
//            Log::error('Failed to send order emails', [
//                'order_id' => $orderId,
//                'error' => $e->getMessage()
//            ]);
//        }
//    }

    /**
     * Send payment completion emails
     */
//    private function sendPaymentEmails(PaymentLogs $paymentLog): void
//    {
//        try {
//            $order_mail = get_static_option('order_page_form_mail') ?: get_static_option('site_global_email');
//            $all_fields = [];
//            $all_attachment = [];
//
//            Mail::to($order_mail)->send(new PlaceOrder($all_fields, $all_attachment, $paymentLog, "admin", 'regular'));
//            Mail::to($paymentLog->email)->send(new PlaceOrder($all_fields, $all_attachment, $paymentLog, 'user', 'regular'));
//
//            Log::info('Payment emails sent', ['payment_log_id' => $paymentLog->id]);
//
//        } catch (\Exception $e) {
//            Log::error('Failed to send payment emails', [
//                'payment_log_id' => $paymentLog->id,
//                'error' => $e->getMessage()
//            ]);
//        }
//    }

    /**
     * Send payment failure email to user
     */
//    private function sendPaymentFailureEmail(PaymentLogs $paymentLog, $errorDescription, $errorReason): void
//    {
//        try {
//            $mailData = [
//                'customer_name' => $paymentLog->name,
//                'email' => $paymentLog->email,
//                'amount' => $paymentLog->package_price,
//                'error_description' => $errorDescription,
//                'error_reason' => $errorReason,
//                'payment_id' => $paymentLog->transaction_id,
//                'package_name' => $paymentLog->package_name,
//                'retry_message' => 'Please try again with a different payment method or contact support.'
//            ];
//
//            Mail::to($paymentLog->email)->send(new \App\Mail\BasicMail($mailData, [], $paymentLog, 'user', 'payment_failed'));
//
//            Log::info('Payment failure email sent', ['payment_log_id' => $paymentLog->id]);
//
//        } catch (\Exception $e) {
//            Log::error('Failed to send payment failure email', [
//                'payment_log_id' => $paymentLog->id,
//                'error' => $e->getMessage()
//            ]);
//        }
//    }

    /**
     * Send failure alert to admin
     */
//    private function sendAdminFailureAlert(PaymentLogs $paymentLog, $errorDescription, $errorCode, $errorReason): void
//    {
//        try {
//            $adminEmail = get_static_option('order_page_form_mail') ?: get_static_option('site_global_email');
//
//            $mailData = [
//                'payment_log_id' => $paymentLog->id,
//                'customer_name' => $paymentLog->name,
//                'customer_email' => $paymentLog->email,
//                'amount' => $paymentLog->package_price,
//                'package_name' => $paymentLog->package_name,
//                'error_code' => $errorCode,
//                'error_description' => $errorDescription,
//                'error_reason' => $errorReason,
//                'transaction_id' => $paymentLog->transaction_id,
//                'alert_type' => 'Payment Failed'
//            ];
//
//            Mail::to($adminEmail)->send(new \App\Mail\BasicMail($mailData, [], $paymentLog, 'admin', 'payment_failed'));
//
//            Log::info('Admin failure alert sent', ['payment_log_id' => $paymentLog->id]);
//
//        } catch (\Exception $e) {
//            Log::error('Failed to send admin failure alert', [
//                'payment_log_id' => $paymentLog->id,
//                'error' => $e->getMessage()
//            ]);
//        }
//    }

    /**
     * Send direct payment failure email (when payment log not found)
     */
//    private function sendPaymentFailureEmailDirect($email, $amount, $errorDescription, $paymentId): void
//    {
//        try {
//            $subject = 'Payment Failed - ' . config('app.name', 'App');
//            $message = "Your payment of ₹{$amount} failed. Reason: {$errorDescription}. Payment ID: {$paymentId}. Please try again.";
//
//            Mail::raw($message, function($msg) use ($email, $subject) {
//                $msg->to($email)->subject($subject);
//            });
//
//            Log::info('Direct failure email sent', ['email' => $email]);
//
//        } catch (\Exception $e) {
//            Log::error('Failed to send direct failure email', [
//                'email' => $email,
//                'error' => $e->getMessage()
//            ]);
//        }
//    }
}
