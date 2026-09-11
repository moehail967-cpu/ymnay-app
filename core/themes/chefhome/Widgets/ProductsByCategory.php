<?php

namespace Themes\Chefhome\Widgets;

use Modules\Attributes\Entities\Category;
use Modules\Product\Entities\Product;
use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class ProductsByCategory extends BaseWidget
{
    protected function getWidgetType(): string { return 'chefhome_products_by_category'; }
    protected function getWidgetName(): string { return 'ChefHome: Products by Category'; }
    protected function getWidgetIcon(): string|array { return 'las la-grip-horizontal'; }
    protected function getWidgetDescription(): string { return __('Tabbed section showing products grouped by category with ChefHome card style'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['products', 'tabs', 'category', 'chefhome']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Section Title')
                ->setDefault('Shop by Category'))
            ->registerField('view_all_url', FieldManager::URL()
                ->setLabel('View All URL')
                ->setDefault('#'))
            ->registerField('category_count', FieldManager::NUMBER()
                ->setLabel('Number of Category Tabs')
                ->setDefault(4)->setMin(2)->setMax(8))
            ->registerField('products_per_tab', FieldManager::NUMBER()
                ->setLabel('Products Per Tab')
                ->setDefault(4)->setMin(2)->setMax(12))
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
                        <p style="margin:0;font-size:14px;">ChefHome: Products by Category — preview on the live page</p>
                    </div>';
        }
        $content  = $settings['general']['content'] ?? [];
        $spacing  = $settings['style']['spacing']   ?? [];
        $catCount = (int) ($content['category_count']   ?? 4);
        $perTab   = (int) ($content['products_per_tab'] ?? 4);

        try {
            $categories = theme_categories()->take($catCount);

            $tabs = $categories->map(function (Category $cat) use ($perTab) {
                $productIds = \Modules\Product\Entities\ProductCategory::where('category_id', $cat->id)
                    ->pluck('product_id');

                $products = Product::with('badge', 'inventoryDetail', 'category')
                    ->where('status_id', \App\Enums\StatusEnums::PUBLISH)
                    ->whereIn('id', $productIds)
                    ->latest()
                    ->take($perTab)
                    ->get();

                return ['category' => $cat, 'products' => $products];
            });
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('ProductsByCategory render error: ' . $e->getMessage());
            $tabs = collect();
        }

        $rawViewAllUrl = $content['view_all_url'] ?? null;
        $viewAllUrl = is_array($rawViewAllUrl)
            ? ($rawViewAllUrl['url'] ?? '#')
            : ($rawViewAllUrl ?? (function_exists('theme_shop_url') ? theme_shop_url() : '#'));

        return view('theme-chefhome::widgets.products_by_category', [
            'title'          => $content['title']        ?? 'Shop by Category',
            'view_all_url'   => $viewAllUrl,
            'tabs'           => $tabs,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 72),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 72),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'chefhome';
    }
}
