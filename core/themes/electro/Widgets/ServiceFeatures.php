<?php

namespace Themes\Electro\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class ServiceFeatures extends BaseWidget
{
    protected function getWidgetType(): string { return 'electro_service_features'; }
    protected function getWidgetName(): string { return 'Electro: Service Features'; }
    protected function getWidgetIcon(): string|array { return 'las la-shield-alt'; }
    protected function getWidgetDescription(): string { return __('4-column icon + title + description service highlights'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['services', 'features', 'icons', 'electro']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $defaults = [
            'feature_1' => ['las la-shipping-fast', 'Free Shipping',   'Free shopping on all online order'],
            'feature_2' => ['las la-headset',        '24/7 Support',    'Contact us 24 hours'],
            'feature_3' => ['las la-plane-departure', '90 Days Return', 'If goods have damage issues'],
            'feature_4' => ['las la-dollar-sign',    'Payment Secure',  'We Ensure Secure Transaction'],
        ];

        foreach ($defaults as $key => [$icon, $title, $desc]) {
            $num = substr($key, -1);
            $control->addGroup($key, "Feature $num")
                ->registerField('icon', FieldManager::TEXT()->setLabel('Icon Class (Line Awesome)')->setDefault($icon))
                ->registerField('title', FieldManager::TEXT()->setLabel('Title')->setDefault($title))
                ->registerField('description', FieldManager::TEXT()->setLabel('Description')->setDefault($desc))
                ->endGroup();
        }

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
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">Electro: Service Features — preview on the live page</p></div>';
        }

        $g       = $settings['general'] ?? [];
        $spacing = $settings['style']['spacing'] ?? [];

        $defaults = [
            ['las la-shipping-fast', 'Free Shipping',   'Free shopping on all online order'],
            ['las la-headset',        '24/7 Support',    'Contact us 24 hours'],
            ['las la-plane-departure','90 Days Return',  'If goods have damage issues'],
            ['las la-dollar-sign',    'Payment Secure',  'We Ensure Secure Transaction'],
        ];

        $features = [];
        foreach (['feature_1', 'feature_2', 'feature_3', 'feature_4'] as $i => $key) {
            $f = $g[$key] ?? [];
            $features[] = [
                'icon'        => $f['icon']        ?? $defaults[$i][0],
                'title'       => $f['title']       ?? $defaults[$i][1],
                'description' => $f['description'] ?? $defaults[$i][2],
            ];
        }

        return view('theme-electro::widgets.service_features', [
            'features'       => $features,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 60),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 60),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'electro';
    }
}
