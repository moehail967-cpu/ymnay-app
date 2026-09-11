<?php

namespace Themes\Trailco\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class Newsletter extends BaseWidget
{
    protected function getWidgetType(): string { return 'trailco_newsletter'; }
    protected function getWidgetName(): string { return 'TrailCo: Newsletter'; }
    protected function getWidgetIcon(): string|array { return 'mdi mdi-email-outline'; }
    protected function getWidgetDescription(): string { return __('Email subscription section with TrailCo olive/bark theme'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['newsletter', 'email', 'subscribe', 'trailco']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()->setLabel('Title')->setDefault('Stay on the Trail'))
            ->registerField('subtitle', FieldManager::TEXT()->setLabel('Subtitle')->setDefault('Get trail reports, exclusive deals and adventure inspiration delivered to your inbox.'))
            ->registerField('button_text', FieldManager::TEXT()->setLabel('Button Text')->setDefault('Subscribe'))
            ->endGroup();
        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top', FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(72)->setMin(0)->setMax(300))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(72)->setMin(0)->setMax(300))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {

        // Not in tenant context (editor preview API has no tenant middleware)
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;">TrailCo: Newsletter — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];
        $spacing = $settings['style']['spacing']   ?? [];

        return view('theme-trailco::widgets.newsletter', [
            'title'          => $content['title']       ?? 'Stay on the Trail',
            'subtitle'       => $content['subtitle']    ?? '',
            'button_text'    => $content['button_text'] ?? 'Subscribe',
            'padding_top'    => (int) ($spacing['padding_top']    ?? 72),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 72),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'trailco';
    }
}
