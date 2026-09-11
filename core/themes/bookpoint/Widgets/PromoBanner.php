<?php

namespace Themes\Bookpoint\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class PromoBanner extends BaseWidget
{
    protected function getWidgetType(): string { return 'bookpoint_promo_banner'; }
    protected function getWidgetName(): string { return 'BookPoint: Promo Banner'; }
    protected function getWidgetIcon(): string|array { return 'las la-ad'; }
    protected function getWidgetDescription(): string { return __('Two side-by-side promotional banners with images'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['promo', 'banner', 'bookpoint']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('banner1', 'Banner 1')
            ->registerField('tag1', FieldManager::TEXT()->setLabel('Tag')->setDefault('40 Life Changing Books'))
            ->registerField('title1', FieldManager::TEXT()->setLabel('Title')->setDefault('Get special discounts on'))
            ->registerField('link1_text', FieldManager::TEXT()->setLabel('Link Text')->setDefault('Browse Now'))
            ->registerField('link1_url', FieldManager::URL()->setLabel('Link URL')->setDefault('#'))
            ->registerField('image1', FieldManager::IMAGE()->setLabel('Banner Image'))
            ->endGroup();
        $control->addGroup('banner2', 'Banner 2')
            ->registerField('tag2', FieldManager::TEXT()->setLabel('Tag')->setDefault('The Sci-Fi world with N. K. Jemisin'))
            ->registerField('title2', FieldManager::TEXT()->setLabel('Title')->setDefault('Lose yourself'))
            ->registerField('link2_text', FieldManager::TEXT()->setLabel('Link Text')->setDefault('Browse Now'))
            ->registerField('link2_url', FieldManager::URL()->setLabel('Link URL')->setDefault('#'))
            ->registerField('image2', FieldManager::IMAGE()->setLabel('Banner Image'))
            ->endGroup();
        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top', FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(80)->setMin(0)->setMax(200))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(80)->setMin(0)->setMax(200))
            ->endGroup();
        return $control->getFields();
    }

    private function resolveImage(mixed $field, string $fallbackAsset = ''): ?string
    {
        if (is_array($field)) {
            $url = $field['img_url'] ?? $field['url'] ?? null;
            if (empty($url) && !empty($field['id'])) {
                $d = get_attachment_image_by_id((int) $field['id']);
                $url = $d['img_url'] ?? null;
            }
            return !empty($url) ? $url : ($fallbackAsset ?: null);
        } elseif (!empty($field)) {
            $d = get_attachment_image_by_id((int) $field);
            $url = $d['img_url'] ?? null;
            return !empty($url) ? $url : ($fallbackAsset ?: null);
        }
        return $fallbackAsset ?: null;
    }

    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">BookPoint: Promo Banner — preview on the live page</p></div>';
        }

        $b1      = $settings['general']['banner1'] ?? [];
        $b2      = $settings['general']['banner2'] ?? [];
        $spacing = $settings['style']['spacing']   ?? [];

        $raw1 = $b1['link1_url'] ?? '#';
        $raw2 = $b2['link2_url'] ?? '#';

        return view('theme-bookpoint::widgets.promo_banner', [
            'tag1'        => $b1['tag1']       ?? '40 Life Changing Books',
            'title1'      => $b1['title1']     ?? 'Get special discounts on',
            'link1_text'  => $b1['link1_text'] ?? 'Browse Now',
            'link1_url'   => is_array($raw1) ? ($raw1['url'] ?? '#') : $raw1,
            'image1_url'  => $this->resolveImage($b1['image1'] ?? null, global_asset('core/' . theme_assets('images/prod-book-2.jpg', 'bookpoint'))),
            'tag2'        => $b2['tag2']       ?? 'The Sci-Fi world with N. K. Jemisin',
            'title2'      => $b2['title2']     ?? 'Lose yourself',
            'link2_text'  => $b2['link2_text'] ?? 'Browse Now',
            'link2_url'   => is_array($raw2) ? ($raw2['url'] ?? '#') : $raw2,
            'image2_url'  => $this->resolveImage($b2['image2'] ?? null, global_asset('core/' . theme_assets('images/prod-book-3.jpg', 'bookpoint'))),
            'padding_top'    => (int) ($spacing['padding_top']    ?? 80),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 80),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'bookpoint';
    }
}
