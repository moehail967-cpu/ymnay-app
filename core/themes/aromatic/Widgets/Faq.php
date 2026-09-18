<?php

namespace Themes\Aromatic\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class Faq extends BaseWidget
{
    protected function getWidgetType(): string { return 'aromatic_faq'; }
    protected function getWidgetName(): string { return 'Aromatic: الأسئلة الشائعة'; }
    protected function getWidgetIcon(): string|array { return 'las la-question-circle'; }
    protected function getWidgetDescription(): string { return __('أسئلة وأجوبة قابلة للتعديل'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['aromatic', 'faq', 'contact']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'المحتوى')
            ->registerField('title', FieldManager::TEXT()->setLabel('العنوان')->setDefault('الأسئلة الأكثر شيوعاً'));
        for ($i = 1; $i <= 4; $i++) {
            $control->registerField("question_{$i}", FieldManager::TEXT()->setLabel("السؤال {$i}"))
                ->registerField("answer_{$i}", FieldManager::TEXTAREA()->setLabel("الإجابة {$i}"));
        }
        $control->endGroup();
        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'المسافات')
            ->registerField('padding_top', FieldManager::NUMBER()->setLabel('المسافة العلوية')->setDefault(90)->setMin(0)->setMax(300))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('المسافة السفلية')->setDefault(90)->setMin(0)->setMax(300))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        $content = $settings['general']['content'] ?? [];
        $spacing = $settings['style']['spacing'] ?? [];
        $items = [];
        for ($i = 1; $i <= 4; $i++) {
            if (!empty($content["question_{$i}"])) {
                $items[] = ['question' => $content["question_{$i}"], 'answer' => $content["answer_{$i}"] ?? ''];
            }
        }
        return view('theme-aromatic::widgets.faq', [
            'title' => $content['title'] ?? 'الأسئلة الأكثر شيوعاً',
            'items' => $items,
            'padding_top' => (int) ($spacing['padding_top'] ?? 90),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 90),
        ])->render();
    }

    public function enable(): bool
    {
        return function_exists('tenant') && tenant() && (tenant()->theme_slug ?? '') === 'aromatic';
    }
}
