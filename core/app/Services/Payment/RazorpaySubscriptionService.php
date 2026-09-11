<?php

namespace App\Services\Payment;
use App\Helpers\Payment\PaymentGatewayCredential;
use App\Models\PaymentLogs;
use App\Models\PricePlan;
use App\Models\Tenant;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\BasicMail;
class RazorpaySubscriptionService
{
    /**
     * Check if Razorpay recurring is globally enabled
     */
    public static function isGloballyEnabled(): bool
    {
        return (bool) get_static_option('razorpay_recurring_enabled');
    }
    /**
     * Check if recurring should be used for a specific tenant
     */
    public static function shouldUseRecurring(?string $tenant_id = null): bool
    {
        // Check global setting first
        if (!self::isGloballyEnabled()) {
            return false;
        }

        // If no tenant_id provided, use global setting
        if (!$tenant_id) {
            return true;
        }

        // Check per-tenant override
        $tenant = Tenant::find($tenant_id);
        if (!$tenant) {
            return true; // Default to global setting
        }

        // If tenant has explicitly disabled recurring, respect that
        return (bool) $tenant->recurring_enabled;
    }
    /**
     * Sync a price plan to Razorpay
     */
    public static function syncPlanToRazorpay(PricePlan $plan): array
    {
        try {
            $razorpay = PaymentGatewayCredential::get_razorpay_credential();

            $period = match($plan->type) {
                0 => 'monthly',
                1 => 'yearly',
                2 => null,
                default => 'monthly'
            };

            if ($period === null) {
                return [
                    'status' => 'skipped',
                    'message' => 'Lifetime plans do not support recurring subscriptions'
                ];
            }


            $result = $razorpay->create_subscription_plan([
                'period' => $period,
                'interval' => 1,
                'item_name' => $plan->title,
                'amount' => $plan->price,
                'description' => $plan->package_description ?? 'Subscription Plan'
            ]);

            if ($result['status'] === 'success') {
                $plan->update([
                    'razorpay_plan_id' => $result['plan_id'],
                    'razorpay_synced_at' => now()
                ]);

                Log::info('Razorpay plan synced successfully', [
                    'plan_id' => $plan->id,
                    'razorpay_plan_id' => $result['plan_id']
                ]);

                return $result;
            }

            // Check if this is a subscription feature not enabled error
            if (isset($result['message']) && strpos($result['message'], 'requested URL was not found') !== false) {
                Log::error('Razorpay Subscriptions feature not enabled on account', [
                    'plan_id' => $plan->id,
                    'error' => $result['message']
                ]);

                return [
                    'status' => 'failed',
                    'message' => 'Razorpay Subscriptions feature is not enabled on your account. Please enable it in Razorpay Dashboard > Settings > Product Configuration > Subscriptions, or contact Razorpay support.'
                ];
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Failed to sync plan to Razorpay', [
                'plan_id' => $plan->id,
                'error' => $e->getMessage()
            ]);

            return [
                'status' => 'failed',
                'message' => $e->getMessage()
            ];
        }
    }
    /**
     * Create a subscription for a tenant
     */
    public static function createSubscriptionForTenant(PaymentLogs $paymentLog, PricePlan $plan, ?string $returnUrl = null): array
    {
        try {
            $razorpay = PaymentGatewayCredential::get_razorpay_credential();

            // Ensure plan is synced
            if (!$plan->razorpay_plan_id) {
                $syncResult = self::syncPlanToRazorpay($plan);
                if ($syncResult['status'] !== 'success') {
                    return $syncResult;
                }
                $plan->refresh();
            }

            $totalCount = match($plan->type) {
                0 => 120,  // Monthly: 10 years
                1 => 10,   // Yearly: 10 years
                default => 120
            };

            $subscriptionData = [
                'plan_id' => $plan->razorpay_plan_id,
                'customer_notify' => 1,
                'total_count' => $totalCount,
                'notes' => [
                    'tenant_id' => $paymentLog->tenant_id,
                    'payment_log_id' => $paymentLog->id,  //   Add payment log ID
                    'user_id' => $paymentLog->user_id
                ]
            ];

            if ($returnUrl) {
                $subscriptionData['return_url'] = $returnUrl;
            }

            $result = $razorpay->create_subscription($subscriptionData);

            if ($result['status'] === 'success') {
                //   Update the EXISTING payment log
                $paymentLog->update([
                    'razorpay_subscription_id' => $result['subscription_id'],
                    'razorpay_plan_id' => $plan->razorpay_plan_id,
                    'is_recurring' => 1,
                    'subscription_status' => 'pending'
                ]);

                // Update tenant
                DB::table('tenants')
                    ->where('id', $paymentLog->tenant_id)
                    ->update([
                        'razorpay_subscription_id' => $result['subscription_id'],
                        'recurring_enabled' => 1
                    ]);

                Log::info('Razorpay subscription created', [
                    'subscription_id' => $result['subscription_id'],
                    'payment_log_id' => $paymentLog->id,
                    'tenant_id' => $paymentLog->tenant_id
                ]);

                return $result;
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Failed to create Razorpay subscription', [
                'payment_log_id' => $paymentLog->id,
                'error' => $e->getMessage()
            ]);

            return [
                'status' => 'failed',
                'message' => $e->getMessage()
            ];
        }
    }
    /**
     * Cancel a subscription
     */
    public static function cancelSubscription(string $subscriptionId, bool $cancelAtCycleEnd = true): array
    {
        try {
            $razorpay = PaymentGatewayCredential::get_razorpay_credential();
            $result = $razorpay->cancel_subscription($subscriptionId, $cancelAtCycleEnd);

            if ($result['status'] === 'success') {
                // Always set to 'cancelled' - this is the valid ENUM value
                // The Razorpay API handles the actual cancellation timing
                $newStatus = 'cancelled';

                // Update related payment logs
                $updatedLogs = PaymentLogs::where('razorpay_subscription_id', $subscriptionId)
                    ->update(['subscription_status' => $newStatus]);

                // Update related tenants
                $updatedTenants = DB::table('tenants')->where('razorpay_subscription_id', $subscriptionId)
                    ->update([
                        'razorpay_subscription_id' => null,
                        'recurring_enabled' => 0
                    ]);

                Log::info('Razorpay subscription cancelled', [
                    'subscription_id' => $subscriptionId,
                    'cancel_at_cycle_end' => $cancelAtCycleEnd,
                    'new_status' => $newStatus,
                    'updated_logs' => $updatedLogs,
                    'updated_tenants' => $updatedTenants
                ]);

                return $result;
            }

            // Log if Razorpay API returned unsuccessful response
            Log::warning('Razorpay API returned unsuccessful response', [
                'subscription_id' => $subscriptionId,
                'result' => $result
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('Failed to cancel Razorpay subscription', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'status' => 'failed',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Pause a subscription
     */
    public static function pauseSubscription(string $subscriptionId): array
    {
        try {
            $razorpay = PaymentGatewayCredential::get_razorpay_credential();
            $result = $razorpay->pause_subscription($subscriptionId);

            if ($result['status'] === 'success') {
                // Update related records
                PaymentLogs::where('razorpay_subscription_id', $subscriptionId)
                    ->update(['subscription_status' => 'paused']);

                Log::info('Razorpay subscription paused', [
                    'subscription_id' => $subscriptionId
                ]);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Failed to pause Razorpay subscription', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage()
            ]);

            return [
                'status' => 'failed',
                'message' => $e->getMessage()
            ];
        }
    }
    /**
     * Resume a paused subscription
     */
    public static function resumeSubscription(string $subscriptionId): array
    {
        try {
            $razorpay = PaymentGatewayCredential::get_razorpay_credential();
            $result = $razorpay->resume_subscription($subscriptionId);

            if ($result['status'] === 'success') {
                // Update related records
                PaymentLogs::where('razorpay_subscription_id', $subscriptionId)
                    ->update(['subscription_status' => 'active']);

                Log::info('Razorpay subscription resumed', [
                    'subscription_id' => $subscriptionId
                ]);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Failed to resume Razorpay subscription', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage()
            ]);

            return [
                'status' => 'failed',
                'message' => $e->getMessage()
            ];
        }
    }

    public static function handleSubscriptionCharged(array $webhookData): void
    {
        try {
            $subscription = $webhookData['payload']['subscription']['entity'] ?? null;
            $payment = $webhookData['payload']['payment']['entity'] ?? null;

            if (!$subscription || !$payment) {
                Log::warning('Invalid webhook data for subscription.charged');
                return;
            }

            $subscriptionId = $subscription['id'];
            $paymentId = $payment['id'];
            $amount = (int)($payment['amount'] * 100);

            // Get tenant_id and payment_log_id from subscription notes
            $subscriptionNotes = $subscription['notes'] ?? [];
            $tenantId = $subscriptionNotes['tenant_id'] ?? null;
            $paymentLogId = $subscriptionNotes['payment_log_id'] ?? null;

            if (!$tenantId) {
                Log::warning('Tenant ID not found in subscription notes', [
                    'subscription_id' => $subscriptionId,
                    'notes' => $subscriptionNotes
                ]);
                return;
            }

            Log::info('Processing subscription.charged webhook', [
                'subscription_id' => $subscriptionId,
                'tenant_id' => $tenantId,
                'payment_log_id' => $paymentLogId,
                'amount' => $amount
            ]);

            $latestLog = PaymentLogs::find($paymentLogId);
            Log::info('Processing subscription.charged webhook', [$latestLog]);
            if (!$latestLog) {
                Log::warning('Payment log not found for subscription', [
                    'subscription_id' => $subscriptionId,
                    'tenant_id' => $tenantId
                ]);
                return;
            }

            // Get the plan for date calculations
            $plan = PricePlan::find($latestLog->package_id);
            if (!$plan) {
                Log::warning('Plan not found for subscription renewal', [
                    'package_id' => $latestLog->package_id,
                    'subscription_id' => $subscriptionId
                ]);
                return;
            }

            // Calculate next expiry date
            $nextExpireDate = self::calculateNextExpiry($plan);
            $nextBillingDate = self::calculateNextBilling($plan);

            Log::info('next billing date', [$nextBillingDate, $nextExpireDate]);

            // ========================================
            // STEP 2: CREATE NEW PAYMENT LOG FOR RENEWAL
            // ========================================
            $renewalLog = PaymentLogs::create([
                'email' => $latestLog->email,
                'name' => $latestLog->name,
                'package_name' => $latestLog->package_name,
                'package_price' => $amount,
                'package_gateway' => 'razorpay',
                'package_id' => $latestLog->package_id,
                'user_id' => $latestLog->user_id,
                'tenant_id' => $tenantId,
                'theme_slug' => $latestLog->theme_slug,
                'status' => 'complete',
                'payment_status' => 'complete',
                'transaction_id' => $paymentId,
                'is_renew' => 1,
                'is_recurring' => 1,
                'renew_status' => ($latestLog->renew_status ?? 0) + 1,
                'razorpay_subscription_id' => $subscriptionId,
                'razorpay_plan_id' => $latestLog->razorpay_plan_id,
                'subscription_status' => 'active',
                'start_date' => now(),
                'expire_date' => $nextExpireDate,
                'next_billing_date' => $nextBillingDate
            ]);

            Log::info('Renewal payment log created', [
                'renewal_log_id' => $renewalLog->id,
                'subscription_id' => $subscriptionId,
                'tenant_id' => $tenantId,
                'transaction_id' => $paymentId,
                'theme_slug' => $latestLog->theme_slug,
            ]);

            // ========================================
            // STEP 3: UPDATE TENANT
            // ========================================
           $tenant = DB::table('tenants')->where('id', $tenantId)->first();
//            $tenant = Tenant::find($tenantId);

            if (!$tenant) {
                Log::info('Tenant not found, creating new tenant', [
                    'tenant_id' => $tenantId,
                    'subscription_id' => $subscriptionId,
                    'theme_slug' => $latestLog->theme_slug,
                ]);

                try {
                    $tenant = Tenant::create([
                        'id' => $tenantId,
                        'user_id' => $latestLog->user_id,
                        'theme_slug' => $latestLog->theme_slug,
                        'razorpay_subscription_id' => $subscriptionId,
                        'recurring_enabled' => 1,
                        'start_date' => now(),
                        'expire_date' => $nextExpireDate,
                        'renewal_payment_log_id' => $renewalLog->id,
                    ]);

                    Log::info('Tenant created successfully during renewal', [
                        'tenant_id' => $tenantId,
                        'subscription_id' => $subscriptionId,
                        'renewal_log_id' => $renewalLog->id,
                        'theme_slug' => $latestLog->theme_slug,
                    ]);
                } catch (\Exception $createException) {
                    Log::error('Failed to create tenant during renewal', [
                        'tenant_id' => $tenantId,
                        'error' => $createException->getMessage(),
                        'trace' => $createException->getTraceAsString()
                    ]);
                    return;
                }
            } else {
                // Update existing tenant
                $updateData = [
                    'renew_status' => ($tenant->renew_status ?? 0) + 1,
                    'is_renew' => 1,
                    'start_date' => now(),
                    'expire_date' => $nextExpireDate,
                    'renewal_payment_log_id' => $renewalLog->id,
                    'grace_period_end' => null,
                    'razorpay_subscription_id' => $subscriptionId,
                    'recurring_enabled' => 1,
                    'updated_at' => now(),
                ];

                $updatedRows = DB::table('tenants')
                    ->where('id', $tenantId)
                    ->update($updateData);

                Log::info('Tenant updated via DB query during renewal', [
                    'tenant_id' => $tenantId,
                    'updated_rows' => $updatedRows,
                    'theme_slug' => $latestLog->theme_slug,
                    'razorpay_subscription_id' => $subscriptionId,
                    'expire_date' => $nextExpireDate,


                ]);
            }

            // ========================================
            // STEP 4: SEND RENEWAL EMAILS
            // ========================================
//            self::sendRenewalMails($renewalLog);

            Log::info('Subscription renewal processed successfully', [
                'subscription_id' => $subscriptionId,
                'payment_id' => $paymentId,
                'tenant_id' => $tenant->id,
                'renewal_log_id' => $renewalLog->id,
                'amount' => $amount,
                'expire_date' => $nextExpireDate,
                'next_billing_date' => $nextBillingDate,
                'theme_slug' => $latestLog->theme_slug,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to handle subscription.charged webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    /**
     * Handle subscription payment failure
     */
//    public static function handleSubscriptionFailed(array $webhookData): void
//    {
//        try {
//            $subscription = $webhookData['payload']['subscription']['entity'] ?? null;
//
//            if (!$subscription) {
//                Log::warning('Invalid webhook data for subscription payment failed');
//                return;
//            }
//
//            $subscriptionId = $subscription['id'];
//
//            // Find the tenant
//            $tenant = Tenant::where('razorpay_subscription_id', $subscriptionId)->first();
//            if (!$tenant) {
//                Log::warning('Tenant not found for failed subscription', ['subscription_id' => $subscriptionId]);
//                return;
//            }
//
//            // Set grace period
//            $graceDays = $tenant->payment_grace_days ?? 3;
//            $tenant->update([
//                'grace_period_end' => now()->addDays($graceDays)
//            ]);
//
//            // Update subscription status
//            PaymentLogs::where('razorpay_subscription_id', $subscriptionId)
//                ->where('payment_status', '!=', 'complete')
//                ->update(['subscription_status' => 'payment_failed']);
//
//            Log::info('Subscription payment failed, grace period set', [
//                'subscription_id' => $subscriptionId,
//                'tenant_id' => $tenant->id,
//                'grace_period_end' => $tenant->grace_period_end
//            ]);
//
//            // TODO: Send email notification to tenant and admin
//
//        } catch (\Exception $e) {
//            Log::error('Failed to handle subscription payment failed webhook', [
//                'error' => $e->getMessage(),
//                'webhook_data' => $webhookData
//            ]);
//        }
//    }


    public static function handleSubscriptionFailed(array $webhookData): void
    {
        try {
            $subscription = $webhookData['payload']['subscription']['entity'] ?? null;

            if (!$subscription) {
                Log::warning('Invalid webhook data for subscription payment failed');
                return;
            }

            $subscriptionId = $subscription['id'];

            // Find tenant using Eloquent
            $tenant = Tenant::where('razorpay_subscription_id', $subscriptionId)->first();
            $paymentLogSubscriptionStatus = PaymentLogs::where('id',$tenant->renewal_payment_log_id )->select('subscription_status')->first();
            Log::info('Renewal payment log info', $paymentLogSubscriptionStatus);

            if (!$tenant) {
                Log::warning('Tenant not found for failed subscription', [
                    'subscription_id' => $subscriptionId
                ]);
                return;
            }

            // Set grace period
            $graceDays = $tenant->payment_grace_days ?? 3;
            $graceEndDate = now()->addDays($graceDays);

            $updateData = [
                'grace_period_end' => $graceEndDate,
                'updated_at' => now(),
            ];

            $updatedRows = DB::table('tenants')
                ->where('razorpay_subscription_id', $subscriptionId)
                ->update($updateData);

            Log::info('Tenant grace period updated via DB query', [
                'subscription_id' => $subscriptionId,
                'grace_end_date' => $graceEndDate,
                'updated_rows' => $updatedRows
            ]);

            // Update related payment logs
            PaymentLogs::where('razorpay_subscription_id', $subscriptionId)
                ->where('payment_status', '!=', 'complete')
                ->update([
                    'subscription_status' => 'payment_failed'
                ]);


            Log::info('Subscription payment failed, grace period set', [
                'subscription_id' => $subscriptionId,
                'tenant_id' => $tenant->id,
                'grace_period_end' => $graceEndDate
            ]);

        } catch (\Throwable $e) {
            Log::error('Failed to handle subscription payment failed webhook', [
                'error' => $e->getMessage(),
                'subscription_id' => $subscription['id'] ?? null
            ]);
        }
    }

    /**
     * Calculate next expiry date based on plan type
     */
    private static function calculateNextExpiry(PricePlan $plan): string
    {
        return match($plan->type) {
            0 => now()->addMonth()->format('Y-m-d H:i:s'),    // Monthly
            1 => now()->addYear()->format('Y-m-d H:i:s'),     // Yearly
            default => now()->addMonth()->format('Y-m-d H:i:s')
        };
    }
    /**
     * Calculate next billing date
     */
    private static function calculateNextBilling(PricePlan $plan): string
    {
        return self::calculateNextExpiry($plan);
    }
    /**
     * Send renewal emails to admin and user
     */
//    private static function sendRenewalMails(PaymentLogs $renewalLog): void
//    {
//        try {
//            $all_fields = [];
//            $all_attachment = [];
//            $order_mail = get_static_option('order_page_form_mail')
//                ? get_static_option('order_page_form_mail')
//                : get_static_option('site_global_email');
//
//            // Send to admin
//            Mail::to($order_mail)->send(new BasicMail($all_fields, $all_attachment, $renewalLog, "admin", 'regular'));
//
//            // Send to user
//            Mail::to($renewalLog->email)->send(new BasicMail($all_fields, $all_attachment, $renewalLog, 'user', 'regular'));
//
//            Log::info('Renewal emails sent', ['payment_log_id' => $renewalLog->id]);
//
//        } catch (\Exception $e) {
//            Log::error('Failed to send renewal emails', [
//                'payment_log_id' => $renewalLog->id,
//                'error' => $e->getMessage()
//            ]);
//        }
//    }



}
