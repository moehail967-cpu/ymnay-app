<?php

namespace Plugins\WidgetBuilder\Widgets\landlord\HowItWorks;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class HowItWorksOne extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'how_it_works_one';
    }

    protected function getWidgetName(): string
    {
        return 'How It Works One';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-list-ol';
    }

    protected function getWidgetDescription(): string
    {
        return 'Step-by-step process section with connected icons, numbering, and hover card effect';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['how it works', 'steps', 'process', 'timeline'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('heading', 'Section Heading')
            ->registerField('badge_text', FieldManager::TEXT()
                ->setLabel('Badge Text')
                ->setDefault('How It Works')
            )
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Heading')
                ->setDefault('From idea to online store in minutes, not months')
            )
            ->endGroup();

        $control->addGroup('steps', 'Steps')
            ->registerField('step_items', FieldManager::REPEATER()
                ->setLabel('Steps')
                ->setItemLabel('Step')
                ->setAddButtonText('Add Step')
                ->setMin(1)
                ->setMax(6)
                ->setFields([
                    'icon' => FieldManager::IMAGE()
                        ->setLabel('Icon Image')
                        ->setDescription('White icon on dark background'),
                    'title' => FieldManager::TEXT()
                        ->setLabel('Title')
                        ->setDefault('Choose Template'),
                    'description' => FieldManager::TEXTAREA()
                        ->setLabel('Description')
                        ->setDefault('Browse through our extensive library of stunning, templates—each carefully crafted to help you launch your eCommerce store faster'),
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
                ->setDefault('#f0f1e8')
            )
            ->registerField('badge_text_color', FieldManager::COLOR()
                ->setLabel('Badge Text Color')
                ->setDefault('#1a1a2e')
            )
            ->registerField('badge_font_size', FieldManager::NUMBER()
                ->setLabel('Badge Font Size (px)')
                ->setDefault(13)
                ->setMin(10)
                ->setMax(20)
                ->setStep(1)
            )
            ->registerField('badge_font_weight', FieldManager::SELECT()
                ->setLabel('Badge Font Weight')
                ->setOptions([
                    '400' => 'Normal',
                    '500' => 'Medium',
                    '600' => 'Semi Bold',
                    '700' => 'Bold',
                ])
                ->setDefault('600')
            )
            ->registerField('title_color', FieldManager::COLOR()
                ->setLabel('Title Color')
                ->setDefault('#1a1a2e')
            )
            ->registerField('title_font_size', FieldManager::NUMBER()
                ->setLabel('Title Font Size (px)')
                ->setDefault(40)
                ->setMin(20)
                ->setMax(72)
                ->setStep(1)
            )
            ->registerField('title_font_weight', FieldManager::SELECT()
                ->setLabel('Title Font Weight')
                ->setOptions([
                    '400' => 'Normal',
                    '500' => 'Medium',
                    '600' => 'Semi Bold',
                    '700' => 'Bold',
                    '800' => 'Extra Bold',
                ])
                ->setDefault('700')
            )
            ->registerField('title_font_style', FieldManager::SELECT()
                ->setLabel('Title Font Style')
                ->setOptions([
                    'normal' => 'Normal',
                    'italic' => 'Italic',
                ])
                ->setDefault('italic')
            )
            ->endGroup();

        $control->addGroup('icon_style', 'Icon')
            ->registerField('icon_bg', FieldManager::COLOR()
                ->setLabel('Icon Background')
                ->setDefault('#0f3d3e')
            )
            ->registerField('icon_size', FieldManager::NUMBER()
                ->setLabel('Icon Box Size (px)')
                ->setDefault(56)
                ->setMin(30)
                ->setMax(100)
                ->setStep(2)
            )
            ->registerField('icon_border_radius', FieldManager::NUMBER()
                ->setLabel('Icon Border Radius (px)')
                ->setDefault(14)
                ->setMin(0)
                ->setMax(50)
                ->setStep(2)
            )
            ->endGroup();

        $control->addGroup('line_style', 'Connecting Line')
            ->registerField('line_color_start', FieldManager::COLOR()
                ->setLabel('Line Gradient Start')
                ->setDefault('#60a5fa')
            )
            ->registerField('line_color_end', FieldManager::COLOR()
                ->setLabel('Line Gradient End')
                ->setDefault('#e879a8')
            )
            ->registerField('line_thickness', FieldManager::NUMBER()
                ->setLabel('Line Thickness (px)')
                ->setDefault(2)
                ->setMin(1)
                ->setMax(6)
                ->setStep(1)
            )
            ->endGroup();

        $control->addGroup('step_number_style', 'Step Number')
            ->registerField('number_color', FieldManager::COLOR()
                ->setLabel('Number Color')
                ->setDefault('#e5e7eb')
            )
            ->registerField('number_font_size', FieldManager::NUMBER()
                ->setLabel('Number Font Size (px)')
                ->setDefault(80)
                ->setMin(40)
                ->setMax(150)
                ->setStep(5)
            )
            ->registerField('number_font_weight', FieldManager::SELECT()
                ->setLabel('Number Font Weight')
                ->setOptions([
                    '400' => 'Normal',
                    '500' => 'Medium',
                    '600' => 'Semi Bold',
                    '700' => 'Bold',
                    '800' => 'Extra Bold',
                ])
                ->setDefault('700')
            )
            ->endGroup();

        $control->addGroup('card_text_style', 'Card Text')
            ->registerField('card_title_color', FieldManager::COLOR()
                ->setLabel('Card Title Color')
                ->setDefault('#1a1a2e')
            )
            ->registerField('card_title_font_size', FieldManager::NUMBER()
                ->setLabel('Card Title Font Size (px)')
                ->setDefault(18)
                ->setMin(12)
                ->setMax(36)
                ->setStep(1)
            )
            ->registerField('card_title_font_weight', FieldManager::SELECT()
                ->setLabel('Card Title Font Weight')
                ->setOptions([
                    '400' => 'Normal',
                    '500' => 'Medium',
                    '600' => 'Semi Bold',
                    '700' => 'Bold',
                ])
                ->setDefault('600')
            )
            ->registerField('card_desc_color', FieldManager::COLOR()
                ->setLabel('Card Description Color')
                ->setDefault('#6b7280')
            )
            ->registerField('card_desc_font_size', FieldManager::NUMBER()
                ->setLabel('Card Description Font Size (px)')
                ->setDefault(14)
                ->setMin(11)
                ->setMax(20)
                ->setStep(1)
            )
            ->registerField('card_desc_font_weight', FieldManager::SELECT()
                ->setLabel('Card Description Font Weight')
                ->setOptions([
                    '300' => 'Light',
                    '400' => 'Normal',
                    '500' => 'Medium',
                ])
                ->setDefault('400')
            )
            ->endGroup();

        $control->addGroup('card_spacing', 'Card Spacing')
            ->registerField('card_padding_top', FieldManager::NUMBER()
                ->setLabel('Card Padding Top (px)')
                ->setDefault(20)
                ->setMin(0)
                ->setMax(80)
                ->setStep(5)
            )
            ->registerField('card_padding_right', FieldManager::NUMBER()
                ->setLabel('Card Padding Right (px)')
                ->setDefault(15)
                ->setMin(0)
                ->setMax(80)
                ->setStep(5)
            )
            ->registerField('card_padding_bottom', FieldManager::NUMBER()
                ->setLabel('Card Padding Bottom (px)')
                ->setDefault(30)
                ->setMin(0)
                ->setMax(80)
                ->setStep(5)
            )
            ->registerField('card_padding_left', FieldManager::NUMBER()
                ->setLabel('Card Padding Left (px)')
                ->setDefault(15)
                ->setMin(0)
                ->setMax(80)
                ->setStep(5)
            )
            ->registerField('card_margin_top', FieldManager::NUMBER()
                ->setLabel('Card Margin Top (px)')
                ->setDefault(0)
                ->setMin(0)
                ->setMax(80)
                ->setStep(5)
            )
            ->registerField('card_margin_right', FieldManager::NUMBER()
                ->setLabel('Card Margin Right (px)')
                ->setDefault(0)
                ->setMin(0)
                ->setMax(80)
                ->setStep(5)
            )
            ->registerField('card_margin_bottom', FieldManager::NUMBER()
                ->setLabel('Card Margin Bottom (px)')
                ->setDefault(0)
                ->setMin(0)
                ->setMax(80)
                ->setStep(5)
            )
            ->registerField('card_margin_left', FieldManager::NUMBER()
                ->setLabel('Card Margin Left (px)')
                ->setDefault(0)
                ->setMin(0)
                ->setMax(80)
                ->setStep(5)
            )
            ->endGroup();

        $control->addGroup('hover_style', 'Hover Effect')
            ->registerField('hover_card_bg', FieldManager::COLOR()
                ->setLabel('Hover Card Background')
                ->setDefault('#ffffff')
            )
            ->registerField('hover_shadow_color', FieldManager::COLOR()
                ->setLabel('Hover Shadow Color')
                ->setDefault('rgba(0,0,0,0.08)')
            )
            ->registerField('hover_card_radius', FieldManager::NUMBER()
                ->setLabel('Hover Card Border Radius (px)')
                ->setDefault(16)
                ->setMin(0)
                ->setMax(40)
                ->setStep(2)
            )
            ->registerField('hover_scale', FieldManager::RANGE()
                ->setLabel('Hover Scale (%)')
                ->setDefault(105)
                ->setMin(100)
                ->setMax(120)
                ->setStep(1)
            )
            ->registerField('hover_icon_scale', FieldManager::RANGE()
                ->setLabel('Hover Icon Scale (%)')
                ->setDefault(120)
                ->setMin(100)
                ->setMax(150)
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
        $badgeText = $headingGroup['badge_text'] ?? 'How It Works';
        $title = $headingGroup['title'] ?? 'From idea to online store in minutes, not months';

        // Steps
        $stepsGroup = $general['steps'] ?? [];
        $stepItems = $stepsGroup['step_items'] ?? [];

        if (empty($stepItems)) {
            return '';
        }

        // Process steps
        $steps = [];
        foreach ($stepItems as $idx => $item) {
            $steps[] = [
                'number' => str_pad($idx + 1, 2, '0', STR_PAD_LEFT),
                'icon' => $this->resolveImageUrl($item['icon'] ?? ''),
                'title' => $item['title'] ?? 'Step',
                'description' => $item['description'] ?? '',
            ];
        }

        return view('widgetbuilder::landlord.how_it_works.how_it_works', [
            'badgeText' => $badgeText,
            'title' => $title,
            'steps' => $steps,
        ])->render();
    }
}
