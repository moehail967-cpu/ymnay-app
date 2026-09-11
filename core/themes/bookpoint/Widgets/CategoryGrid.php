<?php

namespace Themes\Bookpoint\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class CategoryGrid extends BaseWidget
{
    protected function getWidgetType(): string { return 'bookpoint_category_grid'; }
    protected function getWidgetName(): string { return 'BookPoint: Category Grid'; }
    protected function getWidgetIcon(): string|array { return 'las la-th-large'; }
    protected function getWidgetDescription(): string { return __('Tabbed category filter with product list'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['category', 'grid', 'bookpoint']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()->setLabel('Title')->setDefault('Explore by Categories'))
            ->registerField('show_view_all', FieldManager::SELECT()->setLabel('Show View All')->setOptions(['yes' => 'Yes', 'no' => 'No'])->setDefault('yes'))
            ->registerField('limit', FieldManager::NUMBER()->setLabel('Category Limit')->setDefault(6)->setMin(1)->setMax(20))
            ->registerField('product_limit', FieldManager::NUMBER()->setLabel('Product Limit')->setDefault(6)->setMin(1)->setMax(24))
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
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">BookPoint: Category Grid — preview on the live page</p></div>';
        }

        $content = $settings['general']['content'] ?? [];
        $spacing = $settings['style']['spacing']   ?? [];

        return view('theme-bookpoint::widgets.category_grid', [
            'title'         => $content['title']         ?? 'Explore by Categories',
            'show_view_all' => ($content['show_view_all'] ?? 'yes') === 'yes',
            'limit'         => (int) ($content['limit']         ?? 6),
            'product_limit' => (int) ($content['product_limit'] ?? 6),
            'padding_top'   => (int) ($spacing['padding_top']    ?? 80),
            'padding_bottom'=> (int) ($spacing['padding_bottom'] ?? 80),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'bookpoint';
    }
}
