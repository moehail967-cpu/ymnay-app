<?php

namespace Plugins\WidgetBuilder\Widgets\Tenant;

use Modules\Attributes\Entities\Category;
use Modules\Product\Entities\Product;
use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class ProductByCategory extends BaseWidget
{
    protected function getWidgetType(): string { return 'tenant_products_by_category'; }
    protected function getWidgetName(): string { return 'Products by Category'; }
    protected function getWidgetIcon(): string|array { return 'las la-tags'; }
    protected function getWidgetDescription(): string { return __('Show products filtered by a specific category with tab switching'); }
    protected function getCategory(): string { return WidgetCategory::ECOMMERCE; }
    protected function getWidgetTags(): array { return ['products', 'category', 'tabs', 'filter']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Section Title')
                ->setDefault('Shop by Category'))
            ->registerField('view_all_text', FieldManager::TEXT()
                ->setLabel('View All Text')
                ->setDefault('View All'))
            ->registerField('view_all_url', FieldManager::URL()
                ->setLabel('View All URL')
                ->setDefault('#'))
            ->endGroup();

        $control->addGroup('query', 'Query')
            ->registerField('item_count', FieldManager::NUMBER()
                ->setLabel('Products per Category')
                ->setDefault(8)->setMin(2)->setMax(24))
            ->registerField('show_all_tab', FieldManager::TOGGLE()
                ->setLabel('Show "All" Tab')
                ->setDefault(true))
            ->registerField('max_categories', FieldManager::NUMBER()
                ->setLabel('Max Category Tabs')
                ->setDefault(5)->setMin(1)->setMax(10))
            ->registerField('columns', FieldManager::SELECT()
                ->setLabel('Product Columns')
                ->setOptions(['2' => '2', '3' => '3', '4' => '4', '5' => '5'])
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

        $content     = $general['content'] ?? [];
        $query       = $general['query'] ?? [];
        $spacing     = $style['spacing'] ?? [];

        $count       = (int) ($query['item_count'] ?? 8);
        $maxCats     = (int) ($query['max_categories'] ?? 5);
        $cols        = (int) ($query['columns'] ?? 4);
        $showAllTab  = (bool) ($query['show_all_tab'] ?? true);

        $categories = Category::where('status', 1)->take($maxCats)->get();

        $allProducts = $showAllTab
            ? Product::where('status_id', 1)->latest()->take($count)->get()
            : collect();

        $catProducts = [];
        foreach ($categories as $cat) {
            $catProducts[$cat->id] = Product::where('status_id', 1)
                ->whereHas('categories', fn($q) => $q->where('category_id', $cat->id))
                ->latest()->take($count)->get();
        }

        return view('widgetbuilder::tenant.product_by_category', [
            'title'          => $content['title'] ?? 'Shop by Category',
            'view_all_text'  => $content['view_all_text'] ?? 'View All',
            'view_all_url'   => $content['view_all_url'] ?? '#',
            'categories'     => $categories,
            'all_products'   => $allProducts,
            'cat_products'   => $catProducts,
            'show_all_tab'   => $showAllTab,
            'columns'        => $cols,
            'padding_top'    => (int) ($spacing['padding_top'] ?? 80),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 80),
        ])->render();
    }

    public function enable(): bool { return !is_null(tenant()); }
}
