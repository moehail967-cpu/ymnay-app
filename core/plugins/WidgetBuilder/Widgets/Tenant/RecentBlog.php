<?php

namespace Plugins\WidgetBuilder\Widgets\Tenant;

use Modules\Blog\Entities\Blog;
use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class RecentBlog extends BaseWidget
{
    protected function getWidgetType(): string { return 'tenant_recent_blog'; }
    protected function getWidgetName(): string { return 'Recent Blog Posts'; }
    protected function getWidgetIcon(): string|array { return 'las la-blog'; }
    protected function getWidgetDescription(): string { return __('Display the latest blog posts in a grid'); }
    protected function getCategory(): string { return WidgetCategory::CONTENT; }
    protected function getWidgetTags(): array { return ['blog', 'posts', 'news']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Section Title')
                ->setDefault('Latest from the Blog'))
            ->registerField('subtitle', FieldManager::TEXT()
                ->setLabel('Subtitle')
                ->setDefault(''))
            ->registerField('view_all_text', FieldManager::TEXT()
                ->setLabel('View All Button Text')
                ->setDefault('View All Posts'))
            ->registerField('view_all_url', FieldManager::URL()
                ->setLabel('View All URL')
                ->setDefault('#'))
            ->endGroup();

        $control->addGroup('query', 'Query')
            ->registerField('item_count', FieldManager::NUMBER()
                ->setLabel('Number of Posts')
                ->setDefault(3)->setMin(1)->setMax(12))
            ->registerField('columns', FieldManager::SELECT()
                ->setLabel('Columns')
                ->setOptions(['2' => '2', '3' => '3', '4' => '4'])
                ->setDefault('3'))
            ->registerField('show_date', FieldManager::TOGGLE()->setLabel('Show Date')->setDefault(true))
            ->registerField('show_author', FieldManager::TOGGLE()->setLabel('Show Author')->setDefault(false))
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top', FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(80)->setMin(0)->setMax(300))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(80)->setMin(0)->setMax(300))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        $general = $settings['general'] ?? [];
        $style   = $settings['style'] ?? [];

        $content = $general['content'] ?? [];
        $query   = $general['query'] ?? [];
        $spacing = $style['spacing'] ?? [];

        $count   = (int) ($query['item_count'] ?? 3);
        $cols    = (int) ($query['columns'] ?? 3);

        $posts = Blog::where('status', 1)->latest()->take($count)->get();

        return view('widgetbuilder::tenant.recent_blog', [
            'title'          => $content['title'] ?? 'Latest from the Blog',
            'subtitle'       => $content['subtitle'] ?? '',
            'view_all_text'  => $content['view_all_text'] ?? 'View All Posts',
            'view_all_url'   => $content['view_all_url'] ?? '#',
            'posts'          => $posts,
            'columns'        => $cols,
            'show_date'      => (bool) ($query['show_date'] ?? true),
            'show_author'    => (bool) ($query['show_author'] ?? false),
            'padding_top'    => (int) ($spacing['padding_top'] ?? 80),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 80),
        ])->render();
    }

    public function enable(): bool { return !is_null(tenant()); }
}
