<?php

namespace Themes\Aromatic\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class HeroSection extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'aromatic_hero_section';
    }

    protected function getWidgetName(): string
    {
        return 'Aromatic: Hero Section';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-flask';
    }

    protected function getWidgetDescription(): string
    {
        return __('Full-width luxury hero banner with fragrance imagery and CTA buttons');
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['hero', 'banner', 'aromatic', 'perfume'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('section_tag', FieldManager::TEXT()->setLabel('Section Tag')->setDefault('New Arrivals'))
            ->registerField('title', FieldManager::TEXTAREA()->setLabel('Title (HTML allowed)')->setDefault('Discover Your <br>Signature Scent'))
            ->registerField('subtitle', FieldManager::TEXTAREA()->setLabel('Subtitle')->setDefault('Explore our curated collection of premium fragrances crafted for every mood.'))
            ->registerField('button_text', FieldManager::TEXT()->setLabel('Primary Button Text')->setDefault('Shop Collection'))
            ->registerField('button_url', FieldManager::URL()->setLabel('Primary Button URL')->setDefault('#'))
            ->registerField('button2_text', FieldManager::TEXT()->setLabel('Secondary Button Text')->setDefault('Learn More'))
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
            ->registerField('padding_top', FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(0)->setMin(0)->setMax(200))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(0)->setMin(0)->setMax(200))
            ->endGroup();

        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;">Aromatic: Hero Section — preview on the live page</p>
                    </div>';
        }

        try {
            $content = $settings['general']['content'] ?? [];
            $media   = $settings['general']['media'] ?? [];
            $spacing = $settings['style']['spacing'] ?? [];

            $hero_img   = null;
            $imageField = $media['hero_image'] ?? null;
            if (is_array($imageField)) {
                $hero_img = $imageField['img_url'] ?? $imageField['url'] ?? null;
                if (empty($hero_img) && !empty($imageField['id'])) {
                    $d = get_attachment_image_by_id((int) $imageField['id']);
                    $hero_img = $d['img_url'] ?? null;
                }
            } elseif (!empty($imageField)) {
                $d = get_attachment_image_by_id((int) $imageField);
                $hero_img = $d['img_url'] ?? null;
            }

            if (empty($hero_img)) {
                $hero_img = global_asset('core/' . theme_assets('images/hero-banner.png', 'aromatic'));
            }

            $rawUrl1 = $content['button_url'] ?? '#';
            $rawUrl2 = $content['button2_url'] ?? '#';

            return view('theme-aromatic::widgets.hero_section', [
                'section_tag'    => $content['section_tag'] ?? 'New Arrivals',
                'title'          => $content['title'] ?? 'Discover Your <br>Signature Scent',
                'subtitle'       => $content['subtitle'] ?? 'Explore our curated collection of premium fragrances crafted for every mood.',
                'button_text'    => $content['button_text'] ?? 'Shop Collection',
                'button_url'     => is_array($rawUrl1) ? ($rawUrl1['url'] ?? '#') : $rawUrl1,
                'button2_text'   => $content['button2_text'] ?? 'Learn More',
                'button2_url'    => is_array($rawUrl2) ? ($rawUrl2['url'] ?? '#') : $rawUrl2,
                'image_url'      => $hero_img,
                'padding_top'    => (int) ($spacing['padding_top'] ?? 0),
                'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 0),
            ])->render();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('aromatic_hero_section render failed: ' . $e->getMessage(), [
                'file' => $e->getFile(), 'line' => $e->getLine(),
            ]);
            return '<div style="padding:20px;text-align:center;color:#c00;font-size:13px;font-family:sans-serif;">Hero Section: preview unavailable</div>';
        }
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'aromatic';
    }
}
