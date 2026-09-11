<?php

namespace Themes\Maison\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class PromoBanner extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'maison_promo_banner';
    }

    protected function getWidgetName(): string
    {
        return 'Maison: Promo Banner';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-paint-brush';
    }

    protected function getWidgetDescription(): string
    {
        return __('Olive-toned CTA section with headline, text, and icon tiles');
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['promo', 'cta', 'banner', 'maison'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXTAREA()
                ->setLabel('Title')
                ->setDefault('Free Interior Design Consultation'))
            ->registerField('text', FieldManager::TEXTAREA()
                ->setLabel('Text')
                ->setDefault('Work with our in-house designers to create your dream room. Personalised recommendations, no obligation.'))
            ->registerField('promo_image', FieldManager::IMAGE()
                ->setLabel('Right Side Image (overrides icon when set)'))
            ->registerField('promo_icon', FieldManager::TEXT()
                ->setLabel('Right Side Icon (emoji, shown when no image)')
                ->setDefault('🛋️'))
            ->registerField('button_text', FieldManager::TEXT()
                ->setLabel('Button Text')
                ->setDefault('Book a Consultation'))
            ->registerField('button_url', FieldManager::URL()
                ->setLabel('Button URL')
                ->setDefault('#'))
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding', FieldManager::DIMENSION()
                ->asPadding()
                ->setLabel('Section Padding')
                ->setDefault(['top' => 64, 'right' => 0, 'bottom' => 64, 'left' => 0])
                ->setSelectors([
                    '{{WRAPPER}} .ms-widget-promo' => 'padding-top: {{VALUE.TOP}}{{UNIT}}; padding-bottom: {{VALUE.BOTTOM}}{{UNIT}};',
                ])
            )
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {

        // Not in tenant context (editor preview API has no tenant middleware)
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;">Maison: Promo Banner — preview on the live page</p>
                    </div>';
        }
        $content    = $settings['general']['content'] ?? [];
        $raw_url    = $content['button_url'] ?? '#';
        $button_url = is_array($raw_url) ? ($raw_url['url'] ?? '#') : ($raw_url ?: '#');

        $promo_img_url = null;
        $raw = $content['promo_image'] ?? null;
        if (!empty($raw)) {
            if (is_int($raw) || (is_string($raw) && is_numeric($raw))) {
                $d = get_attachment_image_by_id((int) $raw);
                $promo_img_url = $d['img_url'] ?? null;
            } elseif (is_array($raw)) {
                $promo_img_url = $raw['url'] ?? $raw['img_url'] ?? null;
                if (empty($promo_img_url) && !empty($raw['id'])) {
                    $d = get_attachment_image_by_id((int) $raw['id']);
                    $promo_img_url = $d['img_url'] ?? null;
                }
            }
        }
        if (empty($promo_img_url)) {
            $promo_img_url = global_asset('core/' . theme_assets('images/promo-banner.png', 'maison'));
        }

        return view('theme-maison::widgets.promo_banner', [
            'title'        => $content['title']       ?? 'Free Interior Design Consultation',
            'text'         => $content['text']        ?? 'Work with our in-house designers to create your dream room.',
            'promo_image'  => $promo_img_url,
            'promo_icon'   => $content['promo_icon']  ?? '🛋️',
            'button_text'  => $content['button_text'] ?? 'Book a Consultation',
            'button_url'   => $button_url,
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'maison';
    }
}
