<?php

namespace Themes\Techzone\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class PromoBanner extends BaseWidget
{
    protected function getWidgetType(): string { return 'techzone_promo_banner'; }
    protected function getWidgetName(): string { return 'TechZone: Promo Banner'; }
    protected function getWidgetIcon(): string|array { return 'las la-bullhorn'; }
    protected function getWidgetDescription(): string { return __('Full-width blue promo banner with CTA and device image'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['promo', 'banner', 'cta', 'techzone']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Title')
                ->setDefault('Trade In & Save Big'))
            ->registerField('description', FieldManager::TEXTAREA()
                ->setLabel('Description')
                ->setDefault('Trade in your old device and save up to $400 on your next purchase. Instant valuation, collection from your door, and payment within 48 hours.'))
            ->registerField('button_text', FieldManager::TEXT()
                ->setLabel('Button Text')
                ->setDefault('Get Trade-In Value'))
            ->registerField('button_url', FieldManager::URL()
                ->setLabel('Button URL')
                ->setDefault('#'))
            ->registerField('button_icon', FieldManager::TEXT()
                ->setLabel('Button Icon Class (MDI)')
                ->setDefault('mdi mdi-swap-horizontal'))
            ->endGroup();

        $control->addGroup('media', 'Media')
            ->registerField('promo_image', FieldManager::IMAGE()
                ->setLabel('Right Side Image (optional)'))
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
                ->setDefault(['top' => 60, 'right' => 0, 'bottom' => 60, 'left' => 0])
                ->setSelectors([
                    '{{WRAPPER}} .tz-promo' => 'padding-top: {{VALUE.TOP}}{{UNIT}}; padding-bottom: {{VALUE.BOTTOM}}{{UNIT}};',
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
                        <p style="margin:0;font-size:14px;">TechZone: Promo Banner — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];
        $media   = $settings['general']['media']   ?? [];

        $btnUrl = $content['button_url'] ?? '#';
        if (is_array($btnUrl)) $btnUrl = $btnUrl['url'] ?? '#';

        $promoImgUrl = null;
        if (!empty($media['promo_image'])) {
            $raw = $media['promo_image'];
            $id  = is_array($raw) ? ($raw['id'] ?? null) : $raw;
            if ($id) {
                $d = get_attachment_image_by_id($id);
                $promoImgUrl = is_array($raw) ? ($raw['url'] ?? $d['img_url'] ?? null) : ($d['img_url'] ?? null);
            }
        }
        if (empty($promoImgUrl)) {
            $promoImgUrl = global_asset('core/' . theme_assets('images/promo-banner.jpg', 'techzone'));
        }

        return view('theme-techzone::widgets.promo_banner', [
            'title'       => $content['title']       ?? 'Trade In & Save Big',
            'description' => $content['description'] ?? 'Trade in your old device and save up to $400 on your next purchase. Instant valuation, collection from your door, and payment within 48 hours.',
            'button_text' => $content['button_text'] ?? 'Get Trade-In Value',
            'button_url'  => $btnUrl,
            'button_icon' => $content['button_icon'] ?? 'mdi mdi-swap-horizontal',
            'promo_image' => $promoImgUrl,
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'techzone';
    }
}
