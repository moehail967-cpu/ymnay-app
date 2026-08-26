<?php

namespace Themes\Aromatic\Widgets;

use Modules\Attributes\Entities\Brand as BrandModel;
use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class Brand extends BaseWidget
{
    protected function getWidgetType(): string  { return 'aromatic_brand'; }
    protected function getWidgetName(): string  { return 'Aromatic: Brand Logos'; }
    protected function getWidgetIcon(): string|array { return 'las la-award'; }
    protected function getWidgetDescription(): string { return __('Brand logo carousel with pagination dots'); }
    protected function getCategory(): string    { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array   { return ['brand', 'logos', 'slider', 'aromatic']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('item_show',  FieldManager::NUMBER()->setLabel('Brands to Show')->setDefault(8)->setMin(1)->setMax(30))
            ->registerField('per_slide',  FieldManager::NUMBER()->setLabel('Brands Per Slide')->setDefault(5)->setMin(2)->setMax(8))
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top',    FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(60)->setMin(0)->setMax(200))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(60)->setMin(0)->setMax(200))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;color:#888;">Aromatic: Brand Logos — preview on the live page</div>';
        }

        try {
            $content  = $settings['general']['content'] ?? [];
            $spacing  = $settings['style']['spacing']   ?? [];

            $count    = (int) ($content['item_show'] ?? 8);
            $perSlide = max(1, (int) ($content['per_slide'] ?? 5));

            try {
                $brands = BrandModel::orderBy('id')->take($count)->get();
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('aromatic_brand DB query failed: ' . $e->getMessage());
                $brands = collect();
            }

            $chunks = $brands->chunk($perSlide);

            return view('theme-aromatic::widgets.brand', [
                'brands'         => $brands,
                'chunks'         => $chunks,
                'per_slide'      => $perSlide,
                'padding_top'    => (int) ($spacing['padding_top']    ?? 60),
                'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 60),
            ])->render();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('aromatic_brand render failed: ' . $e->getMessage(), [
                'file' => $e->getFile(), 'line' => $e->getLine(),
            ]);
            return '<div style="padding:20px;text-align:center;color:#c00;font-size:13px;font-family:sans-serif;">Brand Logos: preview unavailable</div>';
        }
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'aromatic';
    }
}
