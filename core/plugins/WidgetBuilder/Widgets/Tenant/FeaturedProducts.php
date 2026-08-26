<?php

namespace Plugins\WidgetBuilder\Widgets\Tenant;

use Modules\Product\Entities\Product;
use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class FeaturedProducts extends BaseWidget
{
    protected function getWidgetType(): string { return 'tenant_featured_products'; }
    protected function getWidgetName(): string { return 'Featured Products'; }
    protected function getWidgetIcon(): string|array { return 'las la-shopping-bag'; }
    protected function getWidgetDescription(): string { return __('Display a grid of products with sorting and count options'); }
    protected function getCategory(): string { return WidgetCategory::ECOMMERCE; }
    protected function getWidgetTags(): array { return ['products', 'featured', 'shop', 'grid']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Section Title')
                ->setDefault('Featured Products'))
            ->registerField('subtitle', FieldManager::TEXT()
                ->setLabel('Section Subtitle')
                ->setDefault(''))
            ->registerField('view_all_text', FieldManager::TEXT()
                ->setLabel('View All Button Text')
                ->setDefault('View All'))
            ->registerField('view_all_url', FieldManager::URL()
                ->setLabel('View All URL')
                ->setDefault('#'))
            ->endGroup();

        $control->addGroup('query', 'Product Query')
            ->registerField('item_count', FieldManager::NUMBER()
                ->setLabel('Number of Products')
                ->setDefault(8)->setMin(2)->setMax(24))
            ->registerField('sort_by', FieldManager::SELECT()
                ->setLabel('Sort By')
                ->setOptions([
                    'latest'     => 'Latest',
                    'price_asc'  => 'Price: Low to High',
                    'price_desc' => 'Price: High to Low',
                    'popular'    => 'Most Popular',
                ])
                ->setDefault('latest'))
            ->registerField('columns', FieldManager::SELECT()
                ->setLabel('Columns per Row')
                ->setOptions(['2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6'])
                ->setDefault('4'))
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top', FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(80)->setMin(0)->setMax(300))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(80)->setMin(0)->setMax(300))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        $general = $settings['general'] ?? [];
        $style   = $settings['style'] ?? [];

        $content = $general['content'] ?? [];
        $query   = $general['query'] ?? [];
        $spacing = $style['spacing'] ?? [];

        $count  = (int) ($query['item_count'] ?? 8);
        $sortBy = $query['sort_by'] ?? 'latest';
        $cols   = (int) ($query['columns'] ?? 4);

        $q = Product::with('badge', 'campaign_product', 'inventory', 'inventoryDetail')
            ->where('status_id', 1)
            ->withSum('taxOptions', 'rate');

        match ($sortBy) {
            'price_asc'  => $q->orderBy('sale_price', 'asc'),
            'price_desc' => $q->orderBy('sale_price', 'desc'),
            default      => $q->latest(),
        };

        $products = $q->take($count)->get();

        return view('widgetbuilder::tenant.featured_products', [
            'title'          => $content['title'] ?? 'Featured Products',
            'subtitle'       => $content['subtitle'] ?? '',
            'view_all_text'  => $content['view_all_text'] ?? 'View All',
            'view_all_url'   => $content['view_all_url'] ?? '#',
            'products'       => $products,
            'columns'        => $cols,
            'padding_top'    => (int) ($spacing['padding_top'] ?? 80),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 80),
        ])->render();
    }

    public function enable(): bool { return !is_null(tenant()); }
}
