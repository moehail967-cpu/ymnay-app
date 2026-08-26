<?php

namespace Plugins\WidgetBuilder\Widgets\landlord\Accordion;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class AccordionOne extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'accordion_one';
    }

    protected function getWidgetName(): string
    {
        return 'Accordion One';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-question-circle';
    }

    protected function getWidgetDescription(): string
    {
        return 'FAQ accordion section with expandable question and answer items';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['accordion', 'faq', 'questions', 'answers'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('heading', 'Section Heading')
            ->registerField('badge_text', FieldManager::TEXT()
                ->setLabel('Badge Text')
                ->setDefault('Need Help?')
            )
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Heading')
                ->setDefault('Let us clear your doubts about our eCommerce builder.')
            )
            ->endGroup();

        $control->addGroup('faq_items', 'FAQ Items')
            ->registerField('items', FieldManager::REPEATER()
                ->setLabel('FAQ Items')
                ->setItemLabel('FAQ Item')
                ->setAddButtonText('Add FAQ Item')
                ->setMin(1)
                ->setMax(20)
                ->setFields([
                    'question' => FieldManager::TEXT()
                        ->setLabel('Question')
                        ->setDefault('Do I need coding skills to build a store?'),
                    'answer' => FieldManager::TEXTAREA()
                        ->setLabel('Answer')
                        ->setDefault('No! Our drag & drop builder is designed for anyone to use. Simply choose a template, customize it to your liking, and you are ready to launch. No coding knowledge required.'),
                ])
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
                ->setDefault(144)
                ->setMin(0)
                ->setMax(300)
                ->setStep(5)
            )
            ->registerField('padding_bottom', FieldManager::NUMBER()
                ->setLabel('Padding Bottom (px)')
                ->setDefault(144)
                ->setMin(0)
                ->setMax(300)
                ->setStep(5)
            )
            ->endGroup();

        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        $general = $settings['general'] ?? [];

        // Heading
        $headingGroup = $general['heading'] ?? [];
        $badgeText = $headingGroup['badge_text'] ?? 'Need Help?';
        $title = $headingGroup['title'] ?? 'Let us clear your doubts about our eCommerce builder.';

        // FAQ Items
        $faqGroup = $general['faq_items'] ?? [];
        $faqItems = $faqGroup['items'] ?? [];

        if (empty($faqItems)) {
            return '';
        }

        $items = [];
        foreach ($faqItems as $item) {
            $items[] = [
                'question' => $item['question'] ?? '',
                'answer' => $item['answer'] ?? '',
            ];
        }

        return view('widgetbuilder::landlord.accordion.accordion', [
            'badgeText' => $badgeText,
            'title' => $title,
            'items' => $items,
        ])->render();
    }
}
