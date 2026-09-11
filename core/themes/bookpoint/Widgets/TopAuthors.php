<?php

namespace Themes\Bookpoint\Widgets;

use Modules\Attributes\Entities\Brand as BrandModel;
use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class TopAuthors extends BaseWidget
{
    protected function getWidgetType(): string { return 'bookpoint_top_authors'; }
    protected function getWidgetName(): string { return 'BookPoint: Top Authors'; }
    protected function getWidgetIcon(): string|array { return 'las la-user-edit'; }
    protected function getWidgetDescription(): string { return __('Author/publisher slider with colored cards'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['authors', 'brands', 'slider', 'bookpoint']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()->setLabel('Section Title')->setDefault('Top Authors'))
            ->registerField('limit', FieldManager::NUMBER()->setLabel('Authors to Show')->setDefault(8)->setMin(1)->setMax(30))
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
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">BookPoint: Top Authors — preview on the live page</p></div>';
        }

        $content = $settings['general']['content'] ?? [];
        $spacing = $settings['style']['spacing']   ?? [];
        $limit   = (int) ($content['limit'] ?? 8);

        try {
            $authors = BrandModel::with('logo')->take($limit)->get();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('bookpoint_top_authors query failed: ' . $e->getMessage());
            $authors = collect();
        }

        return view('theme-bookpoint::widgets.top_authors', [
            'title'          => $content['title']          ?? 'Top Authors',
            'authors'        => $authors,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 80),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 80),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'bookpoint';
    }
}
