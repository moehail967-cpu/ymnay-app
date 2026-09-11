<?php

namespace Themes\Hexfashion\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class FlashStore extends BaseWidget
{
    protected function getWidgetType(): string { return 'hexfashion_flash_store'; }
    protected function getWidgetName(): string { return 'HexFashion: Flash Store'; }
    protected function getWidgetIcon(): string|array { return 'las la-shopping-bag'; }
    protected function getWidgetDescription(): string { return __('Flash store product slider with prev/next arrows'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['flash', 'store', 'slider', 'hexfashion']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()->setLabel('Title')->setDefault('FLASH STORE'))
            ->registerField('count', FieldManager::NUMBER()->setLabel('Products to Show')->setDefault(8)->setMin(1)->setMax(24))
            ->endGroup();
        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top', FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(80)->setMin(0)->setMax(200))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(80)->setMin(0)->setMax(200))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">HexFashion: Flash Store — preview on the live page</p></div>';
        }

        $content  = $settings['general']['content'] ?? [];
        $spacing  = $settings['style']['spacing']   ?? [];
        $count    = (int) ($content['count'] ?? 8);
        $products = function_exists('theme_featured_products') ? theme_featured_products($count) : collect();

        return view('theme-hexfashion::widgets.flash_store', [
            'title'          => $content['title']          ?? 'FLASH STORE',
            'products'       => $products,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 80),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 80),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'hexfashion';
    }
}
