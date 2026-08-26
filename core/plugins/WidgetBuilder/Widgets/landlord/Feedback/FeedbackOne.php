<?php

namespace Plugins\WidgetBuilder\Widgets\landlord\Feedback;

use App\Models\Testimonial;
use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class FeedbackOne extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'feedback_one';
    }

    protected function getWidgetName(): string
    {
        return 'Feedback One';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-comments';
    }

    protected function getWidgetDescription(): string
    {
        return 'Testimonial masonry grid section with quote cards, author info and see-more toggle';
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['feedback', 'testimonial', 'reviews', 'masonry'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('heading', 'Section Heading')
            ->registerField('badge_text', FieldManager::TEXT()
                ->setLabel('Badge Text')
                ->setDefault('Success Stories')
            )
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Heading')
                ->setDefault('What our users says about nazmart')
            )
            ->registerField('quote_icon', FieldManager::IMAGE()
                ->setLabel('Quote Icon')
                ->setDescription('Quote icon displayed on each card')
            )
            ->endGroup();

        $control->addGroup('testimonials', 'Testimonials')
            ->registerField('items_count', FieldManager::NUMBER()
                ->setLabel('Number of Testimonials to Show')
                ->setDefault(6)
                ->setMin(1)
                ->setMax(24)
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
                ->setDefault(0)
                ->setMin(0)
                ->setMax(300)
                ->setStep(5)
            )
            ->registerField('padding_bottom', FieldManager::NUMBER()
                ->setLabel('Padding Bottom (px)')
                ->setDefault(144)
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
        $badgeText = $headingGroup['badge_text'] ?? 'Success Stories';
        $title = $headingGroup['title'] ?? 'What our users says about nazmart';
        $quoteIcon = $this->resolveImageUrl($headingGroup['quote_icon'] ?? '');

        // Testimonials
        $testimonialsGroup = $general['testimonials'] ?? [];
        $itemsCount = (int) ($testimonialsGroup['items_count'] ?? 6);

        try {
            $testimonialRecords = Testimonial::where('status', 1)
                ->orderBy('id', 'desc')
                ->take($itemsCount)
                ->get();
        } catch (\Exception $e) {
            return '';
        }

        if ($testimonialRecords->isEmpty()) {
            return '';
        }

        $testimonials = [];
        foreach ($testimonialRecords as $record) {
            $testimonials[] = [
                'quote' => $record->description ?? '',
                'author_name' => $record->name ?? '',
                'author_role' => $record->designation ?? '',
                'author_avatar' => $record->image ?? '',
            ];
        }

        return view('widgetbuilder::landlord.feedback.feedback', [
            'badgeText' => $badgeText,
            'title' => $title,
            'quoteIcon' => $quoteIcon,
            'testimonials' => $testimonials,
        ])->render();
    }
}
