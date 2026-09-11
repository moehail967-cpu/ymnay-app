<?php

namespace Themes\Freshmart\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class PromoBanner extends BaseWidget
{
    protected function getWidgetType(): string { return 'freshmart_promo_banner'; }
    protected function getWidgetName(): string { return 'FreshMart: Promo Banner'; }
    protected function getWidgetIcon(): string|array { return 'mdi mdi-tag-heart-outline'; }
    protected function getWidgetDescription(): string { return __('Green subscription/promo banner with heading, description and two CTA buttons'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['promo', 'banner', 'subscription', 'freshmart']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXTAREA()
                ->setLabel('Heading (HTML allowed)')
                ->setDefault('Get Your Weekly Organic<br>Subscription Box'))
            ->registerField('text', FieldManager::TEXTAREA()
                ->setLabel('Description')
                ->setDefault('Curated seasonal produce delivered every week. Customize your box, cancel anytime.'))
            ->registerField('button_text', FieldManager::TEXT()
                ->setLabel('Primary Button Text')
                ->setDefault('Build My Box'))
            ->registerField('button_url', FieldManager::URL()
                ->setLabel('Primary Button URL')
                ->setDefault('#'))
            ->registerField('button2_text', FieldManager::TEXT()
                ->setLabel('Secondary Button Text')
                ->setDefault('See Plans'))
            ->registerField('button2_url', FieldManager::URL()
                ->setLabel('Secondary Button URL')
                ->setDefault('#'))
            ->registerField('icon1', FieldManager::TEXT()
                ->setLabel('Icon 1 (emoji or text)')
                ->setDefault('🥬'))
            ->registerField('icon2', FieldManager::TEXT()
                ->setLabel('Icon 2 — centre, larger (emoji or text)')
                ->setDefault('🍎'))
            ->registerField('icon3', FieldManager::TEXT()
                ->setLabel('Icon 3 (emoji or text)')
                ->setDefault('🥕'))
            ->registerField('promo_image', FieldManager::IMAGE()
                ->setLabel('Right Side Image (optional)'))
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
                ->setDefault(['top' => 70, 'right' => 0, 'bottom' => 70, 'left' => 0])
                ->setSelectors([
                    '{{WRAPPER}} .fm-promo-banner' => 'padding-top: {{VALUE.TOP}}{{UNIT}}; padding-bottom: {{VALUE.BOTTOM}}{{UNIT}};',
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
                        <p style="margin:0;font-size:14px;">FreshMart: Promo Banner — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];

        $buttonUrl = $content['button_url'] ?? null;
        if (is_array($buttonUrl)) { $buttonUrl = $buttonUrl['url'] ?? null; }
        if (empty($buttonUrl)) { $buttonUrl = function_exists('theme_shop_url') ? theme_shop_url() : '#'; }

        $button2Url = $content['button2_url'] ?? null;
        if (is_array($button2Url)) { $button2Url = $button2Url['url'] ?? null; }
        if (empty($button2Url)) { $button2Url = '#'; }

        
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

        if (empty($promoImgUrl)) {
            $promoImgUrl = global_asset('core/' . theme_assets('images/grocery-basket.jpg', 'freshmart'));
        }

        return view('theme-freshmart::widgets.promo_banner', [
            'title'        => $content['title']        ?? 'Get Your Weekly Organic<br>Subscription Box',
            'text'         => $content['text']         ?? 'Curated seasonal produce delivered every week. Customize your box, cancel anytime.',
            'button_text'  => $content['button_text']  ?? 'Build My Box',
            'button_url'   => $buttonUrl,
            'button2_text' => $content['button2_text'] ?? 'See Plans',
            'button2_url'  => $button2Url,
            'icon1'        => $content['icon1']        ?? '🥬',
            'icon2'        => $content['icon2']        ?? '🍎',
            'icon3'        => $content['icon3']        ?? '🥕',
        
            'promo_image' => $promoImgUrl,])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'freshmart';
    }
}
