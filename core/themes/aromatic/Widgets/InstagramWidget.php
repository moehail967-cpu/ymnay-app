<?php

namespace Themes\Aromatic\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class InstagramWidget extends BaseWidget
{
    protected function getWidgetType(): string  { return 'aromatic_instagram_widget'; }
    protected function getWidgetName(): string  { return 'Aromatic: Instagram'; }
    protected function getWidgetIcon(): string|array { return 'lab la-instagram'; }
    protected function getWidgetDescription(): string { return __('Instagram follow CTA section'); }
    protected function getCategory(): string    { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array   { return ['instagram', 'social', 'aromatic']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('title',        FieldManager::TEXT()->setLabel('Title')->setDefault('Follow Us on Instagram'))
            ->registerField('instagram_url', FieldManager::URL()->setLabel('Instagram URL')->setDefault('#'))
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top',    FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(50)->setMin(0)->setMax(200))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(50)->setMin(0)->setMax(200))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;color:#888;">Aromatic: Instagram — preview on the live page</div>';
        }

        $content = $settings['general']['content'] ?? [];
        $spacing = $settings['style']['spacing']   ?? [];

        $rawUrl = $content['instagram_url'] ?? '#';

        return view('theme-aromatic::widgets.instagram_widget', [
            'title'          => $content['title'] ?? 'Follow Us on Instagram',
            'instagram_url'  => is_array($rawUrl) ? ($rawUrl['url'] ?? '#') : $rawUrl,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 50),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 50),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'aromatic';
    }
}
