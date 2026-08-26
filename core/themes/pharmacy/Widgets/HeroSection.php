<?php

namespace Themes\Pharmacy\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class HeroSection extends BaseWidget
{
    protected function getWidgetType(): string { return 'pharmacy_hero_section'; }
    protected function getWidgetName(): string { return 'Pharmacy: Hero Section'; }
    protected function getWidgetIcon(): string|array { return 'las la-heartbeat'; }
    protected function getWidgetDescription(): string { return __('Full-width hero banner with teal gradient, product image and CTA buttons'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['hero', 'banner', 'pharmacy']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('eyebrow', FieldManager::TEXT()
                ->setLabel('Eyebrow / Tag Text')
                ->setDefault('Your Trusted Health Partner'))
            ->registerField('title', FieldManager::TEXTAREA()
                ->setLabel('Title (HTML allowed)')
                ->setDefault('Quality Medicines<br>Delivered to Your Door'))
            ->registerField('subtitle', FieldManager::TEXTAREA()
                ->setLabel('Subtitle')
                ->setDefault('Browse thousands of trusted health products — delivered fast, safely, and affordably.'))
            ->registerField('button_text', FieldManager::TEXT()
                ->setLabel('Primary Button Text')
                ->setDefault('Shop Now'))
            ->registerField('button_url', FieldManager::URL()
                ->setLabel('Primary Button URL')
                ->setDefault('#'))
            ->registerField('button2_text', FieldManager::TEXT()
                ->setLabel('Secondary Button Text')
                ->setDefault('View Categories'))
            ->registerField('button2_url', FieldManager::URL()
                ->setLabel('Secondary Button URL')
                ->setDefault('#'))
            ->endGroup();

        $control->addGroup('media', 'Media')
            ->registerField('hero_image', FieldManager::IMAGE()
                ->setLabel('Background Image (optional)'))
            ->registerField('product_image', FieldManager::IMAGE()
                ->setLabel('Product / Illustration Image (right side)'))
            ->endGroup();

        $control->addGroup('trust', 'Trust Bar Items')
            ->registerField('trust_1_icon',  FieldManager::TEXT()->setLabel('Item 1 — Icon class')->setDefault('las la-certificate'))
            ->registerField('trust_1_title', FieldManager::TEXT()->setLabel('Item 1 — Title')->setDefault('GPhC Registered'))
            ->registerField('trust_1_sub',   FieldManager::TEXT()->setLabel('Item 1 — Subtitle')->setDefault('Reg. No. 9011482'))
            ->registerField('trust_2_icon',  FieldManager::TEXT()->setLabel('Item 2 — Icon class')->setDefault('las la-user-md'))
            ->registerField('trust_2_title', FieldManager::TEXT()->setLabel('Item 2 — Title')->setDefault('Qualified Pharmacists'))
            ->registerField('trust_2_sub',   FieldManager::TEXT()->setLabel('Item 2 — Subtitle')->setDefault('Available 7 days/week'))
            ->registerField('trust_3_icon',  FieldManager::TEXT()->setLabel('Item 3 — Icon class')->setDefault('las la-snowflake'))
            ->registerField('trust_3_title', FieldManager::TEXT()->setLabel('Item 3 — Title')->setDefault('Cold Chain Storage'))
            ->registerField('trust_3_sub',   FieldManager::TEXT()->setLabel('Item 3 — Subtitle')->setDefault('Temperature controlled'))
            ->registerField('trust_4_icon',  FieldManager::TEXT()->setLabel('Item 4 — Icon class')->setDefault('las la-lock'))
            ->registerField('trust_4_title', FieldManager::TEXT()->setLabel('Item 4 — Title')->setDefault('Secure Checkout'))
            ->registerField('trust_4_sub',   FieldManager::TEXT()->setLabel('Item 4 — Subtitle')->setDefault('256-bit SSL encryption'))
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

    public function render(array $settings = []): string
    {

        // Not in tenant context (editor preview API has no tenant middleware)
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;">Pharmacy: Hero Section — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];
        $media   = $settings['general']['media']   ?? [];
        $trust   = $settings['general']['trust']   ?? [];
        $spacing = $settings['style']['spacing']   ?? [];

        $hero_img_url    = null;
        $product_img_url = null;

        if (!empty($media['hero_image'])) {
            $raw = $media['hero_image'];
            $id  = is_array($raw) ? ($raw['id'] ?? null) : $raw;
            if ($id) {
                $d = get_attachment_image_by_id($id);
                $hero_img_url = is_array($raw) ? ($raw['url'] ?? $d['img_url'] ?? null) : ($d['img_url'] ?? null);
            }
        }
        if (!empty($media['product_image'])) {
            $raw = $media['product_image'];
            $id  = is_array($raw) ? ($raw['id'] ?? null) : $raw;
            if ($id) {
                $d = get_attachment_image_by_id($id);
                $product_img_url = is_array($raw) ? ($raw['url'] ?? $d['img_url'] ?? null) : ($d['img_url'] ?? null);
            }
        }
        if (empty($hero_img_url)) {
            $hero_img_url = global_asset('core/' . theme_assets('images/pharmacy-hero.jpg', 'pharmacy'));
        }
        if (empty($product_img_url)) {
            $product_img_url = global_asset('core/' . theme_assets('images/pills-tablets.jpg', 'pharmacy'));
        }
        $btnUrl  = $content['button_url']  ?? '#';
        $btn2Url = $content['button2_url'] ?? '#';
        if (is_array($btnUrl))  $btnUrl  = $btnUrl['url']  ?? '#';
        if (is_array($btn2Url)) $btn2Url = $btn2Url['url'] ?? '#';

        $trustItems = [
            ['icon' => $trust['trust_1_icon'] ?? 'las la-certificate', 'title' => $trust['trust_1_title'] ?? 'GPhC Registered',     'sub' => $trust['trust_1_sub'] ?? 'Reg. No. 9011482'],
            ['icon' => $trust['trust_2_icon'] ?? 'las la-user-md',     'title' => $trust['trust_2_title'] ?? 'Qualified Pharmacists','sub' => $trust['trust_2_sub'] ?? 'Available 7 days/week'],
            ['icon' => $trust['trust_3_icon'] ?? 'las la-snowflake',   'title' => $trust['trust_3_title'] ?? 'Cold Chain Storage',   'sub' => $trust['trust_3_sub'] ?? 'Temperature controlled'],
            ['icon' => $trust['trust_4_icon'] ?? 'las la-lock',        'title' => $trust['trust_4_title'] ?? 'Secure Checkout',      'sub' => $trust['trust_4_sub'] ?? '256-bit SSL encryption'],
        ];

        return view('theme-pharmacy::widgets.hero_section', [
            'eyebrow'        => $content['eyebrow']     ?? 'Your Trusted Health Partner',
            'title'          => $content['title']        ?? 'Quality Medicines<br>Delivered to Your Door',
            'subtitle'       => $content['subtitle']     ?? 'Browse thousands of trusted health products — delivered fast, safely, and affordably.',
            'button_text'    => $content['button_text']  ?? 'Shop Now',
            'button_url'     => $btnUrl,
            'button2_text'   => $content['button2_text'] ?? 'View Categories',
            'button2_url'    => $btn2Url,
            'hero_image'     => $hero_img_url,
            'product_image'  => $product_img_url,
            'trust_items'    => $trustItems,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 60),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 60),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'pharmacy';
    }
}
