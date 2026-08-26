<?php

namespace Modules\TrackingPixel\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\TrackingPixel\Models\TrackingPixelEventLog;

class TrackingPixelController extends Controller
{
    private function plugin()
    {
        return app(\App\PluginSystem\PluginManager::class)->get('tracking-pixel');
    }

    public function settings()
    {
        $plugin = $this->plugin();
        if (!$plugin) abort(500, 'Plugin not available');
        $options = [];
        $keys = [
            'ga4_enabled','ga4_measurement_id','ga4_server_enabled','ga4_server_api_secret',
            'gtm_enabled','gtm_container_id',
            'meta_pixel_enabled','meta_pixel_id','meta_server_enabled','meta_access_token','meta_test_event_code',
            'tiktok_pixel_enabled','tiktok_pixel_id','tiktok_server_enabled','tiktok_access_token',
        ];
        foreach ($keys as $key) {
            $options[$key] = $plugin->get_option($key, '');
        }

        return view('tracking-pixel::admin.settings', compact('options'));
    }

    public function saveSettings(Request $request)
    {
        $plugin = $this->plugin();
        if (!$plugin) abort(500, 'Plugin not available');
        $toggles = [
            'ga4_enabled','ga4_server_enabled','gtm_enabled',
            'meta_pixel_enabled','meta_server_enabled',
            'tiktok_pixel_enabled','tiktok_server_enabled',
        ];
        $texts = [
            'ga4_measurement_id','ga4_server_api_secret','gtm_container_id',
            'meta_pixel_id','meta_access_token','meta_test_event_code',
            'tiktok_pixel_id','tiktok_access_token',
        ];

        foreach ($toggles as $key) {
            $plugin->update_option($key, $request->has($key) ? '1' : '0');
        }
        foreach ($texts as $key) {
            $plugin->update_option($key, $request->input($key, ''));
        }

        return back()->with('success', __('Tracking settings saved.'));
    }

    public function log()
    {
        $tenantId = function_exists('tenant') && tenant() ? (string) tenant('id') : null;
        $events = TrackingPixelEventLog::where('tenant_id', $tenantId)
            ->orderByDesc('fired_at')
            ->limit(50)
            ->get();

        return view('tracking-pixel::admin.log', compact('events'));
    }

    public function test(Request $request)
    {
        $channel = $request->input('channel');
        $allowed = ['ga4', 'meta', 'tiktok'];
        if (!in_array($channel, $allowed)) {
            return response()->json(['error' => 'Invalid channel'], 422);
        }

        $plugin = $this->plugin();
        if (!$plugin) abort(500, 'Plugin not available');
        $tenantId = function_exists('tenant') && tenant() ? (string) tenant('id') : null;

        // Build a fake order object
        $fakeOrder = (object)[
            'id'            => 0,
            'total_amount'  => 1.00,
            'currency_code' => 'USD',
            'user'          => (object)['email' => 'test@example.com', 'phone' => ''],
            'billing_phone' => '',
            'items'         => [],
        ];

        // Dispatch synchronously for the test
        try {
            $job = new \Modules\TrackingPixel\Jobs\SendServerConversionJob($fakeOrder, $tenantId);
            $job->handle();

            $last = TrackingPixelEventLog::where('tenant_id', $tenantId)
                ->where('channel', $channel)
                ->where('order_id', 0)
                ->orderByDesc('fired_at')
                ->first();

            return response()->json([
                'status'   => $last?->http_status ?? 0,
                'response' => $last?->response_snippet ?? 'No response logged',
            ]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
