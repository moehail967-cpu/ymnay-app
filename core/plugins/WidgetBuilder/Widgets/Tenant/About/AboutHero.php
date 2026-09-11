<?php

namespace Plugins\WidgetBuilder\Widgets\Tenant\About;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class AboutHero extends BaseWidget
{
    protected function getWidgetType(): string { return 'tenant_about_hero'; }
    protected function getWidgetName(): string { return 'About Hero'; }
    protected function getWidgetIcon(): string|array { return 'las la-building'; }
    protected function getWidgetDescription(): string { return __('Two-column about section: image left, title + description + stats right'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['about', 'hero', 'story', 'stats']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('section_tag',  FieldManager::TEXT()->setLabel('Section Tag')->setDefault('Our Story'))
            ->registerField('title',        FieldManager::TEXT()->setLabel('Title')->setDefault('We Are Passionate About What We Do'))
            ->registerField('description',  FieldManager::TEXTAREA()->setLabel('Description')->setDefault('Mi cursus perspiciatis reiciendis excepturi aliquid et cillum occaecat interdum laboris? Porta, torquent pretium ratione fringilla, blandit aut unde platea.'))
            ->registerField('image',        FieldManager::IMAGE()->setLabel('Image'))
            ->endGroup();
        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top',    FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(80)->setMin(0)->setMax(200))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(80)->setMin(0)->setMax(200))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;"><p style="margin:0;font-size:14px;">About Hero — preview on the live page</p></div>';
        }

        $content = $settings['general']['content'] ?? [];
        $spacing = $settings['style']['spacing']   ?? [];

        $image_url = '';
        $imageRaw  = $content['image'] ?? null;
        if (!empty($imageRaw)) {
            if (is_array($imageRaw) && isset($imageRaw['url'])) {
                $image_url = $imageRaw['url'];
            } elseif (is_array($imageRaw) && isset($imageRaw['id'])) {
                $att = get_attachment_image_by_id($imageRaw['id']);
                $image_url = $att['img_url'] ?? '';
            } elseif (is_numeric($imageRaw)) {
                $att = get_attachment_image_by_id($imageRaw);
                $image_url = $att['img_url'] ?? '';
            }
        }

        // Fall back to the theme's bundled about.jpg when no image is configured
        if (empty($image_url)) {
            $themeSlug   = tenant()->theme_slug ?? '';
            $defaultPath = public_path('themes/' . $themeSlug . '/images/about.jpg');
            if ($themeSlug && file_exists($defaultPath)) {
                $image_url = global_asset('themes/' . $themeSlug . '/images/about.jpg');
            }
        }

        return view('widgetbuilder::tenant.about.about-hero', [
            'section_tag'    => $content['section_tag']  ?? 'Our Story',
            'title'          => $content['title']        ?? 'We Are Passionate About What We Do',
            'description'    => $content['description']  ?? 'Mi cursus perspiciatis reiciendis excepturi aliquid et cillum occaecat interdum laboris? Porta, torquent pretium ratione fringilla, blandit aut unde platea.',
            'image_url'      => $image_url,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 80),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 80),
        ])->render();
    }

    public function enable(): bool { return !is_null(tenant()); }
}
