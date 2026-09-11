<?php

namespace Themes\Bookpoint\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class HeroSection extends BaseWidget
{
    protected function getWidgetType(): string  { return 'bookpoint_hero_section'; }
    protected function getWidgetName(): string  { return 'BookPoint: Hero Section'; }
    protected function getWidgetIcon(): string|array { return 'las la-book-open'; }
    protected function getWidgetDescription(): string { return __('Split hero: text left, three stacked book covers right'); }
    protected function getCategory(): string    { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array   { return ['hero', 'banner', 'books', 'bookpoint']; }

    /* ------------------------------------------------------------------ */
    /*  Fields                                                              */
    /* ------------------------------------------------------------------ */
    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        /* Content */
        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXTAREA()
                ->setLabel('Title (HTML allowed)')
                ->setDefault('Find the greatest life books ever written'))
            ->registerField('description', FieldManager::TEXTAREA()
                ->setLabel('Description')
                ->setDefault('Reading books are good for you because it improves your focus, memory, empathy, and communication skills. It can reduce stress, improve your mental health, and help you live longer.'))
            ->registerField('btn_text', FieldManager::TEXT()
                ->setLabel('Button Text')->setDefault('Shop Now'))
            ->registerField('btn_url', FieldManager::URL()
                ->setLabel('Button URL')->setDefault('#'))
            ->endGroup();

        /* Media */
        $control->addGroup('media', 'Media')
            ->registerField('book_image_1', FieldManager::IMAGE()
                ->setLabel('Book Image 1 (Left — behind center)'))
            ->registerField('book_image_2', FieldManager::IMAGE()
                ->setLabel('Book Image 2 (Center — featured, front)'))
            ->registerField('book_image_3', FieldManager::IMAGE()
                ->setLabel('Book Image 3 (Right — behind center)'))
            ->registerField('bg_wave_image', FieldManager::IMAGE()
                ->setLabel('Panel Background / Wave Image (optional)'))
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('style', 'Style')
            ->registerField('panel_color', FieldManager::COLOR()
                ->setLabel('Right Panel Background Color')->setDefault('#daf2ea'))
            ->registerField('padding_top', FieldManager::NUMBER()
                ->setLabel('Padding Top (px)')->setDefault(0)->setMin(0)->setMax(200))
            ->registerField('padding_bottom', FieldManager::NUMBER()
                ->setLabel('Padding Bottom (px)')->setDefault(0)->setMin(0)->setMax(200))
            ->endGroup();
        return $control->getFields();
    }

    /* ------------------------------------------------------------------ */
    /*  Helpers                                                             */
    /* ------------------------------------------------------------------ */
    private function resolveImage(mixed $field, string $fallback = ''): string
    {
        // Unresolved media tokens — treat as missing and use fallback
        if (is_string($field) && str_starts_with($field, '@media:')) {
            return $fallback;
        }
        if (is_array($field)) {
            $url = $field['img_url'] ?? $field['url'] ?? null;
            if (empty($url) && !empty($field['id'])) {
                $d = get_attachment_image_by_id((int) $field['id']);
                $url = $d['img_url'] ?? null;
            }
            return $url ?? $fallback;
        }
        if (!empty($field)) {
            $d = get_attachment_image_by_id((int) $field);
            return $d['img_url'] ?? $fallback;
        }
        return $fallback;
    }

    private function resolveUrl(mixed $field): string
    {
        if (is_array($field)) return $field['url'] ?? '#';
        return $field ?: '#';
    }

    /* ------------------------------------------------------------------ */
    /*  Render                                                              */
    /* ------------------------------------------------------------------ */
    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;">BookPoint: Hero Section — preview on the live page</p>
                    </div>';
        }

        $content = $settings['general']['content'] ?? [];
        $media   = $settings['general']['media']   ?? [];
        $style   = $settings['style']['style']     ?? [];

        $fb = fn(string $n) => global_asset('themes/bookpoint/images/' . $n);

        return view('theme-bookpoint::widgets.hero_section', [
            // Content
            'title'       => $content['title']       ?? 'Find the greatest life books ever written',
            'description' => $content['description'] ?? '',
            'btn_text'    => $content['btn_text']    ?? 'Shop Now',
            'btn_url'     => $this->resolveUrl($content['btn_url'] ?? '#'),

            // Media — book1=left-behind, book2=center-featured, book3=right-behind
            'book1_url' => $this->resolveImage($media['book_image_1']  ?? null, $fb('prod-book-1.jpg')),
            'book2_url' => $this->resolveImage($media['book_image_2']  ?? null, $fb('prod-book-2.jpg')),
            'book3_url' => $this->resolveImage($media['book_image_3']  ?? null, $fb('prod-book-3.jpg')),
            'wave_url'  => $this->resolveImage($media['bg_wave_image'] ?? null),

            // Style
            'panel_color'    => $style['panel_color']    ?? '#daf2ea',
            'padding_top'    => (int) ($style['padding_top']    ?? 0),
            'padding_bottom' => (int) ($style['padding_bottom'] ?? 0),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'bookpoint';
    }
}
