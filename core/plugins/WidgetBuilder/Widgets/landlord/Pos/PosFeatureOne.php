<?php

namespace Plugins\WidgetBuilder\Widgets\landlord\Pos;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class PosFeatureOne extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'pos_feature_one';
    }

    protected function getWidgetName(): string
    {
        return 'POS Feature One';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-cash-register';
    }

    protected function getWidgetDescription(): string
    {
        return 'POS feature cards section with dark background, icons, titles and descriptions';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['pos', 'features', 'cards', 'grid'];
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
                ->setDefault('Everything you need to run your store efficiently')
            )
            ->registerField('bg_image', FieldManager::IMAGE()
                ->setLabel('Background Image')
                ->setDescription('Dark background image for the section')
            )
            ->endGroup();

        $control->addGroup('features', 'Feature Cards')
            ->registerField('feature_items', FieldManager::REPEATER()
                ->setLabel('Features')
                ->setItemLabel('Feature')
                ->setAddButtonText('Add Feature')
                ->setMin(1)
                ->setMax(12)
                ->setFields([
                    'icon' => FieldManager::IMAGE()
                        ->setLabel('Icon Image')
                        ->setDescription('Feature icon (SVG or PNG)'),
                    'title' => FieldManager::TEXT()
                        ->setLabel('Title')
                        ->setDefault('Real-time Sync'),
                    'description' => FieldManager::TEXTAREA()
                        ->setLabel('Description')
                        ->setDefault('Instant inventory & sales updates across all channels'),
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

        $control->addGroup('card_style', 'Card')
            ->registerField('card_bg', FieldManager::COLOR()
                ->setLabel('Card Background')
                ->setDefault('#00252A')
            )
            ->registerField('card_border_color', FieldManager::COLOR()
                ->setLabel('Card Border Color')
                ->setDefault('#2a4a4e')
            )
            ->registerField('icon_bg', FieldManager::COLOR()
                ->setLabel('Icon Background')
                ->setDefault('#92E721')
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
        $title = $headingGroup['title'] ?? 'Everything you need to run your store efficiently';
        $bgImage = $this->resolveImageUrl($headingGroup['bg_image'] ?? '');

        // Features
        $featuresGroup = $general['features'] ?? [];
        $featureItems = $featuresGroup['feature_items'] ?? [];

        if (empty($featureItems)) {
            return '';
        }

        $cards = [];
        foreach ($featureItems as $item) {
            $cards[] = [
                'icon' => $this->resolveImageUrl($item['icon'] ?? ''),
                'title' => $item['title'] ?? '',
                'description' => $item['description'] ?? '',
            ];
        }

        return view('widgetbuilder::landlord.pos.pos_feature', [
            'badgeText' => $badgeText,
            'title' => $title,
            'bgImage' => $bgImage,
            'cards' => $cards,
        ])->render();
    }
}
