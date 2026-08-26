<?php

namespace Themes\Drivekit\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class LatestBlogPosts extends BaseWidget
{
    protected function getWidgetType(): string { return 'drivekit_latest_blog_posts'; }
    protected function getWidgetName(): string { return 'DriveKit: Latest Blog Posts'; }
    protected function getWidgetIcon(): string|array { return 'las la-newspaper'; }
    protected function getWidgetDescription(): string { return __('Dark 3-column blog post grid with date, title, excerpt'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['blog', 'posts', 'articles', 'drivekit']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('tag', FieldManager::TEXT()->setLabel('Eyebrow Tag')->setDefault('Workshop Bulletin'))
            ->registerField('title', FieldManager::TEXT()->setLabel('Section Title')->setDefault('Latest Articles'))
            ->registerField('view_all_url', FieldManager::URL()->setLabel('View All URL')->setDefault('#'))
            ->registerField('post_count', FieldManager::NUMBER()->setLabel('Posts to Show')->setDefault(3)->setMin(1)->setMax(6))
            ->endGroup();
        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding', FieldManager::DIMENSION()
                ->asPadding()
                ->setLabel('Section Padding')
                ->setDefault(['top' => 100, 'right' => 0, 'bottom' => 100, 'left' => 0])
                ->setSelectors([
                    '{{WRAPPER}} .dk-widget-blogs' => 'padding-top: {{VALUE.TOP}}{{UNIT}}; padding-bottom: {{VALUE.BOTTOM}}{{UNIT}};',
                ])
            )
            ->endGroup();

        return $control->getFields();
    }

    public function render(array $settings = []): string
    {

        // Not in tenant context (editor preview API has no tenant middleware)
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;">DriveKit: Latest Blog Posts — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];
        $count   = (int) ($content['post_count'] ?? 3);

        $posts = collect();
        try {
            $posts = \Modules\Blog\Entities\Blog::where('status', 1)->latest()->take($count)->get();
        } catch (\Throwable $e) {
            // Blog module not active
        }

        $viewAllUrl = $content['view_all_url'] ?? '#';
        if (is_array($viewAllUrl)) { $viewAllUrl = $viewAllUrl['url'] ?? '#'; }
        if (empty($viewAllUrl)) { $viewAllUrl = '#'; }

        return view('theme-drivekit::widgets.latest_blog_posts', [
            'tag'          => $content['tag']          ?? 'Workshop Bulletin',
            'title'        => $content['title']        ?? 'Latest Articles',
            'view_all_url' => $viewAllUrl,
            'posts'        => $posts,
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'drivekit';
    }
}
