<?php

namespace Themes\Velvetlux\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class HeroSection extends BaseWidget
{
    protected function getWidgetType(): string { return 'velvetlux_hero_section'; }
    protected function getWidgetName(): string { return 'VelvetLux: Hero Section'; }
    protected function getWidgetIcon(): string|array { return 'las la-star'; }
    protected function getWidgetDescription(): string { return __('Cinematic full-width hero with plum gradient and champagne accents'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['hero', 'banner', 'velvetlux']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('season_text', FieldManager::TEXT()
                ->setLabel('Season / Label Text')
                ->setDefault('Autumn / Winter 2025'))
            ->registerField('title', FieldManager::TEXTAREA()
                ->setLabel('Title (HTML allowed)')
                ->setDefault('Dress for the Life<br>You <span>Deserve.</span>'))
            ->registerField('subtitle', FieldManager::TEXTAREA()
                ->setLabel('Subtitle')
                ->setDefault('Timeless elegance for the modern woman. Discover pieces crafted for those who demand the extraordinary.'))
            ->registerField('button_text', FieldManager::TEXT()
                ->setLabel('Primary Button Text')
                ->setDefault('Shop Women'))
            ->registerField('button_url', FieldManager::URL()
                ->setLabel('Primary Button URL')
                ->setDefault('#'))
            ->registerField('button2_text', FieldManager::TEXT()
                ->setLabel('Secondary Button Text')
                ->setDefault('Shop Men'))
            ->registerField('button2_url', FieldManager::URL()
                ->setLabel('Secondary Button URL')
                ->setDefault('#'))
            ->endGroup();

        $control->addGroup('media', 'Media')
            ->registerField('hero_image', FieldManager::IMAGE()
                ->setLabel('Hero Editorial Image'))
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
        // Not in tenant context (editor preview API has no tenant middleware)
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;">VelvetLux: Hero Section — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];
        $media   = $settings['general']['media']   ?? [];
        $spacing = $settings['style']['spacing']   ?? [];

        $rawUrl1 = $content['button_url'] ?? '#';
        $rawUrl2 = $content['button2_url'] ?? '#';
        $button_url  = is_array($rawUrl1) ? ($rawUrl1['url'] ?? '#') : ($rawUrl1 ?: '#');
        $button2_url = is_array($rawUrl2) ? ($rawUrl2['url'] ?? '#') : ($rawUrl2 ?: '#');

        $hero_img_url = null;
        if (!empty($media['hero_image'])) {
            $img_id = is_array($media['hero_image']) ? ($media['hero_image']['id'] ?? null) : $media['hero_image'];
            if ($img_id) {
                $d = get_attachment_image_by_id($img_id);
                $hero_img_url = $d['img_url'] ?? null;
            }
        }
        if (empty($hero_img_url)) {
            $hero_img_url = global_asset('core/' . theme_assets('images/velvetlux-hero.jpg', 'velvetlux'));
        }

        return view('theme-velvetlux::widgets.hero_section', [
            'season_text'    => $content['season_text']  ?? 'Autumn / Winter 2025',
            'title'          => $content['title']        ?? 'Dress for the Life<br>You <span>Deserve.</span>',
            'subtitle'       => $content['subtitle']     ?? 'Timeless elegance for the modern woman.',
            'button_text'    => $content['button_text']  ?? 'Shop Women',
            'button_url'     => $button_url,
            'button2_text'   => $content['button2_text'] ?? 'Shop Men',
            'button2_url'    => $button2_url,
            'hero_image'     => $hero_img_url,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 0),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 0),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'velvetlux';
    }
}
