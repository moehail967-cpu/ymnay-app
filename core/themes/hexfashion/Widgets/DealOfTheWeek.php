<?php

namespace Themes\Hexfashion\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class DealOfTheWeek extends BaseWidget
{
    protected function getWidgetType(): string { return 'hexfashion_deal_of_the_week'; }
    protected function getWidgetName(): string { return 'HexFashion: Deal of the Week'; }
    protected function getWidgetIcon(): string|array { return 'las la-bolt'; }
    protected function getWidgetDescription(): string { return __('Deal of the week banner with image and countdown'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['deal', 'sale', 'banner', 'hexfashion']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()->setLabel('Title')->setDefault('Deal of the Week'))
            ->registerField('subtitle', FieldManager::TEXT()->setLabel('Subtitle')->setDefault('Why does this particular feature stand out to you right now?'))
            ->registerField('countdown_end', FieldManager::TEXT()->setLabel('Countdown End Date & Time (YYYY-MM-DD HH:MM:SS)')->setDefault(''))
            ->registerField('link_text', FieldManager::TEXT()->setLabel('Link Text')->setDefault('Shop Now'))
            ->registerField('link_url', FieldManager::URL()->setLabel('Link URL')->setDefault('#'))
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
            return '<div style="padding:40px;text-align:center;background:#fdf5f0;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">HexFashion: Deal of the Week — preview on the live page</p></div>';
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

        return view('theme-hexfashion::widgets.deal_of_the_week', [
            'title'          => $content['title']         ?? 'Deal of the Week',
            'subtitle'       => $content['subtitle']      ?? 'Why does this particular feature stand out to you right now?',
            'countdown_end'  => trim($content['countdown_end'] ?? ''),
            'link_text'      => $content['link_text']     ?? 'Shop Now',
            'link_url'       => is_array($rawUrl) ? ($rawUrl['url'] ?? '#') : $rawUrl,
            'banner_url'     => $banner_url ?: global_asset('core/' . theme_assets('images/banner.jpg', 'hexfashion')),
            'padding_top'    => (int) ($spacing['padding_top']    ?? 60),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 60),
            'countdown_id'   => 'hfdeal_' . substr(md5(uniqid()), 0, 8),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'hexfashion';
    }
}
