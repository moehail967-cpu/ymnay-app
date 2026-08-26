<?php

namespace Themes\Furnito\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class CategoryBanners extends BaseWidget
{
    protected function getWidgetType(): string { return 'furnito_category_banners'; }
    protected function getWidgetName(): string { return 'Furnito: Category Banners'; }
    protected function getWidgetIcon(): string|array { return 'las la-image'; }
    protected function getWidgetDescription(): string { return __('Two side-by-side promo banner cards with text and bottom-aligned product image'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['banners', 'promo', 'category', 'furnito']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('banner_1', 'Banner 1')
            ->registerField('title',    FieldManager::TEXT()->setLabel('Title')->setDefault('Furniture Collection'))
            ->registerField('subtitle', FieldManager::TEXT()->setLabel('Subtitle')->setDefault('Modern & Classic Pieces'))
            ->registerField('btn_text', FieldManager::TEXT()->setLabel('Link Text')->setDefault('Shop Now'))
            ->registerField('btn_url',  FieldManager::TEXT()->setLabel('Link URL')->setDefault('#'))
            ->registerField('bg_color', FieldManager::TEXT()->setLabel('Background Color (hex)')->setDefault('#ECEDF5'))
            ->registerField('image',    FieldManager::IMAGE()->setLabel('Banner Image'))
            ->endGroup();

        $control->addGroup('banner_2', 'Banner 2')
            ->registerField('title',    FieldManager::TEXT()->setLabel('Title')->setDefault('Plant Collection'))
            ->registerField('subtitle', FieldManager::TEXT()->setLabel('Subtitle')->setDefault('Bring Nature Indoors'))
            ->registerField('btn_text', FieldManager::TEXT()->setLabel('Link Text')->setDefault('Shop Now'))
            ->registerField('btn_url',  FieldManager::TEXT()->setLabel('Link URL')->setDefault('#'))
            ->registerField('bg_color', FieldManager::TEXT()->setLabel('Background Color (hex)')->setDefault('#F5F0E8'))
            ->registerField('image',    FieldManager::IMAGE()->setLabel('Banner Image'))
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top',    FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(40)->setMin(0)->setMax(200))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(40)->setMin(0)->setMax(200))
            ->registerField('card_height',    FieldManager::NUMBER()->setLabel('Card Height (px)')->setDefault(320)->setMin(200)->setMax(600))
            ->endGroup();
        return $control->getFields();
    }

    private function resolveImage(mixed $field): ?string
    {
        if (is_array($field)) {
            $url = $field['img_url'] ?? $field['url'] ?? null;
            if (!$url && !empty($field['id'])) {
                $d = get_attachment_image_by_id((int) $field['id']);
                $url = $d['img_url'] ?? null;
            }
            return $url;
        }
        if (!empty($field)) {
            $d = get_attachment_image_by_id((int) $field);
            return $d['img_url'] ?? null;
        }
        return null;
    }

    private function resolveUrl(mixed $val): string
    {
        if (is_array($val)) return $val['url'] ?? '#';
        return $val ?: '#';
    }

    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f5f0;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">Furnito: Category Banners — preview on the live page</p></div>';
        }

        $b1      = $settings['general']['banner_1'] ?? [];
        $b2      = $settings['general']['banner_2'] ?? [];
        $spacing = $settings['style']['spacing']     ?? [];

        $img1 = $this->resolveImage($b1['image'] ?? null)
            ?: global_asset('core/' . theme_assets('images/cat-banner-1.jpg', 'furnito'));
        $img2 = $this->resolveImage($b2['image'] ?? null)
            ?: global_asset('core/' . theme_assets('images/cat-banner-2.jpg', 'furnito'));

        return view('theme-furnito::widgets.category_banners', [
            'banner_1' => [
                'title'    => $b1['title']    ?? 'Furniture Collection',
                'subtitle' => $b1['subtitle'] ?? 'Modern & Classic Pieces',
                'btn_text' => $b1['btn_text'] ?? 'Shop Now',
                'btn_url'  => $this->resolveUrl($b1['btn_url'] ?? '#'),
                'bg_color' => $b1['bg_color'] ?? '#ECEDF5',
                'image'    => $img1,
            ],
            'banner_2' => [
                'title'    => $b2['title']    ?? 'Plant Collection',
                'subtitle' => $b2['subtitle'] ?? 'Bring Nature Indoors',
                'btn_text' => $b2['btn_text'] ?? 'Shop Now',
                'btn_url'  => $this->resolveUrl($b2['btn_url'] ?? '#'),
                'bg_color' => $b2['bg_color'] ?? '#F5F0E8',
                'image'    => $img2,
            ],
            'padding_top'    => (int) ($spacing['padding_top']    ?? 40),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 40),
            'card_height'    => (int) ($spacing['card_height']    ?? 320),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'furnito';
    }
}
