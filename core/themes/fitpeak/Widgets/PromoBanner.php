<?php

namespace Themes\Fitpeak\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class PromoBanner extends BaseWidget
{
    protected function getWidgetType(): string { return 'fitpeak_promo_banner'; }
    protected function getWidgetName(): string { return 'FitPeak: Promo Banner'; }
    protected function getWidgetIcon(): string|array { return 'mdi mdi-lightning-bolt'; }
    protected function getWidgetDescription(): string { return __('Green promotional banner with heading, description and CTA — ideal for custom stack or sale offers'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['promo', 'banner', 'fitpeak']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXTAREA()
                ->setLabel('Heading (HTML allowed)')
                ->setDefault('Build Your Custom<br>Performance Stack'))
            ->registerField('text', FieldManager::TEXTAREA()
                ->setLabel('Description')
                ->setDefault('Tell us your goal — build muscle, lose fat, boost endurance — and we\'ll build a personalised supplement plan that actually works.'))
            ->registerField('button_text', FieldManager::TEXT()
                ->setLabel('Button Text')
                ->setDefault('Build My Stack'))
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
            ->registerField('padding', FieldManager::DIMENSION()
                ->asPadding()
                ->setLabel('Section Padding')
                ->setDefault(['top' => 80, 'right' => 0, 'bottom' => 80, 'left' => 0])
                ->setSelectors([
                    '{{WRAPPER}} .fp-promo' => 'padding-top: {{VALUE.TOP}}{{UNIT}}; padding-bottom: {{VALUE.BOTTOM}}{{UNIT}};',
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
                        <p style="margin:0;font-size:14px;">FitPeak: Promo Banner — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];

        $buttonUrl = $content['button_url'] ?? null;
        if (is_array($buttonUrl)) { $buttonUrl = $buttonUrl['url'] ?? null; }
        if (empty($buttonUrl)) { $buttonUrl = function_exists('theme_shop_url') ? theme_shop_url() : '#'; }

        
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
            $promoImgUrl = global_asset('core/' . theme_assets('images/pre-workout.jpg', 'fitpeak'));
        }

        return view('theme-fitpeak::widgets.promo_banner', [
            'title'       => $content['title']       ?? 'Build Your Custom<br>Performance Stack',
            'text'        => $content['text']        ?? 'Tell us your goal — build muscle, lose fat, boost endurance — and we\'ll build a personalised supplement plan that actually works.',
            'button_text' => $content['button_text'] ?? 'Build My Stack',
            'button_url'  => $buttonUrl,
        
            'promo_image' => $promoImgUrl,])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'fitpeak';
    }
}
