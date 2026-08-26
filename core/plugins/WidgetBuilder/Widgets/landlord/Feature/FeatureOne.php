<?php

namespace Plugins\WidgetBuilder\Widgets\landlord\Feature;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class FeatureOne extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'feature_one';
    }

    protected function getWidgetName(): string
    {
        return 'Feature One';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-star';
    }

    protected function getWidgetDescription(): string
    {
        return 'Feature cards carousel with badge, heading, and background image cards';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['feature', 'card', 'slider', 'carousel', 'service'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('heading', 'Section Heading')
            ->registerField('badge_text', FieldManager::TEXT()
                ->setLabel('Badge Text')
                ->setDefault('Features')
            )
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Heading')
                ->setDefault('Powerful features to build and grow your online store')
            )
            ->endGroup();

        $control->addGroup('features', 'Feature Cards')
            ->registerField('feature_items', FieldManager::REPEATER()
                ->setLabel('Features')
                ->setItemLabel('Feature')
                ->setAddButtonText('Add Feature')
                ->setMin(1)
                ->setMax(20)
                ->setFields([
                    'title' => FieldManager::TEXT()
                        ->setLabel('Title')
                        ->setDefault('Payment Gateway'),
                    'description' => FieldManager::TEXTAREA()
                        ->setLabel('Description')
                        ->setDefault('Use your own domain to create a strong, memorable brand presence. A custom domain helps customers trust and recognize.'),
                    'bg_image' => FieldManager::IMAGE()
                        ->setLabel('Card Background Image')
                        ->setRequired(true),
                    'image' => FieldManager::IMAGE()
                        ->setLabel('Card Bottom Image')
                        ->setDescription('Icon or image displayed at the bottom of the card'),
                ])
            )
            ->endGroup();

        $control->addGroup('slider_settings', 'Slider Settings')
            ->registerField('cards_per_view', FieldManager::NUMBER()
                ->setLabel('Cards Per View (Desktop)')
                ->setDefault(4)
                ->setMin(1)
                ->setMax(6)
                ->setStep(1)
            )
            ->registerField('autoplay', FieldManager::TOGGLE()
                ->setLabel('Auto Play')
                ->setDefault(true)
            )
            ->registerField('autoplay_speed', FieldManager::NUMBER()
                ->setLabel('Auto Play Speed (ms)')
                ->setDefault(4000)
                ->setMin(1000)
                ->setMax(15000)
                ->setStep(500)
            )
            ->registerField('transition_speed', FieldManager::NUMBER()
                ->setLabel('Transition Speed (ms)')
                ->setDefault(500)
                ->setMin(200)
                ->setMax(2000)
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
                ->setDefault('#f0f1f3')
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
            ->registerField('card_fallback_bg', FieldManager::COLOR()
                ->setLabel('Card Fallback Background')
                ->setDescription('Used when no background image is set')
                ->setDefault('#1c3a4a')
            )
            ->registerField('overlay_color', FieldManager::COLOR()
                ->setLabel('Card Overlay Color')
                ->setDefault('#1a2e3a')
            )
            ->registerField('overlay_opacity', FieldManager::RANGE()
                ->setLabel('Card Overlay Opacity (%)')
                ->setDefault(70)
                ->setMin(0)
                ->setMax(100)
                ->setStep(5)
            )
            ->registerField('card_title_color', FieldManager::COLOR()
                ->setLabel('Card Title Color')
                ->setDefault('#ffffff')
            )
            ->registerField('card_desc_color', FieldManager::COLOR()
                ->setLabel('Card Description Color')
                ->setDefault('rgba(255,255,255,0.7)')
            )
            ->registerField('dot_active_color', FieldManager::COLOR()
                ->setLabel('Active Dot Color')
                ->setDefault('#1a1a2e')
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
        $badgeText = $headingGroup['badge_text'] ?? 'Features';
        $title = $headingGroup['title'] ?? 'Powerful features to build and grow your online store';

        // Features
        $featuresGroup = $general['features'] ?? [];
        $featureItems = $featuresGroup['feature_items'] ?? [];

        if (empty($featureItems)) {
            return '';
        }

        // Process feature items
        $cards = [];
        foreach ($featureItems as $item) {
            $cards[] = [
                'title' => $item['title'] ?? '',
                'description' => $item['description'] ?? '',
                'bg_image' => $this->resolveImageUrl($item['bg_image'] ?? ''),
                'image' => $this->resolveImageUrl($item['image'] ?? ''),
            ];
        }

        return view('widgetbuilder::landlord.feature.feature_one', [
            'badgeText' => $badgeText,
            'title' => $title,
            'cards' => $cards,
        ])->render();
    }
}
