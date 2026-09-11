<?php

namespace Modules\FomoNotifications\Src;

use App\PluginSystem\PluginBase;
use Modules\FomoNotifications\Http\Controllers\FomoApiController;
use Modules\FomoNotifications\Http\Controllers\FomoController;

class FomoNotificationsPlugin extends PluginBase
{
    public function id(): string
    {
        return 'fomo-notifications';
    }

    public function on_activate(): void
    {
        $this->run_migrations();

        $defaults = [
            'enabled'          => '0',
            'data_source'      => 'mixed',
            'real_order_count' => '10',
            'min_real_orders'  => '3',
            'display_delay'    => '5',
            'display_duration' => '6',
            'cycle_interval'   => '15',
            'loop'             => '1',
            'show_on_pages'    => json_encode(['all']),
            'anonymise_names'  => '1',
        ];

        foreach ($defaults as $key => $value) {
            if ($this->get_option($key) === null) {
                $this->update_option($key, $value);
            }
        }
    }

    public function on_deactivate(): void {}

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
            ['key' => 'enabled',          'label' => 'Enable FOMO Notifications',   'type' => 'toggle'],
            ['key' => 'data_source',      'label' => 'Data Source',                  'type' => 'select',      'options' => ['real' => 'Real Orders', 'synthetic' => 'Synthetic', 'mixed' => 'Mixed']],
            ['key' => 'real_order_count', 'label' => 'Recent Orders to Show',        'type' => 'number',      'description' => 'How many recent completed orders to pull'],
            ['key' => 'min_real_orders',  'label' => 'Min Real Orders (Mixed mode)', 'type' => 'number',      'description' => 'Fall back to synthetic if fewer than this'],
            ['key' => 'display_delay',    'label' => 'Initial Delay (seconds)',       'type' => 'number'],
            ['key' => 'display_duration', 'label' => 'Display Duration (seconds)',    'type' => 'number'],
            ['key' => 'cycle_interval',   'label' => 'Interval Between Popups (seconds)', 'type' => 'number'],
            ['key' => 'loop',             'label' => 'Loop Notifications',           'type' => 'toggle'],
            ['key' => 'anonymise_names',  'label' => 'Anonymise Customer Names',     'type' => 'toggle',      'description' => 'Shows "Sarah F." instead of full name'],
            ['key' => 'show_on_pages',    'label' => 'Show On Pages',                'type' => 'multiselect', 'options' => ['all' => 'All Pages', 'home' => 'Home', 'shop' => 'Shop', 'product' => 'Product', 'cart' => 'Cart', 'checkout' => 'Checkout']],
        ]);
    }

    private function registerMenus(): void
    {
        $this->add_menu([
            'id'      => 'fomo-notifications-menu',
            'label'   => __('FOMO Notifications'),
            'icon'    => 'mdi-bell-ring',
            'route'   => 'tenant.admin.fomo-notifications.settings',
            'order'   => 87,
            'context' => 'tenant',
        ]);

        $this->add_submenu('fomo-notifications-menu', [
            'id'    => 'fomo-settings',
            'label' => __('Settings'),
            'icon'  => 'mdi-cog',
            'route' => 'tenant.admin.fomo-notifications.settings',
        ]);

        $this->add_submenu('fomo-notifications-menu', [
            'id'    => 'fomo-entries',
            'label' => __('Synthetic Entries'),
            'icon'  => 'mdi-table-edit',
            'route' => 'tenant.admin.fomo-notifications.entries',
        ]);

        $this->add_submenu('fomo-notifications-menu', [
            'id'    => 'fomo-preview',
            'label' => __('Preview'),
            'icon'  => 'mdi-eye',
            'route' => 'tenant.admin.fomo-notifications.preview',
        ]);
    }

    private function registerHooks(): void
    {
        $this->add_action('nazmart:frontend_footer', function () {
            if (!$this->get_option('enabled')) return;

            $showOnPages = json_decode($this->get_option('show_on_pages', '["all"]'), true) ?? ['all'];
            if (!$this->currentPageMatches($showOnPages)) return;

            $config = [
                'delay'    => (int) $this->get_option('display_delay', 5),
                'duration' => (int) $this->get_option('display_duration', 6),
                'interval' => (int) $this->get_option('cycle_interval', 15),
                'loop'     => $this->get_option('loop', '1') !== '0',
                'apiUrl'   => route('api.fomo-notifications.entries'),
            ];

            $configJson = e(json_encode($config));
            $cssUrl = $this->plugin_url('resources/css/fomo.css');
            $jsUrl  = $this->plugin_url('resources/js/fomo.js');

            echo <<<HTML
<link rel="stylesheet" href="{$cssUrl}">
<div id="fomo-widget" data-config="{$configJson}"></div>
<script src="{$jsUrl}" defer></script>
HTML;
        });
    }

    private function currentPageMatches(array $pages): bool
    {
        if (in_array('all', $pages)) return true;

        $route = request()->route()?->getName() ?? '';

        foreach ($pages as $page) {
            $hit = match ($page) {
                'home'     => str_contains($route, 'homepage'),
                'shop'     => str_contains($route, 'shop') || str_contains($route, 'all.products'),
                'product'  => str_contains($route, 'product') && !str_contains($route, 'admin'),
                'cart'     => str_contains($route, 'cart'),
                'checkout' => str_contains($route, 'checkout'),
                default    => false,
            };
            if ($hit) return true;
        }

        return false;
    }

    private function registerRoutes(): void
    {
        // Admin routes
        $this->register_web_routes(function () {
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
                ->prefix('admin-home/fomo-notifications')
                ->name('tenant.admin.fomo-notifications.')
                ->group(function () {
                    \Route::get('/settings',   [FomoController::class, 'settings'])->name('settings');
                    \Route::post('/settings',  [FomoController::class, 'saveSettings'])->name('settings.save');
                    \Route::get('/entries',    [FomoController::class, 'entries'])->name('entries');
                    \Route::post('/entries',   [FomoController::class, 'storeEntry'])->name('entries.store');
                    \Route::put('/entries/{id}',   [FomoController::class, 'updateEntry'])->name('entries.update');
                    \Route::delete('/entries/{id}',[FomoController::class, 'destroyEntry'])->name('entries.destroy');
                    \Route::post('/entries/{id}/toggle', [FomoController::class, 'toggleEntry'])->name('entries.toggle');
                    \Route::get('/preview',    [FomoController::class, 'preview'])->name('preview');
                });
        });

        // Public API endpoint — serves entries to frontend JS
        $this->register_api_routes(function ($router) {
            $router->get('/entries', [FomoApiController::class, 'entries'])->name('api.fomo-notifications.entries');
        });
    }
}
