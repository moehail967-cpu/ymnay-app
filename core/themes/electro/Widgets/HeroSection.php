<?php

namespace Themes\Electro\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class HeroSection extends BaseWidget
{
    protected function getWidgetType(): string { return 'electro_hero_section'; }
    protected function getWidgetName(): string { return 'Electro: Hero Section'; }
    protected function getWidgetIcon(): string|array { return 'las la-laptop'; }
    protected function getWidgetDescription(): string { return __('Split hero with floating product circles on left and headline on right'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['hero', 'banner', 'electro']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('eyebrow', FieldManager::TEXT()->setLabel('Eyebrow Text')->setDefault('Best Of 2023'))
            ->registerField('title', FieldManager::TEXTAREA()->setLabel('Title')->setDefault('Your Tech Experience'))
            ->registerField('description', FieldManager::TEXTAREA()->setLabel('Description')->setDefault('Every Tech Enthusiast\'s Haven: Unveiling PCs, Laptops, Headphones, RAM, Motherboards, and Electronics Gadgets Galore!'))
            ->registerField('button_text', FieldManager::TEXT()->setLabel('Button Text')->setDefault('Buy Now'))
            ->registerField('button_url', FieldManager::URL()->setLabel('Button URL')->setDefault('#'))
            ->registerField('product_count', FieldManager::NUMBER()->setLabel('Floating Products to Show (shown when no image set)')->setDefault(5)->setMin(1)->setMax(8))
            ->endGroup();
        $control->addGroup('media', 'Media')
            ->registerField('hero_image', FieldManager::IMAGE()->setLabel('Hero Image (replaces floating product circles)'))
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
            return '<div style="padding:40px;text-align:center;background:#fff5f0;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">Electro: Hero Section — preview on the live page</p></div>';
        }

        $content = $settings['general']['content'] ?? [];
        $media   = $settings['general']['media']   ?? [];
        $spacing = $settings['style']['spacing']   ?? [];
        $count   = (int) ($content['product_count'] ?? 5);
        $rawUrl  = $content['button_url'] ?? '#';

        // Resolve hero image — read stored URL first, fall back to DB lookup, then static asset.
        $hero_img   = null;
        $imageField = $media['hero_image'] ?? null;
        if (is_array($imageField)) {
            $hero_img = $imageField['img_url'] ?? $imageField['url'] ?? null;
            if (empty($hero_img) && !empty($imageField['id'])) {
                $d = get_attachment_image_by_id((int) $imageField['id']);
                $hero_img = $d['img_url'] ?? null;
            }
        } elseif (is_numeric($imageField) && $imageField > 0) {
            $d = get_attachment_image_by_id((int) $imageField);
            $hero_img = $d['img_url'] ?? null;
        } elseif (is_string($imageField) && filter_var($imageField, FILTER_VALIDATE_URL)) {
            $hero_img = $imageField;
        }
        if (empty($hero_img)) {
            $hero_img = global_asset('core/' . theme_assets('images/hero-banner.jpg', 'electro'));
        }

        $products = $hero_img ? collect() : (function_exists('theme_featured_products') ? theme_featured_products($count) : collect());

        return view('theme-electro::widgets.hero_section', [
            'eyebrow'        => $content['eyebrow']      ?? 'Best Of 2023',
            'title'          => $content['title']        ?? 'Your Tech Experience',
            'description'    => $content['description']  ?? '',
            'button_text'    => $content['button_text']  ?? 'Buy Now',
            'button_url'     => is_array($rawUrl) ? ($rawUrl['url'] ?? '#') : $rawUrl,
            'hero_img'       => $hero_img,
            'products'       => $products,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 0),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 0),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'electro';
    }
}
