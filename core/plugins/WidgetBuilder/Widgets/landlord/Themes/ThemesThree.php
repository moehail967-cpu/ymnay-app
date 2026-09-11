<?php

namespace Plugins\WidgetBuilder\Widgets\landlord\Themes;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class ThemesThree extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'themes_three';
    }

    protected function getWidgetName(): string
    {
        return 'Themes Three';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-palette';
    }

    protected function getWidgetDescription(): string
    {
        return 'Theme showcase section with Swiper slider featuring side thumbnail and large preview';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['themes', 'showcase', 'swiper', 'preview'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('heading', 'Section Heading')
            ->registerField('badge_text', FieldManager::TEXT()
                ->setLabel('Badge Text')
                ->setDefault('Themes')
            )
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Heading')
                ->setDefault('Modern Themes Built to Convert')
            )
            ->registerField('bg_image', FieldManager::IMAGE()
                ->setLabel('Background Image')
                ->setDescription('Dark background image for the section')
            )
            ->endGroup();

        $control->addGroup('slides', 'Theme Slides')
            ->registerField('slides', FieldManager::REPEATER()
                ->setLabel('Theme Slides')
                ->setItemLabel('Slide')
                ->setAddButtonText('Add Slide')
                ->setMin(1)
                ->setMax(20)
                ->setFields([
                    'thumbnail' => FieldManager::IMAGE()
                        ->setLabel('Side Thumbnail Image')
                        ->setRequired(true),
                    'preview' => FieldManager::IMAGE()
                        ->setLabel('Large Preview Image')
                        ->setRequired(true),
                ])
            )
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('section_style', 'Section')
            ->registerField('overlay_opacity', FieldManager::RANGE()
                ->setLabel('Overlay Opacity (%)')
                ->setDefault(80)
                ->setMin(0)
                ->setMax(100)
                ->setStep(5)
            )
            ->registerField('padding_top', FieldManager::NUMBER()
                ->setLabel('Padding Top (px)')
                ->setDefault(140)
                ->setMin(0)
                ->setMax(300)
                ->setStep(5)
            )
            ->registerField('padding_bottom', FieldManager::NUMBER()
                ->setLabel('Padding Bottom (px)')
                ->setDefault(140)
                ->setMin(0)
                ->setMax(300)
                ->setStep(5)
            )
            ->registerField('margin_top', FieldManager::NUMBER()
                ->setLabel('Margin Top (px)')
                ->setDefault(144)
                ->setMin(0)
                ->setMax(300)
                ->setStep(5)
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

        // Heading
        $headingGroup = $general['heading'] ?? [];
        $badgeText = $headingGroup['badge_text'] ?? 'Themes';
        $title = $headingGroup['title'] ?? 'Modern Themes Built to Convert';
        $bgImage = $this->resolveImageUrl($headingGroup['bg_image'] ?? '');

        // Slides
        $slidesGroup = $general['slides'] ?? [];
        $slideItems = $slidesGroup['slides'] ?? [];
        $slides = [];
        foreach ($slideItems as $item) {
            $thumbnail = $this->resolveImageUrl($item['thumbnail'] ?? '');
            $preview = $this->resolveImageUrl($item['preview'] ?? '');
            if (!empty($thumbnail) && !empty($preview)) {
                $slides[] = [
                    'thumbnail' => $thumbnail,
                    'preview' => $preview,
                ];
            }
        }

        if (empty($slides)) {
            return '';
        }

        return view('widgetbuilder::landlord.themes.themes_three', [
            'badgeText' => $badgeText,
            'title' => $title,
            'bgImage' => $bgImage,
            'slides' => $slides,
        ])->render();
    }
}
