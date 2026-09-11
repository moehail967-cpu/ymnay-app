<?php

namespace Themes\Pharmacy\Widgets;

use Modules\Blog\Entities\Blog;
use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class BlogSection extends BaseWidget
{
    protected function getWidgetType(): string { return 'pharmacy_blog_section'; }
    protected function getWidgetName(): string { return 'Pharmacy: Blog Section'; }
    protected function getWidgetIcon(): string|array { return 'las la-newspaper'; }
    protected function getWidgetDescription(): string { return __('Latest health articles in a 3-column card grid'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['blog', 'articles', 'pharmacy']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()->setLabel('Section Title')->setDefault('Health Advice'))
            ->registerField('subtitle', FieldManager::TEXT()->setLabel('Section Subtitle')->setDefault('Expert tips from our pharmacists'))
            ->registerField('post_count', FieldManager::NUMBER()->setLabel('Number of Posts')->setDefault(3)->setMin(1)->setMax(6))
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
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;">Pharmacy: Blog Section — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];
        $spacing = $settings['style']['spacing']   ?? [];
        $count   = (int) ($content['post_count'] ?? 3);

        try {
            $posts = Blog::where('status', 1)
                ->orderBy('created_at', 'desc')
                ->take($count)
                ->with('category')
                ->get();
        } catch (\Exception $e) {
            $posts = collect();
        }

        return view('theme-pharmacy::widgets.blog_section', [
            'title'          => $content['title']    ?? 'Health Advice',
            'subtitle'       => $content['subtitle'] ?? 'Expert tips from our pharmacists',
            'posts'          => $posts,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 72),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 72),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'pharmacy';
    }
}
