<?php

namespace Themes\Drivekit\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class Newsletter extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'drivekit_newsletter';
    }

    protected function getWidgetName(): string
    {
        return 'DriveKit: Newsletter';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-envelope';
    }

    protected function getWidgetDescription(): string
    {
        return __('Dark newsletter subscription section with DriveKit styling');
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['newsletter', 'subscribe', 'drivekit'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('tag', FieldManager::TEXT()->setLabel('Tag / Eyebrow')->setDefault('Workshop Bulletin'))
            ->registerField('title', FieldManager::TEXT()->setLabel('Title (HTML allowed)')->setDefault('Tools, Tips & <span>Trade Deals</span>'))
            ->registerField('subtitle', FieldManager::TEXT()->setLabel('Subtitle')->setDefault('Weekly deals on parts, workshop guides, and trade member exclusives.'))
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
                ->setDefault(['top' => 100, 'right' => 0, 'bottom' => 100, 'left' => 0])
                ->setSelectors([
                    '{{WRAPPER}} .dk-widget-newsletter' => 'padding-top: {{VALUE.TOP}}{{UNIT}}; padding-bottom: {{VALUE.BOTTOM}}{{UNIT}};',
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
                        <p style="margin:0;font-size:14px;">DriveKit: Newsletter — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];

        return view('theme-drivekit::widgets.newsletter', [
            'tag'         => $content['tag']         ?? 'Workshop Bulletin',
            'title'       => $content['title']       ?? 'Tools, Tips & <span>Trade Deals</span>',
            'subtitle'    => $content['subtitle']    ?? 'Weekly deals on parts, workshop guides, and trade member exclusives.',
            'button_text' => $content['button_text'] ?? 'Subscribe',
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'drivekit';
    }
}
