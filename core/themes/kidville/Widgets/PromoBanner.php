<?php

namespace Themes\Kidville\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class PromoBanner extends BaseWidget
{
    protected function getWidgetType(): string { return 'kidville_promo_banner'; }
    protected function getWidgetName(): string { return 'KidVille: Promo Banner'; }
    protected function getWidgetIcon(): string|array { return 'las la-star'; }
    protected function getWidgetDescription(): string { return __('Bold full-width promo section with headline, text and CTA buttons'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['promo', 'banner', 'cta', 'kidville']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXTAREA()
                ->setLabel('Title (wrap accent in <span> for yellow highlight)')
                ->setDefault('Need Help Finding the Perfect <span>Gift?</span> 🎁'))
            ->registerField('text', FieldManager::TEXTAREA()
                ->setLabel('Text')
                ->setDefault("Tell us their age and interests, and we'll pick the perfect toy. Our gift finder makes gifting easy and fun!"))
            ->registerField('icon1', FieldManager::TEXT()
                ->setLabel('Right Icon 1 (emoji)')
                ->setDefault('🎁'))
            ->registerField('icon2', FieldManager::TEXT()
                ->setLabel('Right Icon 2 (emoji)')
                ->setDefault('🧸'))
            ->registerField('icon3', FieldManager::TEXT()
                ->setLabel('Right Icon 3 (emoji)')
                ->setDefault('🎮'))
            ->registerField('button_text', FieldManager::TEXT()
                ->setLabel('Primary Button Text')
                ->setDefault('Use Gift Finder'))
            ->registerField('button_url', FieldManager::URL()
                ->setLabel('Primary Button URL')
                ->setDefault('#'))
            ->registerField('button2_text', FieldManager::TEXT()
                ->setLabel('Secondary Button Text (optional)')
                ->setDefault(''))
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
                ->setDefault(['top' => 60, 'right' => 0, 'bottom' => 60, 'left' => 0])
                ->setSelectors([
                    '{{WRAPPER}} .kv-promo-widget' => 'padding-top: {{VALUE.TOP}}{{UNIT}}; padding-bottom: {{VALUE.BOTTOM}}{{UNIT}};',
                ])
            )
            ->endGroup();

        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;">KidVille: Promo Banner — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];
        $btnUrl  = $content['button_url']  ?? '#';
        $btn2Url = $content['button2_url'] ?? '#';
        if (is_array($btnUrl))  $btnUrl  = $btnUrl['url']  ?? '#';
        if (is_array($btn2Url)) $btn2Url = $btn2Url['url'] ?? '#';

        
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
            $promoImgUrl = global_asset('core/' . theme_assets('images/playset-box.jpg', 'kidville'));
        }

        return view('theme-kidville::widgets.promo_banner', [
            'title'        => $content['title']        ?? 'Need Help Finding the Perfect <span>Gift?</span> 🎁',
            'text'         => $content['text']         ?? '',
            'icon1'        => $content['icon1']        ?? '🎁',
            'icon2'        => $content['icon2']        ?? '🧸',
            'icon3'        => $content['icon3']        ?? '🎮',
            'button_text'  => $content['button_text']  ?? 'Use Gift Finder',
            'button_url'   => $btnUrl,
            'button2_text' => $content['button2_text'] ?? '',
            'button2_url'  => $btn2Url,
        
            'promo_image' => $promoImgUrl,])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'kidville';
    }
}
