<?php

namespace Themes\Bookpoint\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class ServiceFeatures extends BaseWidget
{
    protected function getWidgetType(): string { return 'bookpoint_service_features'; }
    protected function getWidgetName(): string { return 'BookPoint: Service Features'; }
    protected function getWidgetIcon(): string|array { return 'las la-concierge-bell'; }
    protected function getWidgetDescription(): string { return __('4-column service features with icons, separated by dividers'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['features', 'services', 'bookpoint']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('feature1', 'Feature 1')
            ->registerField('icon1', FieldManager::TEXT()->setLabel('Icon Class (las la-*)')->setDefault('las la-shipping-fast'))
            ->registerField('title1', FieldManager::TEXT()->setLabel('Title')->setDefault('Free Shipping'))
            ->registerField('desc1', FieldManager::TEXT()->setLabel('Description')->setDefault('Free delivery for order above $200'))
            ->endGroup();
        $control->addGroup('feature2', 'Feature 2')
            ->registerField('icon2', FieldManager::TEXT()->setLabel('Icon Class (las la-*)')->setDefault('las la-money-bill-wave'))
            ->registerField('title2', FieldManager::TEXT()->setLabel('Title')->setDefault('Money Back'))
            ->registerField('desc2', FieldManager::TEXT()->setLabel('Description')->setDefault('100% money back guarantee'))
            ->endGroup();
        $control->addGroup('feature3', 'Feature 3')
            ->registerField('icon3', FieldManager::TEXT()->setLabel('Icon Class (las la-*)')->setDefault('las la-headset'))
            ->registerField('title3', FieldManager::TEXT()->setLabel('Title')->setDefault('24/7 Support'))
            ->registerField('desc3', FieldManager::TEXT()->setLabel('Description')->setDefault('Friendly customer support'))
            ->endGroup();
        $control->addGroup('feature4', 'Feature 4')
            ->registerField('icon4', FieldManager::TEXT()->setLabel('Icon Class (las la-*)')->setDefault('las la-undo'))
            ->registerField('title4', FieldManager::TEXT()->setLabel('Title')->setDefault('Easy Returns'))
            ->registerField('desc4', FieldManager::TEXT()->setLabel('Description')->setDefault('Return within 30 days'))
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
            return '<div style="padding:40px;text-align:center;background:#f5f5f5;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">BookPoint: Service Features — preview on the live page</p></div>';
        }

        $f1      = $settings['general']['feature1'] ?? [];
        $f2      = $settings['general']['feature2'] ?? [];
        $f3      = $settings['general']['feature3'] ?? [];
        $f4      = $settings['general']['feature4'] ?? [];
        $spacing = $settings['style']['spacing']    ?? [];

        return view('theme-bookpoint::widgets.service_features', [
            'icon1'  => $f1['icon1']  ?? 'las la-shipping-fast',
            'title1' => $f1['title1'] ?? 'Free Shipping',
            'desc1'  => $f1['desc1']  ?? 'Free delivery for order above $200',
            'icon2'  => $f2['icon2']  ?? 'las la-money-bill-wave',
            'title2' => $f2['title2'] ?? 'Money Back',
            'desc2'  => $f2['desc2']  ?? '100% money back guarantee',
            'icon3'  => $f3['icon3']  ?? 'las la-headset',
            'title3' => $f3['title3'] ?? '24/7 Support',
            'desc3'  => $f3['desc3']  ?? 'Friendly customer support',
            'icon4'  => $f4['icon4']  ?? 'las la-undo',
            'title4' => $f4['title4'] ?? 'Easy Returns',
            'desc4'  => $f4['desc4']  ?? 'Return within 30 days',
            'padding_top'    => (int) ($spacing['padding_top']    ?? 60),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 60),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'bookpoint';
    }
}
