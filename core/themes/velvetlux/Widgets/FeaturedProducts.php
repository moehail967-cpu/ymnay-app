<?php

namespace Themes\Velvetlux\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;
use Modules\Product\Entities\Product;

class FeaturedProducts extends BaseWidget
{
    protected function getWidgetType(): string { return 'velvetlux_featured_products'; }
    protected function getWidgetName(): string { return 'VelvetLux: Featured Products'; }
    protected function getWidgetIcon(): string|array { return 'las la-shopping-bag'; }
    protected function getWidgetDescription(): string { return __('Showcase featured fashion products with hover overlay'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['products', 'featured', 'velvetlux']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('section_tag', FieldManager::TEXT()
                ->setLabel('Section Tag')
                ->setDefault('New Arrivals'))
            ->registerField('section_title', FieldManager::TEXT()
                ->setLabel('Section Title')
                ->setDefault('Featured Pieces'))
            ->registerField('section_subtitle', FieldManager::TEXT()
                ->setLabel('Section Subtitle')
                ->setDefault('Hand-selected for the discerning taste'))
            ->registerField('view_all_url', FieldManager::URL()
                ->setLabel('View All URL')
                ->setDefault('#'))
            ->registerField('limit', FieldManager::NUMBER()
                ->setLabel('Number of Products')
                ->setDefault(8)->setMin(1)->setMax(24))
            ->registerField('sort', FieldManager::SELECT()
                ->setLabel('Sort By')
                ->setOptions(['latest' => 'Latest', 'popular' => 'Most Popular', 'price_asc' => 'Price: Low to High', 'price_desc' => 'Price: High to Low'])
                ->setDefault('latest'))
            ->endGroup();
        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top', FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(50)->setMin(0)->setMax(200))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(50)->setMin(0)->setMax(200))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {

        // Not in tenant context (editor preview API has no tenant middleware)
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;">VelvetLux: Featured Products — preview on the live page</p>
                    </div>';
        }
        $content  = $settings['general']['content'] ?? [];
        $spacing  = $settings['style']['spacing'] ?? [];
        $limit    = (int) ($content['limit'] ?? 8);
        $sort     = $content['sort'] ?? 'latest';

        $rawViewAll = $content['view_all_url'] ?? null;
        $view_all_url = $rawViewAll
            ? (is_array($rawViewAll) ? ($rawViewAll['url'] ?? theme_shop_url()) : $rawViewAll)
            : theme_shop_url();

        $query = Product::with('badge', 'campaign_product', 'inventory', 'inventoryDetail', 'category')
            ->where('status_id', 1)
            ->withSum('taxOptions', 'rate');

        match ($sort) {
            'price_asc'  => $query->orderBy('sale_price', 'asc'),
            'price_desc' => $query->orderBy('sale_price', 'desc'),
            'popular'    => $query->orderBy('view_count', 'desc'),
            default      => $query->latest(),
        };

        $products = $query->take($limit)->get();

        return view('theme-velvetlux::widgets.featured_products', [
            'section_tag'      => $content['section_tag']       ?? 'New Arrivals',
            'section_title'    => $content['section_title']    ?? 'Featured Pieces',
            'section_subtitle' => $content['section_subtitle'] ?? 'Hand-selected for the discerning taste',
            'view_all_url'     => $view_all_url,
            'products'         => $products,
            'padding_top'      => (int) ($spacing['padding_top']    ?? 50),
            'padding_bottom'   => (int) ($spacing['padding_bottom'] ?? 50),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'velvetlux';
    }
}
