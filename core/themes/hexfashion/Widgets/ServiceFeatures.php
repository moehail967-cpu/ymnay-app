<?php

namespace Themes\Hexfashion\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class ServiceFeatures extends BaseWidget
{
    protected function getWidgetType(): string { return 'hexfashion_service_features'; }
    protected function getWidgetName(): string { return 'HexFashion: Service Features'; }
    protected function getWidgetIcon(): string|array { return 'las la-concierge-bell'; }
    protected function getWidgetDescription(): string { return __('4-column fashion service icon highlights'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['features', 'services', 'hexfashion']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('feature_1_icon',  FieldManager::TEXT()->setLabel('Feature 1 Icon')->setDefault('las la-shipping-fast'))
            ->registerField('feature_1_title', FieldManager::TEXT()->setLabel('Feature 1 Title')->setDefault('Free Shipping'))
            ->registerField('feature_1_desc',  FieldManager::TEXT()->setLabel('Feature 1 Description')->setDefault('On orders over $50'))
            ->registerField('feature_2_icon',  FieldManager::TEXT()->setLabel('Feature 2 Icon')->setDefault('las la-undo'))
            ->registerField('feature_2_title', FieldManager::TEXT()->setLabel('Feature 2 Title')->setDefault('Easy Returns'))
            ->registerField('feature_2_desc',  FieldManager::TEXT()->setLabel('Feature 2 Description')->setDefault('30-day free returns'))
            ->registerField('feature_3_icon',  FieldManager::TEXT()->setLabel('Feature 3 Icon')->setDefault('las la-lock'))
            ->registerField('feature_3_title', FieldManager::TEXT()->setLabel('Feature 3 Title')->setDefault('Secure Payment'))
            ->registerField('feature_3_desc',  FieldManager::TEXT()->setLabel('Feature 3 Description')->setDefault('Safe & encrypted'))
            ->registerField('feature_4_icon',  FieldManager::TEXT()->setLabel('Feature 4 Icon')->setDefault('las la-headset'))
            ->registerField('feature_4_title', FieldManager::TEXT()->setLabel('Feature 4 Title')->setDefault('24/7 Support'))
            ->registerField('feature_4_desc',  FieldManager::TEXT()->setLabel('Feature 4 Description')->setDefault('Always here for you'))
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
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">HexFashion: Service Features — preview on the live page</p></div>';
        }

        $content = $settings['general']['content'] ?? [];
        $spacing = $settings['style']['spacing']   ?? [];

        $features = [
            ['icon' => $content['feature_1_icon'] ?? 'las la-shipping-fast', 'title' => $content['feature_1_title'] ?? 'Free Shipping',   'desc' => $content['feature_1_desc'] ?? 'On orders over $50'],
            ['icon' => $content['feature_2_icon'] ?? 'las la-undo',          'title' => $content['feature_2_title'] ?? 'Easy Returns',    'desc' => $content['feature_2_desc'] ?? '30-day free returns'],
            ['icon' => $content['feature_3_icon'] ?? 'las la-lock',          'title' => $content['feature_3_title'] ?? 'Secure Payment',  'desc' => $content['feature_3_desc'] ?? 'Safe & encrypted'],
            ['icon' => $content['feature_4_icon'] ?? 'las la-headset',       'title' => $content['feature_4_title'] ?? '24/7 Support',    'desc' => $content['feature_4_desc'] ?? 'Always here for you'],
        ];

        return view('theme-hexfashion::widgets.service_features', [
            'features'       => $features,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 60),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 60),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'hexfashion';
    }
}
