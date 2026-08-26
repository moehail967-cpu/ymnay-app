<?php

namespace Themes\Furnito\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class TrendyProducts extends BaseWidget
{
    protected function getWidgetType(): string { return 'furnito_trendy_products'; }
    protected function getWidgetName(): string { return 'Furnito: Trendy Products'; }
    protected function getWidgetIcon(): string|array { return 'las la-fire'; }
    protected function getWidgetDescription(): string { return __('4-column featured grid + 6 small thumbnail row below'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['trendy', 'products', 'furnito']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('title',         FieldManager::TEXT()->setLabel('Section Title')->setDefault('Trendy Product'))
            ->registerField('subtitle',      FieldManager::TEXTAREA()->setLabel('Section Subtitle')->setDefault('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus malesuada nulla.'))
            ->registerField('grid_count',    FieldManager::NUMBER()->setLabel('Grid Products (top row)')->setDefault(4)->setMin(2)->setMax(8))
            ->registerField('thumb_count',   FieldManager::NUMBER()->setLabel('Thumbnail Products (bottom row)')->setDefault(6)->setMin(2)->setMax(12))
            ->endGroup();
        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top',    FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(80)->setMin(0)->setMax(200))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(80)->setMin(0)->setMax(200))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">Furnito: Trendy Products — preview on the live page</p></div>';
        }

        $content = $settings['general']['content'] ?? [];
        $spacing = $settings['style']['spacing']   ?? [];
        $total   = ((int) ($content['grid_count']  ?? 4)) + ((int) ($content['thumb_count'] ?? 6));

        $all_products  = function_exists('theme_featured_products') ? theme_featured_products($total) : collect();
        $grid_count    = (int) ($content['grid_count']  ?? 4);
        $thumb_count   = (int) ($content['thumb_count'] ?? 6);
        $grid_products = $all_products->take($grid_count);
        $thumb_products= $all_products->skip($grid_count)->take($thumb_count);

        return view('theme-furnito::widgets.trendy_products', [
            'title'          => $content['title']    ?? 'Trendy Product',
            'subtitle'       => $content['subtitle'] ?? '',
            'grid_products'  => $grid_products,
            'thumb_products' => $thumb_products,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 80),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 80),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'furnito';
    }
}
