<?php

namespace Modules\FeedSync\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\FeedSync\Jobs\RegenerateFeedJob;
use Modules\FeedSync\Models\FeedSyncCache;

class FeedController extends Controller
{
    private function plugin()
    {
        return app(\App\PluginSystem\PluginManager::class)->get('feed-sync');
    }

    /** Public feed — no auth, protected by secret token */
    public function google(string $token): Response
    {
        return $this->serveFeed($token, 'google');
    }

    public function facebook(string $token): Response
    {
        return $this->serveFeed($token, 'facebook');
    }

    private function serveFeed(string $token, string $type): Response
    {
        // Resolve tenant by token from plugin_options
        $row = \DB::table('plugin_options')
            ->where('plugin_id', 'feed-sync')
            ->where('option_key', 'feed_token')
            ->where('option_value', $token)
            ->first();

        if (!$row) {
            abort(404);
        }

        $cached = FeedSyncCache::where('tenant_id', $row->tenant_id)
            ->where('feed_type', $type)
            ->first();

        if (!$cached) {
            abort(404, 'Feed not yet generated. Please regenerate from the admin panel.');
        }

        return response($cached->content, 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    // ── Admin actions ────────────────────────────────────────

    public function settings()
    {
        $plugin = $this->plugin();
        if (!$plugin) abort(500, 'Plugin not available');
        $keys = [
            'google_merchant_enabled','google_shipping_country','google_shipping_price',
            'google_brand_field','google_custom_brand','google_condition',
            'facebook_catalog_enabled','facebook_include_sale_price','facebook_availability',
        ];
        $options = [];
        foreach ($keys as $key) {
            $options[$key] = $plugin->get_option($key, '');
        }
        return view('feed-sync::admin.settings', compact('options'));
    }

    public function saveSettings(Request $request)
    {
        $plugin = $this->plugin();
        if (!$plugin) abort(500, 'Plugin not available');

        $toggles = ['google_merchant_enabled','facebook_catalog_enabled','facebook_include_sale_price'];
        $texts   = ['google_shipping_country','google_shipping_price','google_brand_field','google_custom_brand','google_condition','facebook_availability'];

        foreach ($toggles as $key) {
            $plugin->update_option($key, $request->has($key) ? '1' : '0');
        }
        foreach ($texts as $key) {
            $plugin->update_option($key, $request->input($key, ''));
        }

        return back()->with('success', __('Feed settings saved.'));
    }

    public function feedUrls()
    {
        $plugin   = $this->plugin();
        if (!$plugin) abort(500, 'Plugin not available');
        $token    = $plugin->get_option('feed_token', '');
        $tenantId = function_exists('tenant') && tenant() ? (string) tenant('id') : null;

        $googleCache   = FeedSyncCache::where('tenant_id', $tenantId)->where('feed_type', 'google')->first();
        $facebookCache = FeedSyncCache::where('tenant_id', $tenantId)->where('feed_type', 'facebook')->first();

        $googleUrl   = $token ? url("/feeds/google-merchant/{$token}.xml") : null;
        $facebookUrl = $token ? url("/feeds/facebook-catalog/{$token}.xml") : null;

        return view('feed-sync::admin.feed-urls', compact('googleUrl', 'facebookUrl', 'googleCache', 'facebookCache'));
    }

    public function regenerate(Request $request)
    {
        $plugin   = $this->plugin();
        if (!$plugin) abort(500, 'Plugin not available');
        $tenantId = function_exists('tenant') && tenant() ? (string) tenant('id') : null;

        $optionKeys = [
            'google_merchant_enabled','google_shipping_country','google_shipping_price',
            'google_brand_field','google_custom_brand','google_condition',
            'facebook_catalog_enabled','facebook_include_sale_price','facebook_availability',
        ];
        $options = [];
        foreach ($optionKeys as $key) {
            $options[$key] = $plugin->get_option($key, '');
        }

        $options['store_name'] = get_static_option('site_title') ?? config('app.name');
        $options['store_url']  = url('/');
        $options['currency']   = get_static_option('site_currency') ?? 'USD';

        dispatch_sync(new RegenerateFeedJob($tenantId, $options));

        $googleCache   = FeedSyncCache::where('tenant_id', $tenantId)->where('feed_type', 'google')->first();
        $facebookCache = FeedSyncCache::where('tenant_id', $tenantId)->where('feed_type', 'facebook')->first();

        return response()->json([
            'google_generated_at'   => $googleCache?->generated_at?->diffForHumans(),
            'facebook_generated_at' => $facebookCache?->generated_at?->diffForHumans(),
        ]);
    }
}
