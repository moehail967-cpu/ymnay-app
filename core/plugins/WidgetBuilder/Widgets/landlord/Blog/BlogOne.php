<?php

namespace Plugins\WidgetBuilder\Widgets\landlord\Blog;

use Modules\Blog\Entities\Blog;
use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class BlogOne extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'blog_one';
    }

    protected function getWidgetName(): string
    {
        return 'Blog One';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-blog';
    }

    protected function getWidgetDescription(): string
    {
        return 'Blog posts grid section with dynamic data from blog table';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['blog', 'posts', 'articles', 'news'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('heading', 'Section Heading')
            ->registerField('calendar_icon', FieldManager::IMAGE()
                ->setLabel('Calendar Icon')
                ->setDescription('Calendar icon shown beside dates')
            )
            ->endGroup();

        $control->addGroup('posts', 'Blog Posts')
            ->registerField('items_count', FieldManager::NUMBER()
                ->setLabel('Number of Posts to Show')
                ->setDefault(6)
                ->setMin(1)
                ->setMax(24)
            )
            ->registerField('order_by', FieldManager::SELECT()
                ->setLabel('Order By')
                ->setOptions([
                    'id' => 'ID',
                    'created_at' => 'Date',
                    'views' => 'Views',
                ])
                ->setDefault('id')
            )
            ->registerField('order', FieldManager::SELECT()
                ->setLabel('Order')
                ->setOptions([
                    'asc' => 'Ascending',
                    'desc' => 'Descending',
                ])
                ->setDefault('desc')
            )
            ->registerField('read_more_text', FieldManager::TEXT()
                ->setLabel('Read More Text')
                ->setDefault('Read More')
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
                ->setDefault(56)
                ->setMin(0)
                ->setMax(300)
                ->setStep(5)
            )
            ->registerField('padding_bottom', FieldManager::NUMBER()
                ->setLabel('Padding Bottom (px)')
                ->setDefault(56)
                ->setMin(0)
                ->setMax(300)
                ->setStep(5)
            )
            ->endGroup();

        return $control->getFields();
    }

    private function resolveImageUrl($value): string
    {
        if (is_array($value)) {
            return $value['url'] ?? '';
        }
        return is_string($value) ? $value : '';
    }

    public function render(array $settings = []): string
    {
        $general = $settings['general'] ?? [];

        // Heading
        $headingGroup = $general['heading'] ?? [];
        $calendarIcon = $this->resolveImageUrl($headingGroup['calendar_icon'] ?? '');

        // Posts
        $postsGroup = $general['posts'] ?? [];
        $itemsCount = (int) ($postsGroup['items_count'] ?? 6);
        $orderBy = $postsGroup['order_by'] ?? 'id';
        $order = $postsGroup['order'] ?? 'desc';
        $readMoreText = $postsGroup['read_more_text'] ?? 'Read More';

        $allowedOrderBy = ['id', 'created_at', 'views'];
        if (!in_array($orderBy, $allowedOrderBy)) {
            $orderBy = 'id';
        }

        try {
            $blogs = Blog::where('status', 1)
                ->orderBy($orderBy, $order === 'desc' ? 'desc' : 'asc')
                ->paginate($itemsCount);
        } catch (\Exception $e) {
            return '';
        }

        if ($blogs->isEmpty()) {
            return '';
        }

        $posts = [];
        foreach ($blogs as $blog) {
            try {
                $detailUrl = route('landlord.frontend.blog.single', $blog->slug);
            } catch (\Exception $e) {
                $detailUrl = '#';
            }

            $posts[] = [
                'title' => $blog->title ?? '',
                'slug' => $blog->slug ?? '',
                'image' => $blog->image ?? '',
                'date' => $blog->created_at ? $blog->created_at->format('d F, Y') : '',
                'detail_url' => $detailUrl,
            ];
        }

        return view('widgetbuilder::landlord.blog.blog', [
            'calendarIcon' => $calendarIcon,
            'readMoreText' => $readMoreText,
            'posts' => $posts,
            'blogs' => $blogs,
        ])->render();
    }
}
