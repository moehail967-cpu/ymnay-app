<?php

namespace Themes\Velvetlux\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;
use Modules\Attributes\Entities\Category;
use App\Enums\StatusEnums;

class CategoryGrid extends BaseWidget
{
    protected function getWidgetType(): string { return 'velvetlux_category_grid'; }
    protected function getWidgetName(): string { return 'VelvetLux: Category Grid'; }
    protected function getWidgetIcon(): string|array { return 'las la-th-large'; }
    protected function getWidgetDescription(): string { return __('Display fashion categories in an elegant grid layout'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['category', 'grid', 'velvetlux']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('section_tag', FieldManager::TEXT()
                ->setLabel('Section Tag')
                ->setDefault('Collections'))
            ->registerField('section_title', FieldManager::TEXT()
                ->setLabel('Section Title')
                ->setDefault('Shop by Category'))
            ->registerField('section_subtitle', FieldManager::TEXT()
                ->setLabel('Section Subtitle')
                ->setDefault('Explore our curated collections'))
            ->registerField('limit', FieldManager::NUMBER()
                ->setLabel('Number of Categories')
                ->setDefault(6)->setMin(1)->setMax(20))
            ->endGroup();
        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top', FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(40)->setMin(0)->setMax(200))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(40)->setMin(0)->setMax(200))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {

        // Not in tenant context (editor preview API has no tenant middleware)
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;">VelvetLux: Category Grid — preview on the live page</p>
                    </div>';
        }
        $content    = $settings['general']['content'] ?? [];
        $spacing    = $settings['style']['spacing'] ?? [];
        $limit      = (int) ($content['limit'] ?? 6);
        $categories = theme_categories()
            ->each(fn($c) => $c->product_count = $c->product_categories()->count())
            ->take($limit);

        return view('theme-velvetlux::widgets.category_grid', [
            'section_tag'      => $content['section_tag']       ?? 'Collections',
            'section_title'    => $content['section_title']    ?? 'Shop by Category',
            'section_subtitle' => $content['section_subtitle'] ?? 'Explore our curated collections',
            'categories'       => $categories,
            'padding_top'      => (int) ($spacing['padding_top']    ?? 40),
            'padding_bottom'   => (int) ($spacing['padding_bottom'] ?? 40),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'velvetlux';
    }
}
