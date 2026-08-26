<?php

namespace Themes\Furnito\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class HeroSection extends BaseWidget
{
    protected function getWidgetType(): string { return 'furnito_hero_section'; }
    protected function getWidgetName(): string { return 'Furnito: Hero Section'; }
    protected function getWidgetIcon(): string|array { return 'las la-couch'; }
    protected function getWidgetDescription(): string { return __('Slider hero: multiple slides, left text + right product image'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['hero', 'banner', 'slider', 'furnito']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('slides', 'Slides')
            ->registerField('slides', FieldManager::REPEATER()
                ->setLabel('Hero Slides')
                ->setItemLabel('Slide')
                ->setAddButtonText('Add Slide')
                ->setMin(1)->setMax(8)
                ->setFields([
                    'title'       => FieldManager::TEXTAREA()->setLabel('Title')->setDefault('Decorate Your Home'),
                    'description' => FieldManager::TEXTAREA()->setLabel('Description')->setDefault('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Feugiat pulvinar enim hendrerit pellentesque Pharetra consectetur quam.'),
                    'button_text' => FieldManager::TEXT()->setLabel('Button Text')->setDefault('Shop Now'),
                    'button_url'  => FieldManager::TEXT()->setLabel('Button URL')->setDefault('#'),
                    'hero_image'  => FieldManager::IMAGE()->setLabel('Hero Image'),
                ])
                ->setDefault([
                    [
                        'title'       => 'Decorate Your Home',
                        'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Feugiat pulvinar enim hendrerit pellentesque Pharetra consectetur quam.',
                        'button_text' => 'Shop Now',
                        'button_url'  => '#',
                        'hero_image'  => null,
                    ],
                ])
            )
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('min_height', FieldManager::NUMBER()->setLabel('Min Height (px)')->setDefault(520)->setMin(300)->setMax(900))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#EEF3F7;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">Furnito: Hero Section — preview on the live page</p></div>';
        }

        $slidesGroup = $settings['general']['slides'] ?? [];
        $spacing     = $settings['style']['spacing']  ?? [];
        $rawSlides   = $slidesGroup['slides'] ?? [];

        // Fallback default slide
        if (empty($rawSlides)) {
            $rawSlides = [[
                'title'       => 'Decorate Your Home',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Feugiat pulvinar enim hendrerit pellentesque Pharetra consectetur quam.',
                'button_text' => 'Shop Now',
                'button_url'  => '#',
                'hero_image'  => null,
            ]];
        }

        // Resolve images for each slide
        $slides = [];
        foreach ($rawSlides as $slide) {
            $img        = null;
            $imageField = $slide['hero_image'] ?? null;

            if (is_array($imageField)) {
                $img = $imageField['img_url'] ?? $imageField['url'] ?? null;
                if (empty($img) && !empty($imageField['id'])) {
                    $d   = get_attachment_image_by_id((int) $imageField['id']);
                    $img = $d['img_url'] ?? null;
                }
            } elseif (!empty($imageField)) {
                $d   = get_attachment_image_by_id((int) $imageField);
                $img = $d['img_url'] ?? null;
            }

            if (empty($img)) {
                $img = global_asset('core/' . theme_assets('images/hero-banner.jpg', 'furnito'));
            }

            $btnUrl = $slide['button_url'] ?? '#';
            if (is_array($btnUrl)) {
                $btnUrl = $btnUrl['url'] ?? '#';
            }

            $slides[] = [
                'title'       => $slide['title']       ?? 'Decorate Your Home',
                'description' => $slide['description'] ?? '',
                'button_text' => $slide['button_text'] ?? 'Shop Now',
                'button_url'  => $btnUrl,
                'hero_img'    => $img,
            ];
        }

        return view('theme-furnito::widgets.hero_section', [
            'slides'     => $slides,
            'min_height' => (int) ($spacing['min_height'] ?? 520),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'furnito';
    }
}
