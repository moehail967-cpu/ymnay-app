<?php

namespace Plugins\WidgetBuilder\Widgets\landlord\Blog;

use Modules\Blog\Entities\Blog;
use Modules\Blog\Entities\BlogComment;
use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class BlogDetailOne extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'blog_detail_one';
    }

    protected function getWidgetName(): string
    {
        return 'Blog Detail One';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-newspaper';
    }

    protected function getWidgetDescription(): string
    {
        return 'Single blog post detail page with comments, sidebar and share section';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['blog', 'detail', 'single', 'post', 'article'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('sidebar', 'Sidebar Settings')
            ->registerField('toc_heading', FieldManager::TEXT()
                ->setLabel('Table Of Content Heading')
                ->setDefault('Table Of Content')
            )
            ->registerField('popular_heading', FieldManager::TEXT()
                ->setLabel('Popular Posts Heading')
                ->setDefault('Popular post')
            )
            ->registerField('popular_count', FieldManager::NUMBER()
                ->setLabel('Number of Popular Posts')
                ->setDefault(4)
                ->setMin(1)
                ->setMax(10)
            )
            ->registerField('share_heading', FieldManager::TEXT()
                ->setLabel('Share Section Heading')
                ->setDefault('Share This Articles:')
            )
            ->endGroup();

        $control->addGroup('comments', 'Comments Settings')
            ->registerField('comments_heading', FieldManager::TEXT()
                ->setLabel('Comments Section Heading')
                ->setDefault('Comments')
            )
            ->registerField('comments_count', FieldManager::NUMBER()
                ->setLabel('Number of Comments to Show')
                ->setDefault(3)
                ->setMin(1)
                ->setMax(20)
            )
            ->registerField('reply_text', FieldManager::TEXT()
                ->setLabel('Reply Button Text')
                ->setDefault('Reply')
            )
            ->endGroup();

        $control->addGroup('comment_form', 'Comment Form')
            ->registerField('form_heading', FieldManager::TEXT()
                ->setLabel('Form Heading')
                ->setDefault('Leave a Comment')
            )
            ->registerField('submit_text', FieldManager::TEXT()
                ->setLabel('Submit Button Text')
                ->setDefault('SUBMIT')
            )
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('section_style', 'Section')
            ->registerField('section_bg', FieldManager::COLOR()
                ->setLabel('Section Background')
                ->setDefault('#ffffff')
            )
            ->registerField('padding_top', FieldManager::NUMBER()
                ->setLabel('Padding Top (px)')
                ->setDefault(128)
                ->setMin(0)
                ->setMax(300)
                ->setStep(5)
            )
            ->registerField('padding_bottom', FieldManager::NUMBER()
                ->setLabel('Padding Bottom (px)')
                ->setDefault(128)
                ->setMin(0)
                ->setMax(300)
                ->setStep(5)
            )
            ->endGroup();

        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        $general = $settings['general'] ?? [];

        // Sidebar settings
        $sidebarGroup = $general['sidebar'] ?? [];
        $tocHeading = $sidebarGroup['toc_heading'] ?? 'Table Of Content';
        $popularHeading = $sidebarGroup['popular_heading'] ?? 'Popular post';
        $popularCount = (int) ($sidebarGroup['popular_count'] ?? 4);
        $shareHeading = $sidebarGroup['share_heading'] ?? 'Share This Articles:';

        // Comments settings
        $commentsGroup = $general['comments'] ?? [];
        $commentsHeading = $commentsGroup['comments_heading'] ?? 'Comments';
        $commentsCount = (int) ($commentsGroup['comments_count'] ?? 3);
        $replyText = $commentsGroup['reply_text'] ?? 'Reply';

        // Comment form settings
        $formGroup = $general['comment_form'] ?? [];
        $formHeading = $formGroup['form_heading'] ?? 'Leave a Comment';
        $submitText = $formGroup['submit_text'] ?? 'SUBMIT';

        // Fetch blog post from URL slug
        $slug = request()->route('slug') ?? request()->segment(2);

        if (empty($slug)) {
            return '';
        }

        try {
            $blog = Blog::with(['user', 'admin', 'category', 'comments'])
                ->where('slug', $slug)
                ->where('status', 1)
                ->first();
        } catch (\Exception $e) {
            return '';
        }

        if (!$blog) {
            return '';
        }

        // Get comments (top-level only)
        try {
            $comments = BlogComment::with('reply')
                ->where('blog_id', $blog->id)
                ->whereNull('parent_id')
                ->orderByDesc('created_at')
                ->take($commentsCount)
                ->get();
        } catch (\Exception $e) {
            $comments = collect();
        }

        // Get popular posts
        try {
            $popularPosts = Blog::where('status', 1)
                ->where('id', '!=', $blog->id)
                ->orderByDesc('views')
                ->limit($popularCount)
                ->get();
        } catch (\Exception $e) {
            $popularPosts = collect();
        }

        // Parse tags
        $tags = [];
        if (!empty($blog->tags)) {
            $tags = array_filter(array_map('trim', explode(',', $blog->tags)));
        }

        // Get author data
        $author = $blog->author_data();
        $authorName = $author->name ?? $blog->author ?? '';

        // Comment store route
        try {
            $commentStoreUrl = route(route_prefix() . 'frontend.blog.comment.store');
        } catch (\Exception $e) {
            $commentStoreUrl = '#';
        }

        return view('widgetbuilder::landlord.blog.blog_detail', [
            'blog' => $blog,
            'comments' => $comments,
            'popularPosts' => $popularPosts,
            'tags' => $tags,
            'authorName' => $authorName,
            'author' => $author,
            'commentStoreUrl' => $commentStoreUrl,
            'tocHeading' => $tocHeading,
            'popularHeading' => $popularHeading,
            'shareHeading' => $shareHeading,
            'commentsHeading' => $commentsHeading,
            'replyText' => $replyText,
            'formHeading' => $formHeading,
            'submitText' => $submitText,
        ])->render();
    }
}
