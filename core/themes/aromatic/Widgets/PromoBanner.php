<?php

namespace Themes\Aromatic\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class PromoBanner extends BaseWidget
{
    protected function getWidgetType(): string  { return 'aromatic_promo_banner'; }
    protected function getWidgetName(): string  { return 'Aromatic: Promo Banner'; }
    protected function getWidgetIcon(): string|array { return 'las la-star'; }
    protected function getWidgetDescription(): string { return __('Split promo banner: text + price + CTA on left, product image on right'); }
    protected function getCategory(): string    { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array   { return ['promo', 'banner', 'aromatic', 'cta']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('title',       FieldManager::TEXTAREA()->setLabel('Title (HTML allowed)')->setDefault('New Perfume Collection'))
            ->registerField('price',       FieldManager::TEXT()->setLabel('Price (number only)')->setDefault('320'))
            ->registerField('button_text', FieldManager::TEXT()->setLabel('Button Text')->setDefault('Buy Now'))
            ->registerField('button_url',  FieldManager::URL()->setLabel('Button URL')->setDefault('#'))
            ->endGroup();

        $control->addGroup('media', 'Media')
            ->registerField('promo_image', FieldManager::IMAGE()->setLabel('Product Image'))
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top',    FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(80)->setMin(0)->setMax(200))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(80)->setMin(0)->setMax(200))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;">Aromatic: Promo Banner — preview on the live page</p>
                    </div>';
        }

        try {
            $content = $settings['general']['content'] ?? [];
            $media   = $settings['general']['media']   ?? [];
            $spacing = $settings['style']['spacing']   ?? [];

            $image_url  = null;
            $imageField = $media['promo_image'] ?? null;
            if (is_array($imageField)) {
                $image_url = $imageField['img_url'] ?? $imageField['url'] ?? null;
                if (empty($image_url) && !empty($imageField['id'])) {
                    $d = get_attachment_image_by_id((int) $imageField['id']);
                    $image_url = $d['img_url'] ?? null;
                }
            } elseif (!empty($imageField)) {
                $d = get_attachment_image_by_id((int) $imageField);
                $image_url = $d['img_url'] ?? null;
            }

            if (empty($image_url)) {
                $image_url = global_asset('core/' . theme_assets('images/promo-banner.jpg', 'aromatic'));
            }

            $rawUrl = $content['button_url'] ?? '#';

            return view('theme-aromatic::widgets.promo_banner', [
                'title'          => $content['title']       ?? 'New Perfume Collection',
                'price'          => $content['price']       ?? '320',
                'button_text'    => $content['button_text'] ?? 'Buy Now',
                'button_url'     => is_array($rawUrl) ? ($rawUrl['url'] ?? '#') : $rawUrl,
                'image_url'      => $image_url,
                'padding_top'    => (int) ($spacing['padding_top']    ?? 80),
                'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 80),
            ])->render();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('aromatic_promo_banner render failed: ' . $e->getMessage(), [
                'file' => $e->getFile(), 'line' => $e->getLine(),
            ]);
            return '<div style="padding:20px;text-align:center;color:#c00;font-size:13px;font-family:sans-serif;">Promo Banner: preview unavailable</div>';
        }
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'aromatic';
    }
}
