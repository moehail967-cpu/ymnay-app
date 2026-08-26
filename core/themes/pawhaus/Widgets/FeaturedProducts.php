<?php

namespace Themes\Pawhaus\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class FeaturedProducts extends BaseWidget
{
    protected function getWidgetType(): string { return 'pawhaus_featured_products'; }
    protected function getWidgetName(): string { return 'PawHaus: Featured Products'; }
    protected function getWidgetIcon(): string|array { return 'las la-star'; }
    protected function getWidgetDescription(): string { return __('Featured pet product grid with ATC buttons'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['products', 'featured', 'pawhaus']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('section_tag', FieldManager::TEXT()
                ->setLabel('Section Tag')
                ->setDefault('Pet Parent Picks'))
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Section Title (HTML allowed)')
                ->setDefault('Most Loved <span>Products</span> 🐾'))
            ->registerField('product_type', FieldManager::SELECT()
                ->setLabel('Product Type')
                ->setOptions([
                    'featured' => 'Featured',
                    'new'      => 'New Arrivals',
                    'sale'     => 'On Sale',
                ])
                ->setDefault('featured'))
            ->registerField('item_count', FieldManager::NUMBER()
                ->setLabel('Products to Show')
                ->setDefault(8)->setMin(2)->setMax(24))
            ->registerField('view_all_url', FieldManager::URL()
                ->setLabel('View All URL')
                ->setDefault('#'))
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
                    '{{WRAPPER}} .ph-featured-products-widget' => 'padding-top: {{VALUE.TOP}}{{UNIT}}; padding-bottom: {{VALUE.BOTTOM}}{{UNIT}};',
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
                        <p style="margin:0;font-size:14px;">PawHaus: Featured Products — preview on the live page</p>
                    </div>';
        }
        $content  = $settings['general']['content'] ?? [];
        $count    = (int) ($content['item_count'] ?? 8);
        $products = theme_featured_products($count);

        $raw_url      = $content['view_all_url'] ?? [];
        $view_all_url = is_array($raw_url) ? ($raw_url['url'] ?? '#') : ($raw_url ?: '#');

        return view('theme-pawhaus::widgets.featured_products', [
            'section_tag'  => $content['section_tag'] ?? 'Pet Parent Picks',
            'title'        => $content['title']       ?? 'Most Loved <span>Products</span> 🐾',
            'view_all_url' => $view_all_url,
            'products'     => $products,
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'pawhaus';
    }
}
