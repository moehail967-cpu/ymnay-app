<?php

namespace Themes\Furnito\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class OurStore extends BaseWidget
{
    protected function getWidgetType(): string { return 'furnito_our_store'; }
    protected function getWidgetName(): string { return 'Furnito: Our Store'; }
    protected function getWidgetIcon(): string|array { return 'las la-store'; }
    protected function getWidgetDescription(): string { return __('Tabbed product grid: Sale / Popular / New filter tabs'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['store', 'tabs', 'products', 'furnito']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('title',         FieldManager::TEXT()->setLabel('Section Title')->setDefault('Our Store'))
            ->registerField('subtitle',      FieldManager::TEXTAREA()->setLabel('Section Subtitle')->setDefault('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus malesuada nulla.'))
            ->registerField('tab_1_label',   FieldManager::TEXT()->setLabel('Tab 1 Label')->setDefault('Sale'))
            ->registerField('tab_2_label',   FieldManager::TEXT()->setLabel('Tab 2 Label')->setDefault('Popular'))
            ->registerField('tab_3_label',   FieldManager::TEXT()->setLabel('Tab 3 Label')->setDefault('New'))
            ->registerField('product_limit', FieldManager::NUMBER()->setLabel('Products per Tab')->setDefault(8)->setMin(4)->setMax(16))
            ->endGroup();
        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top',    FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(80)->setMin(0)->setMax(200))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(80)->setMin(0)->setMax(200))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">Furnito: Our Store — preview on the live page</p></div>';
        }

        $content = $settings['general']['content'] ?? [];
        $spacing = $settings['style']['spacing']   ?? [];
        $limit   = (int) ($content['product_limit'] ?? 8);

        // Fetch products for each tab
        $all   = function_exists('theme_featured_products') ? theme_featured_products($limit * 3) : collect();
        $tab1  = $all->take($limit);
        $tab2  = $all->skip($limit)->take($limit);
        $tab3  = $all->skip($limit * 2)->take($limit);

        // If not enough products, cycle
        if ($tab2->isEmpty()) $tab2 = $tab1;
        if ($tab3->isEmpty()) $tab3 = $tab1;

        return view('theme-furnito::widgets.our_store', [
            'title'          => $content['title']       ?? 'Our Store',
            'subtitle'       => $content['subtitle']    ?? '',
            'tab_1_label'    => $content['tab_1_label'] ?? 'Sale',
            'tab_2_label'    => $content['tab_2_label'] ?? 'Popular',
            'tab_3_label'    => $content['tab_3_label'] ?? 'New',
            'tab_1_products' => $tab1,
            'tab_2_products' => $tab2,
            'tab_3_products' => $tab3,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 80),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 80),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'furnito';
    }
}
