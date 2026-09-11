<?php

namespace Themes\Casual\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class HeroSection extends BaseWidget
{
    protected function getWidgetType(): string { return 'casual_hero_section'; }
    protected function getWidgetName(): string { return 'Casual: Hero Section'; }
    protected function getWidgetIcon(): string|array { return 'las la-tshirt'; }
    protected function getWidgetDescription(): string { return __('Split hero with eyebrow, title, browse button and mini product cards'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['hero', 'banner', 'casual']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('eyebrow', FieldManager::TEXT()->setLabel('Eyebrow Text')->setDefault('Womens Dress Store'))
            ->registerField('title', FieldManager::TEXTAREA()->setLabel('Title (HTML allowed)')->setDefault('Summer Collection'))
            ->registerField('button_text', FieldManager::TEXT()->setLabel('Button Text')->setDefault('Browse All Categories'))
            ->registerField('button_url', FieldManager::URL()->setLabel('Button URL')->setDefault('#'))
            ->registerField('product_count', FieldManager::NUMBER()->setLabel('Mini Products to Show')->setDefault(2)->setMin(1)->setMax(4))
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
            ->registerField('padding_top', FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(0)->setMin(0)->setMax(200))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(0)->setMin(0)->setMax(200))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#fdf5f0;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">Casual: Hero Section — preview on the live page</p></div>';
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

        $count    = (int) ($content['product_count'] ?? 2);
        $products = function_exists('theme_featured_products') ? theme_featured_products($count) : collect();
        $rawUrl   = $content['button_url'] ?? '#';

        return view('theme-casual::widgets.hero_section', [
            'eyebrow'        => $content['eyebrow']      ?? 'Womens Dress Store',
            'title'          => $content['title']        ?? 'Summer Collection',
            'button_text'    => $content['button_text']  ?? 'Browse All Categories',
            'button_url'     => is_array($rawUrl) ? ($rawUrl['url'] ?? '#') : $rawUrl,
            'hero_img'       => $hero_img,
            'products'       => $products,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 0),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 0),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'casual';
    }
}
