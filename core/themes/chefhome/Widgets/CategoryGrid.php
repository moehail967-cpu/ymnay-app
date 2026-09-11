<?php

namespace Themes\Chefhome\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class CategoryGrid extends BaseWidget
{
    protected function getWidgetType(): string { return 'chefhome_category_grid'; }
    protected function getWidgetName(): string { return 'ChefHome: Category Grid'; }
    protected function getWidgetIcon(): string|array { return 'las la-th-large'; }
    protected function getWidgetDescription(): string { return __('Cuisine category cards with circular images, name and product count'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['category', 'grid', 'cuisine', 'chefhome']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Section Title')
                ->setDefault('Browse by Cuisine'))
            ->registerField('view_all_url', FieldManager::URL()
                ->setLabel('View All URL')
                ->setDefault('#'))
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
            ->registerField('padding_top', FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(64)->setMin(0)->setMax(300))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(64)->setMin(0)->setMax(300))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {

        // Not in tenant context (editor preview API has no tenant middleware)
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;">ChefHome: Category Grid — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];
        $spacing = $settings['style']['spacing']   ?? [];
        $count   = (int) ($content['item_count']   ?? 6);

        $categories = theme_categories()
            ->each(function ($cat) {
                $cat->product_categories_count = $cat->product_categories()->count();
            })
            ->take($count);

        $rawViewAllUrl = $content['view_all_url'] ?? null;
        $viewAllUrl = is_array($rawViewAllUrl)
            ? ($rawViewAllUrl['url'] ?? '#')
            : ($rawViewAllUrl ?? '#');

        return view('theme-chefhome::widgets.category_grid', [
            'title'          => $content['title'] ?? 'Browse by Cuisine',
            'view_all_url'   => $viewAllUrl,
            'categories'     => $categories,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 64),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 64),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'chefhome';
    }
}
