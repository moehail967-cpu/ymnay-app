<?php

// Add this debug method to see ALL payment logs for your user
// This will help identify which record is being used where

namespace App\Helpers;

use App\Models\PaymentLogs;
use Illuminate\Support\Facades\Log;

class PaymentLogDebugHelper
{
    public static function debugAllPaymentLogs($location = 'unknown')
    {
        $user = auth()->user();
        $tenant = tenant();

        Log::info("PAYMENT LOGS DEBUG at {$location}:", [
            'user_id' => $user ? $user->id : 'none',
            'tenant_id' => $tenant ? $tenant->id : 'none',
        ]);

        if ($user) {
            // Get ALL payment logs for this user
            $userPaymentLogs = PaymentLogs::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($userPaymentLogs as $index => $log) {
                Log::info("Payment Log #{$index} for user {$user->id}:", [
                    'id' => $log->id,
                    'status' => $log->status,
                    'expire_date' => $log->expire_date,
                    'is_expired' => $log->expire_date < now(),
                    'tenant_id' => $log->tenant_id,
                    'created_at' => $log->created_at,
                    'updated_at' => $log->updated_at,
                ]);
            }
        }

        if ($tenant) {
            // Get ALL payment logs for this tenant
            $tenantPaymentLogs = PaymentLogs::where('tenant_id', $tenant->id)
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($tenantPaymentLogs as $index => $log) {
                Log::info("Payment Log #{$index} for tenant {$tenant->id}:", [
                    'id' => $log->id,
                    'status' => $log->status,
                    'expire_date' => $log->expire_date,
                    'is_expired' => $log->expire_date < now(),
                    'user_id' => $log->user_id,
                    'created_at' => $log->created_at,
                    'updated_at' => $log->updated_at,
                ]);
            }
        }
    }

    public static function getCorrectPaymentLog()
    {
        $user = auth()->user();
        $tenant = tenant();

        // Try to get the payment log the same way your app does
        $methods = [
            'latest_by_user_complete' => function() use ($user) {
                return PaymentLogs::where('user_id', $user->id)
                    ->where('status', 'complete')
                    ->latest()
                    ->first();
            },
            'latest_by_user_any' => function() use ($user) {
                return PaymentLogs::where('user_id', $user->id)
                    ->latest()
                    ->first();
            },
            'latest_by_tenant_complete' => function() use ($tenant) {
                return $tenant ? PaymentLogs::where('tenant_id', $tenant->id)
                    ->where('status', 'complete')
                    ->latest()
                    ->first() : null;
            },
            'latest_by_tenant_any' => function() use ($tenant) {
                return $tenant ? PaymentLogs::where('tenant_id', $tenant->id)
                    ->latest()
                    ->first() : null;
            },
            'latest_expire_date' => function() use ($user) {
                return PaymentLogs::where('user_id', $user->id)
                    ->orderBy('expire_date', 'desc')
                    ->first();
            }
        ];

        foreach ($methods as $method => $callable) {
            $result = $callable();
            if ($result) {
                Log::info("Payment Log found via {$method}:", [
                    'id' => $result->id,
                    'expire_date' => $result->expire_date,
                    'is_expired' => $result->expire_date < now(),
                    'status' => $result->status,
                ]);
            } else {
                Log::info("Payment Log NOT found via {$method}");
            }
        }
    }
}
