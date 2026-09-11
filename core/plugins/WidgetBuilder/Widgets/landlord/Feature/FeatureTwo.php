<?php

namespace Plugins\WidgetBuilder\Widgets\landlord\Feature;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class FeatureTwo extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'feature_two';
    }

    protected function getWidgetName(): string
    {
        return 'Feature Two';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-th-large';
    }

    protected function getWidgetDescription(): string
    {
        return 'Detailed features grid — 2 columns top row, 3 columns bottom row with image cards';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['feature', 'detailed', 'grid', 'card', 'service'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('heading', 'Section Heading')
            ->registerField('badge_text', FieldManager::TEXT()
                ->setLabel('Badge Text')
                ->setDefault('Detailed Features')
            )
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Heading')
                ->setDefault('Everything you need to run a successful online business')
            )
            ->endGroup();

        $control->addGroup('top_row', 'Top Row (2 Cards)')
            ->registerField('top_items', FieldManager::REPEATER()
                ->setLabel('Top Row Cards')
                ->setItemLabel('Card')
                ->setAddButtonText('Add Card')
                ->setMin(1)
                ->setMax(2)
                ->setFields([
                    'image' => FieldManager::IMAGE()
                        ->setLabel('Card Image')
                        ->setDescription('Screenshot or illustration displayed inside the card'),
                    'title' => FieldManager::TEXT()
                        ->setLabel('Title')
                        ->setDefault('Build Your Online Store'),
                    'description' => FieldManager::TEXTAREA()
                        ->setLabel('Description')
                        ->setDefault('You can choose from a wide range of professionally designed templates easily customize fonts, colors, and layout to reflect your branding.'),
                    'bg_image' => FieldManager::IMAGE()
                        ->setLabel('Card Background Image (optional)'),
                    'bg_color' => FieldManager::COLOR()
                        ->setLabel('Card Background Color')
                        ->setDefault('#f5f7fa'),
                ])
            )
            ->endGroup();

        $control->addGroup('bottom_row', 'Bottom Row (3 Cards)')
            ->registerField('bottom_items', FieldManager::REPEATER()
                ->setLabel('Bottom Row Cards')
                ->setItemLabel('Card')
                ->setAddButtonText('Add Card')
                ->setMin(1)
                ->setMax(3)
                ->setFields([
                    'image' => FieldManager::IMAGE()
                        ->setLabel('Card Image')
                        ->setDescription('Screenshot or illustration displayed inside the card'),
                    'title' => FieldManager::TEXT()
                        ->setLabel('Title')
                        ->setDefault('Simplify Payment Process'),
                    'description' => FieldManager::TEXTAREA()
                        ->setLabel('Description')
                        ->setDefault('You can choose from a wide range of professionally designed'),
                    'bg_image' => FieldManager::IMAGE()
                        ->setLabel('Card Background Image (optional)'),
                    'bg_color' => FieldManager::COLOR()
                        ->setLabel('Card Background Color')
                        ->setDefault('#f5f7fa'),
                ])
            )
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('section_style', 'Section')
            ->registerField('section_bg', FieldManager::COLOR()
                ->setLabel('Section Background')
                ->setDefault('#ffffff')
            )
            ->registerField('padding_top', FieldManager::NUMBER()
                ->setLabel('Padding Top (px)')
                ->setDefault(80)
                ->setMin(0)
                ->setMax(200)
                ->setStep(5)
            )
            ->registerField('padding_bottom', FieldManager::NUMBER()
                ->setLabel('Padding Bottom (px)')
                ->setDefault(80)
                ->setMin(0)
                ->setMax(200)
                ->setStep(5)
            )
            ->endGroup();

        $control->addGroup('heading_style', 'Heading')
            ->registerField('badge_bg', FieldManager::COLOR()
                ->setLabel('Badge Background')
                ->setDefault('#e6f4ea')
            )
            ->registerField('badge_text_color', FieldManager::COLOR()
                ->setLabel('Badge Text Color')
                ->setDefault('#1a1a2e')
            )
            ->registerField('title_color', FieldManager::COLOR()
                ->setLabel('Title Color')
                ->setDefault('#1a1a2e')
            )
            ->endGroup();

        $control->addGroup('card_style', 'Cards')
            ->registerField('card_border_color', FieldManager::COLOR()
                ->setLabel('Card Border Color')
                ->setDefault('#eaeef3')
            )
            ->registerField('card_border_radius', FieldManager::NUMBER()
                ->setLabel('Card Border Radius (px)')
                ->setDefault(16)
                ->setMin(0)
                ->setMax(40)
                ->setStep(2)
            )
            ->registerField('overlay_color', FieldManager::COLOR()
                ->setLabel('BG Image Overlay Color')
                ->setDefault('#1a2e3a')
            )
            ->registerField('overlay_opacity', FieldManager::RANGE()
                ->setLabel('BG Image Overlay Opacity (%)')
                ->setDefault(0)
                ->setMin(0)
                ->setMax(100)
                ->setStep(5)
            )
            ->registerField('card_title_color', FieldManager::COLOR()
                ->setLabel('Card Title Color')
                ->setDefault('#1a1a2e')
            )
            ->registerField('card_desc_color', FieldManager::COLOR()
                ->setLabel('Card Description Color')
                ->setDefault('#6b7280')
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
        $badgeText = $headingGroup['badge_text'] ?? 'Detailed Features';
        $title = $headingGroup['title'] ?? 'Everything you need to run a successful online business';

        // Rows
        $topItems = ($general['top_row'] ?? [])['top_items'] ?? [];
        $bottomItems = ($general['bottom_row'] ?? [])['bottom_items'] ?? [];

        if (empty($topItems) && empty($bottomItems)) {
            return '';
        }

        // Process items
        $processItem = function ($item) {
            return [
                'title' => $item['title'] ?? '',
                'description' => $item['description'] ?? '',
                'image' => $this->resolveImageUrl($item['image'] ?? ''),
                'bg_image' => $this->resolveImageUrl($item['bg_image'] ?? ''),
                'bg_color' => $item['bg_color'] ?? '#f5f7fa',
            ];
        };

        return view('widgetbuilder::landlord.feature.feature_two', [
            'badgeText' => $badgeText,
            'title' => $title,
            'topCards' => array_map($processItem, $topItems),
            'bottomCards' => array_map($processItem, $bottomItems),
        ])->render();
    }
}
