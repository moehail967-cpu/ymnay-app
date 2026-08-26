<?php

namespace Plugins\WidgetBuilder\Widgets\landlord\Header;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class HeaderTwo extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'header_two';
    }

    protected function getWidgetName(): string
    {
        return 'Header Two';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-heading';
    }

    protected function getWidgetDescription(): string
    {
        return 'Hero banner with heading, buttons, subtitle, and a full-width banner image';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['header', 'hero', 'banner', 'landing'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Title')
                ->setDefault('Build Your <span class="text-lime-500">E-Commerce</span> Store Within Minutes')
                ->setDescription('Use <span class="text-lime-500">text</span> to highlight words')
            )
            ->registerField('subtitle', FieldManager::TEXT()
                ->setLabel('Subtitle Text')
                ->setDefault('Try Now. No credit card required')
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
                ->setLabel('Section Background Image')
                ->setDescription('Full section background image')
            )
            ->endGroup();

        $control->addGroup('banner', 'Banner Image')
            ->registerField('banner_image', FieldManager::IMAGE()
                ->setLabel('Banner Image')
                ->setDescription('Full-width banner image displayed below the heading')
            )
            ->endGroup();

        $control->addGroup('decoration', 'Decoration')
            ->registerField('underline_image', FieldManager::IMAGE()
                ->setLabel('Underline Decoration Image')
                ->setDescription('SVG underline image shown on desktop under the title')
            )
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('section_style', 'Section')
            ->registerField('padding_top', FieldManager::NUMBER()
                ->setLabel('Padding Top (px)')
                ->setDefault(0)
                ->setMin(0)
                ->setMax(200)
                ->setStep(5)
                ->setSelectors(['{{WRAPPER}} .header_two_wrapper' => 'padding-top: {{VALUE}}px'])
            )
            ->registerField('padding_bottom', FieldManager::NUMBER()
                ->setLabel('Padding Bottom (px)')
                ->setDefault(0)
                ->setMin(0)
                ->setMax(200)
                ->setStep(5)
                ->setSelectors(['{{WRAPPER}} .header_two_wrapper' => 'padding-bottom: {{VALUE}}px'])
            )
            ->registerField('margin_top', FieldManager::NUMBER()
                ->setLabel('Margin Top (px)')
                ->setDefault(0)
                ->setMin(0)
                ->setMax(200)
                ->setStep(5)
                ->setSelectors(['{{WRAPPER}} .header_two_wrapper' => 'margin-top: {{VALUE}}px'])
            )
            ->registerField('margin_bottom', FieldManager::NUMBER()
                ->setLabel('Margin Bottom (px)')
                ->setDefault(0)
                ->setMin(0)
                ->setMax(200)
                ->setStep(5)
                ->setSelectors(['{{WRAPPER}} .header_two_wrapper' => 'margin-bottom: {{VALUE}}px'])
            )
            ->endGroup();

        return $control->getFields();
    }

    private function resolveImageUrl($value): string
    {
        if (is_array($value)) {
            return $value['url'] ?? '';
        }

        if (is_numeric($value)) {
            $attachment = get_attachment_image_by_id($value);
            return $attachment['img_url'] ?? '';
        }

        return is_string($value) ? $value : '';
    }

    public function render(array $settings = []): string
    {
        $general = $settings['general'] ?? [];
        $style = $settings['style'] ?? [];

        // Content
        $content = $general['content'] ?? [];
        $title = $content['title'] ?? 'Build Your <span class="text-lime-500">E-Commerce</span> Store Within Minutes';
        $subtitle = $content['subtitle'] ?? 'Try Now. No credit card required';

        // Buttons
        $buttons = $general['buttons'] ?? [];
        $primaryBtnText = $buttons['primary_button_text'] ?? 'Start Free Trial';
        $primaryBtnUrl = $buttons['primary_button_url'] ?? '#';
        $secondaryBtnText = $buttons['secondary_button_text'] ?? 'Watch Demo';
        $secondaryBtnUrl = $buttons['secondary_button_url'] ?? '#';

        // Background
        $bg = $general['background'] ?? [];
        $bgImage = $this->resolveImageUrl($bg['background_image'] ?? '');

        // Banner
        $banner = $general['banner'] ?? [];
        $bannerImage = $this->resolveImageUrl($banner['banner_image'] ?? '');

        // Decoration
        $decoration = $general['decoration'] ?? [];
        $underlineImage = $this->resolveImageUrl($decoration['underline_image'] ?? '');

        return view('widgetbuilder::landlord.header.header_two', [
            'uid' => 'ht_' . uniqid(),
            'title' => $title,
            'subtitle' => $subtitle,
            'primaryBtnText' => $primaryBtnText,
            'primaryBtnUrl' => $primaryBtnUrl,
            'secondaryBtnText' => $secondaryBtnText,
            'secondaryBtnUrl' => $secondaryBtnUrl,
            'bgImage' => $bgImage,
            'bannerImage' => $bannerImage,
            'underlineImage' => $underlineImage,
            'style' => $style,
        ])->render();
    }
}
