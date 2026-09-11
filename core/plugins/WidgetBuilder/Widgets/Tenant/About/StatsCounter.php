<?php

namespace Plugins\WidgetBuilder\Widgets\Tenant\About;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class StatsCounter extends BaseWidget
{
    protected function getWidgetType(): string { return 'tenant_stats_counter'; }
    protected function getWidgetName(): string { return 'Stats Counter'; }
    protected function getWidgetIcon(): string|array { return 'las la-chart-bar'; }
    protected function getWidgetDescription(): string { return __('Animated number counters with icon, number, suffix and label'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['stats', 'counter', 'numbers', 'about']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()->setLabel('Section Title (optional)')->setDefault(''))
            ->registerField('stats', FieldManager::REPEATER()
                ->setLabel('Stats')
                ->setItemLabel('Stat')
                ->setAddButtonText('Add Stat')
                ->setMin(1)->setMax(8)
                ->setFields([
                    'icon'   => FieldManager::TEXT()->setLabel('Icon Class (e.g. las la-users)')->setDefault('las la-users'),
                    'number' => FieldManager::TEXT()->setLabel('Number')->setDefault('10'),
                    'suffix' => FieldManager::TEXT()->setLabel('Suffix (e.g. K+ or %)')->setDefault('K+'),
                    'label'  => FieldManager::TEXT()->setLabel('Label')->setDefault('Happy Customers'),
                ])
                ->setDefault([
                    ['icon' => 'las la-users',        'number' => '10',  'suffix' => 'K+', 'label' => 'Happy Customers'],
                    ['icon' => 'las la-box',          'number' => '500', 'suffix' => '+',  'label' => 'Products'],
                    ['icon' => 'las la-star',         'number' => '4.9', 'suffix' => '',   'label' => 'Average Rating'],
                    ['icon' => 'las la-shipping-fast','number' => '99',  'suffix' => '%',  'label' => 'On-Time Delivery'],
                ])
            )
            ->endGroup();
        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('style', 'Style')
            ->registerField('background_color', FieldManager::COLOR()->setLabel('Background Color')->setDefault('#f8f9fa'))
            ->endGroup();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top',    FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(60)->setMin(0)->setMax(200))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(60)->setMin(0)->setMax(200))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">Stats Counter — preview on the live page</p></div>';
        }

        $content = $settings['general']['content'] ?? [];
        $style   = $settings['style']['style']     ?? [];
        $spacing = $settings['style']['spacing']   ?? [];

        return view('widgetbuilder::tenant.about.stats-counter', [
            'title'            => $content['title']          ?? '',
            'stats'            => !empty($content['stats']) ? $content['stats'] : [
                ['icon' => 'las la-users',        'number' => '10',  'suffix' => 'K+', 'label' => 'Happy Customers'],
                ['icon' => 'las la-box',          'number' => '500', 'suffix' => '+',  'label' => 'Products'],
                ['icon' => 'las la-star',         'number' => '4.9', 'suffix' => '',   'label' => 'Average Rating'],
                ['icon' => 'las la-shipping-fast','number' => '99',  'suffix' => '%',  'label' => 'On-Time Delivery'],
            ],
            'background_color' => $style['background_color'] ?? '#f8f9fa',
            'padding_top'      => (int) ($spacing['padding_top']    ?? 60),
            'padding_bottom'   => (int) ($spacing['padding_bottom'] ?? 60),
        ])->render();
    }

    public function enable(): bool { return !is_null(tenant()); }
}
