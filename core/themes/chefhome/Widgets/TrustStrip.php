<?php

namespace Themes\Chefhome\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class TrustStrip extends BaseWidget
{
    protected function getWidgetType(): string { return 'chefhome_trust_strip'; }
    protected function getWidgetName(): string { return 'ChefHome: Trust Strip'; }
    protected function getWidgetIcon(): string|array { return 'las la-shield-alt'; }
    protected function getWidgetDescription(): string { return __('Horizontal strip of trust/service icons with label and description'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['trust', 'services', 'strip', 'chefhome']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('items', 'Trust Items')
            ->registerField('items', FieldManager::REPEATER()
                ->setLabel('Items')
                ->setItemLabel('Item')
                ->setAddButtonText('Add Item')
                ->setMin(1)->setMax(6)
                ->setFields([
                    'icon'        => FieldManager::TEXT()->setLabel('Icon Class (las la-*)')->setDefault('las la-shipping-fast'),
                    'label'       => FieldManager::TEXT()->setLabel('Label')->setDefault('Fast Delivery'),
                    'description' => FieldManager::TEXT()->setLabel('Description')->setDefault('Order by noon, delivered by 6pm'),
                ])
                ->setDefault([
                    ['icon' => 'las la-shipping-fast', 'label' => 'Fast Delivery',  'description' => 'Order by noon, delivered by 6pm'],
                    ['icon' => 'las la-leaf',          'label' => 'Fresh Daily',     'description' => 'Ingredients sourced every morning'],
                    ['icon' => 'las la-utensils',      'label' => 'Chef Crafted',    'description' => 'Recipes from professional kitchens'],
                    ['icon' => 'las la-headset',       'label' => '24/7 Support',    'description' => 'Always here to help you'],
                ]))
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top', FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(0)->setMin(0)->setMax(200))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(40)->setMin(0)->setMax(200))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {

        // Not in tenant context (editor preview API has no tenant middleware)
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;">ChefHome: Trust Strip — preview on the live page</p>
                    </div>';
        }
        $itemsGrp = $settings['general']['items']   ?? [];
        $spacing  = $settings['style']['spacing']   ?? [];

        $items = $itemsGrp['items'] ?? [
            ['icon' => 'las la-shipping-fast', 'label' => 'Fast Delivery',  'description' => 'Order by noon, delivered by 6pm'],
            ['icon' => 'las la-leaf',          'label' => 'Fresh Daily',     'description' => 'Ingredients sourced every morning'],
            ['icon' => 'las la-utensils',      'label' => 'Chef Crafted',    'description' => 'Recipes from professional kitchens'],
            ['icon' => 'las la-headset',       'label' => '24/7 Support',    'description' => 'Always here to help you'],
        ];

        return view('theme-chefhome::widgets.trust_strip', [
            'items'          => $items,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 0),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 40),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'chefhome';
    }
}
