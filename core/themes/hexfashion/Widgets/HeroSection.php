<?php

namespace Themes\Hexfashion\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class HeroSection extends BaseWidget
{
    protected function getWidgetType(): string { return 'hexfashion_hero_section'; }
    protected function getWidgetName(): string { return 'HexFashion: Hero Section'; }
    protected function getWidgetIcon(): string|array { return 'las la-tshirt'; }
    protected function getWidgetDescription(): string { return __('Hero section matching the screenshot with large outline text and circle background'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['hero', 'banner', 'hexfashion']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXTAREA()->setLabel('Title (HTML allowed)')->setDefault('SUMMER <span class="text-orange">NEW</span><br>COLLECTION'))
            ->registerField('description', FieldManager::TEXTAREA()->setLabel('Description')->setDefault("Here's a voice that keeps on calling me. Down the road, that's where I'll always be."))
            ->registerField('button_text', FieldManager::TEXT()->setLabel('Button Text')->setDefault('Shop Now'))
            ->registerField('button_url', FieldManager::URL()->setLabel('Button URL')->setDefault('#'))
            ->registerField('bg_text', FieldManager::TEXT()->setLabel('Background Outline Text')->setDefault('JACKET'))
            ->endGroup();
        $control->addGroup('media', 'Media')
            ->registerField('hero_image', FieldManager::IMAGE()->setLabel('Hero Model Image'))
            ->endGroup();
        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top', FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(100)->setMin(0)->setMax(200))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(100)->setMin(0)->setMax(200))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#fff5ef;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">HexFashion: Hero Section — preview on the live page</p></div>';
        }

        $content = $settings['general']['content'] ?? [];
        $media   = $settings['general']['media']   ?? [];
        $spacing = $settings['style']['spacing']   ?? [];

        $hero_img   = null;
        $imageField = $media['hero_image'] ?? null;
        if (is_array($imageField)) {
            $hero_img = $imageField['img_url'] ?? $imageField['url'] ?? null;
            if (empty($hero_img) && !empty($imageField['id'])) {
                $d = get_attachment_image_by_id((int) $imageField['id']);
                $hero_img = $d['img_url'] ?? null;
            }
        } elseif (is_string($imageField) && !empty($imageField)) {
            if (filter_var($imageField, FILTER_VALIDATE_URL) || str_starts_with($imageField, '/')) {
                $hero_img = $imageField;
            } else {
                $d = get_attachment_image_by_id((int) $imageField);
                $hero_img = $d['img_url'] ?? null;
            }
        }

        $rawUrl   = $content['button_url'] ?? '#';

        return view('theme-hexfashion::widgets.hero_section', [
            'title'          => $content['title']        ?? 'SUMMER <span class="text-orange">NEW</span><br>COLLECTION',
            'description'    => $content['description']  ?? "Here's a voice that keeps on calling me. Down the road, that's where I'll always be.",
            'button_text'    => $content['button_text']  ?? 'Shop Now',
            'button_url'     => is_array($rawUrl) ? ($rawUrl['url'] ?? '#') : $rawUrl,
            'bg_text'        => $content['bg_text']      ?? 'JACKET',
            'hero_img'       => $hero_img ?: global_asset('core/' . theme_assets('images/hero-banner.jpg', 'hexfashion')),
            'padding_top'    => (int) ($spacing['padding_top']    ?? 100),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 100),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'hexfashion';
    }
}
