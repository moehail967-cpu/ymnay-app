<?php

namespace Plugins\WidgetBuilder\Widgets\landlord\Themes;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class ThemesOne extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'themes_one';
    }

    protected function getWidgetName(): string
    {
        return 'Themes One';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-palette';
    }

    protected function getWidgetDescription(): string
    {
        return 'Theme showcase section with dark background and two marquee rows of theme screenshots';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['themes', 'showcase', 'marquee', 'gallery'];
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
                ->setDefault('Modern themes built to convert')
            )
            ->registerField('bg_image', FieldManager::IMAGE()
                ->setLabel('Background Image')
                ->setDescription('Dark background image for the section')
            )
            ->endGroup();

        $control->addGroup('row_one', 'Marquee Row 1 (Left to Right)')
            ->registerField('row_one_images', FieldManager::REPEATER()
                ->setLabel('Row 1 Images')
                ->setItemLabel('Image')
                ->setAddButtonText('Add Image')
                ->setMin(1)
                ->setMax(20)
                ->setFields([
                    'image' => FieldManager::IMAGE()
                        ->setLabel('Theme Screenshot'),
                    'alt' => FieldManager::TEXT()
                        ->setLabel('Alt Text')
                        ->setDefault('Theme Screenshot'),
                ])
            )
            ->endGroup();

        $control->addGroup('row_two', 'Marquee Row 2 (Right to Left)')
            ->registerField('row_two_images', FieldManager::REPEATER()
                ->setLabel('Row 2 Images')
                ->setItemLabel('Image')
                ->setAddButtonText('Add Image')
                ->setMin(1)
                ->setMax(20)
                ->setFields([
                    'image' => FieldManager::IMAGE()
                        ->setLabel('Theme Screenshot'),
                    'alt' => FieldManager::TEXT()
                        ->setLabel('Alt Text')
                        ->setDefault('Theme Screenshot'),
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
                ->setDefault(90)
                ->setMin(0)
                ->setMax(100)
                ->setStep(5)
            )
            ->registerField('padding_top', FieldManager::NUMBER()
                ->setLabel('Padding Top (px)')
                ->setDefault(128)
                ->setMin(0)
                ->setMax(300)
                ->setStep(5)
            )
            ->registerField('padding_bottom', FieldManager::NUMBER()
                ->setLabel('Padding Bottom (px)')
                ->setDefault(128)
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
        $title = $headingGroup['title'] ?? 'Modern themes built to convert';
        $bgImage = $this->resolveImageUrl($headingGroup['bg_image'] ?? '');

        // Row 1
        $rowOneGroup = $general['row_one'] ?? [];
        $rowOneItems = $rowOneGroup['row_one_images'] ?? [];

        // Row 2
        $rowTwoGroup = $general['row_two'] ?? [];
        $rowTwoItems = $rowTwoGroup['row_two_images'] ?? [];

        $processImages = function ($items) {
            $images = [];
            foreach ($items as $item) {
                $images[] = [
                    'url' => $this->resolveImageUrl($item['image'] ?? ''),
                    'alt' => $item['alt'] ?? 'Theme Screenshot',
                ];
            }
            return $images;
        };

        $rowOneImages = $processImages($rowOneItems);
        $rowTwoImages = $processImages($rowTwoItems);

        if (empty($rowOneImages) && empty($rowTwoImages)) {
            return '';
        }

        return view('widgetbuilder::landlord.themes.themes', [
            'badgeText' => $badgeText,
            'title' => $title,
            'bgImage' => $bgImage,
            'rowOneImages' => $rowOneImages,
            'rowTwoImages' => $rowTwoImages,
        ])->render();
    }
}
