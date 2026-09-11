<?php

namespace Themes\Tinynest\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;
use Modules\Attributes\Entities\Category;
use App\Enums\StatusEnums;

class CategoryGrid extends BaseWidget
{
    protected function getWidgetType(): string { return 'tinynest_category_grid'; }
    protected function getWidgetName(): string { return 'TinyNest: Category Grid'; }
    protected function getWidgetIcon(): string|array { return 'las la-th'; }
    protected function getWidgetDescription(): string { return __('Display baby product categories in a soft rounded grid'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['categories', 'grid', 'tinynest']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()->setLabel('Section Title')->setDefault('Shop by Category'))
            ->registerField('subtitle', FieldManager::TEXT()->setLabel('Subtitle')->setDefault('Find exactly what your little one needs'))
            ->registerField('count', FieldManager::NUMBER()->setLabel('Number of Categories')->setDefault(8)->setMin(2)->setMax(16))
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

        // Not in tenant context (editor preview API has no tenant middleware)
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;">TinyNest: Category Grid — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];
        $spacing = $settings['style']['spacing']   ?? [];

        $categories = theme_categories()->take((int) ($content['count'] ?? 8));

        return view('theme-tinynest::widgets.category_grid', [
            'title'          => $content['title']    ?? 'What Are You Looking For',
            'subtitle'       => $content['subtitle'] ?? 'Everything for the tiniest members of your family.',
            'categories'     => $categories,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 60),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 60),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'tinynest';
    }
}
