<?php

namespace Themes\Techzone\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class HeroSection extends BaseWidget
{
    protected function getWidgetType(): string { return 'techzone_hero_section'; }
    protected function getWidgetName(): string { return 'TechZone: Hero Section'; }
    protected function getWidgetIcon(): string|array { return 'las la-home'; }
    protected function getWidgetDescription(): string { return __('Dark hero banner for electronics store'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['hero', 'banner', 'techzone']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('eyebrow', FieldManager::TEXT()
                ->setLabel('Eyebrow Tag')
                ->setDefault('New Arrivals 2025'))
            ->registerField('title', FieldManager::TEXTAREA()
                ->setLabel('Title (HTML allowed)')
                ->setDefault('The Future of Tech<br>Starts Here'))
            ->registerField('subtitle', FieldManager::TEXTAREA()
                ->setLabel('Subtitle')
                ->setDefault('Discover cutting-edge electronics, gadgets and accessories at unbeatable prices.'))
            ->registerField('button_text', FieldManager::TEXT()
                ->setLabel('Primary Button Text')
                ->setDefault('Shop Now'))
            ->registerField('button_url', FieldManager::URL()
                ->setLabel('Primary Button URL')
                ->setDefault('#'))
            ->registerField('button2_text', FieldManager::TEXT()
                ->setLabel('Secondary Button Text')
                ->setDefault('View Deals'))
            ->registerField('button2_url', FieldManager::URL()
                ->setLabel('Secondary Button URL')
                ->setDefault('#'))
            ->endGroup();

        $control->addGroup('media', 'Media')
            ->registerField('hero_image', FieldManager::IMAGE()
                ->setLabel('Background Image'))
            ->registerField('product_image', FieldManager::IMAGE()
                ->setLabel('Product / Device Image'))
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
                ->setDefault(['top' => 90, 'right' => 0, 'bottom' => 90, 'left' => 0])
                ->setSelectors([
                    '{{WRAPPER}} .tz-hero-widget' => 'padding-top: {{VALUE.TOP}}{{UNIT}}; padding-bottom: {{VALUE.BOTTOM}}{{UNIT}};',
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
                        <p style="margin:0;font-size:14px;">TechZone: Hero Section — preview on the live page</p>
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
            $hero_img_url = global_asset('core/' . theme_assets('images/techzone-hero.jpg', 'techzone'));
        }
        if (empty($product_img_url)) {
            $product_img_url = global_asset('core/' . theme_assets('images/smartphone.jpg', 'techzone'));
        }

        $btnUrl  = $content['button_url']  ?? '#';
        if (is_array($btnUrl))  $btnUrl  = $btnUrl['url']  ?? '#';
        $btn2Url = $content['button2_url'] ?? '#';
        if (is_array($btn2Url)) $btn2Url = $btn2Url['url'] ?? '#';

        return view('theme-techzone::widgets.hero_section', [
            'eyebrow'       => $content['eyebrow']      ?? 'New Arrivals 2025',
            'title'         => $content['title']        ?? 'The Future of Tech<br>Starts Here',
            'subtitle'      => $content['subtitle']     ?? 'Cutting-edge electronics, gadgets, and accessories from the world\'s leading brands. Next-day delivery, expert support, unbeatable prices.',
            'button_text'   => $content['button_text']  ?? 'Shop Now',
            'button_url'    => $btnUrl,
            'button2_text'  => $content['button2_text'] ?? 'View Deals',
            'button2_url'   => $btn2Url,
            'hero_image'    => $hero_img_url,
            'product_image' => $product_img_url,
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'techzone';
    }
}
