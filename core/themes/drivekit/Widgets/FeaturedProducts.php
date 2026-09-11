<?php

namespace Themes\Drivekit\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class FeaturedProducts extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'drivekit_featured_products';
    }

    protected function getWidgetName(): string
    {
        return 'DriveKit: Featured Products';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-star';
    }

    protected function getWidgetDescription(): string
    {
        return __('Dark-themed featured product grid for automotive parts');
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['products', 'featured', 'drivekit'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('tag', FieldManager::TEXT()->setLabel('Tag / Eyebrow')->setDefault('Best Sellers'))
            ->registerField('title', FieldManager::TEXT()->setLabel('Section Title')->setDefault('Mechanic Favourites'))
            ->registerField('limit', FieldManager::NUMBER()->setLabel('Number of Products')->setDefault(8)->setMin(2)->setMax(24))
            ->registerField('sort', FieldManager::SELECT()->setLabel('Sort By')->setOptions([
                'latest'     => 'Latest',
                'bestseller' => 'Best Seller',
                'featured'   => 'Featured',
            ])->setDefault('latest'))
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
                    '{{WRAPPER}} .dk-widget-products' => 'padding-top: {{VALUE.TOP}}{{UNIT}}; padding-bottom: {{VALUE.BOTTOM}}{{UNIT}};',
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
                        <p style="margin:0;font-size:14px;">DriveKit: Featured Products — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];

        $sort  = $content['sort']  ?? 'latest';
        $limit = (int) ($content['limit'] ?? 8);

        $query = \Modules\Product\Entities\Product::with('badge', 'inventoryDetail', 'category')
            ->where('status_id', \App\Enums\StatusEnums::PUBLISH);

        match ($sort) {
            'bestseller' => $query->orderBy('view_count', 'desc'),
            'featured'   => $query->latest(),
            default      => $query->latest(),
        };

        $products = $query->take($limit)->get();

        return view('theme-drivekit::widgets.featured_products', [
            'tag'      => $content['tag']   ?? 'Best Sellers',
            'title'    => $content['title'] ?? 'Mechanic Favourites',
            'products' => $products,
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'drivekit';
    }
}
