<?php

namespace Themes\Goldcraft\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class CategoryGrid extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'goldcraft_category_grid';
    }

    protected function getWidgetName(): string
    {
        return 'Goldcraft: Category Grid';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-th-large';
    }

    protected function getWidgetDescription(): string
    {
        return __('Display product categories in a responsive card grid');
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['category', 'grid', 'goldcraft'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('section_tag', FieldManager::TEXT()->setLabel('Section Tag')->setDefault('Collections'))
            ->registerField('title', FieldManager::TEXT()->setLabel('Title')->setDefault('Shop by Category'))
            ->registerField('subtitle', FieldManager::TEXT()->setLabel('Subtitle')->setDefault('Curated jewellery for every occasion and style.'))
            ->registerField('category_count', FieldManager::NUMBER()->setLabel('Category Count')->setDefault(6)->setMin(1)->setMax(24))
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
                    '{{WRAPPER}} .gc-widget-cats' => 'padding-top: {{VALUE.TOP}}{{UNIT}}; padding-bottom: {{VALUE.BOTTOM}}{{UNIT}};',
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
                        <p style="margin:0;font-size:14px;">Goldcraft: Category Grid — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];
        $count   = (int) ($content['category_count'] ?? 6);

        $categories = theme_categories()->loadCount('product_categories')->take($count);

        return view('theme-goldcraft::widgets.category_grid', [
            'section_tag' => $content['section_tag'] ?? 'Collections',
            'title'       => $content['title']       ?? 'Shop by Category',
            'subtitle'    => $content['subtitle']    ?? 'Curated jewellery for every occasion and style.',
            'categories'  => $categories,
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'goldcraft';
    }
}
