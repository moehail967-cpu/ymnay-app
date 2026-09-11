<?php

namespace Themes\Tinynest\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class HeroSection extends BaseWidget
{
    protected function getWidgetType(): string { return 'tinynest_hero_section'; }
    protected function getWidgetName(): string { return 'TinyNest: Hero Section'; }
    protected function getWidgetIcon(): string|array { return 'las la-baby-carriage'; }
    protected function getWidgetDescription(): string { return __('Full-width hero banner with badge, headline, subtitle and dual CTA buttons'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['hero', 'banner', 'tinynest']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('badge_text', FieldManager::TEXT()->setLabel('Badge Text')->setDefault('Trusted by 50,000+ Parents'))
            ->registerField('title', FieldManager::TEXT()->setLabel('Title')->setDefault('Everything Your Little One Needs, All in One Place'))
            ->registerField('title_accent', FieldManager::TEXT()->setLabel('Title Accent Word')->setDefault('Needs'))
            ->registerField('subtitle', FieldManager::TEXTAREA()->setLabel('Subtitle')->setDefault('Curated baby and toddler essentials from brands you can trust. Every product tested for safety, comfort, and quality.'))
            ->registerField('button_text', FieldManager::TEXT()->setLabel('Primary Button Text')->setDefault('Shop Baby'))
            ->registerField('button_url', FieldManager::URL()->setLabel('Primary Button URL')->setDefault('#'))
            ->registerField('button2_text', FieldManager::TEXT()->setLabel('Secondary Button Text')->setDefault('Gift Registry'))
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
            ->registerField('padding_top', FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(60)->setMin(0)->setMax(200))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(60)->setMin(0)->setMax(200))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        $content = $settings['general']['content'] ?? [];
        $media   = $settings['general']['media']   ?? [];
        $spacing = $settings['style']['spacing']   ?? [];

        // Resolve hero image safely (works in both tenant and editor contexts)
        $hero_image = null;
        if (!empty($media['hero_image'])) {
            $raw = $media['hero_image'];
            $id  = is_array($raw) ? ($raw['id'] ?? null) : $raw;
            if ($id && function_exists('get_attachment_image_by_id')) {
                try {
                    $d = get_attachment_image_by_id($id);
                    $hero_image = is_array($raw)
                        ? ($raw['url'] ?? $d['img_url'] ?? null)
                        : ($d['img_url'] ?? null);
                } catch (\Throwable $e) {
                    $hero_image = is_array($raw) ? ($raw['url'] ?? null) : null;
                }
            } elseif (is_array($raw) && !empty($raw['url'])) {
                $hero_image = $raw['url'];
            }
        }

        if (empty($hero_image)) {
            $hero_image = global_asset('core/' . theme_assets('images/tinynest-hero.jpg', 'tinynest'));
        }

        // URL fields from FieldManager::URL() are stored as arrays {url, target, rel}
        // when saved — unwrap safely so the blade receives a plain string
        $rawUrl1 = $content['button_url']  ?? '#';
        $rawUrl2 = $content['button2_url'] ?? '#';

        return view('theme-tinynest::widgets.hero_section', [
            'badge_text'     => $content['badge_text']   ?? 'Trusted by 50,000+ Parents',
            'title'          => $content['title']         ?? 'Everything Your Little One Needs, All in One Place',
            'title_accent'   => $content['title_accent']  ?? 'Needs',
            'subtitle'       => $content['subtitle']      ?? 'Curated baby and toddler essentials from brands you can trust. Every product tested for safety, comfort, and quality.',
            'button_text'    => $content['button_text']   ?? 'Shop Baby',
            'button_url'     => is_array($rawUrl1) ? ($rawUrl1['url'] ?? '#') : ($rawUrl1 ?: '#'),
            'button2_text'   => $content['button2_text']  ?? 'Gift Registry',
            'button2_url'    => is_array($rawUrl2) ? ($rawUrl2['url'] ?? '#') : ($rawUrl2 ?: '#'),
            'hero_image'     => $hero_image,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 60),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 60),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'tinynest';
    }
}
