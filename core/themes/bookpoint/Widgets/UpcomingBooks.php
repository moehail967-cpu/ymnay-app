<?php

namespace Themes\Bookpoint\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class UpcomingBooks extends BaseWidget
{
    protected function getWidgetType(): string { return 'bookpoint_upcoming_books'; }
    protected function getWidgetName(): string { return 'BookPoint: Upcoming Books'; }
    protected function getWidgetIcon(): string|array { return 'las la-clock'; }
    protected function getWidgetDescription(): string { return __('Pre-order slider for upcoming books'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['upcoming', 'preorder', 'slider', 'bookpoint']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()->setLabel('Section Title')->setDefault('Upcoming Books'))
            ->registerField('count', FieldManager::NUMBER()->setLabel('Books to Show')->setDefault(6)->setMin(1)->setMax(24))
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
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">BookPoint: Upcoming Books — preview on the live page</p></div>';
        }

        $content  = $settings['general']['content'] ?? [];
        $spacing  = $settings['style']['spacing']   ?? [];
        $count    = (int) ($content['count'] ?? 6);
        $products = function_exists('theme_featured_products') ? theme_featured_products($count) : collect();

        return view('theme-bookpoint::widgets.upcoming_books', [
            'title'          => $content['title']          ?? 'Upcoming Books',
            'products'       => $products,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 80),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 80),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'bookpoint';
    }
}
