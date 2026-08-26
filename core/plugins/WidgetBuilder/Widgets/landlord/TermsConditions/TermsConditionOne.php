<?php

namespace Plugins\WidgetBuilder\Widgets\landlord\TermsConditions;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class TermsConditionOne extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'terms_condition_one';
    }

    protected function getWidgetName(): string
    {
        return 'Terms Condition One';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-file-contract';
    }

    protected function getWidgetDescription(): string
    {
        return 'Terms and conditions page with notice box and content sections';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['terms', 'conditions', 'legal', 'policy', 'privacy'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('notice', 'Important Notice Box')
            ->registerField('notice_title', FieldManager::TEXT()
                ->setLabel('Notice Title')
                ->setDefault('Important legal agreement:')
            )
            ->registerField('notice_content', FieldManager::TEXTAREA()
                ->setLabel('Notice Content')
                ->setDefault('These terms constitute a legally binding agreement between you and Nazmart. By creating an account or using our services, you agree to be bound by these terms.')
            )
            ->endGroup();

        $control->addGroup('sections', 'Content Sections')
            ->registerField('section_items', FieldManager::REPEATER()
                ->setLabel('Sections')
                ->setItemLabel('Section')
                ->setAddButtonText('Add Section')
                ->setMin(1)
                ->setMax(20)
                ->setFields([
                    'section_title' => FieldManager::TEXT()
                        ->setLabel('Section Title')
                        ->setDefault('Section Title'),
                    'section_content' => FieldManager::TEXTAREA()
                        ->setLabel('Section Content (HTML supported)')
                        ->setDescription('You can use HTML tags for formatting. Each new line creates a paragraph.')
                        ->setDefault('Section content goes here.'),
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
                ->setDefault(96)
                ->setMin(0)
                ->setMax(300)
                ->setStep(5)
            )
            ->registerField('padding_bottom', FieldManager::NUMBER()
                ->setLabel('Padding Bottom (px)')
                ->setDefault(96)
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

        // Notice box
        $noticeGroup = $general['notice'] ?? [];
        $noticeTitle = $noticeGroup['notice_title'] ?? 'Important legal agreement:';
        $noticeContent = $noticeGroup['notice_content'] ?? 'These terms constitute a legally binding agreement between you and Nazmart. By creating an account or using our services, you agree to be bound by these terms.';

        // Sections
        $sectionsGroup = $general['sections'] ?? [];
        $sectionItems = $sectionsGroup['section_items'] ?? [];

        $sections = [];
        foreach ($sectionItems as $item) {
            $sections[] = [
                'title' => $item['section_title'] ?? '',
                'content' => $item['section_content'] ?? '',
            ];
        }

        if (empty($sections) && empty($noticeContent)) {
            return '';
        }

        return view('widgetbuilder::landlord.terms_conditions.terms_condition', [
            'noticeTitle' => $noticeTitle,
            'noticeContent' => $noticeContent,
            'sections' => $sections,
        ])->render();
    }
}
