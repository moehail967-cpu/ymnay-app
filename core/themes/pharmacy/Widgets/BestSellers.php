<?php

namespace Themes\Pharmacy\Widgets;

use Modules\Product\Entities\Product;
use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class BestSellers extends BaseWidget
{
    protected function getWidgetType(): string { return 'pharmacy_best_sellers'; }
    protected function getWidgetName(): string { return 'Pharmacy: Best Sellers'; }
    protected function getWidgetIcon(): string|array { return 'las la-award'; }
    protected function getWidgetDescription(): string { return __('Best-selling products sorted by popularity in Pharmacy card style'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['products', 'best sellers', 'pharmacy']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Section Title')
                ->setDefault('Best Sellers'))
            ->registerField('subtitle', FieldManager::TEXT()
                ->setLabel('Section Subtitle')
                ->setDefault('Most purchased health products this month'))
            ->registerField('view_all_url', FieldManager::URL()
                ->setLabel('View All URL')
                ->setDefault('#'))
            ->endGroup();

        $control->addGroup('query', 'Product Query')
            ->registerField('item_count', FieldManager::NUMBER()
                ->setLabel('Products to Show')
                ->setDefault(8)->setMin(2)->setMax(24))
            ->registerField('sort_by', FieldManager::SELECT()
                ->setLabel('Sort By')
                ->setOptions([
                    'popular'    => 'Most Popular',
                    'latest'     => 'Latest',
                    'price_asc'  => 'Price: Low to High',
                    'price_desc' => 'Price: High to Low',
                ])
                ->setDefault('popular'))
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top', FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(72)->setMin(0)->setMax(300))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(72)->setMin(0)->setMax(300))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {

        // Not in tenant context (editor preview API has no tenant middleware)
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;">Pharmacy: Best Sellers — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];
        $query   = $settings['general']['query']   ?? [];
        $spacing = $settings['style']['spacing']   ?? [];

        $count  = (int) ($query['item_count'] ?? 8);
        $sortBy = $query['sort_by'] ?? 'popular';

        $q = Product::with('badge', 'campaign_product', 'inventory', 'inventoryDetail', 'category')
            ->where('status_id', 1)
            ->withSum('taxOptions', 'rate');

        match ($sortBy) {
            'price_asc'  => $q->orderBy('sale_price', 'asc'),
            'price_desc' => $q->orderBy('sale_price', 'desc'),
            'latest'     => $q->latest(),
            default      => $q->orderBy('sold_count', 'desc'),
        };

        $products = $q->take($count)->get();

        $viewAllUrl = $content['view_all_url'] ?? theme_shop_url();
        if (is_array($viewAllUrl)) $viewAllUrl = $viewAllUrl['url'] ?? theme_shop_url();

        return view('theme-pharmacy::widgets.best_sellers', [
            'title'          => $content['title']    ?? 'Best Sellers',
            'subtitle'       => $content['subtitle'] ?? 'Most purchased health products this month',
            'view_all_url'   => $viewAllUrl,
            'products'       => $products,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 72),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 72),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'pharmacy';
    }
}
