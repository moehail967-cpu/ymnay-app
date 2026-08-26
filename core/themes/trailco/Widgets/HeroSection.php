<?php

namespace Themes\Trailco\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class HeroSection extends BaseWidget
{
    protected function getWidgetType(): string { return 'trailco_hero_section'; }
    protected function getWidgetName(): string { return 'TrailCo: Hero Section'; }
    protected function getWidgetIcon(): string|array { return 'mdi mdi-terrain'; }
    protected function getWidgetDescription(): string { return __('Full-width outdoor hero with dark bark background, olive accents and CTA buttons'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['hero', 'banner', 'trailco', 'outdoor']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('category_tag', FieldManager::TEXT()->setLabel('Activity Tag')->setDefault('Trail Ready'))
            ->registerField('title', FieldManager::TEXTAREA()->setLabel('Title (HTML allowed)')->setDefault('Conquer Every<br><span class="accent">Trail</span>'))
            ->registerField('subtitle', FieldManager::TEXTAREA()->setLabel('Subtitle')->setDefault('Gear built for the toughest terrain. From basecamp to summit.'))
            ->registerField('button_text', FieldManager::TEXT()->setLabel('Primary Button Text')->setDefault('Shop Gear'))
            ->registerField('button_url', FieldManager::URL()->setLabel('Primary Button URL')->setDefault('#'))
            ->registerField('button2_text', FieldManager::TEXT()->setLabel('Secondary Button Text')->setDefault('View Collections'))
            ->registerField('button2_url', FieldManager::URL()->setLabel('Secondary Button URL')->setDefault('#'))
            ->endGroup();

        $control->addGroup('media', 'Media')
            ->registerField('hero_image', FieldManager::IMAGE()->setLabel('Background Image'))
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
                        <p style="margin:0;font-size:14px;">TrailCo: Hero Section — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];
        $media   = $settings['general']['media']   ?? [];
        $spacing = $settings['style']['spacing']   ?? [];

        $hero_img = null;
        if (!empty($media['hero_image'])) {
            $raw = $media['hero_image'];
            $id  = is_array($raw) ? ($raw['id'] ?? null) : $raw;
            $d   = get_attachment_image_by_id($id);
            $hero_img = $d['img_url'] ?? null;
        }
        if (empty($hero_img)) {
            $hero_img = global_asset('core/' . theme_assets('images/trailco-hero.jpg', 'trailco'));
        }

        $rawUrl1 = $content['button_url']  ?? '#';
        $rawUrl2 = $content['button2_url'] ?? '#';

        return view('theme-trailco::widgets.hero_section', [
            'category_tag'   => $content['category_tag'] ?? 'Trail Ready',
            'title'          => $content['title']        ?? 'Conquer Every<br><span class="accent">Trail</span>',
            'subtitle'       => $content['subtitle']     ?? '',
            'button_text'    => $content['button_text']  ?? 'Shop Gear',
            'button_url'     => is_array($rawUrl1) ? ($rawUrl1['url'] ?? '#') : $rawUrl1,
            'button2_text'   => $content['button2_text'] ?? 'View Collections',
            'button2_url'    => is_array($rawUrl2) ? ($rawUrl2['url'] ?? '#') : $rawUrl2,
            'hero_image'     => $hero_img,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 0),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 0),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'trailco';
    }
}
