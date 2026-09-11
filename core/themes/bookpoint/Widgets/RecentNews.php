<?php

namespace Themes\Bookpoint\Widgets;

use Modules\Blog\Entities\Blog;
use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class RecentNews extends BaseWidget
{
    protected function getWidgetType(): string { return 'bookpoint_recent_news'; }
    protected function getWidgetName(): string { return 'BookPoint: Recent News'; }
    protected function getWidgetIcon(): string|array { return 'las la-newspaper'; }
    protected function getWidgetDescription(): string { return __('Latest blog posts slider with prev/next arrows'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['news', 'blog', 'slider', 'bookpoint']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()->setLabel('Section Title')->setDefault('Recent News'))
            ->registerField('limit', FieldManager::NUMBER()->setLabel('Posts to Show')->setDefault(6)->setMin(1)->setMax(20))
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
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">BookPoint: Recent News — preview on the live page</p></div>';
        }

        $content = $settings['general']['content'] ?? [];
        $spacing = $settings['style']['spacing']   ?? [];
        $limit   = (int) ($content['limit'] ?? 6);

        try {
            $posts = Blog::where('status', 'publish')
                ->orderBy('created_at', 'desc')
                ->with('category')
                ->take($limit)
                ->get();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('bookpoint_recent_news query failed: ' . $e->getMessage());
            $posts = collect();
        }

        return view('theme-bookpoint::widgets.recent_news', [
            'title'          => $content['title']          ?? 'Recent News',
            'posts'          => $posts,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 80),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 80),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'bookpoint';
    }
}
