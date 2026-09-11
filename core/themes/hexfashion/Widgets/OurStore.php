<?php

namespace Themes\Hexfashion\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class OurStore extends BaseWidget
{
    protected function getWidgetType(): string { return 'hexfashion_our_store'; }
    protected function getWidgetName(): string { return 'HexFashion: Our Store'; }
    protected function getWidgetIcon(): string|array { return 'las la-store'; }
    protected function getWidgetDescription(): string { return __('Left category sidebar + right product grid with JS filter'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['store', 'products', 'filter', 'hexfashion']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()->setLabel('Title')->setDefault('OUR STORE'))
            ->registerField('category_limit', FieldManager::NUMBER()->setLabel('Categories in Sidebar')->setDefault(8)->setMin(1)->setMax(30))
            ->registerField('product_limit', FieldManager::NUMBER()->setLabel('Products to Show')->setDefault(9)->setMin(1)->setMax(30))
            ->endGroup();
        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top', FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(80)->setMin(0)->setMax(200))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(80)->setMin(0)->setMax(200))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">HexFashion: Our Store — preview on the live page</p></div>';
        }

        $content       = $settings['general']['content'] ?? [];
        $spacing       = $settings['style']['spacing']   ?? [];
        $cat_limit     = (int) ($content['category_limit'] ?? 8);
        $product_limit = (int) ($content['product_limit']  ?? 9);

        $categories = theme_categories()->take($cat_limit);
        $products   = function_exists('theme_featured_products') ? theme_featured_products($product_limit) : collect();

        return view('theme-hexfashion::widgets.our_store', [
            'title'          => $content['title']          ?? 'OUR STORE',
            'categories'     => $categories,
            'products'       => $products,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 80),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 80),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'hexfashion';
    }
}
