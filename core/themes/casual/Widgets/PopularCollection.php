<?php

namespace Themes\Casual\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class PopularCollection extends BaseWidget
{
    protected function getWidgetType(): string { return 'casual_popular_collection'; }
    protected function getWidgetName(): string { return 'Casual: Popular Collection'; }
    protected function getWidgetIcon(): string|array { return 'las la-fire'; }
    protected function getWidgetDescription(): string { return __('Popular products row with cream card style'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['products', 'popular', 'casual']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()->setLabel('Title')->setDefault('POPULAR COLLECTION'))
            ->registerField('count', FieldManager::NUMBER()->setLabel('Products to Show')->setDefault(4)->setMin(1)->setMax(12))
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
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">Casual: Popular Collection — preview on the live page</p></div>';
        }

        $content  = $settings['general']['content'] ?? [];
        $spacing  = $settings['style']['spacing']   ?? [];
        $count    = (int) ($content['count'] ?? 4);
        $products = function_exists('theme_featured_products') ? theme_featured_products($count) : collect();

        return view('theme-casual::widgets.popular_collection', [
            'title'          => $content['title']          ?? 'POPULAR COLLECTION',
            'products'       => $products,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 80),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 80),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'casual';
    }
}
