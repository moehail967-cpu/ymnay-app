<?php

namespace Plugins\WidgetBuilder\Widgets\Tenant;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class ServicesStrip extends BaseWidget
{
    protected function getWidgetType(): string { return 'tenant_services_strip'; }
    protected function getWidgetName(): string { return 'Services Strip'; }
    protected function getWidgetIcon(): string|array { return 'las la-concierge-bell'; }
    protected function getWidgetDescription(): string { return __('Icon + title + description feature highlights (shipping, payment, returns, support)'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['services', 'features', 'shipping', 'strip']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('items', 'Service Items')
            ->registerField('services', FieldManager::REPEATER()
                ->setLabel('Services')
                ->setItemLabel('Service')
                ->setAddButtonText('Add Service')
                ->setMin(1)->setMax(8)
                ->setFields([
                    'icon'        => FieldManager::TEXT()->setLabel('Icon Class (e.g. las la-truck)')->setDefault('las la-truck'),
                    'title'       => FieldManager::TEXT()->setLabel('Title')->setDefault('Free Shipping'),
                    'description' => FieldManager::TEXT()->setLabel('Description')->setDefault('On orders over $50'),
                ])
                ->setDefault([
                    ['icon' => 'las la-truck',         'title' => 'Free Shipping',    'description' => 'On orders over $50'],
                    ['icon' => 'las la-shield-alt',    'title' => 'Secure Payment',   'description' => '100% secure checkout'],
                    ['icon' => 'las la-undo',          'title' => 'Easy Returns',     'description' => '30-day return policy'],
                    ['icon' => 'las la-headset',       'title' => '24/7 Support',     'description' => 'Always here to help'],
                ]))
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('style', 'Style')
            ->registerField('background_color', FieldManager::COLOR()->setLabel('Background Color')->setDefault('#ffffff'))
            ->registerField('icon_color', FieldManager::COLOR()->setLabel('Icon Color')->setDefault(''))
            ->endGroup();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top', FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(50)->setMin(0)->setMax(200))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(50)->setMin(0)->setMax(200))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        $general = $settings['general'] ?? [];
        $style   = $settings['style'] ?? [];

        $items   = $general['items'] ?? [];
        $styling = $style['style'] ?? [];
        $spacing = $style['spacing'] ?? [];

        $services = $items['services'] ?? [
            ['icon' => 'las la-truck',      'title' => 'Free Shipping',   'description' => 'On orders over $50'],
            ['icon' => 'las la-shield-alt', 'title' => 'Secure Payment',  'description' => '100% secure checkout'],
            ['icon' => 'las la-undo',       'title' => 'Easy Returns',    'description' => '30-day return policy'],
            ['icon' => 'las la-headset',    'title' => '24/7 Support',    'description' => 'Always here to help'],
        ];

        return view('widgetbuilder::tenant.services_strip', [
            'services'       => $services,
            'bg_color'       => $styling['background_color'] ?? '#ffffff',
            'icon_color'     => $styling['icon_color'] ?? '',
            'padding_top'    => (int) ($spacing['padding_top'] ?? 50),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 50),
        ])->render();
    }

    public function enable(): bool { return !is_null(tenant()); }
}
