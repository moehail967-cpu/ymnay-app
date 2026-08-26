<?php

namespace Themes\Goldcraft\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class HeroSection extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'goldcraft_hero_section';
    }

    protected function getWidgetName(): string
    {
        return 'Goldcraft: Hero Section';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-home';
    }

    protected function getWidgetDescription(): string
    {
        return __('Full-width hero banner with image and CTA buttons');
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['hero', 'banner', 'goldcraft'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('badge_text', FieldManager::TEXT()->setLabel('Badge Text')->setDefault('Handcrafted Since 1985'))
            ->registerField('title', FieldManager::TEXTAREA()->setLabel('Title (HTML allowed)')->setDefault('Timeless<br>Beauty in Gold'))
            ->registerField('subtitle', FieldManager::TEXTAREA()->setLabel('Subtitle')->setDefault('Bespoke jewelry and artisan crafts for the discerning collector.'))
            ->registerField('button_text', FieldManager::TEXT()->setLabel('Primary Button Text')->setDefault('Explore Collection'))
            ->registerField('button_url', FieldManager::URL()->setLabel('Primary Button URL')->setDefault('#'))
            ->registerField('button2_text', FieldManager::TEXT()->setLabel('Secondary Button Text')->setDefault('Custom Orders'))
            ->registerField('button2_url', FieldManager::URL()->setLabel('Secondary Button URL')->setDefault('#'))
            ->endGroup();

        $control->addGroup('media', 'Media')
            ->registerField('hero_image', FieldManager::IMAGE()->setLabel('Hero Image'))
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
                    '{{WRAPPER}} .gc-widget-hero' => 'padding-top: {{VALUE.TOP}}{{UNIT}}; padding-bottom: {{VALUE.BOTTOM}}{{UNIT}};',
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
                        <p style="margin:0;font-size:14px;">Goldcraft: Hero Section — preview on the live page</p>
                    </div>';
        }
        $content  = $settings['general']['content'] ?? [];
        $media    = $settings['general']['media']   ?? [];

        $hero_img = null;
        if (!empty($media['hero_image'])) {
            $val = $media['hero_image'];
            if (is_int($val) || is_string($val)) {
                $d = get_attachment_image_by_id((int) $val);
                $hero_img = $d['img_url'] ?? null;
            } else {
                $hero_img = $val['url'] ?? $val['img_url'] ?? null;
                if (empty($hero_img) && !empty($val['id'])) {
                    $d = get_attachment_image_by_id((int) $val['id']);
                    $hero_img = $d['img_url'] ?? null;
                }
            }
        }
        if (empty($hero_img)) {
            $hero_img = global_asset('core/' . theme_assets('images/jewellery-hero.jpg', 'goldcraft'));
        }

        $button_url_raw  = $content['button_url']  ?? '#';
        $button_url      = is_array($button_url_raw)  ? ($button_url_raw['url']  ?? '#') : $button_url_raw;
        $button2_url_raw = $content['button2_url'] ?? '#';
        $button2_url     = is_array($button2_url_raw) ? ($button2_url_raw['url'] ?? '#') : $button2_url_raw;

        return view('theme-goldcraft::widgets.hero_section', [
            'badge_text'   => $content['badge_text']   ?? 'Handcrafted Since 1985',
            'title'        => $content['title']        ?? 'Timeless<br>Beauty in Gold',
            'subtitle'     => $content['subtitle']     ?? 'Bespoke jewelry and artisan crafts for the discerning collector.',
            'button_text'  => $content['button_text']  ?? 'Explore Collection',
            'button_url'   => $button_url,
            'button2_text' => $content['button2_text'] ?? 'Custom Orders',
            'button2_url'  => $button2_url,
            'hero_image'   => $hero_img,
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'goldcraft';
    }
}
