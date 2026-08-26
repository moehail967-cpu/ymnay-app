<?php

namespace Themes\Luxegems\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class PromoBanner extends BaseWidget
{
    protected function getWidgetType(): string { return 'luxegems_promo_banner'; }
    protected function getWidgetName(): string { return 'LuxeGems: Promo Banner'; }
    protected function getWidgetIcon(): string|array { return 'las la-gem'; }
    protected function getWidgetDescription(): string { return __('Dark cinematic promo section with headline, text, CTA and icon — LuxeGems style'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['promo', 'banner', 'cta', 'luxegems']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXTAREA()
                ->setLabel('Title (wrap accent word in <strong> for gold)')
                ->setDefault('BESPOKE. ONE-OF-A-<strong>KIND.</strong>'))
            ->registerField('text', FieldManager::TEXTAREA()
                ->setLabel('Description')
                ->setDefault('Commission your unique piece. Our master jewellers work with you from design concept to the final creation. Every detail considered, every stone hand-selected.'))
            ->registerField('promo_image', FieldManager::IMAGE()
                ->setLabel('Right Side Image (overrides emoji when set)'))
            ->registerField('promo_icon', FieldManager::TEXT()
                ->setLabel('Right Side Icon (emoji, shown when no image)')
                ->setDefault('💎'))
            ->registerField('button_text', FieldManager::TEXT()
                ->setLabel('Primary Button Text')
                ->setDefault('BEGIN YOUR JOURNEY'))
            ->registerField('button_url', FieldManager::URL()
                ->setLabel('Primary Button URL')
                ->setDefault('#'))
            ->registerField('button2_text', FieldManager::TEXT()
                ->setLabel('Secondary Button Text')
                ->setDefault(''))
            ->registerField('button2_url', FieldManager::URL()
                ->setLabel('Secondary Button URL')
                ->setDefault('#'))
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
                    '{{WRAPPER}} .lx-widget-promo' => 'padding-top: {{VALUE.TOP}}{{UNIT}}; padding-bottom: {{VALUE.BOTTOM}}{{UNIT}};',
                ]))
            ->endGroup();

        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#111;border:1px dashed #333;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;">LuxeGems: Promo Banner — preview on the live page</p>
                    </div>';
        }
        $content    = $settings['general']['content'] ?? [];
        $btnUrl     = $content['button_url']  ?? '#';
        $btn2Url    = $content['button2_url'] ?? '#';
        if (is_array($btnUrl))  $btnUrl  = $btnUrl['url']  ?? '#';
        if (is_array($btn2Url)) $btn2Url = $btn2Url['url'] ?? '#';

        $promoImgUrl = null;
        $raw = $content['promo_image'] ?? null;
        if (!empty($raw)) {
            if (is_int($raw) || (is_string($raw) && is_numeric($raw))) {
                $d = get_attachment_image_by_id((int) $raw);
                $promoImgUrl = $d['img_url'] ?? null;
            } elseif (is_array($raw)) {
                $promoImgUrl = $raw['url'] ?? $raw['img_url'] ?? null;
                if (empty($promoImgUrl) && !empty($raw['id'])) {
                    $d = get_attachment_image_by_id((int) $raw['id']);
                    $promoImgUrl = $d['img_url'] ?? null;
                }
            }
        }
        if (empty($promoImgUrl)) {
            $promoImgUrl = global_asset('core/' . theme_assets('images/promo-banner.png', 'luxegems'));
        }

        return view('theme-luxegems::widgets.promo_banner', [
            'title'        => $content['title']       ?? 'BESPOKE. ONE-OF-A-<strong>KIND.</strong>',
            'text'         => $content['text']        ?? '',
            'promo_image'  => $promoImgUrl,
            'promo_icon'   => $content['promo_icon']  ?? '💎',
            'button_text'  => $content['button_text'] ?? 'BEGIN YOUR JOURNEY',
            'button_url'   => $btnUrl,
            'button2_text' => $content['button2_text'] ?? '',
            'button2_url'  => $btn2Url,
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'luxegems';
    }
}
