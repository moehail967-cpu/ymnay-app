<?php

namespace Themes\Luxegems\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class FeaturedProducts extends BaseWidget
{
    protected function getWidgetType(): string { return 'luxegems_featured_products'; }
    protected function getWidgetName(): string { return 'LuxeGems: Featured Products'; }
    protected function getWidgetIcon(): string|array { return 'las la-gem'; }
    protected function getWidgetDescription(): string { return __('Product grid in dark luxury card style with gold accents and add-to-cart'); }
    protected function getCategory(): string { return WidgetCategory::ECOMMERCE; }
    protected function getWidgetTags(): array { return ['products', 'featured', 'jewelry', 'luxegems']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Section Title')
                ->setDefault('Featured Pieces'))
            ->registerField('subtitle', FieldManager::TEXT()
                ->setLabel('Section Subtitle')
                ->setDefault('Handpicked selections from our master artisans'))
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
                    '{{WRAPPER}} .lx-widget-products' => 'padding-top: {{VALUE.TOP}}{{UNIT}}; padding-bottom: {{VALUE.BOTTOM}}{{UNIT}};',
                ]))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#111;border:1px dashed #333;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;">LuxeGems: Featured Products — preview on the live page</p>
                    </div>';
        }

        try {
            $content = $settings['general']['content'] ?? [];
            $query   = $settings['general']['query']   ?? [];
            $count   = (int) ($query['item_count'] ?? 8);

            $products = theme_featured_products($count);

            $raw = $content['view_all_url'] ?? '#';
            $view_all_url = is_array($raw) ? ($raw['url'] ?? '#') : $raw;

            return view('theme-luxegems::widgets.featured_products', [
                'title'        => $content['title']    ?? 'Featured Pieces',
                'subtitle'     => $content['subtitle'] ?? '',
                'view_all_url' => $view_all_url,
                'products'     => $products,
            ])->render();

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('LuxeGems FeaturedProducts render error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return '<div style="padding:40px;text-align:center;background:#111;border:1px dashed #8B0000;color:#c88;font-family:sans-serif;">
                        <p style="margin:0;font-size:13px;">Featured Products render error — check laravel.log</p>
                    </div>';
        }
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'luxegems';
    }
}
