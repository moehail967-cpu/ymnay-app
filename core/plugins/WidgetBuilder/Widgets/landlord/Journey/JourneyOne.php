<?php

namespace Plugins\WidgetBuilder\Widgets\landlord\Journey;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class JourneyOne extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'journey';
    }

    protected function getWidgetName(): string
    {
        return 'Our Journey';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-road';
    }

    protected function getWidgetDescription(): string
    {
        return 'Timeline journey section with milestones';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['journey', 'timeline', 'milestones', 'history'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('badge_text', FieldManager::TEXT()
                ->setLabel('Badge Text')
                ->setDefault('Our Journey')
            )
            ->registerField('heading', FieldManager::TEXT()
                ->setLabel('Heading')
                ->setDefault('Milestones shaped Nazmart')
            )
            ->endGroup();

        $control->addGroup('milestones', 'Milestones')
            ->registerField('milestone_items', FieldManager::REPEATER()
                ->setLabel('Milestones')
                ->setItemLabel('Milestone')
                ->setAddButtonText('Add Milestone')
                ->setMin(1)
                ->setMax(20)
                ->setFields([
                    'year' => FieldManager::TEXT()
                        ->setLabel('Year')
                        ->setRequired(true),
                    'title' => FieldManager::TEXT()
                        ->setLabel('Title')
                        ->setRequired(true),
                    'description' => FieldManager::TEXT()
                        ->setLabel('Description')
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
            ->registerField('padding_top', FieldManager::NUMBER()
                ->setLabel('Padding Top (px)')
                ->setDefault(128)
                ->setMin(0)->setMax(300)->setStep(5)
            )
            ->registerField('padding_bottom', FieldManager::NUMBER()
                ->setLabel('Padding Bottom (px)')
                ->setDefault(128)
                ->setMin(0)->setMax(300)->setStep(5)
            )
            ->registerField('margin_top', FieldManager::NUMBER()
                ->setLabel('Margin Top (px)')
                ->setDefault(0)
                ->setMin(0)->setMax(300)->setStep(5)
            )
            ->registerField('margin_bottom', FieldManager::NUMBER()
                ->setLabel('Margin Bottom (px)')
                ->setDefault(0)
                ->setMin(0)->setMax(300)->setStep(5)
            )
            ->endGroup();

        $control->addGroup('colors', 'Colors')
            ->registerField('section_bg', FieldManager::COLOR()
                ->setLabel('Section Background Color')
                ->setDefault('#ffffff')
            )
            ->registerField('badge_bg', FieldManager::COLOR()
                ->setLabel('Badge Background Color')
                ->setDefault('rgba(12, 77, 84, 0.1)')
            )
            ->registerField('badge_text_color', FieldManager::COLOR()
                ->setLabel('Badge Text Color')
                ->setDefault('#0C4D54')
            )
            ->registerField('badge_border_color', FieldManager::COLOR()
                ->setLabel('Badge Border Color')
                ->setDefault('#e5e7eb')
            )

            ->registerField('timeline_line_color', FieldManager::COLOR()
                ->setLabel('Timeline Line Color')
                ->setDefault('#d1d5db')
            )

            ->registerField('dot_border_color', FieldManager::COLOR()
                ->setLabel('Dot Border Color')
                ->setDefault('#0C4D54')
            )
            ->registerField('card_bg', FieldManager::COLOR()
                ->setLabel('Card Background Color')
                ->setDefault('#ffffff')
            )
            ->registerField('card_border_color', FieldManager::COLOR()
                ->setLabel('Card Border Color')
                ->setDefault('#e5e7eb')
            )
            ->registerField('year_bg', FieldManager::COLOR()
                ->setLabel('Year Badge Background')
                ->setDefault('rgba(12, 77, 84, 0.1)')
            )
            ->registerField('year_text_color', FieldManager::COLOR()
                ->setLabel('Year Text Color')
                ->setDefault('#374151')
            )

            ->registerField('card_desc_color', FieldManager::COLOR()
                ->setLabel('Card Description Color')
                ->setDefault('#6B7280')
            )
            ->endGroup();

        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        $general = $settings['general'] ?? [];
        $style = $settings['style'] ?? [];

        // Content
        $content = $general['content'] ?? [];
        $badgeText = $content['badge_text'] ?? 'Our Journey';
        $heading = $content['heading'] ?? 'Milestones shaped Nazmart';

        // Milestones
        $milestonesGroup = $general['milestones'] ?? [];
        $milestoneItems = $milestonesGroup['milestone_items'] ?? [];
        $milestones = [];
        foreach ($milestoneItems as $item) {
            $milestones[] = [
                'year' => $item['year'] ?? '',
                'title' => $item['title'] ?? '',
                'description' => $item['description'] ?? '',
            ];
        }

        // Colors
        $colors = $style['colors'] ?? [];

        return view('widgetbuilder::landlord.journey.journey', [
            'badgeText' => $badgeText,
            'heading' => $heading,
            'milestones' => $milestones,
            'sectionBg' => $colors['section_bg'] ?? '#ffffff',
            'badgeBg' => $colors['badge_bg'] ?? 'rgba(12, 77, 84, 0.1)',
            'badgeTextColor' => $colors['badge_text_color'] ?? '#0C4D54',
            'badgeBorderColor' => $colors['badge_border_color'] ?? '#e5e7eb',
            'timelineLineColor' => $colors['timeline_line_color'] ?? '#d1d5db',
            'dotBorderColor' => $colors['dot_border_color'] ?? '#0C4D54',
            'cardBg' => $colors['card_bg'] ?? '#ffffff',
            'cardBorderColor' => $colors['card_border_color'] ?? '#e5e7eb',
            'yearBg' => $colors['year_bg'] ?? 'rgba(12, 77, 84, 0.1)',
            'yearTextColor' => $colors['year_text_color'] ?? '#374151',
            'cardDescColor' => $colors['card_desc_color'] ?? '#6B7280',
            'style' => $style,
        ])->render();
    }
}
