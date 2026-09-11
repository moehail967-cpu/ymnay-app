<?php

namespace Themes\Freshmart\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class HeroSection extends BaseWidget
{
    protected function getWidgetType(): string { return 'freshmart_hero_section'; }
    protected function getWidgetName(): string { return 'FreshMart: Hero Section'; }
    protected function getWidgetIcon(): string|array { return 'las la-home'; }
    protected function getWidgetDescription(): string { return __('Full-width hero banner with image and CTA buttons'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['hero', 'banner', 'freshmart']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('eyebrow', FieldManager::TEXT()
                ->setLabel('Eyebrow Text')
                ->setDefault('Fresh. Local. Delivered.'))
            ->registerField('title', FieldManager::TEXTAREA()
                ->setLabel('Title (HTML allowed)')
                ->setDefault('Your Neighbourhood<br><em>Grocery Store</em>'))
            ->registerField('subtitle', FieldManager::TEXTAREA()
                ->setLabel('Subtitle')
                ->setDefault('Fresh produce, pantry essentials, and specialty items — delivered to your door.'))
            ->registerField('button_text', FieldManager::TEXT()
                ->setLabel('Primary Button Text')
                ->setDefault('Shop Now'))
            ->registerField('button_url', FieldManager::URL()
                ->setLabel('Primary Button URL')
                ->setDefault('#'))
            ->registerField('button2_text', FieldManager::TEXT()
                ->setLabel('Secondary Button Text')
                ->setDefault('View Offers'))
            ->registerField('button2_url', FieldManager::URL()
                ->setLabel('Secondary Button URL')
                ->setDefault('#'))
            ->registerField('stat1_value', FieldManager::TEXT()
                ->setLabel('Stat 1 Value')->setDefault('500+'))
            ->registerField('stat1_label', FieldManager::TEXT()
                ->setLabel('Stat 1 Label')->setDefault('Organic Products'))
            ->registerField('stat2_value', FieldManager::TEXT()
                ->setLabel('Stat 2 Value')->setDefault('48h'))
            ->registerField('stat2_label', FieldManager::TEXT()
                ->setLabel('Stat 2 Label')->setDefault('Farm to Door'))
            ->registerField('stat3_value', FieldManager::TEXT()
                ->setLabel('Stat 3 Value')->setDefault('4.8★'))
            ->registerField('stat3_label', FieldManager::TEXT()
                ->setLabel('Stat 3 Label')->setDefault('Customer Rating'))
            ->endGroup();

        $control->addGroup('media', 'Media')
            ->registerField('hero_image', FieldManager::IMAGE()
                ->setLabel('Hero Background Image'))
            ->registerField('product_image', FieldManager::IMAGE()
                ->setLabel('Product / Feature Image (right side)'))
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
                    '{{WRAPPER}} .fm-hero-widget' => 'padding-top: {{VALUE.TOP}}{{UNIT}}; padding-bottom: {{VALUE.BOTTOM}}{{UNIT}};',
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
                        <p style="margin:0;font-size:14px;">FreshMart: Hero Section — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];
        $media   = $settings['general']['media']   ?? [];

        $hero_img_url    = null;
        $product_img_url = null;

        if (!empty($media['hero_image'])) {
            $val = $media['hero_image'];
            if (is_numeric($val)) {
                $d = get_attachment_image_by_id((int) $val);
                $hero_img_url = $d['img_url'] ?? null;
            } elseif (is_array($val)) {
                $hero_img_url = $val['url'] ?? $val['img_url'] ?? null;
                if (empty($hero_img_url) && !empty($val['id'])) {
                    $d = get_attachment_image_by_id((int) $val['id']);
                    $hero_img_url = $d['img_url'] ?? null;
                }
            }
        }
        if (!empty($media['product_image'])) {
            $val = $media['product_image'];
            if (is_numeric($val)) {
                $d = get_attachment_image_by_id((int) $val);
                $product_img_url = $d['img_url'] ?? null;
            } elseif (is_array($val)) {
                $product_img_url = $val['url'] ?? $val['img_url'] ?? null;
                if (empty($product_img_url) && !empty($val['id'])) {
                    $d = get_attachment_image_by_id((int) $val['id']);
                    $product_img_url = $d['img_url'] ?? null;
                }
            }
        }
        if (empty($hero_img_url)) {
            $hero_img_url = global_asset('core/' . theme_assets('images/fresh-produce-hero.jpg', 'freshmart'));
        }
        if (empty($product_img_url)) {
            $product_img_url = global_asset('core/' . theme_assets('images/grocery-basket.jpg', 'freshmart'));
        }

        $buttonUrl = $content['button_url'] ?? null;
        if (is_array($buttonUrl)) { $buttonUrl = $buttonUrl['url'] ?? '#'; }
        if (empty($buttonUrl)) { $buttonUrl = '#'; }

        $button2Url = $content['button2_url'] ?? null;
        if (is_array($button2Url)) { $button2Url = $button2Url['url'] ?? '#'; }
        if (empty($button2Url)) { $button2Url = '#'; }

        return view('theme-freshmart::widgets.hero_section', [
            'eyebrow'        => $content['eyebrow']      ?? '100% Certified Organic',
            'title'          => $content['title']        ?? 'Fresh. Organic.<br><span>Delivered</span> to<br>Your Door.',
            'subtitle'       => $content['subtitle']     ?? 'Farm-to-table groceries sourced directly from certified organic farms. No middlemen, no compromise.',
            'button_text'    => $content['button_text']  ?? 'Shop Now',
            'button_url'     => $buttonUrl,
            'button2_text'   => $content['button2_text'] ?? 'Weekly Box',
            'button2_url'    => $button2Url,
            'hero_image'     => $hero_img_url,
            'product_image'  => $product_img_url,
            'stat1_value'    => $content['stat1_value']  ?? '500+',
            'stat1_label'    => $content['stat1_label']  ?? 'Organic Products',
            'stat2_value'    => $content['stat2_value']  ?? '48h',
            'stat2_label'    => $content['stat2_label']  ?? 'Farm to Door',
            'stat3_value'    => $content['stat3_value']  ?? '4.8★',
            'stat3_label'    => $content['stat3_label']  ?? 'Customer Rating',
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'freshmart';
    }
}
