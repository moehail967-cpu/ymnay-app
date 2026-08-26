<?php

namespace Themes\Velvetlux\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class PromoSection extends BaseWidget
{
    protected function getWidgetType(): string { return 'velvetlux_promo_section'; }
    protected function getWidgetName(): string { return 'VelvetLux: Promo Section'; }
    protected function getWidgetIcon(): string|array { return 'las la-megaphone'; }
    protected function getWidgetDescription(): string { return __('Dark plum promotional section with heading, text, and button'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['promo', 'cta', 'velvetlux']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXTAREA()
                ->setLabel('Title (HTML allowed)')
                ->setDefault('The Art of<br><span>Personalisation</span>'))
            ->registerField('subtitle', FieldManager::TEXTAREA()
                ->setLabel('Description')
                ->setDefault('Our atelier service allows you to customise any piece with monogramming, alterations, and bespoke fitting. An experience as exceptional as the garment.'))
            ->registerField('button_text', FieldManager::TEXT()
                ->setLabel('Button Text')
                ->setDefault('Book Atelier Appointment'))
            ->registerField('button_url', FieldManager::URL()
                ->setLabel('Button URL')
                ->setDefault('#'))
            ->registerField('promo_image', FieldManager::IMAGE()
                ->setLabel('Right Side Image (optional)'))
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

    public function render(array $settings = []): string
    {

        // Not in tenant context (editor preview API has no tenant middleware)
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;">VelvetLux: Promo Section — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];
        $spacing = $settings['style']['spacing'] ?? [];

        $rawUrl = $content['button_url'] ?? '#';
        $button_url = is_array($rawUrl) ? ($rawUrl['url'] ?? '#') : $rawUrl;

        $promoImgUrl = null;
        $raw = $content['promo_image'] ?? null;
        if (!empty($raw)) {
            if (is_int($raw) || (is_string($raw) && is_numeric($raw))) {
                $d = get_attachment_image_by_id((int) $raw);
                $promoImgUrl = $d['img_url'] ?? null;
            } elseif (is_array($raw)) {
                $promoImgUrl = $raw['url'] ?? $raw['img_url'] ?? null;
                if (empty($promoImgUrl) && !empty($raw['id'])) {
                    $d = get_attachment_image_by_id((int) $raw['id']);
                    $promoImgUrl = $d['img_url'] ?? null;
                }
            }
        }
        if (empty($promoImgUrl)) {
            $promoImgUrl = global_asset('core/' . theme_assets('images/promo-banner.png', 'velvetlux'));
        }

        return view('theme-velvetlux::widgets.promo_section', [
            'title'          => $content['title']        ?? 'The Art of<br><span>Personalisation</span>',
            'subtitle'       => $content['subtitle']     ?? 'Our atelier service allows you to customise any piece with monogramming, alterations, and bespoke fitting.',
            'button_text'    => $content['button_text']  ?? 'Book Atelier Appointment',
            'button_url'     => $button_url,
            'promo_image'    => $promoImgUrl,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 80),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 80),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'velvetlux';
    }
}
