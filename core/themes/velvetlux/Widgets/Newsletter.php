<?php

namespace Themes\Velvetlux\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class Newsletter extends BaseWidget
{
    protected function getWidgetType(): string { return 'velvetlux_newsletter'; }
    protected function getWidgetName(): string { return 'VelvetLux: Newsletter'; }
    protected function getWidgetIcon(): string|array { return 'las la-envelope-open'; }
    protected function getWidgetDescription(): string { return __('Email newsletter subscription section with champagne accent'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['newsletter', 'subscribe', 'velvetlux']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Title')
                ->setDefault('Join the Inner Circle'))
            ->registerField('subtitle', FieldManager::TEXT()
                ->setLabel('Subtitle')
                ->setDefault('Exclusive access to new collections, private sales, and curated style notes.'))
            ->registerField('button_text', FieldManager::TEXT()
                ->setLabel('Button Text')
                ->setDefault('Subscribe'))
            ->endGroup();
        return $control->getFields();
    }

    public function getStyleFields(): array { return []; }

    public function render(array $settings = []): string
    {

        // Not in tenant context (editor preview API has no tenant middleware)
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;">VelvetLux: Newsletter — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];
        return view('theme-velvetlux::widgets.newsletter', [
            'title'       => $content['title']       ?? 'Join the Inner Circle',
            'subtitle'    => $content['subtitle']    ?? 'Exclusive access to new collections, private sales, and curated style notes.',
            'button_text' => $content['button_text'] ?? 'Subscribe',
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'velvetlux';
    }
}
