<?php

namespace Plugins\WidgetBuilder\Widgets\Tenant;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class CampaignBanner extends BaseWidget
{
    protected function getWidgetType(): string { return 'tenant_campaign_banner'; }
    protected function getWidgetName(): string { return 'Campaign Banner'; }
    protected function getWidgetIcon(): string|array { return 'las la-percentage'; }
    protected function getWidgetDescription(): string { return __('Promotional banner with optional countdown timer and CTA'); }
    protected function getCategory(): string { return WidgetCategory::ECOMMERCE; }
    protected function getWidgetTags(): array { return ['campaign', 'sale', 'promo', 'banner', 'countdown']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('badge_text', FieldManager::TEXT()
                ->setLabel('Badge / Pre-title')
                ->setDefault('Limited Time Offer'))
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Title')
                ->setDefault('Summer Sale — Up to 50% Off'))
            ->registerField('subtitle', FieldManager::TEXT()
                ->setLabel('Subtitle')
                ->setDefault(''))
            ->registerField('button_text', FieldManager::TEXT()
                ->setLabel('Button Text')
                ->setDefault('Shop the Sale'))
            ->registerField('button_url', FieldManager::URL()
                ->setLabel('Button URL')
                ->setDefault('#'))
            ->endGroup();

        $control->addGroup('media', 'Background')
            ->registerField('background_image', FieldManager::IMAGE()
                ->setLabel('Background Image'))
            ->registerField('background_color', FieldManager::COLOR()
                ->setLabel('Fallback Background Color')
                ->setDefault('#1a1a2e'))
            ->endGroup();

        $control->addGroup('countdown', 'Countdown Timer')
            ->registerField('show_countdown', FieldManager::TOGGLE()
                ->setLabel('Show Countdown Timer')
                ->setDefault(false))
            ->registerField('end_date', FieldManager::TEXT()
                ->setLabel('End Date (YYYY-MM-DD HH:MM:SS)')
                ->setDefault(''))
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

    private function resolveImageUrl($value): string
    {
        if (is_array($value)) {
            if (isset($value['id'])) {
                $img = get_attachment_image_by_id($value['id']);
                return $img['img_url'] ?? '';
            }
            return $value['url'] ?? '';
        }
        return is_string($value) ? $value : '';
    }

    public function render(array $settings = []): string
    {
        $general = $settings['general'] ?? [];
        $style   = $settings['style'] ?? [];

        $content   = $general['content'] ?? [];
        $media     = $general['media'] ?? [];
        $countdown = $general['countdown'] ?? [];
        $spacing   = $style['spacing'] ?? [];

        return view('widgetbuilder::tenant.campaign_banner', [
            'badge_text'      => $content['badge_text'] ?? '',
            'title'           => $content['title'] ?? 'Summer Sale',
            'subtitle'        => $content['subtitle'] ?? '',
            'button_text'     => $content['button_text'] ?? 'Shop Now',
            'button_url'      => $content['button_url'] ?? '#',
            'bg_image_url'    => $this->resolveImageUrl($media['background_image'] ?? null),
            'bg_color'        => $media['background_color'] ?? '#1a1a2e',
            'show_countdown'  => (bool) ($countdown['show_countdown'] ?? false),
            'end_date'        => $countdown['end_date'] ?? '',
            'padding_top'     => (int) ($spacing['padding_top'] ?? 80),
            'padding_bottom'  => (int) ($spacing['padding_bottom'] ?? 80),
        ])->render();
    }

    public function enable(): bool { return !is_null(tenant()); }
}
