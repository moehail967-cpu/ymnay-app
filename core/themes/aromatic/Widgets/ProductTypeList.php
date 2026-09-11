<?php

namespace Themes\Aromatic\Widgets;

use Modules\Product\Entities\Product;
use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class ProductTypeList extends BaseWidget
{
    protected function getWidgetType(): string  { return 'aromatic_product_type_list'; }
    protected function getWidgetName(): string  { return 'Aromatic: Our Store'; }
    protected function getWidgetIcon(): string|array { return 'las la-store'; }
    protected function getWidgetDescription(): string { return __('Tabbed category-filtered product grid'); }
    protected function getCategory(): string    { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array   { return ['products', 'store', 'tabs', 'category', 'aromatic']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('title',     FieldManager::TEXT()->setLabel('Section Title')->setDefault('Our Store'))
            ->registerField('show_line', FieldManager::SELECT()->setLabel('Show Underline')->setOptions(['yes' => 'Yes', 'no' => 'No'])->setDefault('yes'))
            ->registerField('item_show', FieldManager::NUMBER()->setLabel('Products to Show')->setDefault(8)->setMin(2)->setMax(24))
            ->registerField('sort_by',   FieldManager::SELECT()->setLabel('Sort By')->setOptions(['id' => 'Default', 'created_at' => 'Newest', 'sale_price' => 'Price'])->setDefault('id'))
            ->registerField('sort_to',   FieldManager::SELECT()->setLabel('Order')->setOptions(['desc' => 'Descending', 'asc' => 'Ascending'])->setDefault('desc'))
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top',    FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(80)->setMin(0)->setMax(300))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(80)->setMin(0)->setMax(300))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;color:#888;">Aromatic: Our Store — preview on the live page</div>';
        }

        try {
            $content = $settings['general']['content'] ?? [];
            $spacing = $settings['style']['spacing']   ?? [];

            $count  = (int) ($content['item_show'] ?? 8);
            $sortBy = in_array($content['sort_by'] ?? 'id', ['id', 'created_at', 'sale_price']) ? ($content['sort_by'] ?? 'id') : 'id';
            $sortTo = in_array($content['sort_to'] ?? 'desc', ['asc', 'desc']) ? ($content['sort_to'] ?? 'desc') : 'desc';

            try {
                $products = Product::with(['badge', 'campaign_product', 'inventory', 'inventoryDetail', 'category'])
                    ->where('status_id', 1)
                    ->orderBy($sortBy, $sortTo)
                    ->take($count)
                    ->get();
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('aromatic_product_type_list DB query failed: ' . $e->getMessage());
                $products = collect();
            }

            $categories = $products->pluck('category')->filter()->unique('id')->values();

            return view('theme-aromatic::widgets.product_type_list', [
                'title'          => $content['title']    ?? 'Our Store',
                'show_line'      => ($content['show_line'] ?? 'yes') === 'yes',
                'products'       => $products,
                'categories'     => $categories,
                'padding_top'    => (int) ($spacing['padding_top']    ?? 80),
                'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 80),
            ])->render();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('aromatic_product_type_list render failed: ' . $e->getMessage(), [
                'file' => $e->getFile(), 'line' => $e->getLine(),
            ]);
            return '<div style="padding:20px;text-align:center;color:#c00;font-size:13px;font-family:sans-serif;">Our Store: preview unavailable</div>';
        }
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'aromatic';
    }
}
