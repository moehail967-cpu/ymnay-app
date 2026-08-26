<?php

namespace Plugins\WidgetBuilder\Widgets\landlord\Feature;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class FeatureThree extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'feature_three';
    }

    protected function getWidgetName(): string
    {
        return 'Feature Three';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-chart-line';
    }

    protected function getWidgetDescription(): string
    {
        return 'Rise stats section — highlight card left, 3 counter stat cards right';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['feature', 'stats', 'counter', 'rise', 'grid'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('heading', 'Section Heading')
            ->registerField('badge_text', FieldManager::TEXT()
                ->setLabel('Badge Text')
                ->setDefault('Rise')
            )
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Heading')
                ->setDefault('Everything you need to run a successful online business')
            )
            ->endGroup();

        $control->addGroup('highlight_card', 'Highlight Card (Left)')
            ->registerField('icon', FieldManager::IMAGE()
                ->setLabel('Icon Image')
            )
            ->registerField('counter_value', FieldManager::NUMBER()
                ->setLabel('Counter Value')
                ->setDefault(33)
                ->setDescription('The number to count up to')
            )
            ->registerField('counter_prefix', FieldManager::TEXT()
                ->setLabel('Counter Prefix')
                ->setDefault('$')
                ->setDescription('e.g. $, £')
            )
            ->registerField('counter_suffix', FieldManager::TEXT()
                ->setLabel('Counter Suffix')
                ->setDefault(' Billion+')
                ->setDescription('e.g. %, +, Billion+')
            )
            ->registerField('subtitle', FieldManager::TEXT()
                ->setLabel('Subtitle')
                ->setDefault('The future of digital entrepreneurship')
            )
            ->registerField('description', FieldManager::TEXTAREA()
                ->setLabel('Description')
                ->setDefault('Millions of businesses are shifting online every year, increasing demand for powerful, no-code store-building platforms that help brands grow faster.')
            )
            ->endGroup();

        $control->addGroup('stat_cards', 'Stat Cards (Right)')
            ->registerField('stat_items', FieldManager::REPEATER()
                ->setLabel('Stat Cards')
                ->setItemLabel('Stat Card')
                ->setAddButtonText('Add Stat Card')
                ->setMin(1)
                ->setMax(5)
                ->setFields([
                    'counter_value' => FieldManager::NUMBER()
                        ->setLabel('Counter Value')
                        ->setDefault(13)
                        ->setDescription('The number to count up to'),
                    'counter_suffix' => FieldManager::TEXT()
                        ->setLabel('Suffix')
                        ->setDefault('%')
                        ->setDescription('e.g. %, +, K, M'),
                    'counter_prefix' => FieldManager::TEXT()
                        ->setLabel('Prefix')
                        ->setDefault('')
                        ->setDescription('e.g. $, £'),
                    'title' => FieldManager::TEXT()
                        ->setLabel('Title')
                        ->setDefault('Speed matters in e-commerce'),
                    'description' => FieldManager::TEXTAREA()
                        ->setLabel('Description')
                        ->setDefault('Nearly half of today\'s sellers expect instant setup, driving the need for platforms that offer quick, user-friendly store creation without technical hassle.'),
                    'card_bg' => FieldManager::COLOR()
                        ->setLabel('Card Background')
                        ->setDefault('#dbeafe'),
                    'counter_color' => FieldManager::COLOR()
                        ->setLabel('Counter Color')
                        ->setDefault('#1a1a2e'),
                    'title_color' => FieldManager::COLOR()
                        ->setLabel('Title Color')
                        ->setDefault('#1a1a2e'),
                    'desc_color' => FieldManager::COLOR()
                        ->setLabel('Description Color')
                        ->setDefault('#4b5563'),
                ])
            )
            ->endGroup();

        $control->addGroup('counter_settings', 'Counter Settings')
            ->registerField('counter_duration', FieldManager::NUMBER()
                ->setLabel('Counter Duration (ms)')
                ->setDefault(2000)
                ->setMin(500)
                ->setMax(5000)
                ->setStep(100)
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
                ->setDefault('#0f3d3e')
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
                ->setDefault('rgba(255,255,255,0.15)')
            )
            ->registerField('badge_text_color', FieldManager::COLOR()
                ->setLabel('Badge Text Color')
                ->setDefault('#ffffff')
            )
            ->registerField('title_color', FieldManager::COLOR()
                ->setLabel('Title Color')
                ->setDefault('#ffffff')
            )
            ->endGroup();

        $control->addGroup('highlight_style', 'Highlight Card')
            ->registerField('card_bg', FieldManager::COLOR()
                ->setLabel('Card Background')
                ->setDefault('#fef9ef')
            )
            ->registerField('icon_bg', FieldManager::COLOR()
                ->setLabel('Icon Circle Background')
                ->setDefault('#0f3d3e')
            )
            ->registerField('stat_color', FieldManager::COLOR()
                ->setLabel('Stat Number Color')
                ->setDefault('#1a1a2e')
            )
            ->registerField('subtitle_color', FieldManager::COLOR()
                ->setLabel('Subtitle Color')
                ->setDefault('#1a1a2e')
            )
            ->registerField('desc_color', FieldManager::COLOR()
                ->setLabel('Description Color')
                ->setDefault('#4b5563')
            )
            ->endGroup();

        $control->addGroup('card_style', 'Stat Cards Global')
            ->registerField('card_border_radius', FieldManager::NUMBER()
                ->setLabel('Card Border Radius (px)')
                ->setDefault(16)
                ->setMin(0)
                ->setMax(40)
                ->setStep(2)
            )
            ->registerField('gap', FieldManager::NUMBER()
                ->setLabel('Gap Between Cards (px)')
                ->setDefault(20)
                ->setMin(5)
                ->setMax(50)
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
        $badgeText = $headingGroup['badge_text'] ?? 'Rise';
        $title = $headingGroup['title'] ?? 'Everything you need to run a successful online business';

        // Highlight card
        $hlGroup = $general['highlight_card'] ?? [];
        $highlight = [
            'icon' => $this->resolveImageUrl($hlGroup['icon'] ?? ''),
            'counter_value' => (int)($hlGroup['counter_value'] ?? 33),
            'counter_prefix' => $hlGroup['counter_prefix'] ?? '$',
            'counter_suffix' => $hlGroup['counter_suffix'] ?? ' Billion+',
            'subtitle' => $hlGroup['subtitle'] ?? 'The future of digital entrepreneurship',
            'description' => $hlGroup['description'] ?? 'Millions of businesses are shifting online every year, increasing demand for powerful, no-code store-building platforms that help brands grow faster.',
        ];

        // Stat cards
        $statGroup = $general['stat_cards'] ?? [];
        $statItems = [];
        foreach (($statGroup['stat_items'] ?? []) as $item) {
            $statItems[] = [
                'counter_value' => (int)($item['counter_value'] ?? 0),
                'counter_suffix' => $item['counter_suffix'] ?? '',
                'counter_prefix' => $item['counter_prefix'] ?? '',
                'title' => $item['title'] ?? '',
                'description' => $item['description'] ?? '',
                'card_bg' => $item['card_bg'] ?? '#dbeafe',
            ];
        }

        return view('widgetbuilder::landlord.feature.feature_three', [
            'badgeText' => $badgeText,
            'title' => $title,
            'highlight' => $highlight,
            'statItems' => $statItems,
        ])->render();
    }
}
