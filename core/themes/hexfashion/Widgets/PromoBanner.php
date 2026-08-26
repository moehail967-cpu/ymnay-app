<?php

namespace Themes\Hexfashion\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class PromoBanner extends BaseWidget
{
    protected function getWidgetType(): string { return 'hexfashion_promo_banner'; }
    protected function getWidgetName(): string { return 'HexFashion: Promo Banner'; }
    protected function getWidgetIcon(): string|array { return 'las la-ad'; }
    protected function getWidgetDescription(): string { return __('Two images flanking a central text block'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['promo', 'banner', 'hexfashion']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('subtitle', FieldManager::TEXT()->setLabel('Subtitle')->setDefault('Summer Sale'))
            ->registerField('title', FieldManager::TEXTAREA()->setLabel('Title')->setDefault('Exclusive Dress Collection'))
            ->registerField('button_text', FieldManager::TEXT()->setLabel('Button Text')->setDefault('Shop Now'))
            ->registerField('button_url', FieldManager::URL()->setLabel('Button URL')->setDefault('#'))
            ->registerField('left_image', FieldManager::IMAGE()->setLabel('Left Image'))
            ->registerField('right_image', FieldManager::IMAGE()->setLabel('Right Image'))
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
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;"><p style="margin:0;">HexFashion: Promo Banner — preview on the live page</p></div>';
        }

        $content = $settings['general']['content'] ?? [];
        $spacing = $settings['style']['spacing']   ?? [];

        $left_img = $this->getImageUrl($content['left_image'] ?? null);
        $right_img = $this->getImageUrl($content['right_image'] ?? null);

        $rawUrl = $content['button_url'] ?? '#';

        return view('theme-hexfashion::widgets.promo_banner', [
            'subtitle'       => $content['subtitle']    ?? 'Summer Sale',
            'title'          => $content['title']       ?? 'Exclusive Dress Collection',
            'button_text'    => $content['button_text'] ?? 'Shop Now',
            'button_url'     => is_array($rawUrl) ? ($rawUrl['url'] ?? '#') : $rawUrl,
            'left_img'       => $left_img ?: global_asset('core/' . theme_assets('images/promo-banner1.jpg', 'hexfashion')),
            'right_img'      => $right_img ?: global_asset('core/' . theme_assets('images/promo-banner2.jpg', 'hexfashion')),
            'padding_top'    => (int) ($spacing['padding_top']    ?? 60),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 60),
        ])->render();
    }

    private function getImageUrl($imageField): ?string
    {
        if (is_array($imageField)) {
            $img = $imageField['img_url'] ?? $imageField['url'] ?? null;
            if (empty($img) && !empty($imageField['id'])) {
                $d = get_attachment_image_by_id((int) $imageField['id']);
                return $d['img_url'] ?? null;
            }
            return $img;
        } elseif (is_string($imageField) && !empty($imageField)) {
            if (filter_var($imageField, FILTER_VALIDATE_URL) || str_starts_with($imageField, '/')) {
                return $imageField;
            }
            $d = get_attachment_image_by_id((int) $imageField);
            return $d['img_url'] ?? null;
        }
        return null;
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'hexfashion';
    }
}
