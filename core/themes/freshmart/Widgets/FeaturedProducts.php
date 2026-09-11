<?php

namespace Themes\Freshmart\Widgets;

use App\Enums\StatusEnums;
use Modules\Product\Entities\Product;
use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class FeaturedProducts extends BaseWidget
{
    protected function getWidgetType(): string { return 'freshmart_featured_products'; }
    protected function getWidgetName(): string { return 'FreshMart: Featured Products'; }
    protected function getWidgetIcon(): string|array { return 'las la-shopping-basket'; }
    protected function getWidgetDescription(): string { return __('Product grid in FreshMart card style with badges, ratings and add-to-cart'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['products', 'featured', 'freshmart']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Section Title')
                ->setDefault('Fresh Picks'))
            ->registerField('subtitle', FieldManager::TEXT()
                ->setLabel('Subtitle')
                ->setDefault('Handpicked fresh products just for you'))
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
            ->registerField('padding', FieldManager::DIMENSION()
                ->asPadding()
                ->setLabel('Section Padding')
                ->setDefault(['top' => 72, 'right' => 0, 'bottom' => 72, 'left' => 0])
                ->setSelectors([
                    '{{WRAPPER}} .fm-featured-products-widget' => 'padding-top: {{VALUE.TOP}}{{UNIT}}; padding-bottom: {{VALUE.BOTTOM}}{{UNIT}};',
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
                        <p style="margin:0;font-size:14px;">FreshMart: Featured Products — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];
        $query   = $settings['general']['query']   ?? [];

        $count  = (int) ($query['item_count'] ?? 8);
        $sortBy = $query['sort_by'] ?? 'latest';

        $viewAllUrl = $content['view_all_url'] ?? null;
        if (is_array($viewAllUrl)) { $viewAllUrl = $viewAllUrl['url'] ?? null; }
        if (empty($viewAllUrl)) { $viewAllUrl = function_exists('theme_shop_url') ? theme_shop_url() : '#'; }

        $q = Product::with('badge', 'campaign_product', 'inventory', 'inventoryDetail', 'category')
            ->where('status_id', StatusEnums::PUBLISH)
            ->withSum('taxOptions', 'rate');

        match ($sortBy) {
            'price_asc'  => $q->orderBy('sale_price', 'asc'),
            'price_desc' => $q->orderBy('sale_price', 'desc'),
            'popular'    => $q->orderBy('view_count', 'desc'),
            default      => $q->latest(),
        };

        $products = $q->take($count)->get();

        return view('theme-freshmart::widgets.featured_products', [
            'title'        => $content['title']    ?? 'Fresh Picks',
            'subtitle'     => $content['subtitle'] ?? '',
            'view_all_url' => $viewAllUrl,
            'products'     => $products,
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'freshmart';
    }
}
