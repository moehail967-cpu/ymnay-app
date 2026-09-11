<?php

namespace Themes\Kidville\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class HeroSection extends BaseWidget
{
    protected function getWidgetType(): string { return 'kidville_hero_section'; }
    protected function getWidgetName(): string { return 'KidVille: Hero Section'; }
    protected function getWidgetIcon(): string|array { return 'las la-home'; }
    protected function getWidgetDescription(): string { return __('Full-width hero banner with playful design for kids store'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['hero', 'banner', 'kidville']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('badge_text', FieldManager::TEXT()
                ->setLabel('Badge Text')
                ->setDefault('🎁 New Arrivals Every Week'))
            ->registerField('title', FieldManager::TEXTAREA()
                ->setLabel('Title (HTML allowed — wrap red word in <span class="kv-red">, blue in <span class="kv-blue">)')
                ->setDefault('Play, Learn<br>& <span class="kv-red">Grow</span> with <span class="kv-blue">Joy</span>!'))
            ->registerField('subtitle', FieldManager::TEXTAREA()
                ->setLabel('Subtitle')
                ->setDefault('The best toys, games, and kids fashion for every age. Safe, fun, and educational!'))
            ->registerField('button_text', FieldManager::TEXT()
                ->setLabel('Primary Button Text')
                ->setDefault('Shop All Toys'))
            ->registerField('button_url', FieldManager::URL()
                ->setLabel('Primary Button URL')
                ->setDefault('#'))
            ->registerField('button2_text', FieldManager::TEXT()
                ->setLabel('Secondary Button Text')
                ->setDefault('Gift Finder'))
            ->registerField('button2_url', FieldManager::URL()
                ->setLabel('Secondary Button URL')
                ->setDefault('#'))
            ->endGroup();

        $control->addGroup('media', 'Media')
            ->registerField('hero_image', FieldManager::IMAGE()
                ->setLabel('Background Image (optional)'))
            ->registerField('hero_image_right', FieldManager::IMAGE()
                ->setLabel('Right Side Image'))
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('section_padding', FieldManager::DIMENSION()
                ->asPadding()
                ->setLabel('Section Padding')
                ->setDefault(['top' => 80, 'right' => 0, 'bottom' => 80, 'left' => 0])
                ->setSelectors([
                    '{{WRAPPER}} .kv-hero-widget' => 'padding-top: {{VALUE.TOP}}{{UNIT}}; padding-bottom: {{VALUE.BOTTOM}}{{UNIT}};',
                ]))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;">KidVille: Hero Section — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];
        $media   = $settings['general']['media']   ?? [];

        $hero_img_url  = null;
        $right_img_url = null;

        if (!empty($media['hero_image'])) {
            $val = $media['hero_image'];
            if (is_int($val) || is_string($val)) {
                $d = get_attachment_image_by_id((int) $val);
                $hero_img_url = $d['img_url'] ?? null;
            } else {
                $hero_img_url = $val['url'] ?? $val['img_url'] ?? null;
                if (empty($hero_img_url) && !empty($val['id'])) {
                    $d = get_attachment_image_by_id((int) $val['id']);
                    $hero_img_url = $d['img_url'] ?? null;
                }
            }
        }
        if (!empty($media['hero_image_right'])) {
            $val = $media['hero_image_right'];
            if (is_int($val) || is_string($val)) {
                $d = get_attachment_image_by_id((int) $val);
                $right_img_url = $d['img_url'] ?? null;
            } else {
                $right_img_url = $val['url'] ?? $val['img_url'] ?? null;
                if (empty($right_img_url) && !empty($val['id'])) {
                    $d = get_attachment_image_by_id((int) $val['id']);
                    $right_img_url = $d['img_url'] ?? null;
                }
            }
        }
        if (empty($hero_img_url)) {
            $hero_img_url = global_asset('core/' . theme_assets('images/kids-toys-hero.jpg', 'kidville'));
        }
        if (empty($right_img_url)) {
            $right_img_url = global_asset('core/' . theme_assets('images/kids-clothing.jpg', 'kidville'));
        }

        $raw         = $content['button_url']  ?? '#';
        $button_url  = is_array($raw) ? ($raw['url'] ?? '#') : $raw;
        $raw         = $content['button2_url'] ?? '#';
        $button2_url = is_array($raw) ? ($raw['url'] ?? '#') : $raw;

        return view('theme-kidville::widgets.hero_section', [
            'badge_text'       => $content['badge_text']    ?? '🎁 New Arrivals Every Week',
            'title'            => $content['title']         ?? 'Play, Learn<br>& <span class="kv-red">Grow</span> with <span class="kv-blue">Joy</span>!',
            'subtitle'         => $content['subtitle']      ?? 'The best toys, games, and kids fashion for every age.',
            'button_text'      => $content['button_text']   ?? 'Shop All Toys',
            'button_url'       => $button_url,
            'button2_text'     => $content['button2_text']  ?? 'Gift Finder',
            'button2_url'      => $button2_url,
            'hero_image'       => $hero_img_url,
            'hero_image_right' => $right_img_url,
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'kidville';
    }

}
