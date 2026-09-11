<?php

namespace Themes\Pawhaus\Widgets;

use App\Enums\StatusEnums;
use Modules\Attributes\Entities\Category;
use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class CategoryGrid extends BaseWidget
{
    protected function getWidgetType(): string { return 'pawhaus_category_grid'; }
    protected function getWidgetName(): string { return 'PawHaus: Category Grid'; }
    protected function getWidgetIcon(): string|array { return 'las la-th-large'; }
    protected function getWidgetDescription(): string { return __('Pet category cards with circular icons'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['category', 'grid', 'pawhaus']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('section_tag', FieldManager::TEXT()
                ->setLabel('Section Tag/Pill Text')
                ->setDefault('Shop by Pet'))
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Section Title (HTML allowed)')
                ->setDefault('Find the Perfect <span>Products</span>'))
            ->registerField('subtitle', FieldManager::TEXT()
                ->setLabel('Subtitle')
                ->setDefault('Curated ranges for every member of your furry family.'))
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
                    '{{WRAPPER}} .ph-category-grid-widget' => 'padding-top: {{VALUE.TOP}}{{UNIT}}; padding-bottom: {{VALUE.BOTTOM}}{{UNIT}};',
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
                        <p style="margin:0;font-size:14px;">PawHaus: Category Grid — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];
        $count   = (int) ($content['item_count'] ?? 6);

        $categories = theme_categories()
            ->each(fn($c) => $c->product_count = $c->product_categories()->count())
            ->take($count);

        return view('theme-pawhaus::widgets.category_grid', [
            'section_tag' => $content['section_tag'] ?? 'Shop by Pet',
            'title'       => $content['title']       ?? 'Find the Perfect <span>Products</span>',
            'subtitle'    => $content['subtitle']    ?? 'Curated ranges for every member of your furry family.',
            'categories'  => $categories,
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'pawhaus';
    }
}
