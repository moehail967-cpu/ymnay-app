<?php

namespace Themes\Chefhome\Widgets;

use Modules\Product\Entities\Product;
use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class FeaturedDishes extends BaseWidget
{
    protected function getWidgetType(): string { return 'chefhome_featured_dishes'; }
    protected function getWidgetName(): string { return 'ChefHome: Featured Dishes'; }
    protected function getWidgetIcon(): string|array { return 'las la-utensils'; }
    protected function getWidgetDescription(): string { return __('Product grid in ChefHome card style with badges, ratings and add-to-cart button'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['products', 'dishes', 'featured', 'chefhome']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Section Title')
                ->setDefault('Popular Dishes'))
            ->registerField('view_all_url', FieldManager::URL()
                ->setLabel('View All URL')
                ->setDefault('#'))
            ->endGroup();

        $control->addGroup('query', 'Product Query')
            ->registerField('item_count', FieldManager::NUMBER()
                ->setLabel('Dishes to Show')
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
            ->registerField('padding_top', FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(72)->setMin(0)->setMax(300))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(72)->setMin(0)->setMax(300))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {

        // Not in tenant context (editor preview API has no tenant middleware)
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;">ChefHome: Featured Dishes — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];
        $query   = $settings['general']['query']   ?? [];
        $spacing = $settings['style']['spacing']   ?? [];

        $count  = (int) ($query['item_count'] ?? 8);
        $sortBy = $query['sort_by'] ?? 'latest';

        $q = Product::with('badge', 'campaign_product', 'inventory', 'inventoryDetail', 'category')
            ->where('status_id', 1)
            ->withSum('taxOptions', 'rate');

        match ($sortBy) {
            'price_asc'  => $q->orderBy('sale_price', 'asc'),
            'price_desc' => $q->orderBy('sale_price', 'desc'),
            'popular'    => $q->orderBy('view_count', 'desc'),
            default      => $q->latest(),
        };

        $products = $q->take($count)->get();

        $rawViewAllUrl = $content['view_all_url'] ?? null;
        $viewAllUrl = is_array($rawViewAllUrl)
            ? ($rawViewAllUrl['url'] ?? '#')
            : ($rawViewAllUrl ?? (function_exists('theme_shop_url') ? theme_shop_url() : '#'));

        return view('theme-chefhome::widgets.featured_dishes', [
            'title'          => $content['title']        ?? 'Popular Dishes',
            'view_all_url'   => $viewAllUrl,
            'products'       => $products,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 72),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 72),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'chefhome';
    }
}
