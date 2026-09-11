<?php

namespace Themes\Aromatic\Widgets;

use Modules\Product\Entities\Product;
use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class NewCollection extends BaseWidget
{
    protected function getWidgetType(): string  { return 'aromatic_new_collection'; }
    protected function getWidgetName(): string  { return 'Aromatic: New Collection'; }
    protected function getWidgetIcon(): string|array { return 'las la-shopping-bag'; }
    protected function getWidgetDescription(): string { return __('New arrivals product grid – 4 columns with centered title'); }
    protected function getCategory(): string    { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array   { return ['products', 'new', 'collection', 'aromatic']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('title',      FieldManager::TEXT()->setLabel('Section Title')->setDefault('New Collections'))
            ->registerField('show_line',  FieldManager::SELECT()->setLabel('Show Underline')->setOptions(['yes' => 'Yes', 'no' => 'No'])->setDefault('yes'))
            ->registerField('item_show',  FieldManager::NUMBER()->setLabel('Products to Show')->setDefault(4)->setMin(2)->setMax(12))
            ->registerField('item_order', FieldManager::SELECT()->setLabel('Order')->setOptions(['desc' => 'Newest First', 'asc' => 'Oldest First'])->setDefault('desc'))
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
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;color:#888;">Aromatic: New Collection — preview on the live page</div>';
        }

        try {
            $content = $settings['general']['content'] ?? [];
            $spacing = $settings['style']['spacing']   ?? [];

            $count = (int) ($content['item_show'] ?? 4);
            $order = in_array($content['item_order'] ?? 'desc', ['asc', 'desc']) ? ($content['item_order'] ?? 'desc') : 'desc';

            try {
                $products = Product::with(['badge', 'campaign_product', 'inventory', 'inventoryDetail', 'category'])
                    ->where('status_id', 1)
                    ->orderBy('created_at', $order)
                    ->take($count)
                    ->get();
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('aromatic_new_collection DB query failed: ' . $e->getMessage());
                $products = collect();
            }

            return view('theme-aromatic::widgets.new_collection', [
                'title'          => $content['title']    ?? 'New Collections',
                'show_line'      => ($content['show_line'] ?? 'yes') === 'yes',
                'products'       => $products,
                'padding_top'    => (int) ($spacing['padding_top']    ?? 80),
                'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 80),
            ])->render();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('aromatic_new_collection render failed: ' . $e->getMessage(), [
                'file' => $e->getFile(), 'line' => $e->getLine(),
            ]);
            return '<div style="padding:20px;text-align:center;color:#c00;font-size:13px;font-family:sans-serif;">New Collection: preview unavailable</div>';
        }
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'aromatic';
    }
}
