<?php

namespace Themes\Maison\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class HeroSection extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'maison_hero_section';
    }

    protected function getWidgetName(): string
    {
        return 'Maison: Hero Section';
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
        return ['hero', 'banner', 'maison'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('badge_text', FieldManager::TEXT()->setLabel('Badge Text')->setDefault('Curated for Your Home'))
            ->registerField('title', FieldManager::TEXTAREA()->setLabel('Title (HTML allowed)')->setDefault('Live<br>Beautifully'))
            ->registerField('subtitle', FieldManager::TEXTAREA()->setLabel('Subtitle')->setDefault('Thoughtfully designed home goods for everyday living and special moments.'))
            ->registerField('button_text', FieldManager::TEXT()->setLabel('Primary Button Text')->setDefault('Shop Collection'))
            ->registerField('button_url', FieldManager::URL()->setLabel('Primary Button URL')->setDefault('#'))
            ->registerField('button2_text', FieldManager::TEXT()->setLabel('Secondary Button Text')->setDefault('Lookbook'))
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
            ->registerField('padding', FieldManager::DIMENSION()
                ->asPadding()
                ->setLabel('Section Padding')
                ->setDefault(['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0])
                ->setSelectors([
                    '{{WRAPPER}} .ms-widget-hero' => 'padding-top: {{VALUE.TOP}}{{UNIT}}; padding-bottom: {{VALUE.BOTTOM}}{{UNIT}};',
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
                        <p style="margin:0;font-size:14px;">Maison: Hero Section — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];
        $media   = $settings['general']['media']   ?? [];

        $hero_img = null;
        $raw = $media['hero_image'] ?? null;
        if (!empty($raw)) {
            $id = is_array($raw) ? ($raw['id'] ?? null) : $raw;
            if ($id) {
                $d = get_attachment_image_by_id($id);
                $hero_img = $d['img_url'] ?? null;
            }
        }
        if (empty($hero_img)) {
            $hero_img = global_asset('core/' . theme_assets('images/maison-hero.jpg', 'maison'));
        }

        return view('theme-maison::widgets.hero_section', [
            'badge_text'  => $content['badge_text']   ?? 'Curated for Your Home',
            'title'       => $content['title']        ?? 'Style Your Space with Timeless Elegance',
            'subtitle'    => $content['subtitle']     ?? 'Thoughtfully designed home goods for everyday living and special moments.',
            'button_text' => $content['button_text']  ?? 'Shop Collection',
            'button_url'  => (function($r){ return is_array($r) ? ($r['url'] ?? '#') : ($r ?: '#'); })($content['button_url'] ?? []),
            'button2_text'=> $content['button2_text'] ?? 'Lookbook',
            'button2_url' => (function($r){ return is_array($r) ? ($r['url'] ?? '#') : ($r ?: '#'); })($content['button2_url'] ?? []),
            'hero_image'  => $hero_img,
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'maison';
    }
}
