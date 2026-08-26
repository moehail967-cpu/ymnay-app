<?php

namespace Themes\Trailco\Widgets;

use Modules\Attributes\Entities\Category;
use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class CategoryGrid extends BaseWidget
{
    protected function getWidgetType(): string { return 'trailco_category_grid'; }
    protected function getWidgetName(): string { return 'TrailCo: Category Grid'; }
    protected function getWidgetIcon(): string|array { return 'mdi mdi-view-grid'; }
    protected function getWidgetDescription(): string { return __('Outdoor activity category cards with terrain style'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['category', 'grid', 'trailco']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()->setLabel('Section Title')->setDefault('Shop by Activity'))
            ->registerField('subtitle', FieldManager::TEXT()->setLabel('Subtitle')->setDefault('Find gear matched to your adventure.'))
            ->registerField('item_count', FieldManager::NUMBER()->setLabel('Categories to Show')->setDefault(6)->setMin(2)->setMax(12))
            ->endGroup();
        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top', FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(72)->setMin(0)->setMax(300))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(72)->setMin(0)->setMax(300))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {

        // Not in tenant context (editor preview API has no tenant middleware)
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;">TrailCo: Category Grid — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];
        $spacing = $settings['style']['spacing']   ?? [];
        $count   = (int) ($content['item_count']   ?? 6);

        $categories = theme_categories()
            ->each(fn($c) => $c->products_count = $c->product_categories()->count())
            ->take($count);

        return view('theme-trailco::widgets.category_grid', [
            'title'          => $content['title']    ?? 'Shop by Activity',
            'subtitle'       => $content['subtitle'] ?? '',
            'categories'     => $categories,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 72),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 72),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'trailco';
    }
}
