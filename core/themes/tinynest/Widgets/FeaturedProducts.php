<?php

namespace Themes\Tinynest\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;
use Modules\Product\Entities\Product;

class FeaturedProducts extends BaseWidget
{
    protected function getWidgetType(): string { return 'tinynest_featured_products'; }
    protected function getWidgetName(): string { return 'TinyNest: Featured Products'; }
    protected function getWidgetIcon(): string|array { return 'las la-baby'; }
    protected function getWidgetDescription(): string { return __('Display featured baby products in a soft card grid'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['products', 'featured', 'tinynest']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()->setLabel('Section Title')->setDefault('New Arrivals'))
            ->registerField('subtitle', FieldManager::TEXT()->setLabel('Subtitle')->setDefault('Freshly added baby essentials'))
            ->registerField('count', FieldManager::NUMBER()->setLabel('Number of Products')->setDefault(8)->setMin(2)->setMax(24))
            ->registerField('sort', FieldManager::SELECT()
                ->setLabel('Sort By')
                ->setOptions(['latest' => 'Latest', 'popular' => 'Most Popular', 'top_rated' => 'Top Rated', 'best_sell' => 'Best Selling'])
                ->setDefault('latest'))
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
                        <p style="margin:0;font-size:14px;">TinyNest: Featured Products — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];
        $spacing = $settings['style']['spacing']   ?? [];

        $query = Product::with(['badge', 'campaign_product', 'inventory', 'inventoryDetail', 'category'])
            ->where('status_id', 1)
            ->withSum('taxOptions', 'rate');

        match ($content['sort'] ?? 'latest') {
            'popular'   => $query->orderByDesc('view_count'),
            'top_rated' => $query->orderByDesc('rating'),
            'best_sell' => $query->orderByDesc('sell_count'),
            default     => $query->latest(),
        };

        $products = $query->take((int) ($content['count'] ?? 8))->get();

        return view('theme-tinynest::widgets.featured_products', [
            'title'          => $content['title']    ?? 'New Arrivals',
            'subtitle'       => $content['subtitle'] ?? '',
            'products'       => $products,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 60),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 60),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'tinynest';
    }
}
