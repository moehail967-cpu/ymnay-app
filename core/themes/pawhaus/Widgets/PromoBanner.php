<?php

namespace Themes\Pawhaus\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class PromoBanner extends BaseWidget
{
    protected function getWidgetType(): string { return 'pawhaus_promo_banner'; }
    protected function getWidgetName(): string { return 'PawHaus: Promo Banner'; }
    protected function getWidgetIcon(): string|array { return 'las la-bullhorn'; }
    protected function getWidgetDescription(): string { return __('Terracotta subscription or sale CTA banner'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['promo', 'banner', 'cta', 'pawhaus']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXTAREA()
                ->setLabel('Title')
                ->setDefault("Subscribe & Save\n15% Every Month"))
            ->registerField('text', FieldManager::TEXTAREA()
                ->setLabel('Description')
                ->setDefault('Never run out of pet food again. Subscribe to your favourites and save 15% on every auto-delivery. Cancel anytime — no hassle.'))
            ->registerField('button_text', FieldManager::TEXT()
                ->setLabel('Button Text')
                ->setDefault('Set Up Subscription'))
            ->registerField('button_url', FieldManager::URL()
                ->setLabel('Button URL')
                ->setDefault('#'))
            ->registerField('promo_emoji', FieldManager::TEXT()
                ->setLabel('Right Side Emoji (when no image)')
                ->setDefault('🦮 🐈 🐾'))
            ->registerField('promo_image', FieldManager::IMAGE()
                ->setLabel('Right Side Image (overrides emoji)'))
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
                ->setDefault(['top' => 64, 'right' => 0, 'bottom' => 64, 'left' => 0])
                ->setSelectors([
                    '{{WRAPPER}} .ph-promo-widget' => 'padding-top: {{VALUE.TOP}}{{UNIT}}; padding-bottom: {{VALUE.BOTTOM}}{{UNIT}};',
                ])
            )
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;">PawHaus: Promo Banner — preview on the live page</p>
                    </div>';
        }

        $content = $settings['general']['content'] ?? [];

        $raw_url    = $content['button_url'] ?? '#';
        $button_url = is_array($raw_url) ? ($raw_url['url'] ?? '#') : ($raw_url ?: '#');

        $promo_img_url = null;
        $raw = $content['promo_image'] ?? null;
        if (!empty($raw)) {
            if (is_int($raw) || (is_string($raw) && is_numeric($raw))) {
                $d = get_attachment_image_by_id((int) $raw);
                $promo_img_url = $d['img_url'] ?? null;
            } elseif (is_array($raw)) {
                $promo_img_url = $raw['url'] ?? $raw['img_url'] ?? null;
                if (empty($promo_img_url) && !empty($raw['id'])) {
                    $d = get_attachment_image_by_id((int) $raw['id']);
                    $promo_img_url = $d['img_url'] ?? null;
                }
            }
        }
        if (empty($promo_img_url)) {
            $promo_img_url = global_asset('core/' . theme_assets('images/promo-banner.png', 'pawhaus'));
        }

        return view('theme-pawhaus::widgets.promo_banner', [
            'title'       => $content['title']       ?? "Subscribe & Save\n15% Every Month",
            'text'        => $content['text']         ?? 'Never run out of pet food again. Subscribe to your favourites and save 15% on every auto-delivery.',
            'button_text' => $content['button_text']  ?? 'Set Up Subscription',
            'button_url'  => $button_url,
            'promo_emoji' => $content['promo_emoji']  ?? '🦮 🐈 🐾',
            'promo_image' => $promo_img_url,
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'pawhaus';
    }
}
