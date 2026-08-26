<?php

namespace Themes\Fitpeak\Widgets;

use App\Enums\StatusEnums;
use Modules\Attributes\Entities\Category;
use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class CategoryGrid extends BaseWidget
{
    protected function getWidgetType(): string { return 'fitpeak_category_grid'; }
    protected function getWidgetName(): string { return 'FitPeak: Category Grid'; }
    protected function getWidgetIcon(): string|array { return 'mdi mdi-view-grid'; }
    protected function getWidgetDescription(): string { return __('Dark category cards with neon hover effect'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['category', 'grid', 'fitpeak']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('tag', FieldManager::TEXT()
                ->setLabel('Badge Tag')
                ->setDefault('Categories'))
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Title (wrap accent word in <span>)')
                ->setDefault('Shop By <span>Goal</span>'))
            ->registerField('subtitle', FieldManager::TEXT()
                ->setLabel('Subtitle')
                ->setDefault('Find exactly what your training demands.'))
            ->registerField('item_count', FieldManager::NUMBER()
                ->setLabel('Categories to Show')
                ->setDefault(6)->setMin(2)->setMax(12))
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
                ->setDefault(['top' => 72, 'right' => 0, 'bottom' => 72, 'left' => 0])
                ->setSelectors([
                    '{{WRAPPER}} .fp-category-grid-widget' => 'padding-top: {{VALUE.TOP}}{{UNIT}}; padding-bottom: {{VALUE.BOTTOM}}{{UNIT}};',
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
                        <p style="margin:0;font-size:14px;">FitPeak: Category Grid — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];
        $count   = (int) ($content['item_count']   ?? 6);

        $categories = theme_categories()->loadCount('product_categories')->take($count);

        return view('theme-fitpeak::widgets.category_grid', [
            'tag'        => $content['tag']      ?? 'Categories',
            'title'      => $content['title']    ?? 'Shop By <span>Goal</span>',
            'subtitle'   => $content['subtitle'] ?? 'Find exactly what your training demands.',
            'categories' => $categories,
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'fitpeak';
    }
}
