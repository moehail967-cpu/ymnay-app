<?php

namespace Plugins\WidgetBuilder\Widgets\Tenant;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class BoxWidget extends BaseWidget
{
    protected function getWidgetType(): string        { return 'tenant_box'; }
    protected function getWidgetName(): string        { return 'Box'; }
    protected function getWidgetIcon(): string|array  { return 'las la-square'; }
    protected function getWidgetDescription(): string { return 'Content card with heading and body text.'; }
    protected function getCategory(): string          { return WidgetCategory::BASIC; }
    protected function getWidgetTags(): array         { return ['box', 'card', 'content']; }

    public function enable(): bool { return !is_null(tenant()); }

    public function getGeneralFields(): array
    {
        $cm = new ControlManager();
        $cm->addGroup('content', 'Content')
            ->registerField('heading', FieldManager::TEXT()
                ->setLabel('Heading')->setDefault('Your Heading'))
            ->registerField('content', FieldManager::WYSIWYG()
                ->setLabel('Content'))
            ->registerField('alignment', FieldManager::ALIGNMENT()
                ->setDefault('center'))
            ->endGroup();
        return $cm->getFields();
    }

    public function getStyleFields(): array
    {
        $cm = new ControlManager();
        $cm->addGroup('card', 'Card')
            ->registerField('bg_color', FieldManager::COLOR()
                ->setLabel('Background Color')->setDefault('#ffffff'))
            ->registerField('text_color', FieldManager::COLOR()
                ->setLabel('Text Color')->setDefault('#333333'))
            ->registerField('border_radius', FieldManager::NUMBER()
                ->setLabel('Border Radius (px)')->setDefault(12)->setMin(0)->setMax(60))
            ->endGroup();
        $cm->addGroup('spacing', 'Spacing')
            ->registerField('padding_top', FieldManager::NUMBER()
                ->setLabel('Padding Top (px)')->setDefault(60)->setMin(0)->setMax(300))
            ->registerField('padding_bottom', FieldManager::NUMBER()
                ->setLabel('Padding Bottom (px)')->setDefault(60)->setMin(0)->setMax(300))
            ->endGroup();
        return $cm->getFields();
    }

    public function render(array $settings = []): string
    {
        $content = $settings['general']['content'] ?? [];
        $spacing = $settings['style']['spacing'] ?? [];

        return view('widgetbuilder::tenant.box', [
            'heading'        => $content['heading'] ?? 'Your Heading',
            'body'           => $content['content'] ?? '',
            'alignment'      => $content['alignment'] ?? 'center',
            'padding_top'    => (int) ($spacing['padding_top'] ?? 60),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 60),
        ])->render();
    }
}
