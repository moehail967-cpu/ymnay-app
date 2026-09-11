<?php

namespace Themes\Furnito\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class NewArrival extends BaseWidget
{
    protected function getWidgetType(): string { return 'furnito_new_arrival'; }
    protected function getWidgetName(): string { return 'Furnito: New Arrival Banner'; }
    protected function getWidgetIcon(): string|array { return 'las la-star'; }
    protected function getWidgetDescription(): string { return __('Promo split banner: left product image, right headline + CTA'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['new-arrival', 'promo', 'banner', 'furnito']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('eyebrow',     FieldManager::TEXT()->setLabel('Eyebrow / Badge')->setDefault('New Arrival'))
            ->registerField('year',        FieldManager::TEXT()->setLabel('Year / Tag')->setDefault('2025'))
            ->registerField('title',       FieldManager::TEXT()->setLabel('Title')->setDefault('New Arrival'))
            ->registerField('description', FieldManager::TEXTAREA()->setLabel('Description')->setDefault('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Feugiat partner enim hendrerit pellentesque Pharetra consectetur quam.'))
            ->registerField('button_text', FieldManager::TEXT()->setLabel('Button Text')->setDefault('Shop Now'))
            ->registerField('button_url',  FieldManager::URL()->setLabel('Button URL')->setDefault('#'))
            ->endGroup();

        $control->addGroup('media', 'Media')
            ->registerField('image', FieldManager::IMAGE()->setLabel('Banner Image'))
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top',    FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(0)->setMin(0)->setMax(200))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(0)->setMin(0)->setMax(200))
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

    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#EEF3F7;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">Furnito: New Arrival Banner — preview on the live page</p></div>';
        }

        $content = $settings['general']['content'] ?? [];
        $media   = $settings['general']['media']   ?? [];
        $spacing = $settings['style']['spacing']   ?? [];
        $rawUrl  = $content['button_url'] ?? '#';

        $img = $this->resolveImage($media['image'] ?? null)
            ?: global_asset('core/' . theme_assets('images/new_arrival.jpg', 'furnito'));

        return view('theme-furnito::widgets.new_arrival', [
            'eyebrow'        => $content['eyebrow']     ?? 'New Arrival',
            'year'           => $content['year']        ?? '2025',
            'title'          => $content['title']       ?? 'New Arrival',
            'description'    => $content['description'] ?? '',
            'button_text'    => $content['button_text'] ?? 'Shop Now',
            'button_url'     => is_array($rawUrl) ? ($rawUrl['url'] ?? '#') : $rawUrl,
            'image'          => $img,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 0),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 0),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'furnito';
    }
}
