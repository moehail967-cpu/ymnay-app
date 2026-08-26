<?php

namespace Themes\Furnito\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class ServiceFeatures extends BaseWidget
{
    protected function getWidgetType(): string { return 'furnito_service_features'; }
    protected function getWidgetName(): string { return 'Furnito: Service Features'; }
    protected function getWidgetIcon(): string|array { return 'las la-concierge-bell'; }
    protected function getWidgetDescription(): string { return __('4-column icon feature highlights'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['features', 'services', 'furnito']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('feature_1_icon',  FieldManager::TEXT()->setLabel('Feature 1 Icon')->setDefault('las la-truck'))
            ->registerField('feature_1_title', FieldManager::TEXT()->setLabel('Feature 1 Title')->setDefault('Free Delivery'))
            ->registerField('feature_1_desc',  FieldManager::TEXT()->setLabel('Feature 1 Description')->setDefault('On orders over $200'))
            ->registerField('feature_2_icon',  FieldManager::TEXT()->setLabel('Feature 2 Icon')->setDefault('las la-undo'))
            ->registerField('feature_2_title', FieldManager::TEXT()->setLabel('Feature 2 Title')->setDefault('Easy Returns'))
            ->registerField('feature_2_desc',  FieldManager::TEXT()->setLabel('Feature 2 Description')->setDefault('30-day return policy'))
            ->registerField('feature_3_icon',  FieldManager::TEXT()->setLabel('Feature 3 Icon')->setDefault('las la-tools'))
            ->registerField('feature_3_title', FieldManager::TEXT()->setLabel('Feature 3 Title')->setDefault('Assembly Service'))
            ->registerField('feature_3_desc',  FieldManager::TEXT()->setLabel('Feature 3 Description')->setDefault('Professional setup'))
            ->registerField('feature_4_icon',  FieldManager::TEXT()->setLabel('Feature 4 Icon')->setDefault('las la-shield-alt'))
            ->registerField('feature_4_title', FieldManager::TEXT()->setLabel('Feature 4 Title')->setDefault('Quality Guarantee'))
            ->registerField('feature_4_desc',  FieldManager::TEXT()->setLabel('Feature 4 Description')->setDefault('5-year warranty'))
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
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">Furnito: Service Features — preview on the live page</p></div>';
        }

        $content = $settings['general']['content'] ?? [];
        $spacing = $settings['style']['spacing']   ?? [];

        $features = [
            ['icon' => $content['feature_1_icon'] ?? 'las la-truck',      'title' => $content['feature_1_title'] ?? 'Free Delivery',      'desc' => $content['feature_1_desc'] ?? 'On orders over $200'],
            ['icon' => $content['feature_2_icon'] ?? 'las la-undo',       'title' => $content['feature_2_title'] ?? 'Easy Returns',       'desc' => $content['feature_2_desc'] ?? '30-day return policy'],
            ['icon' => $content['feature_3_icon'] ?? 'las la-tools',      'title' => $content['feature_3_title'] ?? 'Assembly Service',   'desc' => $content['feature_3_desc'] ?? 'Professional setup'],
            ['icon' => $content['feature_4_icon'] ?? 'las la-shield-alt', 'title' => $content['feature_4_title'] ?? 'Quality Guarantee',  'desc' => $content['feature_4_desc'] ?? '5-year warranty'],
        ];

        return view('theme-furnito::widgets.service_features', [
            'features'       => $features,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 60),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 60),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'furnito';
    }
}
