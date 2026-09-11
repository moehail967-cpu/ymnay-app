<?php

namespace Plugins\WidgetBuilder\Widgets\Tenant;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class IconBoxWidget extends BaseWidget
{
    protected function getWidgetType(): string        { return 'tenant_icon_box'; }
    protected function getWidgetName(): string        { return 'Icon Box'; }
    protected function getWidgetIcon(): string|array  { return 'las la-icons'; }
    protected function getWidgetDescription(): string { return 'Icon with title and description text.'; }
    protected function getCategory(): string          { return WidgetCategory::BASIC; }
    protected function getWidgetTags(): array         { return ['icon', 'box', 'feature']; }

    public function enable(): bool { return !is_null(tenant()); }

    public function getGeneralFields(): array
    {
        $cm = new ControlManager();
        $cm->addGroup('content', 'Content')
            ->registerField('icon', FieldManager::ICON()
                ->setLabel('Icon')->setDefault('las la-star'))
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Title')->setDefault('Icon Box Title'))
            ->registerField('description', FieldManager::TEXTAREA()
                ->setLabel('Description'))
            ->registerField('link', FieldManager::URL()
                ->setLabel('Link (optional)'))
            ->registerField('alignment', FieldManager::ALIGNMENT()
                ->setDefault('center'))
            ->endGroup();
        return $cm->getFields();
    }

    public function getStyleFields(): array
    {
        $cm = new ControlManager();
        $cm->addGroup('icon_style', 'Icon')
            ->registerField('icon_size', FieldManager::NUMBER()
                ->setLabel('Icon Size (px)')->setDefault(48)->setMin(16)->setMax(120))
            ->registerField('icon_color', FieldManager::COLOR()
                ->setLabel('Icon Color')->setDefault('#6366f1'))
            ->registerField('icon_bg', FieldManager::COLOR()
                ->setLabel('Icon Background')->setDefault('transparent'))
            ->endGroup();
        $cm->addGroup('text_style', 'Text')
            ->registerField('title_color', FieldManager::COLOR()
                ->setLabel('Title Color')->setDefault('#111827'))
            ->registerField('desc_color', FieldManager::COLOR()
                ->setLabel('Description Color')->setDefault('#6b7280'))
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

        return view('widgetbuilder::tenant.icon_box', [
            'icon'           => $content['icon'] ?? 'las la-star',
            'title'          => $content['title'] ?? 'Icon Box Title',
            'description'    => $content['description'] ?? '',
            'link'           => $content['link'] ?? '',
            'alignment'      => $content['alignment'] ?? 'center',
            'padding_top'    => (int) ($spacing['padding_top'] ?? 60),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 60),
        ])->render();
    }
}
