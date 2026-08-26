<?php

namespace Themes\Furnito\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class Newsletter extends BaseWidget
{
    protected function getWidgetType(): string { return 'furnito_newsletter'; }
    protected function getWidgetName(): string { return 'Furnito: Newsletter'; }
    protected function getWidgetIcon(): string|array { return 'las la-envelope'; }
    protected function getWidgetDescription(): string { return __('Newsletter subscription section with email form'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['newsletter', 'email', 'furnito']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('title',        FieldManager::TEXT()->setLabel('Title')->setDefault('Get Inspired'))
            ->registerField('subtitle',     FieldManager::TEXT()->setLabel('Subtitle')->setDefault('Subscribe for design tips & exclusive offers'))
            ->registerField('placeholder',  FieldManager::TEXT()->setLabel('Input Placeholder')->setDefault('Enter your email'))
            ->registerField('button_text',  FieldManager::TEXT()->setLabel('Button Text')->setDefault('Subscribe'))
            ->endGroup();
        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top', FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(80)->setMin(0)->setMax(200))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(80)->setMin(0)->setMax(200))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#2c2c2c;border:1px dashed #555;border-radius:8px;color:#aaa;font-family:sans-serif;"><p style="margin:0;font-size:14px;">Furnito: Newsletter — preview on the live page</p></div>';
        }

        $content = $settings['general']['content'] ?? [];
        $spacing = $settings['style']['spacing']   ?? [];

        return view('theme-furnito::widgets.newsletter', [
            'title'          => $content['title']       ?? 'Get Inspired',
            'subtitle'       => $content['subtitle']    ?? 'Subscribe for design tips & exclusive offers',
            'placeholder'    => $content['placeholder'] ?? 'Enter your email',
            'button_text'    => $content['button_text'] ?? 'Subscribe',
            'padding_top'    => (int) ($spacing['padding_top']    ?? 80),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 80),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'furnito';
    }
}
