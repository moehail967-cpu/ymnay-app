<?php

namespace Plugins\WidgetBuilder\Widgets\Tenant;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class Testimonials extends BaseWidget
{
    protected function getWidgetType(): string { return 'tenant_testimonials'; }
    protected function getWidgetName(): string { return 'Testimonials'; }
    protected function getWidgetIcon(): string|array { return 'las la-comment-dots'; }
    protected function getWidgetDescription(): string { return __('Customer reviews and testimonials slider'); }
    protected function getCategory(): string { return WidgetCategory::CONTENT; }
    protected function getWidgetTags(): array { return ['testimonials', 'reviews', 'customers']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('header', 'Section Header')
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Section Title')
                ->setDefault('What Our Customers Say'))
            ->registerField('subtitle', FieldManager::TEXT()
                ->setLabel('Subtitle')
                ->setDefault(''))
            ->endGroup();

        $control->addGroup('items', 'Testimonial Items')
            ->registerField('testimonials', FieldManager::REPEATER()
                ->setLabel('Testimonials')
                ->setItemLabel('Testimonial')
                ->setAddButtonText('Add Testimonial')
                ->setMin(1)->setMax(20)
                ->setFields([
                    'name'    => FieldManager::TEXT()->setLabel('Customer Name')->setDefault('John Doe'),
                    'role'    => FieldManager::TEXT()->setLabel('Role / Location')->setDefault('Verified Buyer'),
                    'rating'  => FieldManager::SELECT()
                        ->setLabel('Rating (stars)')
                        ->setOptions(['5' => '5 Stars', '4' => '4 Stars', '3' => '3 Stars', '2' => '2 Stars', '1' => '1 Star'])
                        ->setDefault('5'),
                    'review'  => FieldManager::TEXTAREA()->setLabel('Review Text')->setDefault('Great product and fast delivery!'),
                    'avatar'  => FieldManager::IMAGE()->setLabel('Avatar Image (optional)'),
                ])
                ->setDefault([
                    ['name' => 'Sarah M.',  'role' => 'Verified Buyer', 'rating' => '5', 'review' => 'Absolutely love the products! Quality is amazing and delivery was super fast.'],
                    ['name' => 'James K.',  'role' => 'Regular Customer', 'rating' => '5', 'review' => 'Best online store I\'ve shopped at. Will definitely come back again!'],
                    ['name' => 'Emma R.',   'role' => 'New Customer', 'rating' => '4', 'review' => 'Great selection and competitive prices. Very happy with my purchase.'],
                ]))
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('style', 'Style')
            ->registerField('background_color', FieldManager::COLOR()->setLabel('Background Color')->setDefault('#f8f9fa'))
            ->endGroup();
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

        $header  = $general['header'] ?? [];
        $items   = $general['items'] ?? [];
        $styling = $style['style'] ?? [];
        $spacing = $style['spacing'] ?? [];

        $testimonials = $items['testimonials'] ?? [
            ['name' => 'Sarah M.', 'role' => 'Verified Buyer', 'rating' => '5', 'review' => 'Absolutely love the products!'],
            ['name' => 'James K.', 'role' => 'Regular Customer', 'rating' => '5', 'review' => 'Best online store I\'ve shopped at.'],
        ];

        return view('widgetbuilder::tenant.testimonials', [
            'title'          => $header['title'] ?? 'What Our Customers Say',
            'subtitle'       => $header['subtitle'] ?? '',
            'testimonials'   => $testimonials,
            'bg_color'       => $styling['background_color'] ?? '#f8f9fa',
            'padding_top'    => (int) ($spacing['padding_top'] ?? 80),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 80),
        ])->render();
    }

    public function enable(): bool { return !is_null(tenant()); }
}
