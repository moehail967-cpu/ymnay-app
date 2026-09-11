<?php

namespace Themes\Luxegems\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class HeroSection extends BaseWidget
{
    protected function getWidgetType(): string { return 'luxegems_hero_section'; }
    protected function getWidgetName(): string { return 'LuxeGems: Hero Section'; }
    protected function getWidgetIcon(): string|array { return 'las la-gem'; }
    protected function getWidgetDescription(): string { return __('Two-column hero banner with gold accent — LuxeGems luxury style'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['hero', 'banner', 'luxegems']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();

        $control->addGroup('content', 'Content')
            ->registerField('title', FieldManager::TEXTAREA()
                ->setLabel('Title (HTML allowed — line break with <br>)')
                ->setDefault('RARE GEMS.<br>CRAFTED FOR'))
            ->registerField('title_accent', FieldManager::TEXT()
                ->setLabel('Accent Line (displayed in gold below title)')
                ->setDefault('ETERNITY.'))
            ->registerField('subtitle', FieldManager::TEXTAREA()
                ->setLabel('Subtitle')
                ->setDefault('The world\'s most extraordinary diamonds, sapphires, and emeralds, set by master artisans. Every piece comes with a certificate of authenticity.'))
            ->registerField('button_text', FieldManager::TEXT()
                ->setLabel('Primary Button Text')
                ->setDefault('EXPLORE COLLECTIONS'))
            ->registerField('button_url', FieldManager::URL()
                ->setLabel('Primary Button URL')
                ->setDefault('#'))
            ->registerField('button2_text', FieldManager::TEXT()
                ->setLabel('Secondary Button Text')
                ->setDefault('BOOK APPOINTMENT'))
            ->registerField('button2_url', FieldManager::URL()
                ->setLabel('Secondary Button URL')
                ->setDefault('#'))
            ->endGroup();

        $control->addGroup('media', 'Media')
            ->registerField('product_image', FieldManager::IMAGE()
                ->setLabel('Right Side Product Image'))
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('section_padding', FieldManager::DIMENSION()
                ->asPadding()
                ->setLabel('Section Padding')
                ->setDefault(['top' => 80, 'right' => 0, 'bottom' => 80, 'left' => 0])
                ->setSelectors([
                    '{{WRAPPER}} .lx-hero' => 'padding-top: {{VALUE.TOP}}{{UNIT}}; padding-bottom: {{VALUE.BOTTOM}}{{UNIT}};',
                ]))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#111;border:1px dashed #333;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;">LuxeGems: Hero Section — preview on the live page</p>
                    </div>';
        }

        $content         = $settings['general']['content'] ?? [];
        $media           = $settings['general']['media']   ?? [];
        $product_img_url = null;

        if (!empty($media['product_image'])) {
            $val = $media['product_image'];
            if (is_int($val) || is_string($val)) {
                $d = get_attachment_image_by_id((int) $val);
                $product_img_url = $d['img_url'] ?? null;
            } else {
                $product_img_url = $val['url'] ?? $val['img_url'] ?? null;
                if (empty($product_img_url) && !empty($val['id'])) {
                    $d = get_attachment_image_by_id((int) $val['id']);
                    $product_img_url = $d['img_url'] ?? null;
                }
            }
        }
        if (empty($product_img_url)) {
            $product_img_url = global_asset('core/' . theme_assets('images/luxegems-hero.jpg', 'luxegems'));
        }

        $raw         = $content['button_url']  ?? '#';
        $button_url  = is_array($raw) ? ($raw['url'] ?? '#') : $raw;
        $raw         = $content['button2_url'] ?? '#';
        $button2_url = is_array($raw) ? ($raw['url'] ?? '#') : $raw;

        return view('theme-luxegems::widgets.hero_section', [
            'title'         => $content['title']        ?? 'RARE GEMS.<br>CRAFTED FOR',
            'title_accent'  => $content['title_accent'] ?? 'ETERNITY.',
            'subtitle'      => $content['subtitle']     ?? '',
            'button_text'   => $content['button_text']  ?? 'EXPLORE COLLECTIONS',
            'button_url'    => $button_url,
            'button2_text'  => $content['button2_text'] ?? 'BOOK APPOINTMENT',
            'button2_url'   => $button2_url,
            'product_image' => $product_img_url,
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'luxegems';
    }
}
