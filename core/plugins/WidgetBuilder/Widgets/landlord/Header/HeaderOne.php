<?php

namespace Plugins\WidgetBuilder\Widgets\landlord\Header;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class HeaderOne extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'header_one';
    }

    protected function getWidgetName(): string
    {
        return 'Header One';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-heading';
    }

    protected function getWidgetDescription(): string
    {
        return __('Hero banner with heading, buttons, and a showcase image carousel slider');
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['header', 'hero', 'banner', 'slider', 'landing'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Title')
                ->setDefault('Build Your {h}E-Commerce{/h} Store Within Minutes')
                ->setDescription('Use {h}text{/h} to highlight words')
            )
            ->registerField('title_shape_svg', FieldManager::TEXTAREA()
                ->setLabel('Title Highlight SVG')
                ->setDefault('')
                ->setDescription('Optional: Custom SVG code for the text highlight. Leave empty to use default.')
            )
            ->registerField('title_shape_color', FieldManager::COLOR()
                ->setLabel('Title Highlight SVG Color')
                ->setDefault('#fff')
            )
            ->registerField('subtitle', FieldManager::TEXT()
                ->setLabel('Subtitle Text')
                ->setDefault('Try Now. No credit card required.')
            )
            ->endGroup();

        $control->addGroup('buttons', 'Buttons')
            ->registerField('primary_button_text', FieldManager::TEXT()
                ->setLabel('Primary Button Text')
                ->setDefault('Start Free Trial')
            )
            ->registerField('primary_button_url', FieldManager::TEXT()
                ->setLabel('Primary Button URL')
                ->setDefault('#')
            )
            ->registerField('secondary_button_text', FieldManager::TEXT()
                ->setLabel('Secondary Button Text')
                ->setDefault('Watch Demo')
            )
            ->registerField('secondary_button_url', FieldManager::TEXT()
                ->setLabel('Secondary Button URL')
                ->setDefault('#')
            )
            ->endGroup();

        $control->addGroup('background', 'Background')
            ->registerField('background_image', FieldManager::IMAGE()
                ->setLabel('Background Image')
                ->setDescription('Dark overlay will be applied automatically')
            )
            ->registerField('background_video', FieldManager::VIDEO()
                ->setLabel('Background Video (optional)')
                ->setDescription('If set, replaces the background image')
            )
            ->endGroup();

        $control->addGroup('showcase', 'Showcase Slider')
            ->registerField('showcase_images', FieldManager::REPEATER()
                ->setLabel('Showcase Images')
                ->setItemLabel('Image')
                ->setAddButtonText('Add Image')
                ->setMin(1)
                ->setMax(20)
                ->setFields([
                    'image' => FieldManager::IMAGE()
                        ->setLabel('Screenshot / Mockup Image')
                        ->setRequired(true),
                ])
            )
            ->endGroup();

        $control->addGroup('slider_settings', 'Slider Settings')
            ->registerField('autoplay', FieldManager::TOGGLE()
                ->setLabel('Auto Play')
                ->setDefault(true)
            )
            ->registerField('autoplay_speed', FieldManager::NUMBER()
                ->setLabel('Auto Play Speed (ms)')
                ->setDefault(4000)
                ->setMin(1000)
                ->setMax(15000)
                ->setStep(500)
            )
            ->registerField('transition_speed', FieldManager::NUMBER()
                ->setLabel('Transition Speed (ms)')
                ->setDefault(500)
                ->setMin(200)
                ->setMax(2000)
                ->setStep(100)
            )
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('section_style', 'Section')
            ->registerField('section_padding',FieldManager::DIMENSION()
                ->setLabel('Margin')
                ->setSides(['top', 'bottom'])
                ->asMargin()                      // preset: sides top/right/bottom/left, units px/em/rem/%
                ->setDefault(['top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px'])
                ->setLinked(true)                  // link all sides together
                ->setAllowNegative(false)
                ->setSelectors(['{{WRAPPER}} .header_one_container_outer'])
            )
            ->endGroup();
        return $control->getFields();
    }

    private function resolveImageUrl($value): string
    {
        if (is_array($value)) {
            return $value['url'] ?? '';
        }
        return is_string($value) ? $value : '';
    }

    public function render(array $settings = []): string
    {
        $general = $settings['general'] ?? [];
        $style = $settings['style'] ?? [];

        // Content
        $content = $general['content'] ?? [];
        $title = $content['title'] ?? 'Build Your {h}E-Commerce{/h} Store Within Minutes';
        $titleShapeSvg = $content['title_shape_svg'] ?? '';
        $titleShapeColor = $content['title_shape_color'] ?? '#fff';

        // Handle legacy span replacement to {h}
        $title = str_replace(['<span class="highlighted-text">', '</span>'], ['{h}', '{/h}'], $title);

        if (!empty($titleShapeSvg)) {
            $shape_svg = $titleShapeSvg;
        } else {
            $shape_svg = '<svg class="absolute left-4 top-6 md:top-14 md:left-4 w-full" viewBox="0 0 263 45" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.50047 21.963C7.50176 20.2914 13.503 18.6198 23.8509 17.0689C34.1989 15.518 48.7115 14.1385 74.5707 13.1822C100.43 12.2258 137.196 11.7343 170.205 12.096C203.214 12.4577 231.351 13.6873 260.342 14.9542" stroke="' . $titleShapeColor . '" stroke-width="3" stroke-linecap="round"/><path d="M2.03562 30.007C8.03691 28.3353 14.0382 26.6637 24.3861 25.1128C34.734 23.5619 49.2467 22.1825 75.1059 21.2261C100.965 20.2698 137.731 19.7783 170.74 20.1399C203.749 20.5016 231.887 21.7313 260.877 22.9982" stroke="' . $titleShapeColor . '" stroke-width="3" stroke-linecap="round"/></svg>';
        }

        $title = str_replace(['{h}', '{/h}'], ['<span class="relative inline-block text-heading">', $shape_svg . '</span>'], $title);

        $subtitle = $content['subtitle'] ?? 'Try Now. No credit card required.';

        // Buttons
        $buttons = $general['buttons'] ?? [];
        $primaryBtnText = $buttons['primary_button_text'] ?? 'Start Free Trial';
        $primaryBtnUrl = $buttons['primary_button_url'] ?? '#';
        $secondaryBtnText = $buttons['secondary_button_text'] ?? 'Watch Demo';
        $secondaryBtnUrl = $buttons['secondary_button_url'] ?? '#';

        // Background
        $bg = $general['background'] ?? [];
        $bgImage = $this->resolveImageUrl($bg['background_image'] ?? '');
        $bgVideo = $this->resolveImageUrl($bg['background_video'] ?? '');

        // Showcase images
        $showcase = $general['showcase'] ?? [];
        $showcaseItems = $showcase['showcase_images'] ?? [];
        $images = [];
        foreach ($showcaseItems as $item) {
            $url = $this->resolveImageUrl($item['image'] ?? '');
            if (!empty($url)) {
                $images[] = $url;
            }
        }

        // Slider settings
        $sliderSettings = $general['slider_settings'] ?? [];
        $autoplay = $sliderSettings['autoplay'] ?? true;
        $autoplaySpeed = (int)($sliderSettings['autoplay_speed'] ?? 4000);
        $transitionSpeed = (int)($sliderSettings['transition_speed'] ?? 500);

        return view('widgetbuilder::landlord.header.header_one', [
            'uid' => 'ho_' . uniqid(),
            'title' => $title,
            'subtitle' => $subtitle,
            'primaryBtnText' => $primaryBtnText,
            'primaryBtnUrl' => $primaryBtnUrl,
            'secondaryBtnText' => $secondaryBtnText,
            'secondaryBtnUrl' => $secondaryBtnUrl,
            'bgImage' => $bgImage,
            'bgVideo' => $bgVideo,
            'images' => $images,
            'autoplay' => $autoplay,
            'autoplaySpeed' => $autoplaySpeed,
            'transitionSpeed' => $transitionSpeed
        ])->render();
    }
}
