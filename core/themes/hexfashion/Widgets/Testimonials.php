<?php

namespace Themes\Hexfashion\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class Testimonials extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'hexfashion_testimonials';
    }
    protected function getWidgetName(): string
    {
        return 'HexFashion: Testimonials';
    }
    protected function getWidgetIcon(): string|array
    {
        return 'las la-quote-right';
    }
    protected function getWidgetDescription(): string
    {
        return __('3-column customer review cards with quote, name and stars');
    }
    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }
    protected function getWidgetTags(): array
    {
        return ['testimonials', 'reviews', 'hexfashion'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('header', 'Section Header')
            ->registerField('title', FieldManager::TEXT()->setLabel('Title')->setDefault('Client\'s Feedback'))
            ->registerField('subtitle', FieldManager::TEXTAREA()->setLabel('Subtitle')->setDefault('Gourmet Gardening Supplies sells a variety of gardening tools and supplies.'))
            ->registerField('quote_text', FieldManager::TEXT()->setLabel('Quote Icon Text')->setDefault(''))
            ->registerField('item_show', FieldManager::NUMBER()->setLabel('Items to Show')->setDefault(6)->setMin(1)->setMax(20))
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
            return '<div style="padding:40px;text-align:center;background:#fff;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">HexFashion: Testimonials — preview on the live page</p></div>';
        }

        $header = $settings['general']['header'] ?? [];
        $spacing = $settings['style']['spacing'] ?? [];
        $limit = (int) ($header['item_show'] ?? 6);

        $testimonials = \App\Models\Testimonial::where('status', 1)->take($limit)->get();

        return view('theme-hexfashion::widgets.testimonials', [
            'title' => $header['title'] ?? 'Client\'s Feedback',
            'subtitle' => $header['subtitle'] ?? 'Gourmet Gardening Supplies sells a variety of gardening tools and supplies.',
            'quote_text' => $header['quote_text'] ?? '',
            'testimonials' => $testimonials,
            'padding_top' => (int) ($spacing['padding_top'] ?? 80),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 80),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'hexfashion';
    }
}
