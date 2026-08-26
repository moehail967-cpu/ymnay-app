<?php

namespace Plugins\WidgetBuilder\Widgets\Tenant;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class BrandSlider extends BaseWidget
{
    protected function getWidgetType(): string { return 'tenant_brand_slider'; }
    protected function getWidgetName(): string { return 'Brand Slider'; }
    protected function getWidgetIcon(): string|array { return 'las la-award'; }
    protected function getWidgetDescription(): string { return __('Scrolling brand logo strip / slider'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['brands', 'logos', 'slider', 'partners']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('header', 'Section Header')
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Title (optional)')
                ->setDefault(''))
            ->endGroup();

        $control->addGroup('brands', 'Brand Logos')
            ->registerField('brand_list', FieldManager::REPEATER()
                ->setLabel('Brands')
                ->setItemLabel('Brand')
                ->setAddButtonText('Add Brand')
                ->setMin(1)->setMax(20)
                ->setFields([
                    'logo'  => FieldManager::IMAGE()->setLabel('Brand Logo')->setRequired(true),
                    'name'  => FieldManager::TEXT()->setLabel('Brand Name (alt text)')->setDefault('Brand'),
                    'url'   => FieldManager::URL()->setLabel('Link URL (optional)')->setDefault(''),
                ]))
            ->endGroup();

        $control->addGroup('slider_config', 'Slider Settings')
            ->registerField('autoplay', FieldManager::TOGGLE()->setLabel('Auto Play')->setDefault(true))
            ->registerField('autoplay_speed', FieldManager::NUMBER()->setLabel('Speed (ms)')->setDefault(3000)->setMin(1000)->setMax(10000))
            ->registerField('items_per_row', FieldManager::NUMBER()->setLabel('Visible Items')->setDefault(5)->setMin(2)->setMax(10))
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('style', 'Style')
            ->registerField('background_color', FieldManager::COLOR()->setLabel('Background Color')->setDefault('#ffffff'))
            ->registerField('grayscale', FieldManager::TOGGLE()->setLabel('Grayscale Logos')->setDefault(true))
            ->endGroup();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top', FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(50)->setMin(0)->setMax(200))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(50)->setMin(0)->setMax(200))
            ->endGroup();
        return $control->getFields();
    }

    private function resolveImageUrl($value): string
    {
        if (is_array($value) && isset($value['id'])) {
            $img = get_attachment_image_by_id($value['id']);
            return $img['img_url'] ?? '';
        }
        if (is_array($value)) return $value['url'] ?? '';
        return is_string($value) ? $value : '';
    }

    public function render(array $settings = []): string
    {
        $general = $settings['general'] ?? [];
        $style   = $settings['style'] ?? [];

        $header       = $general['header'] ?? [];
        $brands_raw   = ($general['brands'] ?? [])['brand_list'] ?? [];
        $slider_cfg   = $general['slider_config'] ?? [];
        $styling      = $style['style'] ?? [];
        $spacing      = $style['spacing'] ?? [];

        $brands = array_map(function ($b) {
            return [
                'logo_url' => $this->resolveImageUrl($b['logo'] ?? null),
                'name'     => $b['name'] ?? 'Brand',
                'url'      => $b['url'] ?? '',
            ];
        }, $brands_raw);

        return view('widgetbuilder::tenant.brand_slider', [
            'title'          => $header['title'] ?? '',
            'brands'         => $brands,
            'autoplay'       => (bool) ($slider_cfg['autoplay'] ?? true),
            'autoplay_speed' => (int) ($slider_cfg['autoplay_speed'] ?? 3000),
            'items_per_row'  => (int) ($slider_cfg['items_per_row'] ?? 5),
            'bg_color'       => $styling['background_color'] ?? '#ffffff',
            'grayscale'      => (bool) ($styling['grayscale'] ?? true),
            'padding_top'    => (int) ($spacing['padding_top'] ?? 50),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 50),
        ])->render();
    }

    public function enable(): bool { return !is_null(tenant()); }
}
