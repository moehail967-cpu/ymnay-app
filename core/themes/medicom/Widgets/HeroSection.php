<?php

namespace Themes\Medicom\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class HeroSection extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'medicom_hero_section';
    }
    protected function getWidgetName(): string
    {
        return 'Medicom: Hero Section';
    }
    protected function getWidgetIcon(): string|array
    {
        return 'las la-heartbeat';
    }
    protected function getWidgetDescription(): string
    {
        return __('Medical Equipment Hero section with visual banner');
    }
    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }
    protected function getWidgetTags(): array
    {
        return ['hero', 'banner', 'medicom', 'medical'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('eyebrow', FieldManager::TEXT()->setLabel('Eyebrow Text')->setDefault('Your Health, Our Priority'))
            ->registerField('title_black_1', FieldManager::TEXT()->setLabel('Title (First part)')->setDefault('Medical'))
            ->registerField('title_blue', FieldManager::TEXT()->setLabel('Title (Blue part)')->setDefault('Equipment'))
            ->registerField('title_black_2', FieldManager::TEXT()->setLabel('Title (Third part)')->setDefault('Store'))
            ->registerField('description', FieldManager::TEXTAREA()->setLabel('Description')->setDefault('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Feugiat pulvinar enim hendrerit pellentesque Pharetra consectetur quam.'))
            ->registerField('button_text', FieldManager::TEXT()->setLabel('Button Text')->setDefault('Shop Now'))
            ->registerField('button_url', FieldManager::URL()->setLabel('Button URL')->setDefault('#'))
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
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#EAF2F8;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">Medicom: Hero Section — preview on the live page</p></div>';
        }

        $content = $settings['general']['content'] ?? [];
        $media = $settings['general']['media'] ?? [];
        $spacing = $settings['style']['spacing'] ?? [];
        $rawUrl = $content['button_url'] ?? '#';

        $hero_img    = null;
        $imageField  = $media['hero_image'] ?? null;
        if (is_array($imageField)) {
            $hero_img = $imageField['img_url'] ?? $imageField['url'] ?? null;
        } elseif (!empty($imageField)) {
            $d        = get_attachment_image_by_id((int) $imageField);
            $hero_img = $d['img_url'] ?? null;
        }

        // Fallback to bundled theme image when no image is set or import stored null URLs
        if (empty($hero_img)) {
            $hero_img = global_asset('core/' . theme_assets('images/hero-banner.jpg', 'medicom'));
        }

        return view('theme-medicom::widgets.hero_section', [
            'eyebrow'        => $content['eyebrow']       ?? 'Your Health, Our Priority',
            'title_black_1'  => $content['title_black_1'] ?? 'Medical',
            'title_blue'     => $content['title_blue']    ?? 'Equipment',
            'title_black_2'  => $content['title_black_2'] ?? 'Store',
            'description'    => $content['description']   ?? 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Feugiat pulvinar enim hendrerit pellentesque Pharetra consectetur quam.',
            'button_text'    => $content['button_text']   ?? 'Shop Now',
            'button_url'     => is_array($rawUrl) ? ($rawUrl['url'] ?? '#') : $rawUrl,
            'hero_img'       => $hero_img,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 0),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 0),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'medicom';
    }
}
