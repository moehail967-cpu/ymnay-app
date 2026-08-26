<?php

namespace Plugins\WidgetBuilder\Widgets\landlord\Themes;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class ThemesTwo extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'themes_two';
    }

    protected function getWidgetName(): string
    {
        return 'Themes Two';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-palette';
    }

    protected function getWidgetDescription(): string
    {
        return 'Theme showcase section with card-style slider and prev/next navigation';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['themes', 'showcase', 'slider', 'cards'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('heading', 'Section Heading')
            ->registerField('badge_text', FieldManager::TEXT()
                ->setLabel('Badge Text')
                ->setDefault('Themes')
            )
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Heading')
                ->setDefault('Modern Themes Built to Convert')
            )
            ->registerField('bg_image', FieldManager::IMAGE()
                ->setLabel('Background Image')
                ->setDescription('Dark background image for the section')
            )
            ->endGroup();

        $control->addGroup('theme_cards', 'Theme Cards')
            ->registerField('cards', FieldManager::REPEATER()
                ->setLabel('Theme Cards')
                ->setItemLabel('Theme')
                ->setAddButtonText('Add Theme')
                ->setMin(1)
                ->setMax(20)
                ->setFields([
                    'image' => FieldManager::IMAGE()
                        ->setLabel('Theme Screenshot')
                        ->setRequired(true),
                    'title' => FieldManager::TEXT()
                        ->setLabel('Theme Name')
                        ->setDefault('Theme Name'),
                    'button_text' => FieldManager::TEXT()
                        ->setLabel('Button Text')
                        ->setDefault('Preview'),
                    'button_url' => FieldManager::TEXT()
                        ->setLabel('Button URL')
                        ->setDefault('#'),
                ])
            )
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('section_style', 'Section')
            ->registerField('overlay_opacity', FieldManager::RANGE()
                ->setLabel('Overlay Opacity (%)')
                ->setDefault(80)
                ->setMin(0)
                ->setMax(100)
                ->setStep(5)
            )
            ->registerField('padding_top', FieldManager::NUMBER()
                ->setLabel('Padding Top (px)')
                ->setDefault(112)
                ->setMin(0)
                ->setMax(300)
                ->setStep(5)
            )
            ->registerField('padding_bottom', FieldManager::NUMBER()
                ->setLabel('Padding Bottom (px)')
                ->setDefault(112)
                ->setMin(0)
                ->setMax(300)
                ->setStep(5)
            )
            ->registerField('margin_top', FieldManager::NUMBER()
                ->setLabel('Margin Top (px)')
                ->setDefault(0)
                ->setMin(0)
                ->setMax(300)
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
        $badgeText = $headingGroup['badge_text'] ?? 'Themes';
        $title = $headingGroup['title'] ?? 'Modern Themes Built to Convert';
        $bgImage = $this->resolveImageUrl($headingGroup['bg_image'] ?? '');

        // Theme cards
        $cardsGroup = $general['theme_cards'] ?? [];
        $cardItems = $cardsGroup['cards'] ?? [];
        $cards = [];
        foreach ($cardItems as $item) {
            $url = $this->resolveImageUrl($item['image'] ?? '');
            if (!empty($url)) {
                $cards[] = [
                    'image' => $url,
                    'title' => $item['title'] ?? 'Theme Name',
                    'button_text' => $item['button_text'] ?? 'Preview',
                    'button_url' => $item['button_url'] ?? '#',
                ];
            }
        }

        if (empty($cards)) {
            return '';
        }

        return view('widgetbuilder::landlord.themes.themes_two', [
            'badgeText' => $badgeText,
            'title' => $title,
            'bgImage' => $bgImage,
            'cards' => $cards,
        ])->render();
    }
}
