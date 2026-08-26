<?php

namespace Plugins\WidgetBuilder\Widgets\Tenant;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class BrandLogos extends BaseWidget
{
    protected function getWidgetType(): string { return 'tenant_brand_logos'; }
    protected function getWidgetName(): string { return 'Brand Logos'; }
    protected function getWidgetIcon(): string|array { return 'las la-building'; }
    protected function getWidgetDescription(): string { return __('Brand logo carousel — auto-fetched from the Brands table'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['brands', 'logos', 'partners']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('title',       FieldManager::TEXT()->setLabel('Title (optional)')->setDefault(''))
            ->registerField('per_slide',   FieldManager::NUMBER()->setLabel('Logos per Row')->setDefault(6)->setMin(2)->setMax(10))
            ->registerField('brand_count', FieldManager::NUMBER()->setLabel('Total Brands to Show (0 = all)')->setDefault(0)->setMin(0)->setMax(100))
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
            return '<div style="padding:40px;text-align:center;background:#fff;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">Brand Logos — preview on the live page</p></div>';
        }

        $content     = $settings['general']['content'] ?? [];
        $spacing     = $settings['style']['spacing']   ?? [];
        $per_slide   = (int) ($content['per_slide']   ?? 6);
        $brand_count = (int) ($content['brand_count'] ?? 0);

        $brands = collect();
        try {
            if (class_exists(\Modules\Attributes\Entities\Brand::class)) {
                $query = \Modules\Attributes\Entities\Brand::orderBy('id');
                if ($brand_count > 0) $query->take($brand_count);
                $brands = $query->get();
            }
        } catch (\Exception $e) {
            $brands = collect();
        }

        $slides = $brands->chunk($per_slide)->values();

        return view('widgetbuilder::tenant.brand_logos', [
            'title'          => $content['title']  ?? '',
            'brands'         => $brands,
            'slides'         => $slides,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 60),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 60),
        ])->render();
    }

    public function enable(): bool { return !is_null(tenant()); }
}
