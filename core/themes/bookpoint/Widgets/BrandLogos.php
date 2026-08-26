<?php

namespace Themes\Bookpoint\Widgets;

use Modules\Attributes\Entities\Brand as BrandModel;
use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class BrandLogos extends BaseWidget
{
    protected function getWidgetType(): string { return 'bookpoint_brand_logos'; }
    protected function getWidgetName(): string { return 'BookPoint: Brand Logos'; }
    protected function getWidgetIcon(): string|array { return 'las la-award'; }
    protected function getWidgetDescription(): string { return __('Publisher/brand logo carousel with dot navigation'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['brands', 'publishers', 'logos', 'bookpoint']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('item_show', FieldManager::NUMBER()->setLabel('Brands to Show')->setDefault(10)->setMin(1)->setMax(30))
            ->registerField('per_slide', FieldManager::NUMBER()->setLabel('Brands Per Slide')->setDefault(5)->setMin(2)->setMax(8))
            ->endGroup();
        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top', FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(40)->setMin(0)->setMax(200))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(40)->setMin(0)->setMax(200))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">BookPoint: Brand Logos — preview on the live page</p></div>';
        }

        $content  = $settings['general']['content'] ?? [];
        $spacing  = $settings['style']['spacing']   ?? [];
        $count    = (int) ($content['item_show'] ?? 10);
        $perSlide = max(1, (int) ($content['per_slide'] ?? 5));

        try {
            $brands = BrandModel::orderBy('id')->take($count)->get();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('bookpoint_brand_logos query failed: ' . $e->getMessage());
            $brands = collect();
        }

        $chunks = $brands->chunk($perSlide);

        return view('theme-bookpoint::widgets.brand_logos', [
            'brands'         => $brands,
            'chunks'         => $chunks,
            'per_slide'      => $perSlide,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 40),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 40),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'bookpoint';
    }
}
