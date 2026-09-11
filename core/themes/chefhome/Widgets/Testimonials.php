<?php

namespace Themes\Chefhome\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class Testimonials extends BaseWidget
{
    protected function getWidgetType(): string        { return 'chefhome_testimonials'; }
    protected function getWidgetName(): string        { return 'ChefHome: Testimonials'; }
    protected function getWidgetIcon(): string|array  { return 'las la-comment-dots'; }
    protected function getWidgetDescription(): string { return __('Customer review cards in ChefHome style with avatar initial, stars and role'); }
    protected function getCategory(): string          { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array         { return ['testimonials', 'reviews', 'chefhome']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('header', 'Section Header')
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Section Title')
                ->setDefault('What Our Customers Say'))
            ->registerField('subtitle', FieldManager::TEXT()
                ->setLabel('Subtitle')
                ->setDefault('Real reviews from real food lovers'))
            ->endGroup();

        $control->addGroup('items', 'Testimonials')
            ->registerField('testimonials', FieldManager::REPEATER()
                ->setLabel('Testimonials')
                ->setItemLabel('Testimonial')
                ->setAddButtonText('Add Testimonial')
                ->setMin(1)->setMax(20)
                ->setFields([
                    'name'   => FieldManager::TEXT()->setLabel('Customer Name')->setDefault('Sarah M.'),
                    'role'   => FieldManager::TEXT()->setLabel('Role / Label')->setDefault('Verified Buyer'),
                    'rating' => FieldManager::SELECT()
                        ->setLabel('Rating')
                        ->setOptions(['5' => '5 Stars', '4' => '4 Stars', '3' => '3 Stars', '2' => '2 Stars', '1' => '1 Star'])
                        ->setDefault('5'),
                    'review' => FieldManager::TEXTAREA()->setLabel('Review Text')
                        ->setDefault('Best burger I\'ve had delivered. Arrived hot, perfectly wrapped. Will order every week!'),
                    'avatar' => FieldManager::IMAGE()->setLabel('Avatar Image (optional)'),
                ])
                ->setDefault([
                    ['name' => 'Sarah M.',  'role' => 'Verified Buyer',   'rating' => '5', 'review' => 'Best burger I\'ve had delivered. Arrived hot, perfectly wrapped. Will order every week!'],
                    ['name' => 'James K.',  'role' => 'Regular Customer', 'rating' => '5', 'review' => 'The ramen is absolutely incredible. Tastes like a restaurant. Delivery was under 30 min!'],
                    ['name' => 'Emma R.',   'role' => 'New Customer',     'rating' => '4', 'review' => 'Fresh ingredients, great portion sizes, and the tiramisu is to die for. 10/10 would recommend.'],
                    ['name' => 'David L.',  'role' => 'Verified Buyer',   'rating' => '5', 'review' => 'Ordered the steak and it was cooked exactly to my request. ChefHome never disappoints!'],
                ]))
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top',    FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(72)->setMin(0)->setMax(300))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(72)->setMin(0)->setMax(300))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {

        // Not in tenant context (editor preview API has no tenant middleware)
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;">ChefHome: Testimonials — preview on the live page</p>
                    </div>';
        }
        $header  = $settings['general']['header'] ?? [];
        $items   = $settings['general']['items']  ?? [];
        $spacing = $settings['style']['spacing']  ?? [];

        $testimonials = $items['testimonials'] ?? [
            ['name' => 'Sarah M.',  'role' => 'Verified Buyer',   'rating' => '5', 'review' => 'Best burger I\'ve had delivered. Arrived hot, perfectly wrapped. Will order every week!'],
            ['name' => 'James K.',  'role' => 'Regular Customer', 'rating' => '5', 'review' => 'The ramen is absolutely incredible. Tastes like a restaurant. Delivery was under 30 min!'],
            ['name' => 'Emma R.',   'role' => 'New Customer',     'rating' => '4', 'review' => 'Fresh ingredients, great portion sizes, and the tiramisu is to die for. 10/10 would recommend.'],
            ['name' => 'David L.',  'role' => 'Verified Buyer',   'rating' => '5', 'review' => 'Ordered the steak and it was cooked exactly to my request. ChefHome never disappoints!'],
        ];

        return view('theme-chefhome::widgets.testimonials', [
            'title'          => $header['title']    ?? 'What Our Customers Say',
            'subtitle'       => $header['subtitle'] ?? 'Real reviews from real food lovers',
            'testimonials'   => $testimonials,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 72),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 72),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'chefhome';
    }
}
