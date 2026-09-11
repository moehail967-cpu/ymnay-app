<?php

namespace Plugins\WidgetBuilder\Widgets\landlord\MissionVision;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class MissionVisionOne extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'mission_vision';
    }

    protected function getWidgetName(): string
    {
        return 'Mission & Vision';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-bullseye';
    }

    protected function getWidgetDescription(): string
    {
        return 'Mission and Vision section with icon cards';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['mission', 'vision', 'about', 'values'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('mission', 'Mission')
            ->registerField('mission_icon', FieldManager::IMAGE()
                ->setLabel('Mission Icon')
                ->setDescription('Icon image for mission card')
            )
            ->registerField('mission_title', FieldManager::TEXT()
                ->setLabel('Mission Title')
                ->setDefault('Our Mission')
            )
            ->registerField('mission_description', FieldManager::TEXT()
                ->setLabel('Mission Description')
                ->setDefault('To empower every entrepreneur and business—regardless of technical skills or budget—with the tools they need to build, launch, and scale successful online stores.')
            )
            ->endGroup();

        $control->addGroup('vision', 'Vision')
            ->registerField('vision_icon', FieldManager::IMAGE()
                ->setLabel('Vision Icon')
                ->setDescription('Icon image for vision card')
            )
            ->registerField('vision_title', FieldManager::TEXT()
                ->setLabel('Vision Title')
                ->setDefault('Our Vision')
            )
            ->registerField('vision_description', FieldManager::TEXT()
                ->setLabel('Vision Description')
                ->setDefault('To empower every entrepreneur and business—regardless of technical skills or budget—with the tools they need to build, launch, and scale successful online stores.')
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

        // Mission
        $mission = $general['mission'] ?? [];
        $missionIcon = $this->resolveImageUrl($mission['mission_icon'] ?? '');
        $missionTitle = $mission['mission_title'] ?? 'Our Mission';
        $missionDescription = $mission['mission_description'] ?? '';

        // Vision
        $vision = $general['vision'] ?? [];
        $visionIcon = $this->resolveImageUrl($vision['vision_icon'] ?? '');
        $visionTitle = $vision['vision_title'] ?? 'Our Vision';
        $visionDescription = $vision['vision_description'] ?? '';

        // Colors
        $colors = $style['colors'] ?? [];

        return view('widgetbuilder::landlord.mission_vision.mission_vision', [
            'missionIcon' => $missionIcon,
            'missionTitle' => $missionTitle,
            'missionDescription' => $missionDescription,
            'visionIcon' => $visionIcon,
            'visionTitle' => $visionTitle,
            'visionDescription' => $visionDescription,
            'sectionBg' => $colors['section_bg'] ?? '#ffffff',
            'cardBg' => $colors['card_bg'] ?? '#ffffff',
            'cardBorderColor' => $colors['card_border_color'] ?? '#e5e7eb',
            'iconBg' => $colors['icon_bg'] ?? 'rgba(12, 77, 84, 0.1)',
            'titleColor' => $colors['title_color'] ?? '#0C4D54',
            'descriptionColor' => $colors['description_color'] ?? '#6B7280',
            'style' => $style,
        ])->render();
    }
}
