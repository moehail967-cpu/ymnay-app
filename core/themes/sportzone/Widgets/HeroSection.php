<?php

namespace Themes\Sportzone\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class HeroSection extends BaseWidget
{
    protected function getWidgetType(): string { return 'sportzone_hero_section'; }
    protected function getWidgetName(): string { return 'SportZone: Hero Section'; }
    protected function getWidgetIcon(): string|array { return 'la-home'; }
    protected function getWidgetDescription(): string { return __('Full-width dark navy hero banner with red accents, product image and CTA buttons'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['hero', 'banner', 'sportzone']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('badge_text', FieldManager::TEXT()
                ->setLabel('Sport Tag / Badge Text')
                ->setDefault('New Season'))
            ->registerField('title', FieldManager::TEXTAREA()
                ->setLabel('Title (HTML allowed)')
                ->setDefault('Gear Up for<br>Victory'))
            ->registerField('title_accent', FieldManager::TEXT()
                ->setLabel('Accent Word (highlighted in red)')
                ->setDefault(''))
            ->registerField('subtitle', FieldManager::TEXTAREA()
                ->setLabel('Subtitle')
                ->setDefault('Professional-grade sports equipment trusted by athletes worldwide.'))
            ->registerField('button_text', FieldManager::TEXT()
                ->setLabel('Primary Button Text')
                ->setDefault('Shop Now'))
            ->registerField('button_url', FieldManager::URL()
                ->setLabel('Primary Button URL')
                ->setDefault('#'))
            ->registerField('button2_text', FieldManager::TEXT()
                ->setLabel('Secondary Button Text')
                ->setDefault('View Catalogue'))
            ->registerField('button2_url', FieldManager::URL()
                ->setLabel('Secondary Button URL')
                ->setDefault('#'))
            ->endGroup();

        $control->addGroup('media', 'Media')
            ->registerField('hero_image', FieldManager::IMAGE()
                ->setLabel('Background Image (optional)'))
            ->registerField('product_image', FieldManager::IMAGE()
                ->setLabel('Product / Athlete Image (right side)'))
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
                ->setDefault(['top' => 72, 'right' => 0, 'bottom' => 72, 'left' => 0])
                ->setSelectors([
                    '{{WRAPPER}} .sz-hero-widget' => 'padding-top: {{VALUE.TOP}}{{UNIT}}; padding-bottom: {{VALUE.BOTTOM}}{{UNIT}};',
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
                        <p style="margin:0;font-size:14px;">SportZone: Hero Section — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];
        $media   = $settings['general']['media']   ?? [];

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
            $hero_img_url = global_asset('core/' . theme_assets('images/sportzone-hero.jpg', 'sportzone'));
        }
        if (empty($product_img_url)) {
            $product_img_url = global_asset('core/' . theme_assets('images/running-shoes.jpg', 'sportzone'));
        }

        $btnUrl  = $content['button_url']  ?? '#';
        $btn2Url = $content['button2_url'] ?? '#';
        if (is_array($btnUrl))  $btnUrl  = $btnUrl['url']  ?? '#';
        if (is_array($btn2Url)) $btn2Url = $btn2Url['url'] ?? '#';

        return view('theme-sportzone::widgets.hero_section', [
            'badge_text'    => $content['badge_text']   ?? 'New Season',
            'title'         => $content['title']        ?? 'Gear Up for<br>Victory',
            'title_accent'  => $content['title_accent'] ?? '',
            'subtitle'      => $content['subtitle']     ?? 'Professional-grade sports equipment trusted by athletes worldwide.',
            'button_text'   => $content['button_text']  ?? 'Shop Now',
            'button_url'    => $btnUrl,
            'button2_text'  => $content['button2_text'] ?? 'View Catalogue',
            'button2_url'   => $btn2Url,
            'hero_image'    => $hero_img_url,
            'product_image' => $product_img_url,
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'sportzone';
    }
}
