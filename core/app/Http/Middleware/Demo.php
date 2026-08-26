<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Demo
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Allow Razorpay webhook to bypass all demo restrictions
        if ($request->path() === 'razorpay-subscription-webhook') {
            return $next($request);
        }

        // Check if host contains 'xgenious' or 'bytesed'
        /*
         * $host = $request->getHost();
        $isDemoHost = Str::contains(
            $host,
            [
                'xgenious.com',
                'bytesed.com',
                'nazmart.net',
                'multipurposesass.com',
                'hexfashion.xyz',
                'picajobfind.xyz'
            ]
        );
        // If not a demo host, skip all demo restrictions
        if (!$isDemoHost) {
            return $next($request);
        }
         */
        //        $host = $request->getHost();
//        $isDemoHost = Str::contains($host, ['xgenious', 'bytesed']);

        //        // If not a demo host, skip all demo restrictions
//        if (!$isDemoHost) {
//            return $next($request);
//        }

        $host = $request->getHost();
        $isDemoHost = Str::contains(
            $host,
            [
                'xgenious.com',
                'bytesed.com',
                'nazmart.net',
                'multipurposesass.com',
                'hexfashion.xyz',
                'picajobfind.xyz',
            ]
        );
        Log::info('demo middleware paths: ', (array) $host);
        // If not a demo host, skip all demo restrictions
        if (!$isDemoHost) {
            return $next($request);
        }

        if (App::isLocal() || App::hasDebugModeEnabled() || $request->ip() === '75.119.131.29') {
            return $next($request);
        }

        $not_allow_path = [
            'register',
            'admin-home',
            'user-home',
            'custom-domain',
            'user/profile-update',
            'user/password-change',
            'order-confirm',
            'user/trial/account',
            'media-upload/media-upload',
            'admin-home/logs',
            'admin-home/commission-manage/history/withdraw/{id}/delete',
            'admin-home/commission-manage/history/{id}/delete',
            'api/page-builder/save',
        ];

        $allow_path = [
            'admin-home/visited/os',
            'admin-home/visited/browser',
            'admin-home/visited/device',
            'admin-home/visited-url',
            'admin-home/topbar/chart',
            'admin-home/topbar/chart/day',

            's/cart/ajax/add-to-cart',
            's/cart/ajax/update-quantity',
            's/cart/ajax/remove',
            'admin-home/pos/hold-order',
            'admin-home/domain-reseller/search-domain',
            'razorpay-subscription-webhook', // Allow Razorpay webhook

            // Allow media library browsing (read-only); only actual upload is blocked
            'media-upload/media-upload/all',
            'media-upload/media-upload/loadmore',
        ];

        $not_allow_get_path = [
            'admin-home/product/destroy',
            'admin-home/product/clone',
            'admin-home/product/trash/restore',
            'admin-home/product/trash/delete',
        ];

        $contains = Str::contains($request->path(), $not_allow_path);
        $msg = 'This is demonstration purpose only, you may not able to change few settings, you will get working file when you purchased it.';

        if ($request->isMethod('GET')) {
            foreach ($not_allow_get_path as $path) {
                if (Str::contains($request->path(), $path)) {
                    return redirect()->back()->with(['type' => 'warning', 'msg' => $msg]);
                }
            }
        }

        if ($request->isMethod('POST') || $request->isMethod('PUT')) {
            if (Str::is('api/*', $request->path())) {
                $api_not_allow = [
                    'update-profile',
                    'refund-ticket/chat/send',
                    'page-builder/save',
                    'page-builder/publish',
                    'page-builder/unpublish',
                    'save-all-settings',
                    'save-general-settings',
                    'save-style-settings',
                    'save-advanced-settings',
                ];

                foreach ($api_not_allow as $item) {
                    if (Str::contains($request->path(), $item)) {
                        return response()->json(['type' => 'warning', 'msg' => $msg]);
                    }
                }
            }

            if ($request->path() === 'register') {
                return response()->json(
                    [
                        'errors' => [
                            'demo_error' => ['This is demonstration purpose only, you are not allowed to register account, use demo user login details instead'],
                            'username' => ['username: test'],
                            'password' => ['Password: 12345678']
                        ]
                    ],
                    422
                );
            }

            if (str_contains(url()->previous(), 'pos') && !in_array($request->path(), $allow_path)) {
                return response()->json([
                    'type' => 'error',
                    'msg' => $msg,
                    'timer' => 3000
                ]);
            }

            if ($contains && !in_array($request->path(), $allow_path)) {
                if ($request->ajax()) {
                    return response()->json(['type' => 'warning', 'msg' => $msg]);
                }
                return redirect()->back()->with(['type' => 'warning', 'msg' => $msg]);
            }
        }

        return $next($request);
    }
}
