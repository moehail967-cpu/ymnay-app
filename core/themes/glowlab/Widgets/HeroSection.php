<?php

namespace Themes\Glowlab\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class HeroSection extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'glowlab_hero_section';
    }

    protected function getWidgetName(): string
    {
        return 'Glowlab: Hero Section';
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
        return ['hero', 'banner', 'glowlab'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('badge_text', FieldManager::TEXT()->setLabel('Badge Text')->setDefault('Clinically Tested Formulas'))
            ->registerField('title', FieldManager::TEXTAREA()->setLabel('Title (HTML allowed)')->setDefault('Skin That<br><span>Glows</span> with<br>Confidence'))
            ->registerField('subtitle', FieldManager::TEXTAREA()->setLabel('Subtitle')->setDefault('Precision skincare rooted in dermatology. Every formula crafted with active ingredients your skin actually needs.'))
            ->registerField('button_text', FieldManager::TEXT()->setLabel('Primary Button Text')->setDefault('Shop Bestsellers'))
            ->registerField('button_url', FieldManager::URL()->setLabel('Primary Button URL')->setDefault('#'))
            ->registerField('button2_text', FieldManager::TEXT()->setLabel('Secondary Button Text')->setDefault('Skin Quiz'))
            ->registerField('button2_url', FieldManager::URL()->setLabel('Secondary Button URL')->setDefault('#'))
            ->registerField('trust1', FieldManager::TEXT()->setLabel('Trust Badge 1')->setDefault('🧪 Dermatologist Approved'))
            ->registerField('trust2', FieldManager::TEXT()->setLabel('Trust Badge 2')->setDefault('🌿 Cruelty Free'))
            ->registerField('trust3', FieldManager::TEXT()->setLabel('Trust Badge 3')->setDefault('✅ Fragrance Free'))
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
                    '{{WRAPPER}} .gl-widget-hero' => 'padding-top: {{VALUE.TOP}}{{UNIT}}; padding-bottom: {{VALUE.BOTTOM}}{{UNIT}};',
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
                        <p style="margin:0;font-size:14px;">Glowlab: Hero Section — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];
        $media   = $settings['general']['media']   ?? [];

        $hero_img = null;
        $heroVal  = $media['hero_image'] ?? null;
        if (is_array($heroVal)) {
            $hero_img = $heroVal['url'] ?? $heroVal['img_url'] ?? null;
            if (empty($hero_img) && !empty($heroVal['id'])) {
                $d = get_attachment_image_by_id((int) $heroVal['id']);
                $hero_img = $d['img_url'] ?? null;
            }
        } elseif (!empty($heroVal)) {
            $d = get_attachment_image_by_id($heroVal);
            $hero_img = $d['img_url'] ?? null;
        }
        if (empty($hero_img)) {
            $hero_img = global_asset('core/' . theme_assets('images/skincare-hero.jpg', 'glowlab'));
        }

        $btnUrl  = $content['button_url']  ?? '#';
        $btn2Url = $content['button2_url'] ?? '#';
        if (is_array($btnUrl))  $btnUrl  = $btnUrl['url']  ?? '#';
        if (is_array($btn2Url)) $btn2Url = $btn2Url['url'] ?? '#';

        return view('theme-glowlab::widgets.hero_section', [
            'badge_text'   => $content['badge_text']   ?? 'Clinically Tested Formulas',
            'title'        => $content['title']        ?? 'Skin That<br><span>Glows</span> with<br>Confidence',
            'subtitle'     => $content['subtitle']     ?? 'Precision skincare rooted in dermatology. Every formula crafted with active ingredients your skin actually needs.',
            'button_text'  => $content['button_text']  ?? 'Shop Bestsellers',
            'button_url'   => $btnUrl,
            'button2_text' => $content['button2_text'] ?? 'Skin Quiz',
            'button2_url'  => $btn2Url,
            'hero_image'   => $hero_img,
            'trust1'       => $content['trust1'] ?? '🧪 Dermatologist Approved',
            'trust2'       => $content['trust2'] ?? '🌿 Cruelty Free',
            'trust3'       => $content['trust3'] ?? '✅ Fragrance Free',
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'glowlab';
    }
}
