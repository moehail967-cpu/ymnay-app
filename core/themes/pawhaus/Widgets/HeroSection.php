<?php

namespace Themes\Pawhaus\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class HeroSection extends BaseWidget
{
    protected function getWidgetType(): string { return 'pawhaus_hero_section'; }
    protected function getWidgetName(): string { return 'PawHaus: Hero Section'; }
    protected function getWidgetIcon(): string|array { return 'las la-home'; }
    protected function getWidgetDescription(): string { return __('Full-width hero banner for pet supplies store'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['hero', 'banner', 'pawhaus']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('eyebrow', FieldManager::TEXT()
                ->setLabel('Eyebrow / Badge Text')
                ->setDefault('Your Pet Deserves the Best'))
            ->registerField('title', FieldManager::TEXTAREA()
                ->setLabel('Title (HTML allowed)')
                ->setDefault('Happy Pets,<br>Happy <span>Home</span>.'))
            ->registerField('subtitle', FieldManager::TEXTAREA()
                ->setLabel('Subtitle')
                ->setDefault('Natural, nutritious food and thoughtfully designed accessories for dogs, cats, and small pets. Because they\'re family.'))
            ->registerField('button_text', FieldManager::TEXT()
                ->setLabel('Primary Button Text')
                ->setDefault('Shop for Dogs'))
            ->registerField('button_url', FieldManager::URL()
                ->setLabel('Primary Button URL')
                ->setDefault('#'))
            ->registerField('button2_text', FieldManager::TEXT()
                ->setLabel('Secondary Button Text')
                ->setDefault('Shop for Cats'))
            ->registerField('button2_url', FieldManager::URL()
                ->setLabel('Secondary Button URL')
                ->setDefault('#'))
            ->endGroup();

        $control->addGroup('media', 'Media')
            ->registerField('hero_image', FieldManager::IMAGE()
                ->setLabel('Background / Hero Image'))
            ->registerField('product_image', FieldManager::IMAGE()
                ->setLabel('Product / Pet Image (right side)'))
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
                    '{{WRAPPER}} .ph-hero-widget' => 'padding-top: {{VALUE.TOP}}{{UNIT}}; padding-bottom: {{VALUE.BOTTOM}}{{UNIT}};',
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
                        <p style="margin:0;font-size:14px;">PawHaus: Hero Section — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];
        $media   = $settings['general']['media']   ?? [];

        $hero_img_url    = null;
        $product_img_url = null;

        $heroVal = $media['hero_image'] ?? null;
        if (is_array($heroVal)) {
            $hero_img_url = $heroVal['url'] ?? null;
        } elseif (!empty($heroVal)) {
            $d = get_attachment_image_by_id((int) $heroVal);
            $hero_img_url = $d['img_url'] ?? null;
        }

        $prodVal = $media['product_image'] ?? null;
        if (is_array($prodVal)) {
            $product_img_url = $prodVal['url'] ?? null;
        } elseif (!empty($prodVal)) {
            $d = get_attachment_image_by_id((int) $prodVal);
            $product_img_url = $d['img_url'] ?? null;
        }
        if (empty($hero_img_url)) {
            $hero_img_url = global_asset('core/' . theme_assets('images/pawhaus-hero.jpg', 'pawhaus'));
        }
        if (empty($product_img_url)) {
            $product_img_url = global_asset('core/' . theme_assets('images/pet-food-bag.jpg', 'pawhaus'));
        }

        $btnUrl  = $content['button_url']  ?? '#';
        $btn2Url = $content['button2_url'] ?? '#';
        if (is_array($btnUrl))  $btnUrl  = $btnUrl['url']  ?? '#';
        if (is_array($btn2Url)) $btn2Url = $btn2Url['url'] ?? '#';

        return view('theme-pawhaus::widgets.hero_section', [
            'eyebrow'       => $content['eyebrow']      ?? 'Your Pet Deserves the Best',
            'title'         => $content['title']        ?? 'Happy Pets,<br>Happy <span>Home</span>.',
            'subtitle'      => $content['subtitle']     ?? 'Natural, nutritious food and thoughtfully designed accessories for dogs, cats, and small pets.',
            'button_text'   => $content['button_text']  ?? 'Shop for Dogs',
            'button_url'    => $btnUrl,
            'button2_text'  => $content['button2_text'] ?? 'Shop for Cats',
            'button2_url'   => $btn2Url,
            'hero_image'    => $hero_img_url,
            'product_image' => $product_img_url,
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'pawhaus';
    }
}
