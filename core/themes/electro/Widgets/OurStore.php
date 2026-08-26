<?php

namespace Themes\Electro\Widgets;

use Modules\Product\Entities\Product;
use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class OurStore extends BaseWidget
{
    protected function getWidgetType(): string { return 'electro_our_store'; }
    protected function getWidgetName(): string { return 'Electro: Our Store'; }
    protected function getWidgetIcon(): string|array { return 'las la-store'; }
    protected function getWidgetDescription(): string { return __('Category tab filter + 4-column product grid'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['store', 'filter', 'products', 'electro']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()->setLabel('Title')->setDefault('Our Store'))
            ->registerField('category_limit', FieldManager::NUMBER()->setLabel('Filter Tab Categories')->setDefault(6)->setMin(1)->setMax(20))
            ->registerField('product_limit', FieldManager::NUMBER()->setLabel('Products to Show')->setDefault(8)->setMin(1)->setMax(24))
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
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">Electro: Our Store — preview on the live page</p></div>';
        }

        $content       = $settings['general']['content'] ?? [];
        $spacing       = $settings['style']['spacing']   ?? [];
        $cat_limit     = (int) ($content['category_limit'] ?? 6);
        $product_limit = (int) ($content['product_limit']  ?? 8);

        $categories = theme_categories()->take($cat_limit);
        $products   = Product::with(['badge', 'campaign_product', 'inventory', 'category'])
            ->where('status_id', 1)
            ->latest()
            ->take($product_limit)
            ->get();

        return view('theme-electro::widgets.our_store', [
            'title'          => $content['title']          ?? 'Our Store',
            'categories'     => $categories,
            'products'       => $products,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 80),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 80),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'electro';
    }
}
