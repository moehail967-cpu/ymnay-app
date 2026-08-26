<?php

namespace Themes\Hexfashion\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class BrandLogos extends BaseWidget
{
    protected function getWidgetType(): string { return 'hexfashion_brand_logos'; }
    protected function getWidgetName(): string { return 'HexFashion: Brand Logos'; }
    protected function getWidgetIcon(): string|array { return 'las la-award'; }
    protected function getWidgetDescription(): string { return __('Brand/partner logo carousel with dots and auto-advance'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['brand', 'logos', 'partners', 'hexfashion']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()->setLabel('Title')->setDefault('OUR BRANDS'))
            ->registerField('per_slide', FieldManager::NUMBER()->setLabel('Logos per Slide')->setDefault(5)->setMin(2)->setMax(8))
            ->registerField('limit', FieldManager::NUMBER()->setLabel('Total Logos to Show')->setDefault(15)->setMin(1)->setMax(40))
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
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">HexFashion: Brand Logos — preview on the live page</p></div>';
        }

        $content   = $settings['general']['content'] ?? [];
        $spacing   = $settings['style']['spacing']   ?? [];
        $per_slide = (int) ($content['per_slide'] ?? 5);
        $limit     = (int) ($content['limit']     ?? 15);

        $brands = collect();
        try {
            if (class_exists(\Modules\Attributes\Entities\Brand::class)) {
                $brands = \Modules\Attributes\Entities\Brand::with('logo')->take($limit)->get();
            }
        } catch (\Exception $e) {
            $brands = collect();
        }

        $slides = $brands->chunk($per_slide)->values();

        return view('theme-hexfashion::widgets.brand_logos', [
            'title'          => $content['title']     ?? 'OUR BRANDS',
            'slides'         => $slides,
            'brands'         => $brands,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 60),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 60),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'hexfashion';
    }
}
