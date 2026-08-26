<?php

namespace Themes\Luxegems\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class CategoryGrid extends BaseWidget
{
    protected function getWidgetType(): string { return 'luxegems_category_grid'; }
    protected function getWidgetName(): string { return 'LuxeGems: Category Grid'; }
    protected function getWidgetIcon(): string|array { return 'las la-th-large'; }
    protected function getWidgetDescription(): string { return __('Jewelry category cards in dark luxury style with gold accent borders'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['category', 'grid', 'jewelry', 'luxegems']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Section Title')
                ->setDefault('Shop by Category'))
            ->registerField('subtitle', FieldManager::TEXT()
                ->setLabel('Section Subtitle')
                ->setDefault('Explore our curated collections'))
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
                    '{{WRAPPER}} .lx-widget-cats' => 'padding-top: {{VALUE.TOP}}{{UNIT}}; padding-bottom: {{VALUE.BOTTOM}}{{UNIT}};',
                ]))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#111;border:1px dashed #333;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;">LuxeGems: Category Grid — preview on the live page</p>
                    </div>';
        }

        try {
            $content    = $settings['general']['content'] ?? [];
            $count      = (int) ($content['item_count'] ?? 6);
            $categories = theme_categories()->take($count);

            return view('theme-luxegems::widgets.category_grid', [
                'title'      => $content['title']    ?? 'Shop by Category',
                'subtitle'   => $content['subtitle'] ?? 'Explore our curated collections',
                'categories' => $categories,
            ])->render();

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('LuxeGems CategoryGrid render error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return '<div style="padding:40px;text-align:center;background:#111;border:1px dashed #8B0000;color:#c88;font-family:sans-serif;">
                        <p style="margin:0;font-size:13px;">Category Grid render error — check laravel.log</p>
                    </div>';
        }
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'luxegems';
    }
}
