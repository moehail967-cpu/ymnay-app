<?php

namespace Plugins\WidgetBuilder\Widgets\Tenant;

use Modules\Attributes\Entities\Category;
use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class CategoryGrid extends BaseWidget
{
    protected function getWidgetType(): string { return 'tenant_category_grid'; }
    protected function getWidgetName(): string { return 'Category Grid'; }
    protected function getWidgetIcon(): string|array { return 'las la-th-large'; }
    protected function getWidgetDescription(): string { return __('Show product categories in a grid or slider layout'); }
    protected function getCategory(): string { return WidgetCategory::ECOMMERCE; }
    protected function getWidgetTags(): array { return ['categories', 'shop', 'grid']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Section Title')
                ->setDefault('Shop by Category'))
            ->registerField('subtitle', FieldManager::TEXT()
                ->setLabel('Section Subtitle')
                ->setDefault(''))
            ->registerField('view_all_text', FieldManager::TEXT()
                ->setLabel('View All Text')
                ->setDefault('View All'))
            ->registerField('view_all_url', FieldManager::URL()
                ->setLabel('View All URL')
                ->setDefault('#'))
            ->endGroup();

        $control->addGroup('query', 'Display Options')
            ->registerField('item_count', FieldManager::NUMBER()
                ->setLabel('Number of Categories')
                ->setDefault(6)->setMin(2)->setMax(20))
            ->registerField('columns', FieldManager::SELECT()
                ->setLabel('Columns per Row')
                ->setOptions(['2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6'])
                ->setDefault('6'))
            ->registerField('show_count', FieldManager::TOGGLE()
                ->setLabel('Show Product Count')
                ->setDefault(false))
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

        $count = (int) ($query['item_count'] ?? 6);
        $cols  = (int) ($query['columns'] ?? 6);

        $categories = Category::where('status', 1)->take($count)->get();

        return view('widgetbuilder::tenant.category_grid', [
            'title'          => $content['title'] ?? 'Shop by Category',
            'subtitle'       => $content['subtitle'] ?? '',
            'view_all_text'  => $content['view_all_text'] ?? 'View All',
            'view_all_url'   => $content['view_all_url'] ?? '#',
            'categories'     => $categories,
            'columns'        => $cols,
            'show_count'     => (bool) ($query['show_count'] ?? false),
            'padding_top'    => (int) ($spacing['padding_top'] ?? 80),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 80),
        ])->render();
    }

    public function enable(): bool { return !is_null(tenant()); }
}
