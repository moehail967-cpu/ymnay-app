<?php

namespace Themes\Chefhome\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class HeroSection extends BaseWidget
{
    protected function getWidgetType(): string { return 'chefhome_hero_section'; }
    protected function getWidgetName(): string { return 'ChefHome: Hero Section'; }
    protected function getWidgetIcon(): string|array { return 'las la-home'; }
    protected function getWidgetDescription(): string { return __('Full-width hero banner with gradient background, dish image and CTA buttons'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['hero', 'banner', 'chefhome']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('badge_text', FieldManager::TEXT()
                ->setLabel('Badge Text')
                ->setDefault("Chef's Pick of the Week"))
            ->registerField('title', FieldManager::TEXTAREA()
                ->setLabel('Title (HTML allowed)')
                ->setDefault('Fresh, Homemade<br>& Delivered to'))
            ->registerField('title_accent', FieldManager::TEXT()
                ->setLabel('Accent Word (highlighted in amber)')
                ->setDefault('You'))
            ->registerField('subtitle', FieldManager::TEXTAREA()
                ->setLabel('Subtitle')
                ->setDefault('Order from our kitchen — quality ingredients, chef-crafted recipes, delivered hot to your door.'))
            ->registerField('button_text', FieldManager::TEXT()
                ->setLabel('Primary Button Text')
                ->setDefault('Order Now'))
            ->registerField('button_url', FieldManager::URL()
                ->setLabel('Primary Button URL')
                ->setDefault('#'))
            ->registerField('button2_text', FieldManager::TEXT()
                ->setLabel('Secondary Button Text')
                ->setDefault('View Full Menu'))
            ->registerField('button2_url', FieldManager::URL()
                ->setLabel('Secondary Button URL')
                ->setDefault('#'))
            ->endGroup();

        $control->addGroup('media', 'Media')
            ->registerField('dish_image', FieldManager::IMAGE()
                ->setLabel('Hero Image (fills the entire banner)'))
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top', FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(24)->setMin(0)->setMax(200))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(24)->setMin(0)->setMax(200))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {

        // Not in tenant context (editor preview API has no tenant middleware)
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;">ChefHome: Hero Section — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];
        $media   = $settings['general']['media']   ?? [];
        $spacing = $settings['style']['spacing']   ?? [];

        $dishImageField = $media['dish_image'] ?? null;
        $dish_img_url = null;
        if (is_array($dishImageField)) {
            $dish_img_url = $dishImageField['img_url'] ?? $dishImageField['url'] ?? null;
        } elseif (!empty($dishImageField)) {
            $d = get_attachment_image_by_id($dishImageField);
            $dish_img_url = $d['img_url'] ?? null;
        }
        if (empty($dish_img_url)) {
            $dish_img_url = global_asset('core/' . theme_assets('images/burger.jpg', 'chefhome'));
        }

        $rawUrl1 = $content['button_url']  ?? '#';
        $rawUrl2 = $content['button2_url'] ?? '#';

        return view('theme-chefhome::widgets.hero_section', [
            'badge_text'     => $content['badge_text']   ?? "Chef's Pick of the Week",
            'title'          => $content['title']        ?? 'Fresh, Homemade<br>& Delivered to',
            'title_accent'   => $content['title_accent'] ?? 'You',
            'subtitle'       => $content['subtitle']     ?? '',
            'button_text'    => $content['button_text']  ?? 'Order Now',
            'button_url'     => is_array($rawUrl1) ? ($rawUrl1['url'] ?? '#') : $rawUrl1,
            'button2_text'   => $content['button2_text'] ?? 'View Full Menu',
            'button2_url'    => is_array($rawUrl2) ? ($rawUrl2['url'] ?? '#') : $rawUrl2,
            'dish_image'     => $dish_img_url,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 24),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 24),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'chefhome';
    }
}
