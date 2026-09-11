<?php

namespace Plugins\WidgetBuilder\Widgets\landlord\OurAchievements;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class OurAchievementsOne extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'our_achievements';
    }

    protected function getWidgetName(): string
    {
        return 'Our Achievements';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-trophy';
    }

    protected function getWidgetDescription(): string
    {
        return 'Achievement counter stats section with animated numbers';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['achievements', 'counter', 'stats', 'numbers'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('stats', 'Stats')
            ->registerField('stat_items', FieldManager::REPEATER()
                ->setLabel('Statistics')
                ->setItemLabel('Stat')
                ->setAddButtonText('Add Stat')
                ->setMin(1)
                ->setMax(4)
                ->setFields([
                    'count' => FieldManager::NUMBER()
                        ->setLabel('Count Number')
                        ->setDefault(1000)
                        ->setRequired(true),
                    'suffix' => FieldManager::TEXT()
                        ->setLabel('Suffix (e.g. +, %, K)')
                        ->setDefault('+'),
                    'label' => FieldManager::TEXT()
                        ->setLabel('Label Text')
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
                ->setDefault(112)
                ->setMin(0)->setMax(300)->setStep(5)
            )
            ->registerField('padding_bottom', FieldManager::NUMBER()
                ->setLabel('Padding Bottom (px)')
                ->setDefault(144)
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
            ->registerField('container_bg', FieldManager::COLOR()
                ->setLabel('Stats Container Background')
                ->setDefault('rgba(12, 77, 84, 0.05)')
            )
            ->registerField('counter_color', FieldManager::COLOR()
                ->setLabel('Counter Number Color')
                ->setDefault('#0C4D54')
            )

            ->endGroup();

        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        $general = $settings['general'] ?? [];
        $style = $settings['style'] ?? [];

        // Stats
        $statsGroup = $general['stats'] ?? [];
        $statItems = $statsGroup['stat_items'] ?? [];
        $stats = [];
        foreach ($statItems as $item) {
            $stats[] = [
                'count' => (int)($item['count'] ?? 0),
                'suffix' => $item['suffix'] ?? '+',
                'label' => $item['label'] ?? '',
            ];
        }

        // Colors
        $colors = $style['colors'] ?? [];

        return view('widgetbuilder::landlord.our_achievements.our_achievements', [
            'stats' => $stats,
            'sectionBg' => $colors['section_bg'] ?? '#ffffff',
            'containerBg' => $colors['container_bg'] ?? 'rgba(12, 77, 84, 0.05)',
            'counterColor' => $colors['counter_color'] ?? '#0C4D54',
            'style' => $style,
        ])->render();
    }
}
