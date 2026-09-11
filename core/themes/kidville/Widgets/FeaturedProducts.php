<?php

namespace Themes\Kidville\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class FeaturedProducts extends BaseWidget
{
    protected function getWidgetType(): string { return 'kidville_featured_products'; }
    protected function getWidgetName(): string { return 'KidVille: Featured Products'; }
    protected function getWidgetIcon(): string|array { return 'las la-star'; }
    protected function getWidgetDescription(): string { return __('Product grid in KidVille card style with badges and add-to-cart'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['products', 'featured', 'kidville']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('tag', FieldManager::TEXT()
                ->setLabel('Section Tag')
                ->setDefault('Hot Picks'))
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Section Title')
                ->setDefault('Most Popular Toys 🐰'))
            ->registerField('view_all_url', FieldManager::URL()
                ->setLabel('View All URL')
                ->setDefault('#'))
            ->endGroup();
        $control->addGroup('query', 'Product Query')
            ->registerField('item_count', FieldManager::NUMBER()
                ->setLabel('Products to Show')
                ->setDefault(8)->setMin(2)->setMax(24))
            ->registerField('sort_by', FieldManager::SELECT()
                ->setLabel('Sort By')
                ->setOptions([
                    'latest'     => 'Latest',
                    'price_asc'  => 'Price: Low to High',
                    'price_desc' => 'Price: High to Low',
                    'popular'    => 'Most Popular',
                ])
                ->setDefault('latest'))
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
                    '{{WRAPPER}} .kv-featured-products-widget' => 'padding-top: {{VALUE.TOP}}{{UNIT}}; padding-bottom: {{VALUE.BOTTOM}}{{UNIT}};',
                ]))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;">KidVille: Featured Products — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];
        $query   = $settings['general']['query']   ?? [];

        $count    = (int) ($query['item_count'] ?? 8);
        $products = theme_featured_products($count);

        $raw          = $content['view_all_url'] ?? '#';
        $view_all_url = is_array($raw) ? ($raw['url'] ?? '#') : $raw;

        return view('theme-kidville::widgets.featured_products', [
            'tag'          => $content['tag']   ?? 'Hot Picks',
            'title'        => $content['title'] ?? 'Most Popular Toys 🐰',
            'view_all_url' => $view_all_url,
            'products'     => $products,
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'kidville';
    }
}
