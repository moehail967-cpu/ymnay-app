<?php

// Add this debug function to your app/Helpers/ directory or directly in your controller
// File: app/Helpers/PackageDebugHelper.php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PackageDebugHelper
{
    public static function debugPackageStatus($location = 'unknown')
    {
        try {
            $user = Auth::user();
            $tenant = tenant();

            $debugData = [
                'location' => $location,
                'timestamp' => now()->toDateTimeString(),
                'user_id' => $user ? $user->id : 'guest',
                'tenant_id' => $tenant ? $tenant->id : 'no_tenant',
            ];

            // Check if user has package relationship
            if ($user) {
                // Try different possible relationships for package
                $possiblePackageRelations = ['package', 'plan', 'subscription', 'current_plan'];

                foreach ($possiblePackageRelations as $relation) {
                    if (method_exists($user, $relation)) {
                        $package = $user->$relation;
                        if ($package) {
                            $debugData['package_found_via'] = $relation;
                            $debugData['package_id'] = $package->id;
                            $debugData['package_title'] = $package->title ?? 'no_title';

                            // Check various possible expiration fields
                            $possibleExpiryFields = ['expires_at', 'expire_date', 'expiry_date', 'end_date'];
                            foreach ($possibleExpiryFields as $field) {
                                if (isset($package->$field)) {
                                    $debugData['expiry_field'] = $field;
                                    $debugData['expiry_date'] = $package->$field;
                                    $debugData['is_expired'] = $package->$field < now();
                                    $debugData['days_difference'] = now()->diffInDays($package->$field, false);
                                    break;
                                }
                            }
                            break;
                        }
                    }
                }

                // Also check payment logs
                $paymentLog = \App\Models\PaymentLogs::where('user_id', $user->id)
                    ->orWhere('tenant_id', $tenant ? $tenant->id : 0)
                    ->latest()
                    ->first();

                if ($paymentLog) {
                    $debugData['payment_log'] = [
                        'id' => $paymentLog->id,
                        'status' => $paymentLog->status ?? 'no_status',
                        'expire_date' => $paymentLog->expire_date ?? 'no_expire_date',
                        'is_expired' => isset($paymentLog->expire_date) ? $paymentLog->expire_date < now() : 'unknown',
                    ];
                }
            }

            // Check session data
            $debugData['session_data'] = [
                'package_data' => session('package_data'),
                'user_package' => session('user_package'),
                'tenant_package' => session('tenant_package'),
            ];

            // Check cache
            $cacheKeys = [
                'user_package_' . ($user ? $user->id : 'guest'),
                'tenant_package_' . ($tenant ? $tenant->id : 'no_tenant'),
                'package_status_' . ($user ? $user->id : 'guest'),
            ];

            foreach ($cacheKeys as $key) {
                $cachedValue = cache()->get($key);
                if ($cachedValue) {
                    $debugData['cache'][$key] = $cachedValue;
                }
            }

            Log::info('PACKAGE DEBUG: ' . $location, $debugData);

            // Also dump to screen if in debug mode
            if (config('app.debug')) {
                dump("PACKAGE DEBUG at {$location}:", $debugData);
            }

            return $debugData;

        } catch (\Exception $e) {
            Log::error('Package debug failed', [
                'location' => $location,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public static function debugMiddleware()
    {
        // Find all middleware that might be checking package expiration
        $middlewareList = [];

        // Check common middleware locations
        $middlewarePaths = [
            app_path('Http/Middleware'),
            // Add other paths where middleware might be located
        ];

        foreach ($middlewarePaths as $path) {
            if (is_dir($path)) {
                $files = glob($path . '/*.php');
                foreach ($files as $file) {
                    $content = file_get_contents($file);
                    if (strpos($content, 'package') !== false &&
                        (strpos($content, 'expired') !== false || strpos($content, 'expire') !== false)) {
                        $middlewareList[] = basename($file);
                    }
                }
            }
        }

        Log::info('PACKAGE DEBUG: Potential package-checking middleware found', $middlewareList);

        return $middlewareList;
    }

}
