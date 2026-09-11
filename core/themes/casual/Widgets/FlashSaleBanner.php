<?php

namespace Themes\Casual\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class FlashSaleBanner extends BaseWidget
{
    protected function getWidgetType(): string { return 'casual_flash_sale_banner'; }
    protected function getWidgetName(): string { return 'Casual: Flash Sale Banner'; }
    protected function getWidgetIcon(): string|array { return 'las la-bolt'; }
    protected function getWidgetDescription(): string { return __('Full-width flash sale card with wavy decoration and discount badge'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['flash', 'sale', 'banner', 'casual']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()->setLabel('Title')->setDefault('Flash Sale'))
            ->registerField('subtitle', FieldManager::TEXT()->setLabel('Subtitle')->setDefault('Limited time offer — grab your favourites now!'))
            ->registerField('countdown_end', FieldManager::TEXT()->setLabel('Countdown End Date & Time (YYYY-MM-DD HH:MM:SS)')->setDefault(''))
            ->registerField('link_text', FieldManager::TEXT()->setLabel('Link Text')->setDefault('Flash Store'))
            ->registerField('link_url', FieldManager::URL()->setLabel('Link URL')->setDefault('#'))
            ->registerField('discount_text', FieldManager::TEXT()->setLabel('Discount Badge Text (e.g. 10% OFF)')->setDefault('10% OFF'))
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
            return '<div style="padding:40px;text-align:center;background:#fdf5f0;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">Casual: Flash Sale Banner — preview on the live page</p></div>';
        }

        $content = $settings['general']['content'] ?? [];
        $media   = $settings['general']['media']   ?? [];
        $spacing = $settings['style']['spacing']   ?? [];

        $banner_url = null;
        $imgField   = $media['banner_image'] ?? null;
        if (is_array($imgField)) {
            $banner_url = $imgField['img_url'] ?? $imgField['url'] ?? null;
            if (empty($banner_url) && !empty($imgField['id'])) {
                $d = get_attachment_image_by_id((int) $imgField['id']);
                $banner_url = $d['img_url'] ?? null;
            }
        } elseif (is_string($imgField) && !empty($imgField)) {
            // Direct URL string stored by FieldTypes/ImageField
            if (filter_var($imgField, FILTER_VALIDATE_URL) || str_starts_with($imgField, '/')) {
                $banner_url = $imgField;
            } else {
                // Numeric ID stored as string
                $d = get_attachment_image_by_id((int) $imgField);
                $banner_url = $d['img_url'] ?? null;
            }
        }

        $rawUrl = $content['link_url'] ?? '#';

        return view('theme-casual::widgets.flash_sale_banner', [
            'title'          => $content['title']         ?? 'Flash Sale',
            'subtitle'       => $content['subtitle']      ?? 'Limited time offer!',
            'countdown_end'  => trim($content['countdown_end'] ?? ''),
            'link_text'      => $content['link_text']     ?? 'Flash Store',
            'link_url'       => is_array($rawUrl) ? ($rawUrl['url'] ?? '#') : $rawUrl,
            'discount_text'  => $content['discount_text'] ?? '10% OFF',
            'banner_url'     => $banner_url,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 60),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 60),
            'countdown_id'   => 'csflash_' . substr(md5(uniqid()), 0, 8),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'casual';
    }
}
