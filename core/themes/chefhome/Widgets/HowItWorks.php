<?php

namespace Themes\Chefhome\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class HowItWorks extends BaseWidget
{
    protected function getWidgetType(): string { return 'chefhome_how_it_works'; }
    protected function getWidgetName(): string { return 'ChefHome: How It Works'; }
    protected function getWidgetIcon(): string|array { return 'las la-list-ol'; }
    protected function getWidgetDescription(): string { return __('Numbered steps explaining the ordering process with icons and descriptions'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['steps', 'process', 'how it works', 'chefhome']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('header', 'Section Header')
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Section Title')
                ->setDefault('How It Works'))
            ->registerField('subtitle', FieldManager::TEXT()
                ->setLabel('Section Subtitle')
                ->setDefault('From our kitchen to your door in 3 simple steps'))
            ->endGroup();

        $control->addGroup('steps', 'Steps')
            ->registerField('steps', FieldManager::REPEATER()
                ->setLabel('Steps')
                ->setItemLabel('Step')
                ->setAddButtonText('Add Step')
                ->setMin(1)->setMax(6)
                ->setFields([
                    'icon'        => FieldManager::TEXT()->setLabel('Icon Class (las la-*)')->setDefault('las la-shopping-cart'),
                    'title'       => FieldManager::TEXT()->setLabel('Title')->setDefault('Choose Your Meal'),
                    'description' => FieldManager::TEXT()->setLabel('Description')->setDefault('Browse our menu and pick your favourites'),
                ])
                ->setDefault([
                    ['icon' => 'las la-utensils',     'title' => 'Browse Menu',   'description' => 'Pick your favourite dishes from our daily-changing kitchen menu'],
                    ['icon' => 'las la-credit-card',  'title' => 'Place Order',   'description' => 'Secure checkout in under 60 seconds, multiple payment options'],
                    ['icon' => 'las la-motorcycle',   'title' => 'Fast Delivery', 'description' => 'Hot food at your door, tracked in real time, always on time'],
                ]))
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top', FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(72)->setMin(0)->setMax(300))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(72)->setMin(0)->setMax(300))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {

        // Not in tenant context (editor preview API has no tenant middleware)
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;">ChefHome: How It Works — preview on the live page</p>
                    </div>';
        }
        $header   = $settings['general']['header'] ?? [];
        $stepsGrp = $settings['general']['steps']  ?? [];
        $spacing  = $settings['style']['spacing']  ?? [];

        $steps = $stepsGrp['steps'] ?? [
            ['icon' => 'las la-utensils',    'title' => 'Browse Menu',   'description' => 'Pick your favourite dishes from our daily-changing kitchen menu'],
            ['icon' => 'las la-credit-card', 'title' => 'Place Order',   'description' => 'Secure checkout in under 60 seconds, multiple payment options'],
            ['icon' => 'las la-motorcycle',  'title' => 'Fast Delivery', 'description' => 'Hot food at your door, tracked in real time, always on time'],
        ];

        return view('theme-chefhome::widgets.how_it_works', [
            'title'          => $header['title']    ?? 'How It Works',
            'subtitle'       => $header['subtitle'] ?? '',
            'steps'          => $steps,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 72),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 72),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'chefhome';
    }
}
