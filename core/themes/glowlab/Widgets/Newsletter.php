<?php

namespace Themes\Glowlab\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class Newsletter extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'glowlab_newsletter';
    }

    protected function getWidgetName(): string
    {
        return 'Glowlab: Newsletter';
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
        return ['newsletter', 'subscribe', 'glowlab'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('tag_text', FieldManager::TEXT()->setLabel('Tag Label')->setDefault('Skincare Science'))
            ->registerField('title', FieldManager::TEXT()->setLabel('Title')->setDefault('Glow Tips & Exclusives'))
            ->registerField('subtitle', FieldManager::TEXTAREA()->setLabel('Subtitle')->setDefault('Subscribe for skincare tips and exclusive offers.'))
            ->registerField('button_text', FieldManager::TEXT()->setLabel('Button Text')->setDefault('Subscribe'))
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding', FieldManager::DIMENSION()
                ->asPadding()
                ->setLabel('Section Padding')
                ->setDefault(['top' => 80, 'right' => 0, 'bottom' => 80, 'left' => 0])
                ->setSelectors([
                    '{{WRAPPER}} .gl-widget-newsletter' => 'padding-top: {{VALUE.TOP}}{{UNIT}}; padding-bottom: {{VALUE.BOTTOM}}{{UNIT}};',
                ])
            )
            ->endGroup();

        return $control->getFields();
    }

    public function render(array $settings = []): string
    {

        // Not in tenant context (editor preview API has no tenant middleware)
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;">Glowlab: Newsletter — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];

        return view('theme-glowlab::widgets.newsletter', [
            'tag_text'    => $content['tag_text']    ?? 'Skincare Science',
            'title'       => $content['title']       ?? 'Glow Tips & Exclusives',
            'subtitle'    => $content['subtitle']    ?? 'Subscribe for skincare tips and exclusive offers.',
            'button_text' => $content['button_text'] ?? 'Subscribe',
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'glowlab';
    }
}
