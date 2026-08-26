<?php

namespace Plugins\WidgetBuilder\Widgets\Tenant\Contact;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class GoogleMapEmbed extends BaseWidget
{
    protected function getWidgetType(): string { return 'tenant_google_map_embed'; }
    protected function getWidgetName(): string { return 'Google Map'; }
    protected function getWidgetIcon(): string|array { return 'las la-map-marked-alt'; }
    protected function getWidgetDescription(): string { return __('Embedded Google Maps iframe by address / location string'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['map', 'google', 'location', 'contact']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('location',   FieldManager::TEXT()->setLabel('Location / Address')->setDefault('New York, USA'))
            ->registerField('map_height', FieldManager::NUMBER()->setLabel('Map Height (px)')->setDefault(450)->setMin(200)->setMax(800))
            ->endGroup();
        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top',    FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(0)->setMin(0)->setMax(200))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(0)->setMin(0)->setMax(200))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">Google Map — preview on the live page</p></div>';
        }

        $content = $settings['general']['content'] ?? [];
        $spacing = $settings['style']['spacing']   ?? [];

        return view('widgetbuilder::tenant.contact.google-map-embed', [
            'location'       => $content['location']   ?? 'New York, USA',
            'map_height'     => (int) ($content['map_height']        ?? 450),
            'padding_top'    => (int) ($spacing['padding_top']    ?? 0),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 0),
        ])->render();
    }

    public function enable(): bool { return !is_null(tenant()); }
}
