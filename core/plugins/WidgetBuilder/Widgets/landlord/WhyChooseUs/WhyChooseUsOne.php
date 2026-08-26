<?php

namespace Plugins\WidgetBuilder\Widgets\landlord\WhyChooseUs;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class WhyChooseUsOne extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'why_choose_us';
    }

    protected function getWidgetName(): string
    {
        return 'Why Choose Us';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-check-circle';
    }

    protected function getWidgetDescription(): string
    {
        return 'Why choose us section with feature cards grid';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['why', 'choose', 'features', 'benefits'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('badge_text', FieldManager::TEXT()
                ->setLabel('Badge Text')
                ->setDefault('Why Choose Us')
            )
            ->registerField('heading', FieldManager::TEXT()
                ->setLabel('Heading')
                ->setDefault('What Makes Nazmart Different')
            )
            ->endGroup();

        $control->addGroup('feature_items', 'Feature Items')
            ->registerField('items', FieldManager::REPEATER()
                ->setLabel('Features')
                ->setItemLabel('Feature')
                ->setAddButtonText('Add Feature')
                ->setMin(1)
                ->setMax(12)
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
                ->setDefault('#0C4D54')
            )
            ->registerField('badge_bg', FieldManager::COLOR()
                ->setLabel('Badge Background Color')
                ->setDefault('rgba(232, 200, 255, 0.15)')
            )
            ->registerField('badge_text_color', FieldManager::COLOR()
                ->setLabel('Badge Text Color')
                ->setDefault('#ffffff')
            )
            ->registerField('badge_border_color', FieldManager::COLOR()
                ->setLabel('Badge Border Color')
                ->setDefault('#e5e7eb')
            )
            ->registerField('heading_color', FieldManager::COLOR()
                ->setLabel('Heading Color')
                ->setDefault('#ffffff')
            )
            ->registerField('card_bg', FieldManager::COLOR()
                ->setLabel('Card Background Color')
                ->setDefault('#ffffff')
            )
            ->registerField('card_border_color', FieldManager::COLOR()
                ->setLabel('Card Border Color')
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
        $badgeText = $content['badge_text'] ?? 'Why Choose Us';
        $heading = $content['heading'] ?? 'What Makes Nazmart Different';

        // Feature Items
        $featureGroup = $general['feature_items'] ?? [];
        $featureItems = $featureGroup['items'] ?? [];
        $features = [];
        foreach ($featureItems as $item) {
            $features[] = [
                'icon' => $this->resolveImageUrl($item['icon'] ?? ''),
                'title' => $item['title'] ?? '',
                'description' => $item['description'] ?? '',
            ];
        }

        // Colors
        $colors = $style['colors'] ?? [];

        return view('widgetbuilder::landlord.why_choose_us.why_choose_us', [
            'badgeText' => $badgeText,
            'heading' => $heading,
            'features' => $features,
            'sectionBg' => $colors['section_bg'] ?? '#0C4D54',
            'badgeBg' => $colors['badge_bg'] ?? 'rgba(232, 200, 255, 0.15)',
            'badgeTextColor' => $colors['badge_text_color'] ?? '#ffffff',
            'badgeBorderColor' => $colors['badge_border_color'] ?? '#e5e7eb',
            'headingColor' => $colors['heading_color'] ?? '#ffffff',
            'cardBg' => $colors['card_bg'] ?? '#ffffff',
            'cardBorderColor' => $colors['card_border_color'] ?? '#6B7280',
            'style' => $style,
        ])->render();
    }
}
