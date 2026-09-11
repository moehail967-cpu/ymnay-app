<?php

namespace Themes\Medicom\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class PromoBanners extends BaseWidget
{
    protected function getWidgetType(): string { return 'medicom_promo_banners'; }
    protected function getWidgetName(): string { return 'Medicom: Promo Banners'; }
    protected function getWidgetIcon(): string|array { return 'las la-ad'; }
    protected function getWidgetDescription(): string { return __('Two side-by-side promo cards with eyebrow, headline, product image, and CTA'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['promo', 'banner', 'medicom']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('card_1', 'Promo Card 1')
            ->registerField('eyebrow', FieldManager::TEXT()->setLabel('Eyebrow Text')->setDefault('Winter Products'))
            ->registerField('title_accent', FieldManager::TEXT()->setLabel('Title (Accent Color)')->setDefault('Coming'))
            ->registerField('title_dark', FieldManager::TEXT()->setLabel('Title (Dark Color)')->setDefault('Soon'))
            ->registerField('link_text', FieldManager::TEXT()->setLabel('Button Text')->setDefault('Order Now'))
            ->registerField('link_url', FieldManager::URL()->setLabel('Button URL')->setDefault('#'))
            ->registerField('image', FieldManager::IMAGE()->setLabel('Product Image'))
            ->endGroup();

        $control->addGroup('card_2', 'Promo Card 2')
            ->registerField('eyebrow', FieldManager::TEXT()->setLabel('Eyebrow Text')->setDefault('Summer Sale'))
            ->registerField('title_accent', FieldManager::TEXT()->setLabel('Title (Accent Color)')->setDefault('Buy 1'))
            ->registerField('title_dark', FieldManager::TEXT()->setLabel('Title (Dark Color)')->setDefault('Get 1 free'))
            ->registerField('link_text', FieldManager::TEXT()->setLabel('Button Text')->setDefault('Order Now'))
            ->registerField('link_url', FieldManager::URL()->setLabel('Button URL')->setDefault('#'))
            ->registerField('image', FieldManager::IMAGE()->setLabel('Product Image'))
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

    private function resolveImage(mixed $field, string $fallbackAsset = ''): ?string
    {
        if (empty($field)) return $fallbackAsset ?: null;

        if (is_array($field)) {
            $url = $field['img_url'] ?? $field['url'] ?? null;
            if (!empty($url)) return $url;
        } elseif (!empty($field)) {
            $d   = get_attachment_image_by_id((int) $field);
            $url = $d['img_url'] ?? null;
            if (!empty($url)) return $url;
        }

        // Fallback to bundled theme image when import stored null URLs
        return $fallbackAsset ?: null;
    }

    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#EAF2F8;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">Medicom: Promo Banners — preview on the live page</p></div>';
        }

        $g       = $settings['general'] ?? [];
        $spacing = $settings['style']['spacing'] ?? [];

        $defaults = [
            ['Winter Products', 'Coming', 'Soon', 'Order Now'],
            ['Summer Sale', 'Buy 1', 'Get 1 free', 'Order Now'],
        ];

        $fallbacks = [
            global_asset('core/' . theme_assets('images/promo-banner1.jpg', 'medicom')),
            global_asset('core/' . theme_assets('images/promo-banner2.jpg', 'medicom')),
        ];

        $cards = [];
        foreach (['card_1', 'card_2'] as $i => $key) {
            $c = $g[$key] ?? [];
            $rawUrl = $c['link_url'] ?? '#';
            $cards[] = [
                'eyebrow'       => $c['eyebrow']       ?? $defaults[$i][0],
                'title_accent'  => $c['title_accent']  ?? $defaults[$i][1],
                'title_dark'    => $c['title_dark']    ?? $defaults[$i][2],
                'link_text'     => $c['link_text']     ?? $defaults[$i][3],
                'link_url'      => is_array($rawUrl) ? ($rawUrl['url'] ?? '#') : $rawUrl,
                'image'         => $this->resolveImage($c['image'] ?? null, $fallbacks[$i]),
            ];
        }

        return view('theme-medicom::widgets.promo_banners', [
            'cards'          => $cards,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 60),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 60),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'medicom';
    }
}
