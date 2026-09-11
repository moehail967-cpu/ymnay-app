<?php

namespace Themes\Bakerco\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class FeaturedProducts extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'bakerco_featured_products';
    }

    protected function getWidgetName(): string
    {
        return 'BakerCo: Featured Products';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-shopping-bag';
    }

    protected function getWidgetDescription(): string
    {
        return __('Showcase featured, new, or sale products in a card grid');
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['products', 'featured', 'bakerco'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('section_tag', FieldManager::TEXT()->setLabel('Section Tag')->setDefault('Bestsellers'))
            ->registerField('title', FieldManager::TEXT()->setLabel('Title')->setDefault('Fresh From the Oven'))
            ->registerField('subtitle', FieldManager::TEXT()->setLabel('Subtitle')->setDefault(''))
            ->registerField('product_type', FieldManager::SELECT()->setLabel('Product Type')->setOptions([
                'featured' => 'Featured',
                'new' => 'New Arrivals',
                'sale' => 'On Sale',
            ])->setDefault('featured'))
            ->registerField('count', FieldManager::NUMBER()->setLabel('Number of Products')->setDefault(8)->setMin(1)->setMax(24))
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
                        <p style="margin:0;font-size:14px;">BakerCo: Featured Products — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];
        $spacing = $settings['style']['spacing'] ?? [];
        $product_type = $content['product_type'] ?? 'featured';
        $count = (int) ($content['count'] ?? 8);

        if (function_exists('theme_featured_products')) {
            $products = theme_featured_products($count);
        } else {
            $products = collect();
        }

        return view('theme-bakerco::widgets.featured_products', [
            'section_tag' => $content['section_tag'] ?? 'Bestsellers',
            'title' => $content['title'] ?? 'Fresh From the Oven',
            'subtitle' => $content['subtitle'] ?? '',
            'products' => $products,
            'padding_top' => (int) ($spacing['padding_top'] ?? 60),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 60),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'bakerco';
    }
}
