<?php

namespace Themes\Kidville\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class CategoryGrid extends BaseWidget
{
    protected function getWidgetType(): string { return 'kidville_category_grid'; }
    protected function getWidgetName(): string { return 'KidVille: Category Grid'; }
    protected function getWidgetIcon(): string|array { return 'las la-th-large'; }
    protected function getWidgetDescription(): string { return __('Colorful category cards for kids store'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['category', 'grid', 'kidville']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('section_tag', FieldManager::TEXT()
                ->setLabel('Section Tag')
                ->setDefault('Shop Categories'))
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Section Title (wrap accent in <span class="kv-red">)')
                ->setDefault('Find What Kids <span class="kv-red">Love</span>!'))
            ->registerField('subtitle', FieldManager::TEXT()
                ->setLabel('Subtitle')
                ->setDefault('Explore our world of fun and discovery.'))
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
            ->registerField('section_padding', FieldManager::DIMENSION()
                ->asPadding()
                ->setLabel('Section Padding')
                ->setDefault(['top' => 80, 'right' => 0, 'bottom' => 80, 'left' => 0])
                ->setSelectors([
                    '{{WRAPPER}} .kv-category-grid-widget' => 'padding-top: {{VALUE.TOP}}{{UNIT}}; padding-bottom: {{VALUE.BOTTOM}}{{UNIT}};',
                ]))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;">KidVille: Category Grid — preview on the live page</p>
                    </div>';
        }
        $content    = $settings['general']['content'] ?? [];
        $count      = (int) ($content['item_count'] ?? 6);
        $categories = theme_categories()->take($count);

        return view('theme-kidville::widgets.category_grid', [
            'section_tag' => $content['section_tag'] ?? 'Shop Categories',
            'title'       => $content['title']       ?? 'Find What Kids <span class="kv-red">Love</span>!',
            'subtitle'    => $content['subtitle']    ?? 'Explore our world of fun and discovery.',
            'categories'  => $categories,
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'kidville';
    }
}
