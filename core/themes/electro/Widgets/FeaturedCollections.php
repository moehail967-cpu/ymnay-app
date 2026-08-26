<?php

namespace Themes\Electro\Widgets;

use Modules\Product\Entities\Product;
use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class FeaturedCollections extends BaseWidget
{
    protected function getWidgetType(): string { return 'electro_featured_collections'; }
    protected function getWidgetName(): string { return 'Electro: Featured Collections'; }
    protected function getWidgetIcon(): string|array { return 'las la-star'; }
    protected function getWidgetDescription(): string { return __('4-column product slider with dot pagination'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['featured', 'products', 'slider', 'electro']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()->setLabel('Title')->setDefault('Featured Collections'))
            ->registerField('sort_by', FieldManager::SELECT()->setLabel('Product Type')->setOptions([
                'latest'     => 'Latest',
                'popular'    => 'Most Popular',
                'sale'       => 'On Sale',
                'price_asc'  => 'Price: Low to High',
                'price_desc' => 'Price: High to Low',
            ])->setDefault('latest'))
            ->registerField('count', FieldManager::NUMBER()->setLabel('Products to Show')->setDefault(8)->setMin(4)->setMax(24))
            ->registerField('per_slide', FieldManager::NUMBER()->setLabel('Products per Slide')->setDefault(4)->setMin(2)->setMax(6))
            ->endGroup();
        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top', FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(80)->setMin(0)->setMax(200))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(80)->setMin(0)->setMax(200))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">Electro: Featured Collections — preview on the live page</p></div>';
        }

        $content   = $settings['general']['content'] ?? [];
        $spacing   = $settings['style']['spacing']   ?? [];
        $count     = (int) ($content['count']     ?? 8);
        $per_slide = (int) ($content['per_slide'] ?? 4);
        $sort_by   = $content['sort_by'] ?? 'latest';

        $q = Product::with(['badge', 'campaign_product', 'inventory'])
            ->where('status_id', 1);

        match ($sort_by) {
            'popular'    => $q->orderByDesc('sold_count'),
            'sale'       => $q->whereNotNull('sale_price')->whereColumn('sale_price', '<', 'price')->latest(),
            'price_asc'  => $q->orderBy('sale_price'),
            'price_desc' => $q->orderByDesc('sale_price'),
            default      => $q->latest(),
        };

        $products = $q->take($count)->get();
        $slides   = $products->chunk($per_slide)->values();

        return view('theme-electro::widgets.featured_collections', [
            'title'          => $content['title']          ?? 'Featured Collections',
            'slides'         => $slides,
            'products'       => $products,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 80),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 80),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'electro';
    }
}
