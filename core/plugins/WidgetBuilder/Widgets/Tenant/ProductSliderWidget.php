<?php

namespace Plugins\WidgetBuilder\Widgets\Tenant;

use Modules\Product\Entities\Product;
use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class ProductSliderWidget extends BaseWidget
{
    protected function getWidgetType(): string        { return 'tenant_product_slider'; }
    protected function getWidgetName(): string        { return 'Product Slider'; }
    protected function getWidgetIcon(): string|array  { return 'las la-store'; }
    protected function getWidgetDescription(): string { return 'Horizontal scrollable product strip.'; }
    protected function getCategory(): string          { return WidgetCategory::ECOMMERCE; }
    protected function getWidgetTags(): array         { return ['products', 'slider', 'shop']; }

    public function enable(): bool { return !is_null(tenant()); }

    public function getGeneralFields(): array
    {
        $cm = new ControlManager();
        $cm->addGroup('content', 'Content')
            ->registerField('section_title', FieldManager::TEXT()
                ->setLabel('Section Title')->setDefault('Featured Products'))
            ->endGroup();
        $cm->addGroup('query', 'Product Query')
            ->registerField('count', FieldManager::NUMBER()
                ->setLabel('Number of Products')->setDefault(8)->setMin(2)->setMax(24))
            ->registerField('sort_by', FieldManager::SELECT()
                ->setLabel('Sort By')
                ->setOptions([
                    'latest'     => 'Latest',
                    'price_asc'  => 'Price: Low to High',
                    'price_desc' => 'Price: High to Low',
                    'popular'    => 'Most Popular',
                ])
                ->setDefault('latest'))
            ->endGroup();
        return $cm->getFields();
    }

    public function getStyleFields(): array
    {
        $cm = new ControlManager();
        $cm->addGroup('display', 'Display')
            ->registerField('show_price', FieldManager::TOGGLE()
                ->setLabel('Show Price')->setDefault(true))
            ->registerField('show_cart_btn', FieldManager::TOGGLE()
                ->setLabel('Show Add to Cart Button')->setDefault(true))
            ->registerField('card_radius', FieldManager::NUMBER()
                ->setLabel('Card Border Radius (px)')->setDefault(12)->setMin(0)->setMax(40))
            ->endGroup();
        $cm->addGroup('spacing', 'Spacing')
            ->registerField('padding_top', FieldManager::NUMBER()
                ->setLabel('Padding Top (px)')->setDefault(80)->setMin(0)->setMax(300))
            ->registerField('padding_bottom', FieldManager::NUMBER()
                ->setLabel('Padding Bottom (px)')->setDefault(80)->setMin(0)->setMax(300))
            ->endGroup();
        return $cm->getFields();
    }

    public function render(array $settings = []): string
    {
        $content = $settings['general']['content'] ?? [];
        $query   = $settings['general']['query'] ?? [];
        $display = $settings['style']['display'] ?? [];
        $spacing = $settings['style']['spacing'] ?? [];

        $count  = (int) ($query['count'] ?? 8);
        $sortBy = $query['sort_by'] ?? 'latest';

        $q = Product::with('badge', 'campaign_product', 'inventory', 'inventoryDetail')
            ->where('status_id', 1)
            ->withSum('taxOptions', 'rate');

        match ($sortBy) {
            'price_asc'  => $q->orderBy('sale_price', 'asc'),
            'price_desc' => $q->orderBy('sale_price', 'desc'),
            default      => $q->latest(),
        };

        $products = $q->take($count)->get();

        return view('widgetbuilder::tenant.product_slider', [
            'section_title'  => $content['section_title'] ?? 'Featured Products',
            'products'       => $products,
            'show_price'     => (bool) ($display['show_price'] ?? true),
            'show_cart_btn'  => (bool) ($display['show_cart_btn'] ?? true),
            'card_radius'    => (int) ($display['card_radius'] ?? 12),
            'padding_top'    => (int) ($spacing['padding_top'] ?? 80),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 80),
        ])->render();
    }
}
