<?php

namespace Plugins\WidgetBuilder\Widgets\landlord\OurStory;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class OurStoryOne extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'our_story';
    }

    protected function getWidgetName(): string
    {
        return 'Our Story';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-book-open';
    }

    protected function getWidgetDescription(): string
    {
        return 'Our story section with image and text content';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['story', 'about', 'history', 'content'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('heading', FieldManager::TEXT()
                ->setLabel('Heading')
                ->setDefault('Our Story')
            )
            ->registerField('image', FieldManager::IMAGE()
                ->setLabel('Story Image')
                ->setDescription('Main image for the story section')
            )
            ->registerField('image_alt', FieldManager::TEXT()
                ->setLabel('Image Alt Text')
                ->setDefault('Founder & CEO')
            )
            ->endGroup();

        $control->addGroup('paragraphs', 'Paragraphs')
            ->registerField('paragraph_items', FieldManager::REPEATER()
                ->setLabel('Paragraphs')
                ->setItemLabel('Paragraph')
                ->setAddButtonText('Add Paragraph')
                ->setMin(1)
                ->setMax(10)
                ->setFields([
                    'text' => FieldManager::TEXT()
                        ->setLabel('Paragraph Text')
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
                ->setDefault(144)
                ->setMin(0)->setMax(300)->setStep(5)
            )
            ->registerField('padding_bottom', FieldManager::NUMBER()
                ->setLabel('Padding Bottom (px)')
                ->setDefault(0)
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
            ->registerField('heading_color', FieldManager::COLOR()
                ->setLabel('Heading Color')
                ->setDefault('#0C4D54')
            )
            ->registerField('text_color', FieldManager::COLOR()
                ->setLabel('Text Color')
                ->setDefault('#4A5565')
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
        $heading = $content['heading'] ?? 'Our Story';
        $image = $this->resolveImageUrl($content['image'] ?? '');
        $imageAlt = $content['image_alt'] ?? 'Founder & CEO';

        // Paragraphs
        $paragraphGroup = $general['paragraphs'] ?? [];
        $paragraphItems = $paragraphGroup['paragraph_items'] ?? [];
        $paragraphs = [];
        foreach ($paragraphItems as $item) {
            $text = $item['text'] ?? '';
            if (!empty($text)) {
                $paragraphs[] = $text;
            }
        }

        // Colors
        $colors = $style['colors'] ?? [];

        return view('widgetbuilder::landlord.our_story.our_story', [
            'heading' => $heading,
            'image' => $image,
            'imageAlt' => $imageAlt,
            'paragraphs' => $paragraphs,
            'sectionBg' => $colors['section_bg'] ?? '#ffffff',
            'headingColor' => $colors['heading_color'] ?? '#0C4D54',
            'textColor' => $colors['text_color'] ?? '#4A5565',
            'style' => $style,
        ])->render();
    }
}
