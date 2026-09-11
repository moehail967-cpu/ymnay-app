<?php

namespace Themes\Drivekit\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class CategoryGrid extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'drivekit_category_grid';
    }

    protected function getWidgetName(): string
    {
        return 'DriveKit: Category Grid';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-th-large';
    }

    protected function getWidgetDescription(): string
    {
        return __('Dark grid of product categories with icons for automotive theme');
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['categories', 'grid', 'drivekit'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('tag', FieldManager::TEXT()->setLabel('Tag / Eyebrow')->setDefault('Categories'))
            ->registerField('title', FieldManager::TEXT()->setLabel('Section Title (HTML allowed)')->setDefault('Shop by <span>Department</span>'))
            ->registerField('subtitle', FieldManager::TEXT()->setLabel('Section Subtitle')->setDefault('Everything under the bonnet and beyond.'))
            ->registerField('category_count', FieldManager::NUMBER()->setLabel('Number of Categories')->setDefault(6)->setMin(2)->setMax(12))
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding', FieldManager::DIMENSION()
                ->asPadding()
                ->setLabel('Section Padding')
                ->setDefault(['top' => 100, 'right' => 0, 'bottom' => 100, 'left' => 0])
                ->setSelectors([
                    '{{WRAPPER}} .dk-widget-cats' => 'padding-top: {{VALUE.TOP}}{{UNIT}}; padding-bottom: {{VALUE.BOTTOM}}{{UNIT}};',
                ])
            )
            ->endGroup();

        return $control->getFields();
    }

    public function render(array $settings = []): string
    {

        // Not in tenant context (editor preview API has no tenant middleware)
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;">DriveKit: Category Grid — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];

        $catCount = (int) ($content['category_count'] ?? 8);

        $categories = theme_categories()
            ->each(fn($c) => $c->products_count = $c->product_categories()->count())
            ->take($catCount);

        return view('theme-drivekit::widgets.category_grid', [
            'tag'        => $content['tag']      ?? 'Categories',
            'title'      => $content['title']    ?? 'Shop by <span>Department</span>',
            'subtitle'   => $content['subtitle'] ?? 'Everything under the bonnet and beyond.',
            'categories' => $categories,
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'drivekit';
    }
}
