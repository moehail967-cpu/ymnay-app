<?php

namespace Plugins\WidgetBuilder\Widgets\landlord\Brand;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class BrandOne extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'brand_one';
    }

    protected function getWidgetName(): string
    {
        return 'Brand One';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-handshake';
    }

    protected function getWidgetDescription(): string
    {
        return 'Infinite scrolling brand logo marquee with grayscale effect';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['brand', 'logo', 'partner', 'client', 'marquee', 'sponsor'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('brands', 'Brand Logos')
            ->registerField('brand_items', FieldManager::REPEATER()
                ->setLabel('Brands')
                ->setItemLabel('Brand')
                ->setAddButtonText('Add Brand')
                ->setMin(1)
                ->setMax(30)
                ->setFields([
                    'logo' => FieldManager::IMAGE()
                        ->setLabel('Logo Image')
                        ->setRequired(true),
                    'name' => FieldManager::TEXT()
                        ->setLabel('Brand Name (alt text)')
                        ->setDefault('Brand'),
                    'url' => FieldManager::TEXT()
                        ->setLabel('Link URL (optional)')
                        ->setDefault(''),
                ])
            )
            ->endGroup();

        $control->addGroup('settings', 'Marquee Settings')
            ->registerField('scroll_speed', FieldManager::NUMBER()
                ->setLabel('Scroll Duration (seconds)')
                ->setDescription('Lower = faster. Time for one full loop.')
                ->setDefault(25)
                ->setMin(5)
                ->setMax(120)
                ->setStep(1)
            )
            ->registerField('pause_on_hover', FieldManager::TOGGLE()
                ->setLabel('Pause on Hover')
                ->setDefault(true)
            )
            ->registerField('direction', FieldManager::SELECT()
                ->setLabel('Scroll Direction')
                ->setOptions([
                    'left' => 'Left',
                    'right' => 'Right',
                ])
                ->setDefault('left')
            )
            ->registerField('logo_height', FieldManager::NUMBER()
                ->setLabel('Logo Height (px)')
                ->setDefault(35)
                ->setMin(15)
                ->setMax(100)
                ->setStep(5)
            )
            ->registerField('logo_gap', FieldManager::NUMBER()
                ->setLabel('Gap Between Logos (px)')
                ->setDefault(80)
                ->setMin(20)
                ->setMax(200)
                ->setStep(10)
            )
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('colors', 'Colors')
            ->registerField('background_color', FieldManager::COLOR()
                ->setLabel('Section Background')
                ->setDefault('#f9fafb')
            )
            ->registerField('border_color', FieldManager::COLOR()
                ->setLabel('Top/Bottom Border Color')
                ->setDefault('#eee')
            )
            ->registerField('logo_opacity', FieldManager::RANGE()
                ->setLabel('Logo Opacity (%)')
                ->setDefault(60)
                ->setMin(10)
                ->setMax(100)
                ->setStep(5)
            )
            ->registerField('grayscale', FieldManager::TOGGLE()
                ->setLabel('Grayscale Logos')
                ->setDefault(true)
            )
            ->registerField('color_on_hover', FieldManager::TOGGLE()
                ->setLabel('Show Color on Hover')
                ->setDefault(true)
            )
            ->endGroup();

        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top', FieldManager::NUMBER()
                ->setLabel('Padding Top (px)')
                ->setDefault(40)
                ->setMin(0)
                ->setMax(120)
                ->setStep(5)
            )
            ->registerField('padding_bottom', FieldManager::NUMBER()
                ->setLabel('Padding Bottom (px)')
                ->setDefault(40)
                ->setMin(0)
                ->setMax(120)
                ->setStep(5)
            )
            ->endGroup();

        return $control->getFields();
    }

    private function resolveImageUrl($value): string
    {
        if (is_array($value)) {
            return $value['url'] ?? '';
        }
        return is_string($value) ? $value : '';
    }

    public function render(array $settings = []): string
    {
        $general = $settings['general'] ?? [];
        $style = $settings['style'] ?? [];

        // Brands
        $brandsGroup = $general['brands'] ?? [];
        $brandItems = $brandsGroup['brand_items'] ?? [];

        // Build logo items
        $logos = [];
        foreach ($brandItems as $item) {
            $url = $this->resolveImageUrl($item['logo'] ?? '');
            if (!empty($url)) {
                $logos[] = [
                    'url' => $url,
                    'name' => $item['name'] ?? 'Brand',
                    'link' => $item['url'] ?? '',
                ];
            }
        }

        if (empty($logos)) {
            return '';
        }

        return view('widgetbuilder::landlord.brand.brand_one', [
            'logos' => $logos,
        ])->render();
    }
}
