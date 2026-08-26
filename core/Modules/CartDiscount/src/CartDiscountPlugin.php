<?php

namespace Modules\CartDiscount\Src;

use App\PluginSystem\PluginBase;
use Modules\CartDiscount\Http\Controllers\CartDiscountApiController;
use Modules\CartDiscount\Http\Controllers\CartDiscountController;

class CartDiscountPlugin extends PluginBase
{
    public function id(): string
    {
        return 'cart-discount';
    }

    public function on_activate(): void
    {
        $defaults = [
            'enabled'            => '0',
            'tiers'              => json_encode([
                ['threshold' => 100,  'type' => 'free_shipping', 'value' => 0,  'label' => 'Free Shipping'],
                ['threshold' => 200,  'type' => 'percentage',    'value' => 10, 'label' => '10% Off'],
                ['threshold' => 400,  'type' => 'percentage',    'value' => 20, 'label' => '20% Off'],
            ]),
            'widget_title'       => 'Unlock Rewards',
            'widget_accent_color'=> '#6366f1',
            'show_locked_tiers'  => '1',
            'apply_to_shipping'  => '0',
            'stack_discounts'    => '0',
        ];

        foreach ($defaults as $key => $value) {
            if ($this->get_option($key) === null) {
                $this->update_option($key, $value);
            }
        }
    }

    public function on_deactivate(): void {}

    public function on_update(string $from_version): void {}

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
            ['key' => 'enabled',             'label' => 'Enable Cart Discounts',          'type' => 'toggle'],
            ['key' => 'tiers',               'label' => 'Discount Tiers',                  'type' => 'repeater',
             'fields' => [
                 ['key' => 'threshold', 'label' => 'Cart Threshold ($)', 'type' => 'number'],
                 ['key' => 'type',      'label' => 'Discount Type',      'type' => 'select', 'options' => ['free_shipping' => 'Free Shipping', 'percentage' => 'Percentage', 'flat' => 'Flat Amount']],
                 ['key' => 'value',     'label' => 'Value',              'type' => 'number'],
                 ['key' => 'label',     'label' => 'Label',              'type' => 'text'],
             ]],
            ['key' => 'widget_title',        'label' => 'Widget Heading',                  'type' => 'text'],
            ['key' => 'widget_accent_color', 'label' => 'Progress Bar Color',              'type' => 'color'],
            ['key' => 'show_locked_tiers',   'label' => 'Show Locked Tiers',               'type' => 'toggle', 'description' => 'Show upcoming tiers in dimmed state'],
            ['key' => 'apply_to_shipping',   'label' => 'Exclude Shipping from Threshold', 'type' => 'toggle'],
            ['key' => 'stack_discounts',     'label' => 'Stack All Unlocked Discounts',    'type' => 'toggle', 'description' => 'Adds all unlocked % discounts instead of using only the highest'],
        ]);
    }

    private function registerMenus(): void
    {
        $this->add_menu([
            'id'      => 'cart-discount-menu',
            'label'   => __('Cart Discounts'),
            'icon'    => 'mdi-percent',
            'route'   => 'tenant.admin.cart-discount.settings',
            'order'   => 88,
            'context' => 'tenant',
        ]);

        $this->add_submenu('cart-discount-menu', [
            'id'    => 'cart-discount-settings',
            'label' => __('Tiers & Settings'),
            'icon'  => 'mdi-cog',
            'route' => 'tenant.admin.cart-discount.settings',
        ]);
    }

    private function registerHooks(): void
    {
        // Apply discount to cart total
        $this->add_filter('nazmart:cart_total', function (float $total) {
            if (!$this->get_option('enabled')) return $total;

            $tiers = json_decode($this->get_option('tiers', '[]'), true) ?? [];
            if (empty($tiers)) return $total;

            $stack = (bool) $this->get_option('stack_discounts');

            if ($stack) {
                $discount = 0;
                foreach ($tiers as $tier) {
                    // cart_discount:threshold — currency plugins convert base threshold to display currency
                    $threshold = (float) apply_filters('cart_discount:threshold', (float) $tier['threshold']);
                    if ($total >= $threshold && $tier['type'] === 'percentage') {
                        $discount += $tier['value'];
                    }
                }
                return $total * (1 - min($discount, 100) / 100);
            }

            $applicable = collect($tiers)
                ->filter(fn($t) => $total >= (float) apply_filters('cart_discount:threshold', (float) $t['threshold']))
                ->sortByDesc('threshold')
                ->first();

            if (!$applicable) return $total;

            // cart_discount:flat_amount — currency plugins convert base flat-discount to display currency
            $flatAmount = (float) apply_filters('cart_discount:flat_amount', (float) ($applicable['value'] ?? 0));

            return match ($applicable['type']) {
                'percentage'    => $total * (1 - $applicable['value'] / 100),
                'flat'          => max(0, $total - $flatAmount),
                'free_shipping' => $total,
                default         => $total,
            };
        }, priority: 20);

        // Load the widget CSS into every storefront page head
        $this->add_action('nazmart:frontend_head', function () {
            if (!$this->get_option('enabled')) return;
            $cssUrl = $this->plugin_url('resources/css/cart-discount.css');
            echo "<link rel=\"stylesheet\" href=\"{$cssUrl}\">\n";
        });

        // Server-side rendered widget — called inside cart/checkout order summaries via
        // {!! apply_filters('nazmart:cart_summary', '') !!}
        $this->add_filter('nazmart:cart_summary', function (string $html): string {
            if (!$this->get_option('enabled')) return $html;
            $widget = \Modules\CartDiscount\Http\Controllers\CartDiscountController::renderWidget($this);
            return $html . $widget;
        });

        // Embed tier config as JS so the cart page can refresh the widget client-side
        // after quantity changes without an extra round-trip to the server.
        $this->add_action('nazmart:frontend_footer', function () {
            if (!$this->get_option('enabled')) return;
            $tiers  = json_decode($this->get_option('tiers', '[]'), true) ?? [];
            $accent = $this->get_option('widget_accent_color', '#6366f1');
            $title  = $this->get_option('widget_title') ?: 'Unlock Rewards';
            $show   = (bool) $this->get_option('show_locked_tiers', '1');
            usort($tiers, fn($a, $b) => $a['threshold'] <=> $b['threshold']);

            // Resolve the current display-currency symbol (cart_discount:symbol lets currency plugins override)
            $symbolData = apply_filters('cart_discount:symbol', site_currency_symbol() ?? '$');
            $symbol     = is_array($symbolData) ? ($symbolData['symbol'] ?? site_currency_symbol()) : $symbolData;

            // Convert each threshold to the display currency via hook
            $convertedTiers = array_map(function ($tier) {
                $tier['threshold'] = (float) apply_filters('cart_discount:threshold', (float) $tier['threshold']);
                return $tier;
            }, $tiers);

            echo "<script>window.cdConfig=" . json_encode([
                'tiers'      => $convertedTiers,
                'symbol'     => $symbol,
                'accent'     => $accent,
                'title'      => $title,
                'showLocked' => $show,
            ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) . ";</script>\n";
            $jsUrl = $this->plugin_url('resources/js/cart-discount.js');
            echo "<script src=\"{$jsUrl}\" defer></script>\n";
        });
    }

    private function registerRoutes(): void
    {
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
                ->prefix('admin-home/cart-discount')
                ->name('tenant.admin.cart-discount.')
                ->group(function () {
                    \Route::get('/settings',  [CartDiscountController::class, 'settings'])->name('settings');
                    \Route::post('/settings', [CartDiscountController::class, 'saveSettings'])->name('settings.save');
                });
        });

        $this->register_api_routes(function ($router) {
            $router->get('/state', [CartDiscountApiController::class, 'state'])->name('api.cart-discount.state');
        });
    }
}
