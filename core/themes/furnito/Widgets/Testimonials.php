<?php

namespace Themes\Furnito\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class Testimonials extends BaseWidget
{
    protected function getWidgetType(): string { return 'furnito_testimonials'; }
    protected function getWidgetName(): string { return 'Furnito: Testimonials'; }
    protected function getWidgetIcon(): string|array { return 'las la-quote-right'; }
    protected function getWidgetDescription(): string { return __('3-column customer review cards with quote, name and stars'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['testimonials', 'reviews', 'furnito']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('review_1_text',  FieldManager::TEXTAREA()->setLabel('Review 1 Text')->setDefault('There a none who does not wish to be free from the tools from them. Can help only if tools. Can help from them.'))
            ->registerField('review_1_name',  FieldManager::TEXT()->setLabel('Review 1 Author')->setDefault('Eric John'))
            ->registerField('review_1_stars', FieldManager::NUMBER()->setLabel('Review 1 Stars')->setDefault(5)->setMin(1)->setMax(5))
            ->registerField('review_2_text',  FieldManager::TEXTAREA()->setLabel('Review 2 Text')->setDefault('Fabulous pieces that help us along the way from them. Can help only if tools help. Can help from them.'))
            ->registerField('review_2_name',  FieldManager::TEXT()->setLabel('Review 2 Author')->setDefault('Eric Southwood'))
            ->registerField('review_2_stars', FieldManager::NUMBER()->setLabel('Review 2 Stars')->setDefault(5)->setMin(1)->setMax(5))
            ->registerField('review_3_text',  FieldManager::TEXTAREA()->setLabel('Review 3 Text')->setDefault('There a none who does not wish to be free from the tools from them. Help only if tools. Can help from them.'))
            ->registerField('review_3_name',  FieldManager::TEXT()->setLabel('Review 3 Author')->setDefault('Jenny Wilson'))
            ->registerField('review_3_stars', FieldManager::NUMBER()->setLabel('Review 3 Stars')->setDefault(4)->setMin(1)->setMax(5))
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
            return '<div style="padding:40px;text-align:center;background:#fff;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">Furnito: Testimonials — preview on the live page</p></div>';
        }

        $content = $settings['general']['content'] ?? [];
        $spacing = $settings['style']['spacing']   ?? [];

        $reviews = [
            ['text' => $content['review_1_text'] ?? '', 'name' => $content['review_1_name'] ?? 'Customer', 'stars' => (int) ($content['review_1_stars'] ?? 5)],
            ['text' => $content['review_2_text'] ?? '', 'name' => $content['review_2_name'] ?? 'Customer', 'stars' => (int) ($content['review_2_stars'] ?? 5)],
            ['text' => $content['review_3_text'] ?? '', 'name' => $content['review_3_name'] ?? 'Customer', 'stars' => (int) ($content['review_3_stars'] ?? 4)],
        ];

        return view('theme-furnito::widgets.testimonials', [
            'reviews'        => $reviews,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 80),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 80),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'furnito';
    }
}
