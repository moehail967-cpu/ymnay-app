<?php

namespace Plugins\WidgetBuilder\Widgets\Tenant;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class Newsletter extends BaseWidget
{
    protected function getWidgetType(): string { return 'tenant_newsletter'; }
    protected function getWidgetName(): string { return 'Newsletter'; }
    protected function getWidgetIcon(): string|array { return 'las la-envelope'; }
    protected function getWidgetDescription(): string { return __('Email newsletter subscription section'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['newsletter', 'email', 'subscribe']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Title')
                ->setDefault('Subscribe to Our Newsletter'))
            ->registerField('subtitle', FieldManager::TEXT()
                ->setLabel('Subtitle')
                ->setDefault('Get the latest updates on new products and upcoming sales'))
            ->registerField('placeholder', FieldManager::TEXT()
                ->setLabel('Input Placeholder')
                ->setDefault('Enter your email address'))
            ->registerField('button_text', FieldManager::TEXT()
                ->setLabel('Button Text')
                ->setDefault('Subscribe'))
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('style', 'Style')
            ->registerField('background_color', FieldManager::COLOR()->setLabel('Background Color')->setDefault(''))
            ->registerField('text_color', FieldManager::COLOR()->setLabel('Text Color')->setDefault('#ffffff'))
            ->endGroup();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top', FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(80)->setMin(0)->setMax(300))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(80)->setMin(0)->setMax(300))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        $general = $settings['general'] ?? [];
        $style   = $settings['style'] ?? [];

        $content = $general['content'] ?? [];
        $styling = $style['style'] ?? [];
        $spacing = $style['spacing'] ?? [];

        return view('widgetbuilder::tenant.newsletter', [
            'title'          => $content['title'] ?? 'Subscribe to Our Newsletter',
            'subtitle'       => $content['subtitle'] ?? '',
            'placeholder'    => $content['placeholder'] ?? 'Enter your email address',
            'button_text'    => $content['button_text'] ?? 'Subscribe',
            'bg_color'       => $styling['background_color'] ?? '',
            'text_color'     => $styling['text_color'] ?? '#ffffff',
            'padding_top'    => (int) ($spacing['padding_top'] ?? 80),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 80),
        ])->render();
    }

    public function enable(): bool { return !is_null(tenant()); }
}
