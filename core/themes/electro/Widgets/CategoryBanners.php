<?php

namespace Themes\Electro\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class CategoryBanners extends BaseWidget
{
    protected function getWidgetType(): string { return 'electro_category_banners'; }
    protected function getWidgetName(): string { return 'Electro: Category Banners'; }
    protected function getWidgetIcon(): string|array { return 'las la-th-large'; }
    protected function getWidgetDescription(): string { return __('3 horizontal category banner cards with product image and shop link'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['category', 'banner', 'electro']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('banner_1', 'Banner 1')
            ->registerField('name', FieldManager::TEXT()->setLabel('Category Name')->setDefault('Phone Collection'))
            ->registerField('link_url', FieldManager::URL()->setLabel('Link URL')->setDefault('#'))
            ->registerField('image', FieldManager::IMAGE()->setLabel('Product Image'))
            ->endGroup();

        $control->addGroup('banner_2', 'Banner 2')
            ->registerField('name', FieldManager::TEXT()->setLabel('Category Name')->setDefault('Watch Collection'))
            ->registerField('link_url', FieldManager::URL()->setLabel('Link URL')->setDefault('#'))
            ->registerField('image', FieldManager::IMAGE()->setLabel('Product Image'))
            ->endGroup();

        $control->addGroup('banner_3', 'Banner 3')
            ->registerField('name', FieldManager::TEXT()->setLabel('Category Name')->setDefault('Laptop Collection'))
            ->registerField('link_url', FieldManager::URL()->setLabel('Link URL')->setDefault('#'))
            ->registerField('image', FieldManager::IMAGE()->setLabel('Product Image'))
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

    private function resolveImage(string|array|null $field, string $fallbackAsset = ''): ?string
    {
        if (empty($field)) return $fallbackAsset ?: null;
        if (is_array($field)) {
            $url = $field['img_url'] ?? $field['url'] ?? null;
            if (!empty($url)) return $url;
            if (!empty($field['id'])) {
                $d = get_attachment_image_by_id((int) $field['id']);
                $url = $d['img_url'] ?? null;
                if (!empty($url)) return $url;
            }
        } elseif (!empty($field)) {
            $d = get_attachment_image_by_id((int) $field);
            $url = $d['img_url'] ?? null;
            if (!empty($url)) return $url;
        }
        return $fallbackAsset ?: null;
    }

    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">Electro: Category Banners — preview on the live page</p></div>';
        }

        $g = $settings['general'] ?? [];
        $spacing = $settings['style']['spacing'] ?? [];

        $fallbacks = [
            global_asset('core/' . theme_assets('images/category_banner_1.jpg', 'electro')),
            global_asset('core/' . theme_assets('images/category_banner_2.jpg', 'electro')),
            global_asset('core/' . theme_assets('images/category_banner_3.jpg', 'electro')),
        ];

        $banners = [];
        foreach (['banner_1', 'banner_2', 'banner_3'] as $i => $key) {
            $b = $g[$key] ?? [];
            $rawUrl = $b['link_url'] ?? '#';
            $banners[] = [
                'name'     => $b['name']    ?? ['Phone Collection', 'Watch Collection', 'Laptop Collection'][$i],
                'link_url' => is_array($rawUrl) ? ($rawUrl['url'] ?? '#') : $rawUrl,
                'image'    => $this->resolveImage($b['image'] ?? null, $fallbacks[$i]),
            ];
        }

        return view('theme-electro::widgets.category_banners', [
            'banners'        => $banners,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 60),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 60),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'electro';
    }
}
