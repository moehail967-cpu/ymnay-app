<?php

namespace Modules\FeedSync\Src;

use App\PluginSystem\PluginBase;
use Illuminate\Support\Str;
use Modules\FeedSync\Http\Controllers\FeedController;
use Modules\FeedSync\Jobs\RegenerateFeedJob;
use Modules\FeedSync\Models\FeedSyncCache;

class FeedSyncPlugin extends PluginBase
{
    public function id(): string
    {
        return 'feed-sync';
    }

    public function on_activate(): void
    {
        $this->run_migrations();

        // Generate a unique public token for this tenant's feed URLs
        if (!$this->get_option('feed_token')) {
            $this->update_option('feed_token', Str::random(32));
        }

        $defaults = [
            'google_merchant_enabled'     => '0',
            'google_shipping_country'     => 'US',
            'google_shipping_price'       => '0.00',
            'google_brand_field'          => 'product_brand',
            'google_custom_brand'         => '',
            'google_condition'            => 'new',
            'facebook_catalog_enabled'    => '0',
            'facebook_include_sale_price' => '0',
            'facebook_availability'       => 'in stock',
        ];

        foreach ($defaults as $key => $value) {
            if ($this->get_option($key) === null) {
                $this->update_option($key, $value);
            }
        }
    }

    public function on_deactivate(): void
    {
        // Caches cleared; tokens preserved so re-enabling doesn't break existing feed URLs
    }

    public function on_update(string $from_version): void
    {
        $this->run_migrations();
    }

    public function routes(): void
    {
        $this->registerSettings();
        $this->registerRoutes();
    }

    public function boot(): void
    {
        $this->registerSettings();

        if (!function_exists('tenant') || !tenant()) return;

        $this->registerMenus();
        $this->registerHooks();
    }

    private function registerSettings(): void
    {
        $this->register_settings([
            // ── Google Merchant ─────────────────────────────
            ['key' => 'google_merchant_enabled',     'label' => 'Enable Google Merchant Feed',   'type' => 'toggle',  'group' => 'Google Merchant Center'],
            ['key' => 'google_shipping_country',     'label' => 'Shipping Country (ISO code)',   'type' => 'text',    'group' => 'Google Merchant Center', 'description' => 'e.g. US, GB, DE'],
            ['key' => 'google_shipping_price',       'label' => 'Shipping Price',                'type' => 'number',  'group' => 'Google Merchant Center', 'description' => 'Use 0 for free shipping'],
            ['key' => 'google_brand_field',          'label' => 'Brand Source',                  'type' => 'select',  'group' => 'Google Merchant Center', 'options' => ['product_brand' => 'Product Brand', 'store_name' => 'Store Name', 'custom' => 'Custom Value']],
            ['key' => 'google_custom_brand',         'label' => 'Custom Brand Value',            'type' => 'text',    'group' => 'Google Merchant Center'],
            ['key' => 'google_condition',            'label' => 'Product Condition',             'type' => 'select',  'group' => 'Google Merchant Center', 'options' => ['new' => 'New', 'used' => 'Used', 'refurbished' => 'Refurbished']],
            // ── Facebook Catalog ────────────────────────────
            ['key' => 'facebook_catalog_enabled',    'label' => 'Enable Facebook Catalog Feed',  'type' => 'toggle',  'group' => 'Facebook / Meta Product Catalog'],
            ['key' => 'facebook_include_sale_price', 'label' => 'Include Sale Price',            'type' => 'toggle',  'group' => 'Facebook / Meta Product Catalog'],
            ['key' => 'facebook_availability',       'label' => 'Default Availability',          'type' => 'select',  'group' => 'Facebook / Meta Product Catalog', 'options' => ['in stock' => 'In Stock', 'preorder' => 'Preorder', 'available for order' => 'Available for Order']],
        ]);
    }

    private function registerMenus(): void
    {
        $this->add_menu([
            'id'      => 'feed-sync-menu',
            'label'   => __('Product Feeds'),
            'icon'    => 'mdi-rss',
            'route'   => 'tenant.admin.feed-sync.urls',
            'order'   => 86,
            'context' => 'tenant',
        ]);

        $this->add_submenu('feed-sync-menu', [
            'id'    => 'feed-sync-urls',
            'label' => __('Feed URLs'),
            'icon'  => 'mdi-link-variant',
            'route' => 'tenant.admin.feed-sync.urls',
        ]);

        $this->add_submenu('feed-sync-menu', [
            'id'    => 'feed-sync-settings',
            'label' => __('Settings'),
            'icon'  => 'mdi-cog',
            'route' => 'tenant.admin.feed-sync.settings',
        ]);
    }

    private function registerHooks(): void
    {
        // Invalidate cache whenever a product is saved
        $this->add_action('nazmart:after_product_save', function ($product, $action) {
            $tenantId = $this->currentTenantId();
            if ($tenantId) {
                FeedSyncCache::where('tenant_id', $tenantId)->delete();
            }
        });

        // Daily regeneration for all tenants that have this plugin active
        $this->schedule('daily', function () {
            $tenantId = $this->currentTenantId();
            if (!$tenantId) return;

            $options = $this->buildOptionsArray();
            dispatch(new RegenerateFeedJob($tenantId, $options));
        });
    }

    private function registerRoutes(): void
    {
        // Public feed endpoints — no auth
        $this->register_web_routes(function () {
            \Route::get('/feeds/google-merchant/{token}.xml', [FeedController::class, 'google'])
                ->name('feed-sync.google');
            \Route::get('/feeds/facebook-catalog/{token}.xml', [FeedController::class, 'facebook'])
                ->name('feed-sync.facebook');

            // Admin routes
            \Route::middleware([
                    'web',
                    \App\Http\Middleware\Tenant\InitializeTenancyByDomainCustomisedMiddleware::class,
                    \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
                    'auth:admin',
                    'tenant_admin_glvar',
                    'package_expire',
                    'tenantAdminPanelMailVerify',
                    'tenant_status',
                    'set_lang',
                ])
                ->prefix('admin-home/feed-sync')
                ->name('tenant.admin.feed-sync.')
                ->group(function () {
                    \Route::get('/urls',       [FeedController::class, 'feedUrls'])->name('urls');
                    \Route::get('/settings',   [FeedController::class, 'settings'])->name('settings');
                    \Route::post('/settings',  [FeedController::class, 'saveSettings'])->name('settings.save');
                    \Route::post('/regenerate',[FeedController::class, 'regenerate'])->name('regenerate');
                });
        });
    }

    private function buildOptionsArray(): array
    {
        $keys = [
            'google_merchant_enabled','google_shipping_country','google_shipping_price',
            'google_brand_field','google_custom_brand','google_condition',
            'facebook_catalog_enabled','facebook_include_sale_price','facebook_availability',
        ];
        $options = [];
        foreach ($keys as $key) {
            $options[$key] = $this->get_option($key, '');
        }
        $options['store_name'] = get_static_option('site_title') ?? config('app.name');
        $options['store_url']  = function_exists('tenant_domain_url') ? tenant_domain_url() : url('/');
        $options['currency']   = strtoupper(get_static_option('site_currency') ?? 'USD');
        return $options;
    }

    private function currentTenantId(): ?string
    {
        return function_exists('tenant') && tenant() ? (string) tenant('id') : null;
    }
}
