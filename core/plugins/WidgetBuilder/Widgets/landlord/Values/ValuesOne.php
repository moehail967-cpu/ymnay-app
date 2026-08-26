<?php

namespace Plugins\WidgetBuilder\Widgets\landlord\Values;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class ValuesOne extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'values';
    }

    protected function getWidgetName(): string
    {
        return 'Our Values';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-heart';
    }

    protected function getWidgetDescription(): string
    {
        return 'Values and cultures section with icon cards grid';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['values', 'culture', 'about', 'cards'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('badge_text', FieldManager::TEXT()
                ->setLabel('Badge Text')
                ->setDefault('Values')
            )
            ->registerField('heading', FieldManager::TEXT()
                ->setLabel('Heading')
                ->setDefault('Our Values and Cultures')
            )
            ->endGroup();

        $control->addGroup('value_items', 'Value Items')
            ->registerField('items', FieldManager::REPEATER()
                ->setLabel('Values')
                ->setItemLabel('Value')
                ->setAddButtonText('Add Value')
                ->setMin(1)
                ->setMax(10)
                ->setFields([
                    'icon' => FieldManager::IMAGE()
                        ->setLabel('Icon Image')
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
                ->setDefault(20)
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

            ->registerField('card_bg', FieldManager::COLOR()
                ->setLabel('Card Background Color')
                ->setDefault('#ffffff')
            )
            ->registerField('card_border_color', FieldManager::COLOR()
                ->setLabel('Card Border Color')
                ->setDefault('#e5e7eb')
            )
            ->registerField('icon_bg', FieldManager::COLOR()
                ->setLabel('Icon Background Color')
                ->setDefault('rgba(12, 77, 84, 0.1)')
            )

            ->registerField('description_color', FieldManager::COLOR()
                ->setLabel('Description Color')
                ->setDefault('#6B7280')
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
        $badgeText = $content['badge_text'] ?? 'Values';
        $heading = $content['heading'] ?? 'Our Values and Cultures';

        // Value Items
        $valueGroup = $general['value_items'] ?? [];
        $valueItems = $valueGroup['items'] ?? [];
        $values = [];
        foreach ($valueItems as $item) {
            $values[] = [
                'icon' => $this->resolveImageUrl($item['icon'] ?? ''),
                'title' => $item['title'] ?? '',
                'description' => $item['description'] ?? '',
            ];
        }

        // Colors
        $colors = $style['colors'] ?? [];

        return view('widgetbuilder::landlord.values.value', [
            'badgeText' => $badgeText,
            'heading' => $heading,
            'values' => $values,
            'sectionBg' => $colors['section_bg'] ?? '#ffffff',
            'badgeBg' => $colors['badge_bg'] ?? 'rgba(12, 77, 84, 0.1)',
            'badgeTextColor' => $colors['badge_text_color'] ?? '#0C4D54',
            'badgeBorderColor' => $colors['badge_border_color'] ?? '#e5e7eb',
            'cardBg' => $colors['card_bg'] ?? '#ffffff',
            'cardBorderColor' => $colors['card_border_color'] ?? '#e5e7eb',
            'iconBg' => $colors['icon_bg'] ?? 'rgba(12, 77, 84, 0.1)',
            'descriptionColor' => $colors['description_color'] ?? '#6B7280',
            'style' => $style,
        ])->render();
    }
}
