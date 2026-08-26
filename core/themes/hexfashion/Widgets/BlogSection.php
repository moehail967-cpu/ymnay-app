<?php

namespace Themes\Hexfashion\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class BlogSection extends BaseWidget
{
    protected function getWidgetType(): string { return 'hexfashion_blog_section'; }
    protected function getWidgetName(): string { return 'HexFashion: Blog Section'; }
    protected function getWidgetIcon(): string|array { return 'las la-newspaper'; }
    protected function getWidgetDescription(): string { return __('3-column blog cards with image, category tag, and title'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['blog', 'news', 'hexfashion']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()->setLabel('Title')->setDefault('Updated News'))
            ->registerField('count', FieldManager::NUMBER()->setLabel('Posts to Show')->setDefault(3)->setMin(1)->setMax(9))
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
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">HexFashion: Blog Section — preview on the live page</p></div>';
        }

        $content = $settings['general']['content'] ?? [];
        $spacing = $settings['style']['spacing']   ?? [];
        $count   = (int) ($content['count'] ?? 3);

        $posts = collect();
        try {
            if (class_exists(\Modules\Blog\Entities\Blog::class)) {
                $posts = \Modules\Blog\Entities\Blog::where('status', 1)
                    ->orderBy('created_at', 'desc')
                    ->with('category')
                    ->take($count)
                    ->get();
            }
        } catch (\Exception $e) {
            $posts = collect();
        }

        return view('theme-hexfashion::widgets.blog_section', [
            'title'          => $content['title']          ?? 'Updated News',
            'posts'          => $posts,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 80),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 80),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'hexfashion';
    }
}
