<?php

namespace Themes\Bookpoint\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class FeaturedProducts extends BaseWidget
{
    protected function getWidgetType(): string { return 'bookpoint_featured_products'; }
    protected function getWidgetName(): string { return 'BookPoint: Featured Products'; }
    protected function getWidgetIcon(): string|array { return 'las la-star'; }
    protected function getWidgetDescription(): string { return __('Featured Book + Recently Sold dual slider sections'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['featured', 'products', 'slider', 'bookpoint']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('featured_title', FieldManager::TEXT()->setLabel('Featured Section Title')->setDefault('Featured Book'))
            ->registerField('featured_count', FieldManager::NUMBER()->setLabel('Featured Products Count')->setDefault(6)->setMin(1)->setMax(24))
            ->registerField('recent_title', FieldManager::TEXT()->setLabel('Recent Section Title')->setDefault('Recently Sold'))
            ->registerField('recent_count', FieldManager::NUMBER()->setLabel('Recent Products Count')->setDefault(8)->setMin(1)->setMax(24))
            ->endGroup();
        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top', FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(60)->setMin(0)->setMax(200))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(60)->setMin(0)->setMax(200))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">BookPoint: Featured Products — preview on the live page</p></div>';
        }

        $content        = $settings['general']['content'] ?? [];
        $spacing        = $settings['style']['spacing']   ?? [];
        $featured_count = (int) ($content['featured_count'] ?? 6);
        $recent_count   = (int) ($content['recent_count']   ?? 8);

        $featured_products = function_exists('theme_featured_products') ? theme_featured_products($featured_count) : collect();
        $recent_products   = function_exists('theme_featured_products') ? theme_featured_products($recent_count)   : collect();

        return view('theme-bookpoint::widgets.featured_products', [
            'featured_title'    => $content['featured_title'] ?? 'Featured Book',
            'recent_title'      => $content['recent_title']   ?? 'Recently Sold',
            'featured_products' => $featured_products,
            'recent_products'   => $recent_products,
            'padding_top'       => (int) ($spacing['padding_top']    ?? 60),
            'padding_bottom'    => (int) ($spacing['padding_bottom'] ?? 60),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'bookpoint';
    }
}
