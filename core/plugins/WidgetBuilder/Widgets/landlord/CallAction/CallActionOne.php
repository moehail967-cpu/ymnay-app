<?php

namespace Plugins\WidgetBuilder\Widgets\landlord\CallAction;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class CallActionOne extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'call_action_one';
    }

    protected function getWidgetName(): string
    {
        return 'Call To Action One';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-bullhorn';
    }

    protected function getWidgetDescription(): string
    {
        return 'Call to action section with background image, heading, description and two CTA buttons';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['call to action', 'cta', 'banner', 'promotion'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Heading')
                ->setDefault('Ready to Launch Your Store?')
            )
            ->registerField('description', FieldManager::TEXTAREA()
                ->setLabel('Description')
                ->setDefault('Join 10,000 entrepreneurs already building successful online businesses. Start your 14-day free trial')
            )
            ->registerField('bg_image', FieldManager::IMAGE()
                ->setLabel('Background Image')
                ->setDescription('Background image for the CTA section')
            )
            ->endGroup();

        $control->addGroup('primary_button', 'Primary Button')
            ->registerField('primary_btn_text', FieldManager::TEXT()
                ->setLabel('Button Text')
                ->setDefault('Start Free Trial')
            )
            ->registerField('primary_btn_url', FieldManager::TEXT()
                ->setLabel('Button URL')
                ->setDefault('#')
            )
            ->endGroup();

        $control->addGroup('secondary_button', 'Secondary Button')
            ->registerField('secondary_btn_text', FieldManager::TEXT()
                ->setLabel('Button Text')
                ->setDefault('Watch Demo')
            )
            ->registerField('secondary_btn_url', FieldManager::TEXT()
                ->setLabel('Button URL')
                ->setDefault('#')
            )
            ->registerField('secondary_btn_icon', FieldManager::IMAGE()
                ->setLabel('Button Icon')
                ->setDescription('Icon displayed before button text (e.g. video play icon)')
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
                ->setDefault(140)
                ->setMin(0)
                ->setMax(300)
                ->setStep(5)
            )
            ->registerField('padding_bottom', FieldManager::NUMBER()
                ->setLabel('Padding Bottom (px)')
                ->setDefault(140)
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

        // Content
        $contentGroup = $general['content'] ?? [];
        $title = $contentGroup['title'] ?? 'Ready to Launch Your Store?';
        $description = $contentGroup['description'] ?? 'Join 10,000 entrepreneurs already building successful online businesses. Start your 14-day free trial';
        $bgImage = $this->resolveImageUrl($contentGroup['bg_image'] ?? '');

        // Primary Button
        $primaryGroup = $general['primary_button'] ?? [];
        $primaryBtnText = $primaryGroup['primary_btn_text'] ?? 'Start Free Trial';
        $primaryBtnUrl = $primaryGroup['primary_btn_url'] ?? '#';

        // Secondary Button
        $secondaryGroup = $general['secondary_button'] ?? [];
        $secondaryBtnText = $secondaryGroup['secondary_btn_text'] ?? 'Watch Demo';
        $secondaryBtnUrl = $secondaryGroup['secondary_btn_url'] ?? '#';
        $secondaryBtnIcon = $this->resolveImageUrl($secondaryGroup['secondary_btn_icon'] ?? '');

        return view('widgetbuilder::landlord.call_action.call_action', [
            'title' => $title,
            'description' => $description,
            'bgImage' => $bgImage,
            'primaryBtnText' => $primaryBtnText,
            'primaryBtnUrl' => $primaryBtnUrl,
            'secondaryBtnText' => $secondaryBtnText,
            'secondaryBtnUrl' => $secondaryBtnUrl,
            'secondaryBtnIcon' => $secondaryBtnIcon,
        ])->render();
    }
}
