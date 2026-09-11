<?php

namespace Themes\Bakerco\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class CategoryGrid extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'bakerco_category_grid';
    }

    protected function getWidgetName(): string
    {
        return 'BakerCo: Category Grid';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-th-large';
    }

    protected function getWidgetDescription(): string
    {
        return __('Display product categories in a responsive icon grid');
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['category', 'grid', 'bakerco'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('section_tag', FieldManager::TEXT()->setLabel('Section Tag')->setDefault('Our Specialties'))
            ->registerField('title', FieldManager::TEXT()->setLabel('Title')->setDefault('Shop by Category'))
            ->registerField('subtitle', FieldManager::TEXT()->setLabel('Subtitle')->setDefault('Browse our handcrafted collections'))
            ->registerField('limit', FieldManager::NUMBER()->setLabel('Items Limit')->setDefault(12)->setMin(1)->setMax(100))
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
        $content = $settings['general']['content'] ?? [];
        $spacing = $settings['style']['spacing'] ?? [];

        // Not in tenant context (editor preview API has no tenant middleware)
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#fdf6ec;border:1px dashed #ccc;border-radius:8px;color:#888;">
                        <i class="las la-th-large" style="font-size:2rem;"></i>
                        <p style="margin:8px 0 0;">Category Grid — preview on the live page</p>
                    </div>';
        }

        return view('theme-bakerco::widgets.category_grid', [
            'section_tag' => $content['section_tag'] ?? 'Our Specialties',
            'title' => $content['title'] ?? 'Shop by Category',
            'subtitle' => $content['subtitle'] ?? 'Browse our handcrafted collections',
            'limit' => (int) ($content['limit'] ?? 12),
            'padding_top' => (int) ($spacing['padding_top'] ?? 60),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 60),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'bakerco';
    }
}
