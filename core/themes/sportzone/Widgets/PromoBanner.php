<?php

namespace Themes\Sportzone\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class PromoBanner extends BaseWidget
{
    protected function getWidgetType(): string { return 'sportzone_promo_banner'; }
    protected function getWidgetName(): string { return 'SportZone: Promo Banner'; }
    protected function getWidgetIcon(): string|array { return 'la-bolt'; }
    protected function getWidgetDescription(): string { return __('Dark navy promotional banner with red accents, headline, CTA and icon feature tiles — ideal for sales and campaign sections'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['promo', 'banner', 'sportzone']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('tag', FieldManager::TEXT()
                ->setLabel('Section Tag')
                ->setDefault('Limited Time Deal'))
            ->registerField('title', FieldManager::TEXTAREA()
                ->setLabel('Heading (HTML allowed)')
                ->setDefault('Get <span>20% Off</span><br>All Sports Gear'))
            ->registerField('text', FieldManager::TEXTAREA()
                ->setLabel('Description')
                ->setDefault('Top performance equipment, apparel and accessories — all on sale. Limited stock, unbeatable prices.'))
            ->registerField('button_text', FieldManager::TEXT()
                ->setLabel('Primary Button Text')
                ->setDefault('Shop the Sale'))
            ->registerField('button_url', FieldManager::URL()
                ->setLabel('Primary Button URL')
                ->setDefault('#'))
            ->registerField('button2_text', FieldManager::TEXT()
                ->setLabel('Secondary Button Text')
                ->setDefault('View All Deals'))
            ->registerField('button2_url', FieldManager::URL()
                ->setLabel('Secondary Button URL')
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
            ->registerField('padding', FieldManager::DIMENSION()
                ->asPadding()
                ->setLabel('Section Padding')
                ->setDefault(['top' => 80, 'right' => 0, 'bottom' => 80, 'left' => 0])
                ->setSelectors([
                    '{{WRAPPER}} .sz-promo-section' => 'padding-top: {{VALUE.TOP}}{{UNIT}}; padding-bottom: {{VALUE.BOTTOM}}{{UNIT}};',
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
                        <p style="margin:0;font-size:14px;">SportZone: Promo Banner — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];

        $btnUrl = $content['button_url'] ?? null;
        if (is_array($btnUrl)) $btnUrl = $btnUrl['url'] ?? null;
        if (empty($btnUrl)) $btnUrl = theme_shop_url();

        $btn2Url = $content['button2_url'] ?? null;
        if (is_array($btn2Url)) $btn2Url = $btn2Url['url'] ?? null;
        if (empty($btn2Url)) $btn2Url = theme_shop_url();

        
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
            $promoImgUrl = global_asset('core/' . theme_assets('images/runner-action.jpg', 'sportzone'));
        }

        return view('theme-sportzone::widgets.promo_banner', [
            'tag'          => $content['tag']          ?? 'Limited Time Deal',
            'title'        => $content['title']        ?? 'Get <span>20% Off</span><br>All Sports Gear',
            'text'         => $content['text']         ?? '',
            'button_text'  => $content['button_text']  ?? 'Shop the Sale',
            'button_url'   => $btnUrl,
            'button2_text' => $content['button2_text'] ?? 'View All Deals',
            'button2_url'  => $btn2Url,
        
            'promo_image' => $promoImgUrl,])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'sportzone';
    }
}
