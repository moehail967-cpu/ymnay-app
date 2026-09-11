<?php

namespace Themes\Bookpoint\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class SinglePromoBanner extends BaseWidget
{
    protected function getWidgetType(): string { return 'bookpoint_single_promo_banner'; }
    protected function getWidgetName(): string { return 'BookPoint: Single Promo Banner'; }
    protected function getWidgetIcon(): string|array { return 'las la-percentage'; }
    protected function getWidgetDescription(): string { return __('Full-width promo card with discount highlight and book image'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['promo', 'banner', 'discount', 'bookpoint']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('discount_text', FieldManager::TEXT()->setLabel('Discount Text (e.g. 20%)')->setDefault('20%'))
            ->registerField('title', FieldManager::TEXTAREA()->setLabel('Title')->setDefault('Special discount on your favourite comics'))
            ->registerField('description', FieldManager::TEXTAREA()->setLabel('Description')->setDefault('Get 20% special discount when you order your favourite comic books from us just use "Special 20" code'))
            ->registerField('button_text', FieldManager::TEXT()->setLabel('Button Text')->setDefault('Browse Now'))
            ->registerField('button_url', FieldManager::URL()->setLabel('Button URL')->setDefault('#'))
            ->endGroup();
        $control->addGroup('media', 'Media')
            ->registerField('banner_image', FieldManager::IMAGE()->setLabel('Banner Image'))
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
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">BookPoint: Single Promo Banner — preview on the live page</p></div>';
        }

        $content = $settings['general']['content'] ?? [];
        $media   = $settings['general']['media']   ?? [];
        $spacing = $settings['style']['spacing']   ?? [];

        $banner_image_url = null;
        $imageField = $media['banner_image'] ?? null;
        if (is_array($imageField)) {
            $banner_image_url = $imageField['img_url'] ?? $imageField['url'] ?? null;
            if (empty($banner_image_url) && !empty($imageField['id'])) {
                $d = get_attachment_image_by_id((int) $imageField['id']);
                $banner_image_url = $d['img_url'] ?? null;
            }
        } elseif (!empty($imageField)) {
            $d = get_attachment_image_by_id((int) $imageField);
            $banner_image_url = $d['img_url'] ?? null;
        }
        if (empty($banner_image_url)) {
            $banner_image_url = global_asset('core/' . theme_assets('images/prod-book-1.jpg', 'bookpoint'));
        }

        $rawUrl = $content['button_url'] ?? '#';

        return view('theme-bookpoint::widgets.single_promo_banner', [
            'discount_text'    => $content['discount_text'] ?? '20%',
            'title'            => $content['title']         ?? 'Special discount on your favourite comics',
            'description'      => $content['description']   ?? '',
            'button_text'      => $content['button_text']   ?? 'Browse Now',
            'button_url'       => is_array($rawUrl) ? ($rawUrl['url'] ?? '#') : $rawUrl,
            'banner_image_url' => $banner_image_url,
            'padding_top'      => (int) ($spacing['padding_top']    ?? 60),
            'padding_bottom'   => (int) ($spacing['padding_bottom'] ?? 60),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'bookpoint';
    }
}
