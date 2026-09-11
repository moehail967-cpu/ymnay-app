<?php

namespace Plugins\WidgetBuilder\Widgets\Tenant;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class HeroSection extends BaseWidget
{
    protected function getWidgetType(): string { return 'tenant_hero_section'; }
    protected function getWidgetName(): string { return 'Hero Section'; }
    protected function getWidgetIcon(): string|array { return 'las la-image'; }
    protected function getWidgetDescription(): string { return __('Main hero banner with headline, subtext, CTA button and background image'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['hero', 'banner', 'header', 'cta']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXT()
                ->setLabel('Headline')
                ->setDefault('Welcome to Our Store')
                ->setDescription('Main heading text'))
            ->registerField('subtitle', FieldManager::TEXTAREA()
                ->setLabel('Sub-headline')
                ->setDefault('Discover amazing products at great prices'))
            ->registerField('button_text', FieldManager::TEXT()
                ->setLabel('Button Text')
                ->setDefault('Shop Now'))
            ->registerField('button_url', FieldManager::URL()
                ->setLabel('Button URL')
                ->setDefault('#'))
            ->registerField('secondary_button_text', FieldManager::TEXT()
                ->setLabel('Secondary Button Text (optional)')
                ->setDefault(''))
            ->registerField('secondary_button_url', FieldManager::URL()
                ->setLabel('Secondary Button URL')
                ->setDefault('#'))
            ->endGroup();

        $control->addGroup('media', 'Background & Image')
            ->registerField('hero_image', FieldManager::IMAGE()
                ->setLabel('Hero / Product Image')
                ->setDescription('Right-side image or hero visual'))
            ->registerField('background_image', FieldManager::IMAGE()
                ->setLabel('Background Image (optional)')
                ->setDescription('Full section background'))
            ->registerField('background_color', FieldManager::COLOR()
                ->setLabel('Background Color')
                ->setDefault('#f8f9fa'))
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
        if (is_array($value)) return $value['url'] ?? '';
        return is_string($value) ? $value : '';
    }

    public function render(array $settings = []): string
    {
        $general = $settings['general'] ?? [];
        $style   = $settings['style'] ?? [];

        $content = $general['content'] ?? [];
        $media   = $general['media'] ?? [];
        $spacing = $style['spacing'] ?? [];

        $heroImgRaw = $media['hero_image'] ?? null;
        $heroImgUrl = '';
        if (!empty($heroImgRaw)) {
            if (is_array($heroImgRaw) && isset($heroImgRaw['id'])) {
                $img = get_attachment_image_by_id($heroImgRaw['id']);
                $heroImgUrl = $img['img_url'] ?? '';
            } else {
                $heroImgUrl = $this->resolveImageUrl($heroImgRaw);
            }
        }

        $bgImgRaw = $media['background_image'] ?? null;
        $bgImgUrl = '';
        if (!empty($bgImgRaw)) {
            if (is_array($bgImgRaw) && isset($bgImgRaw['id'])) {
                $img = get_attachment_image_by_id($bgImgRaw['id']);
                $bgImgUrl = $img['img_url'] ?? '';
            } else {
                $bgImgUrl = $this->resolveImageUrl($bgImgRaw);
            }
        }

        return view('widgetbuilder::tenant.hero_section', [
            'title'                => $content['title'] ?? 'Welcome to Our Store',
            'subtitle'             => $content['subtitle'] ?? '',
            'button_text'          => $content['button_text'] ?? 'Shop Now',
            'button_url'           => $content['button_url'] ?? '#',
            'secondary_button_text' => $content['secondary_button_text'] ?? '',
            'secondary_button_url'  => $content['secondary_button_url'] ?? '#',
            'hero_image_url'       => $heroImgUrl,
            'bg_image_url'         => $bgImgUrl,
            'bg_color'             => $media['background_color'] ?? '#f8f9fa',
            'padding_top'          => (int) ($spacing['padding_top'] ?? 80),
            'padding_bottom'       => (int) ($spacing['padding_bottom'] ?? 80),
        ])->render();
    }

    public function enable(): bool { return !is_null(tenant()); }
}
