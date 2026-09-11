<?php

namespace Themes\Trailco\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class PromoBanner extends BaseWidget
{
    protected function getWidgetType(): string { return 'trailco_promo_banner'; }
    protected function getWidgetName(): string { return 'TrailCo: Promo Banner'; }
    protected function getWidgetIcon(): string|array { return 'mdi mdi-tent'; }
    protected function getWidgetDescription(): string { return __('Olive-green full-width promo banner with heading, subtitle, CTA button and decorative emoji icons'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['promo', 'banner', 'trailco', 'outdoor']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()->setLabel('Title')->setDefault('Build Your Kit. Own the Trail.'))
            ->registerField('subtitle', FieldManager::TEXTAREA()->setLabel('Subtitle')->setDefault('From boot selection to layering systems — get a personalised gear recommendation from our outdoor experts. Free, no obligation.'))
            ->registerField('button_text', FieldManager::TEXT()->setLabel('Button Text')->setDefault('Talk to an Expert'))
            ->registerField('button_url', FieldManager::TEXT()->setLabel('Button URL')->setDefault('#'))
            ->registerField('promo_image', FieldManager::IMAGE()
                ->setLabel('Right Side Image (optional)'))
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top', FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(0)->setMin(0)->setMax(200))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(0)->setMin(0)->setMax(200))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        $content = $settings['general']['content'] ?? [];
        $spacing = $settings['style']['spacing']   ?? [];

        $button_url = $content['button_url'] ?? '#';
        if (is_array($button_url)) {
            $button_url = $button_url['url'] ?? '#';
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

        if (empty($promoImgUrl)) {
            $promoImgUrl = global_asset('core/' . theme_assets('images/tent-camping.jpg', 'trailco'));
        }

        return view('theme-trailco::widgets.promo_banner', [
            'title'          => $content['title']       ?? 'Build Your Kit. Own the Trail.',
            'subtitle'       => $content['subtitle']    ?? 'From boot selection to layering systems — get a personalised gear recommendation from our outdoor experts. Free, no obligation.',
            'button_text'    => $content['button_text'] ?? 'Talk to an Expert',
            'button_url'     => $button_url,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 0),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 0),
        
            'promo_image' => $promoImgUrl,])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'trailco';
    }
}
