<?php

namespace Themes\Drivekit\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class PromoBanner extends BaseWidget
{
    protected function getWidgetType(): string { return 'drivekit_promo_banner'; }
    protected function getWidgetName(): string { return 'DriveKit: Promo Banner'; }
    protected function getWidgetIcon(): string|array { return 'las la-tag'; }
    protected function getWidgetDescription(): string { return __('Dark promotional banner with heading, text, and CTA — ideal for trade offers'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['promo', 'banner', 'drivekit']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXTAREA()->setLabel('Heading (HTML allowed)')->setDefault('Trade Accounts & <span>Fleet Pricing</span>'))
            ->registerField('text', FieldManager::TEXTAREA()->setLabel('Description')->setDefault('Garages, dealerships, and fleets get dedicated pricing, credit accounts, and priority support. Apply for a trade account in minutes.'))
            ->registerField('button_text', FieldManager::TEXT()->setLabel('Button Text')->setDefault('Apply for Trade Account'))
            ->registerField('button_url', FieldManager::URL()->setLabel('Button URL')->setDefault('#'))
            ->registerField('promo_image', FieldManager::IMAGE()
                ->setLabel('Right Side Image (optional)'))
            ->endGroup();

        $control->addGroup('media', 'Media')
            ->registerField('image', FieldManager::IMAGE()->setLabel('Right Side Image (optional)'))
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
                ->setDefault(['top' => 100, 'right' => 0, 'bottom' => 100, 'left' => 0])
                ->setSelectors([
                    '{{WRAPPER}} .dk-section-promo' => 'padding-top: {{VALUE.TOP}}{{UNIT}}; padding-bottom: {{VALUE.BOTTOM}}{{UNIT}};',
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
                        <p style="margin:0;font-size:14px;">DriveKit: Promo Banner — preview on the live page</p>
                    </div>';
        }
        $content  = $settings['general']['content'] ?? [];
        $media    = $settings['general']['media']   ?? [];

        $buttonUrl = $content['button_url'] ?? null;
        if (is_array($buttonUrl)) { $buttonUrl = $buttonUrl['url'] ?? null; }
        if (empty($buttonUrl)) { $buttonUrl = function_exists('theme_shop_url') ? theme_shop_url() : '#'; }

        $image = null;
        if (!empty($media['image'])) {
            $val = $media['image'];
            if (is_array($val)) {
                $image = $val['url'] ?? null;
            } elseif (is_numeric($val)) {
                $d = get_attachment_image_by_id((int)$val);
                $image = $d['img_url'] ?? null;
            }
        }

        
                $promoImgUrl = null;
                $raw = $content['promo_image'] ?? $settings['general']['media']['promo_image'] ?? null;
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

        if (empty($image)) {
            $image = global_asset('core/' . theme_assets('images/car-polish.jpg', 'drivekit'));
        }
        if (empty($promoImgUrl)) {
            $promoImgUrl = global_asset('core/' . theme_assets('images/car-polish.jpg', 'drivekit'));
        }

        return view('theme-drivekit::widgets.promo_banner', [
            'title'       => $content['title']       ?? 'Trade Accounts & <span>Fleet Pricing</span>',
            'text'        => $content['text']        ?? 'Garages, dealerships, and fleets get dedicated pricing, credit accounts, and priority support. Apply for a trade account in minutes.',
            'button_text' => $content['button_text'] ?? 'Apply for Trade Account',
            'button_url'  => $buttonUrl,
            'image'       => $image,
        
            'promo_image' => $promoImgUrl,])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'drivekit';
    }
}
