<?php

namespace Themes\Bakerco\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class HeroSection extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'bakerco_hero_section';
    }

    protected function getWidgetName(): string
    {
        return 'BakerCo: Hero Section';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-home';
    }

    protected function getWidgetDescription(): string
    {
        return __('Full-width hero banner with image and CTA buttons');
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['hero', 'banner', 'bakerco'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('eyebrow', FieldManager::TEXT()->setLabel('Eyebrow Text')->setDefault('Artisan Bakery Since 1998'))
            ->registerField('title', FieldManager::TEXTAREA()->setLabel('Title (HTML allowed)')->setDefault('Every Bite Tells<br>a <em>Sweet Story</em>'))
            ->registerField('subtitle', FieldManager::TEXTAREA()->setLabel('Subtitle')->setDefault('Handcrafted breads, pastries, and cakes baked fresh every morning with the finest ingredients.'))
            ->registerField('button_text', FieldManager::TEXT()->setLabel('Primary Button Text')->setDefault('Shop Now'))
            ->registerField('button_url', FieldManager::URL()->setLabel('Primary Button URL')->setDefault('#'))
            ->registerField('button2_text', FieldManager::TEXT()->setLabel('Secondary Button Text')->setDefault('Our Story'))
            ->registerField('button2_url', FieldManager::URL()->setLabel('Secondary Button URL')->setDefault('#'))
            ->endGroup();

        $control->addGroup('media', 'Media')
            ->registerField('hero_image', FieldManager::IMAGE()->setLabel('Hero Image'))
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top', FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(0)->setMin(0)->setMax(200))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(0)->setMin(0)->setMax(200))
            ->endGroup();

        return $control->getFields();
    }

    public function render(array $settings = []): string
    {

        // Not in tenant context (editor preview API has no tenant middleware)
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;">BakerCo: Hero Section — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];
        $media = $settings['general']['media'] ?? [];
        $spacing = $settings['style']['spacing'] ?? [];

        $hero_img = null;
        $imageField = $media['hero_image'] ?? null;
        if (is_array($imageField)) {
            $hero_img = $imageField['img_url'] ?? $imageField['url'] ?? null;
        } elseif (!empty($imageField)) {
            $d = get_attachment_image_by_id($imageField);
            $hero_img = $d['img_url'] ?? null;
        }

        // Fallback to theme dummy image if seeding failed or media is missing
        if (empty($hero_img)) {
            $hero_img = global_asset('core/' . theme_assets('images/artisan-bread-hero.jpg', 'bakerco'));
        }

        $rawUrl1 = $content['button_url'] ?? '#';
        $rawUrl2 = $content['button2_url'] ?? '#';

        return view('theme-bakerco::widgets.hero_section', [
            'eyebrow' => $content['eyebrow'] ?? 'Artisan Bakery Since 1998',
            'title' => $content['title'] ?? 'Every Bite Tells<br>a <em>Sweet Story</em>',
            'subtitle' => (!empty($content['subtitle'])) ? $content['subtitle'] : 'Handcrafted breads, pastries, and cakes baked fresh every morning with the finest ingredients.',
            'button_text' => $content['button_text'] ?? 'Shop Now',
            'button_url' => is_array($rawUrl1) ? ($rawUrl1['url'] ?? '#') : $rawUrl1,
            'button2_text' => $content['button2_text'] ?? 'Our Story',
            'button2_url' => is_array($rawUrl2) ? ($rawUrl2['url'] ?? '#') : $rawUrl2,
            'hero_image' => $hero_img,
            'padding_top' => (int) ($spacing['padding_top'] ?? 0),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 0),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'bakerco';
    }
}
