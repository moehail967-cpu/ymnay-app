<?php

namespace Themes\Maison\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class FeaturedProducts extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'maison_featured_products';
    }

    protected function getWidgetName(): string
    {
        return 'Maison: Featured Products';
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
        return ['products', 'featured', 'maison'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('section_tag', FieldManager::TEXT()->setLabel('Section Tag')->setDefault('Editor\'s Choice'))
            ->registerField('title', FieldManager::TEXT()->setLabel('Title')->setDefault('Curated Picks'))
            ->registerField('subtitle', FieldManager::TEXT()->setLabel('Subtitle')->setDefault(''))
            ->registerField('product_type', FieldManager::SELECT()->setLabel('Product Type')->setOptions([
                'featured' => 'Featured',
                'new'      => 'New Arrivals',
                'sale'     => 'On Sale',
            ])->setDefault('featured'))
            ->registerField('count', FieldManager::NUMBER()->setLabel('Number of Products')->setDefault(8)->setMin(1)->setMax(24))
            ->registerField('view_all_url', FieldManager::URL()->setLabel('View All URL')->setDefault('#'))
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
                ->setDefault(['top' => 80, 'right' => 0, 'bottom' => 80, 'left' => 0])
                ->setSelectors([
                    '{{WRAPPER}} .ms-widget-products' => 'padding-top: {{VALUE.TOP}}{{UNIT}}; padding-bottom: {{VALUE.BOTTOM}}{{UNIT}};',
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
                        <p style="margin:0;font-size:14px;">Maison: Featured Products — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];
        $count   = (int) ($content['count'] ?? 8);

        if (function_exists('theme_featured_products')) {
            $products = theme_featured_products($count);
        } else {
            $products = collect();
        }

        $raw_url = $content['view_all_url'] ?? [];
        $view_all_url = is_array($raw_url) ? ($raw_url['url'] ?? '#') : ($raw_url ?: '#');

        return view('theme-maison::widgets.featured_products', [
            'section_tag' => $content['section_tag'] ?? 'Editor\'s Choice',
            'title'       => $content['title']       ?? 'Curated Picks',
            'subtitle'    => $content['subtitle']    ?? '',
            'products'    => $products,
            'view_all_url'=> $view_all_url,
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'maison';
    }
}
