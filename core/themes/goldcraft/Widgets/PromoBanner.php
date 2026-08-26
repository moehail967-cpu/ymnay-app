<?php

namespace Themes\Goldcraft\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class PromoBanner extends BaseWidget
{
    protected function getWidgetType(): string { return 'goldcraft_promo_banner'; }
    protected function getWidgetName(): string { return 'GoldCraft: Promo Banner'; }
    protected function getWidgetIcon(): string|array { return 'las la-gem'; }
    protected function getWidgetDescription(): string { return __('Dark artisan promo section with headline, text and CTA — GoldCraft style'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['promo', 'banner', 'cta', 'goldcraft']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXTAREA()
                ->setLabel('Title (wrap key word in <span> for accent)')
                ->setDefault('Commission a <span>Bespoke</span> Piece'))
            ->registerField('text', FieldManager::TEXTAREA()
                ->setLabel('Text')
                ->setDefault('Work directly with our master jewellers to create a one-of-a-kind piece that carries your story. From concept to creation.'))
            ->registerField('button_text', FieldManager::TEXT()
                ->setLabel('Button Text')
                ->setDefault('Start Your Commission'))
            ->registerField('button_url', FieldManager::URL()
                ->setLabel('Button URL')
                ->setDefault('#'))
            ->registerField('button2_text', FieldManager::TEXT()
                ->setLabel('Secondary Button Text')
                ->setDefault('View Gallery'))
            ->registerField('button2_url', FieldManager::URL()
                ->setLabel('Secondary Button URL')
                ->setDefault('#'))
            ->registerField('icon1', FieldManager::TEXT()
                ->setLabel('Emoji Icon 1')
                ->setDefault('💍'))
            ->registerField('icon2', FieldManager::TEXT()
                ->setLabel('Emoji Icon 2 (large, center)')
                ->setDefault('✨'))
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
                ->setDefault(['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0])
                ->setSelectors([
                    '{{WRAPPER}} .gc-widget-promo' => 'padding-top: {{VALUE.TOP}}{{UNIT}}; padding-bottom: {{VALUE.BOTTOM}}{{UNIT}};',
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
                        <p style="margin:0;font-size:14px;">GoldCraft: Promo Banner — preview on the live page</p>
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
            $promoImgUrl = global_asset('core/' . theme_assets('images/luxury-set.jpg', 'goldcraft'));
        }

        return view('theme-goldcraft::widgets.promo_banner', [
            'title'        => $content['title']        ?? 'Commission a <span>Bespoke</span> Piece',
            'text'         => $content['text']         ?? '',
            'button_text'  => $content['button_text']  ?? 'Start Your Commission',
            'button_url'   => $btnUrl,
            'button2_text' => $content['button2_text'] ?? 'View Gallery',
            'button2_url'  => $btn2Url,
            'icon1'        => $content['icon1']        ?? '💍',
            'icon2'        => $content['icon2']        ?? '✨',
        
            'promo_image' => $promoImgUrl,])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'goldcraft';
    }
}
