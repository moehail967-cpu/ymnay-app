<?php

namespace Themes\Electro\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class NewReleaseBanner extends BaseWidget
{
    protected function getWidgetType(): string { return 'electro_new_release_banner'; }
    protected function getWidgetName(): string { return 'Electro: New Release Banner'; }
    protected function getWidgetIcon(): string|array { return 'las la-certificate'; }
    protected function getWidgetDescription(): string { return __('Full-width split banner: product image with badge on left, headline + price + CTA on right'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['release', 'banner', 'promo', 'electro']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('badge_text', FieldManager::TEXT()->setLabel('Circle Badge Text')->setDefault('PC Gadgets'))
            ->registerField('title_accent', FieldManager::TEXT()->setLabel('Title Line 1 (Accent)')->setDefault('New'))
            ->registerField('title_dark', FieldManager::TEXT()->setLabel('Title Line 2 (Dark)')->setDefault('Release'))
            ->registerField('price', FieldManager::TEXT()->setLabel('Price Text')->setDefault('$499.00'))
            ->registerField('button_text', FieldManager::TEXT()->setLabel('Button Text')->setDefault('Show Now'))
            ->registerField('button_url', FieldManager::URL()->setLabel('Button URL')->setDefault('#'))
            ->endGroup();
        $control->addGroup('media', 'Media')
            ->registerField('product_image', FieldManager::IMAGE()->setLabel('Product Image'))
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
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#fff5f0;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">Electro: New Release Banner — preview on the live page</p></div>';
        }

        $content = $settings['general']['content'] ?? [];
        $media   = $settings['general']['media']   ?? [];
        $spacing = $settings['style']['spacing']   ?? [];

        $imgField = $media['product_image'] ?? null;
        $img_url  = null;
        if (is_array($imgField)) {
            $img_url = $imgField['img_url'] ?? $imgField['url'] ?? null;
            if (empty($img_url) && !empty($imgField['id'])) {
                $d = get_attachment_image_by_id((int) $imgField['id']);
                $img_url = $d['img_url'] ?? null;
            }
        } elseif (!empty($imgField)) {
            $d = get_attachment_image_by_id((int) $imgField);
            $img_url = $d['img_url'] ?? null;
        }
        if (empty($img_url)) {
            $img_url = global_asset('core/' . theme_assets('images/new_release_banner.jpg', 'electro'));
        }

        $rawUrl = $content['button_url'] ?? '#';

        return view('theme-electro::widgets.new_release_banner', [
            'badge_text'     => $content['badge_text']    ?? 'PC Gadgets',
            'title_accent'   => $content['title_accent']  ?? 'New',
            'title_dark'     => $content['title_dark']    ?? 'Release',
            'price'          => $content['price']         ?? '$499.00',
            'button_text'    => $content['button_text']   ?? 'Show Now',
            'button_url'     => is_array($rawUrl) ? ($rawUrl['url'] ?? '#') : $rawUrl,
            'img_url'        => $img_url,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 60),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 60),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'electro';
    }
}
