<?php

namespace App\Traits;

use App\Actions\Sms\SmsSendAction;
use App\Helpers\TenantHelper\TenantHelpers;
use App\Models\PaymentLogs;
use App\Models\Tenant;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait PaymentUpdateTrait
{
    //    private function update_database($order_id, $transaction_id)
//    {
//        $paymentLogs = PaymentLogs::find($order_id);
//
//        if (!$paymentLogs) {
//            return; // optional: handle not found case
//        }
//        $paymentLogs = PaymentLogs::where('id', $order_id)->update([
//            'transaction_id' => $transaction_id,
//            'status' => 'complete',
//            'payment_status' => 'complete',
//            'updated_at' => Carbon::now()
//        ]);
//        event(new OrderCompleted($paymentLogs, tenant()));
//    }
    private function update_database($order_id, $transaction_id, $razorpay_subscription_id = null)
    {
        // First, fetch the model
        $paymentLogs = PaymentLogs::find($order_id);

        if (!$paymentLogs) {
            return; // optional: handle not found case
        }

        // Prepare update data
        $updateData = [
            'transaction_id' => $transaction_id,
            'status' => 'complete',
            'payment_status' => 'complete',
            'updated_at' => Carbon::now(),
        ];

        // Add subscription ID if provided
        if ($razorpay_subscription_id) {
            $updateData['razorpay_subscription_id'] = $razorpay_subscription_id;
            $updateData['subscription_status'] = 'active'; // Mark subscription as active
        }

        // Then update its attributes
        $paymentLogs->update($updateData);

        Log::info('Payment database updated', [
            'order_id' => $order_id,
            'transaction_id' => $transaction_id,
            'razorpay_subscription_id' => $razorpay_subscription_id ?? 'none'
        ]);
    }


    public function update_tenant($payment_data, $renewal_after_expire_date_payment_data = null)
    {
        //
//        dd($payment_data);
        try {
            // Handle different input types (array or object)
            $order_id = is_array($payment_data) ? ($payment_data['order_id'] ?? null) : ($payment_data->id ?? null);
            if (!$order_id) {
                Log::error('Invalid payment_data provided to update_tenant');
                return false;
            }

            $payment_log = PaymentLogs::where('id', $order_id)->first();
            if (!$payment_log) {
                Log::error('Payment log not found for ID: ' . $order_id);
                return false;
            }
            $renewal_at = Carbon::createFromFormat('d-m-Y H:i:s', $payment_log->start_date)->format('Y-m-d H:i:s');

            // Get or create tenant
            $tenant = Tenant::find($payment_log->tenant_id);
            if (!$tenant) {
                $tenant = Tenant::create([
                    'id' => $payment_log->tenant_id,
                    'user_id' => $payment_log->user_id,
                    'theme_slug' => $payment_log->theme_slug ?? 'hexfashion'
                ]);
            }
            Log::info('Tenant go payment status: ' . $payment_log->status);

            if ($payment_log->payment_status == 'complete') {
                // Prevent duplicate updates UNLESS subscription data needs updating
                $needsSubscriptionUpdate = false;
                if ($payment_log->razorpay_subscription_id && $tenant->razorpay_subscription_id != $payment_log->razorpay_subscription_id) {
                    $needsSubscriptionUpdate = true;
                    Log::info('Subscription ID needs updating', [
                        'tenant_id' => $tenant->id,
                        'current' => $tenant->razorpay_subscription_id ?? 'NULL',
                        'new' => $payment_log->razorpay_subscription_id
                    ]);
                }

                if (!$needsSubscriptionUpdate && $tenant->updated_at && Carbon::parse($tenant->updated_at)->gt(Carbon::now()->subSeconds(30))) {
                    Log::info('Tenant update skipped due to recent update: ID=' . $tenant->id);
                    return true;
                }

                $tenantHelper = TenantHelpers::init()
                    ->setTenantId($tenant->id)
                    ->setPaymentLog($payment_log)
                    ->setPackage($payment_log->package)
                    ->setIsRenew($payment_log->is_renew == 1);

                // Calculate expire date properly
                $expire_date = $tenantHelper->getExpiredDate();

                DB::beginTransaction();

                // Update tenant table
                $newRenewStatus = is_null($tenant->renew_status) ? 0 : $tenant->renew_status + 1;
                Log::info('Tenant go');

                $tenantUpdateData = [
                    'renew_status' => $newRenewStatus,
                    'is_renew' => $payment_log->is_renew ?? 0,
                    'start_date' => $payment_log->start_date,
                    'expire_date' => $expire_date,
                    'updated_at' => Carbon::now(),
                    'renewal_at' => $renewal_at,
                    'renewal_after_expire' => $renewal_after_expire_date_payment_data ?? 0,
                    'price_plan_id' => $payment_log->package_id,
                    'renewal_payment_log_id' => $payment_log->id,
                ];

                // Update subscription-related fields if present in payment log
                if ($payment_log->razorpay_subscription_id) {
                    $tenantUpdateData['razorpay_subscription_id'] = $payment_log->razorpay_subscription_id;
                }

                if ($payment_log->is_recurring) {
                    $tenantUpdateData['recurring_enabled'] = 1;
                } else {
                    // When recurring is disabled, explicitly set to 0
                    $tenantUpdateData['recurring_enabled'] = 0;
                }

                DB::table('tenants')->where('id', $tenant->id)->update($tenantUpdateData);
                Log::info('Tenant updated successfully', [
                    'tenant_id' => $tenant->id,
                    'expire_date' => $expire_date,
                    'renewal_after_expire' => $renewal_after_expire_date_payment_data,
                    'razorpay_subscription_id' => $payment_log->razorpay_subscription_id ?? 'none',
                    'recurring_enabled' => $payment_log->is_recurring ? 1 : 0
                ]);

                DB::commit();

                // Send notifications
                try {
                    (new SmsSendAction())->smsSender($tenant->user);
                    if ($payment_log->package_gateway === 'manual_payment') {
                        $tenantHelper->sendSubscriptionApproveMailToUser($payment_log);
                    }
                } catch (\Exception $e) {
                    Log::error('Notification failed: ' . $e->getMessage());
                }

                Log::info('Tenant updated successfully: ID=' . $tenant->id . ', expire_date=' . $expire_date);
                return true;
            }

            Log::error('Tenant update failed: ');
            return false;
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Tenant update failed: ' . $exception->getMessage());

            if (str_contains($exception->getMessage(), 'Access denied')) {
                abort(462, __('Database created failed, Make sure your database user has permission to create database'));
            }

            return false;
        }
    }
}
