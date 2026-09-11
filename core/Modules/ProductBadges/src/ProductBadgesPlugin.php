<?php

namespace Modules\ProductBadges\Src;

use App\PluginSystem\PluginBase;

class ProductBadgesPlugin extends PluginBase
{
    public function id(): string
    {
        return 'product-badges';
    }

    public function boot(): void
    {
        // Inject badge HTML into every rendered product card
        $this->add_filter('nazmart:render_product_card', [$this, 'injectBadges'], 10, 2);

        // Inject badge CSS into the storefront <head>
        $this->add_action('nazmart:frontend_head_end', [$this, 'injectStyles'], 10);

        $this->register_settings([
            ['key' => 'new_days',        'label' => '"New" badge — product added within (days)', 'type' => 'number', 'default' => '14'],
            ['key' => 'low_stock_qty',   'label' => '"Low Stock" badge — quantity below',         'type' => 'number', 'default' => '5'],
            ['key' => 'hot_order_count', 'label' => '"Hot" badge — orders in last 30 days above', 'type' => 'number', 'default' => '20'],
            ['key' => 'show_sale',       'label' => 'Show "Sale" badge when discount applied',    'type' => 'toggle', 'default' => '1'],
        ]);

        $this->add_menu([
            'id'      => 'product-badges-menu',
            'label'   => __('Product Badges'),
            'icon'    => 'mdi-tag-multiple-outline',
            'route'   => 'tenant.admin.product-badges.settings',
            'order'   => 68,
            'context' => 'tenant',
        ]);
    }

    public function on_activate(): void
    {
        $this->update_option('new_days',        '14');
        $this->update_option('low_stock_qty',   '5');
        $this->update_option('hot_order_count', '20');
        $this->update_option('show_sale',       '1');
    }

    public function on_update(string $from_version): void {} // No migrations needed

    // ── Filter callbacks ──────────────────────────────────────────────────────

    /**
     * $html  — the already-rendered product card HTML
     * $product — the product model/object passed as second arg
     */
    public function injectBadges(string $html, mixed $product = null): string
    {
        if (!$product) {
            return $html;
        }

        $badges = $this->resolveBadges($product);

        if (empty($badges)) {
            return $html;
        }

        $badgeHtml = '<div class="pb-badge-wrap">';
        foreach ($badges as $badge) {
            $badgeHtml .= "<span class=\"pb-badge pb-badge--{$badge['key']}\">{$badge['label']}</span>";
        }
        $badgeHtml .= '</div>';

        // Inject right after the opening wrapper div (first <div...>)
        return preg_replace('/(<div[^>]*>)/i', '$1' . $badgeHtml, $html, 1);
    }

    public function injectStyles(): void
    {
        echo <<<CSS
        <style>
        .pb-badge-wrap{position:absolute;top:8px;left:8px;display:flex;flex-direction:column;gap:4px;z-index:10;}
        .pb-badge{display:inline-block;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:700;line-height:1.6;letter-spacing:.03em;text-transform:uppercase;}
        .pb-badge--new{background:#6366f1;color:#fff;}
        .pb-badge--sale{background:#ef4444;color:#fff;}
        .pb-badge--hot{background:#f97316;color:#fff;}
        .pb-badge--low-stock{background:#eab308;color:#fff;}
        </style>
        CSS;
    }

    // ── Internal ──────────────────────────────────────────────────────────────

    private function resolveBadges(mixed $product): array
    {
        $badges = [];

        $newDays      = (int) $this->get_option('new_days', 14);
        $lowStockQty  = (int) $this->get_option('low_stock_qty', 5);
        $hotThreshold = (int) $this->get_option('hot_order_count', 20);
        $showSale     = (bool) $this->get_option('show_sale', '1');

        // "New" — created within configured days
        $createdAt = data_get($product, 'created_at');
        if ($createdAt && now()->diffInDays($createdAt) <= $newDays) {
            $badges[] = ['key' => 'new', 'label' => __('New')];
        }

        // "Sale" — has a sale price lower than regular price
        if ($showSale) {
            $regular = (float) data_get($product, 'product_price', 0);
            $sale    = (float) data_get($product, 'sale_price', 0);
            if ($sale > 0 && $sale < $regular) {
                $badges[] = ['key' => 'sale', 'label' => __('Sale')];
            }
        }

        // "Low Stock"
        $stock = (int) data_get($product, 'product_quantity', PHP_INT_MAX);
        if ($stock <= $lowStockQty && $stock > 0) {
            $badges[] = ['key' => 'low-stock', 'label' => __('Low Stock')];
        }

        // "Hot" — based on order count (cached per product for performance)
        $cacheKey   = "pb_hot_{$product->id}";
        $orderCount = \Cache::remember($cacheKey, 3600, fn () =>
            \DB::table('tbl_order_items')
                ->where('product_id', $product->id)
                ->where('created_at', '>=', now()->subDays(30))
                ->count()
        );
        if ($orderCount >= $hotThreshold) {
            $badges[] = ['key' => 'hot', 'label' => __('Hot')];
        }

        return $badges;
    }
}
