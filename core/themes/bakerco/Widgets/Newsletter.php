<?php

namespace Themes\Bakerco\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class Newsletter extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'bakerco_newsletter';
    }

    protected function getWidgetName(): string
    {
        return 'BakerCo: Newsletter';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-envelope';
    }

    protected function getWidgetDescription(): string
    {
        return __('Email newsletter subscription section');
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['newsletter', 'subscribe', 'bakerco'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()->setLabel('Title')->setDefault('Fresh Bakes in Your Inbox'))
            ->registerField('subtitle', FieldManager::TEXTAREA()->setLabel('Subtitle')->setDefault('Subscribe and get early access to seasonal specials and bakery news.'))
            ->registerField('button_text', FieldManager::TEXT()->setLabel('Button Text')->setDefault('Subscribe'))
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

        // Not in tenant context (editor preview API has no tenant middleware)
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;">BakerCo: Newsletter — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];
        $spacing = $settings['style']['spacing'] ?? [];

        return view('theme-bakerco::widgets.newsletter', [
            'title' => $content['title'] ?? 'Fresh Bakes in Your Inbox',
            'subtitle' => $content['subtitle'] ?? 'Subscribe and get early access to seasonal specials and bakery news.',
            'button_text' => $content['button_text'] ?? 'Subscribe',
            'padding_top' => (int) ($spacing['padding_top'] ?? 60),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 60),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'bakerco';
    }
}
