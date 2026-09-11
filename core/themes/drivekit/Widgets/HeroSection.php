<?php

namespace Themes\Drivekit\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class HeroSection extends BaseWidget
{
    protected function getWidgetType(): string
    {
        return 'drivekit_hero_section';
    }

    protected function getWidgetName(): string
    {
        return 'DriveKit: Hero Section';
    }

    protected function getWidgetIcon(): string|array
    {
        return 'las la-car';
    }

    protected function getWidgetDescription(): string
    {
        return __('Full-width dark hero banner with automotive styling and CTA buttons');
    }

    protected function getCategory(): string
    {
        return WidgetCategory::THEME;
    }

    protected function getWidgetTags(): array
    {
        return ['hero', 'banner', 'drivekit', 'automotive'];
    }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('tag', FieldManager::TEXT()->setLabel('Tag / Eyebrow')->setDefault('2M+ Parts in Stock'))
            ->registerField('title', FieldManager::TEXTAREA()->setLabel('Title (HTML allowed)')->setDefault('Parts. Tools.<br>Performance.<br><span>All Here.</span>'))
            ->registerField('subtitle', FieldManager::TEXTAREA()->setLabel('Subtitle')->setDefault('OEM and aftermarket parts for every make and model. From oil filters to full engine rebuilds — we stock what mechanics and enthusiasts need.'))
            ->registerField('button_text', FieldManager::TEXT()->setLabel('Primary Button Text')->setDefault('Find My Parts'))
            ->registerField('button_url', FieldManager::URL()->setLabel('Primary Button URL')->setDefault('#'))
            ->registerField('button2_text', FieldManager::TEXT()->setLabel('Secondary Button Text')->setDefault('Browse Tools'))
            ->registerField('button2_url', FieldManager::URL()->setLabel('Secondary Button URL')->setDefault('#'))
            ->endGroup();

        $control->addGroup('media', 'Media')
            ->registerField('hero_image', FieldManager::IMAGE()->setLabel('Hero Image (car/part)'))
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
                    '{{WRAPPER}} .dk-hero' => 'padding-top: {{VALUE.TOP}}{{UNIT}}; padding-bottom: {{VALUE.BOTTOM}}{{UNIT}};',
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
                        <p style="margin:0;font-size:14px;">DriveKit: Hero Section — preview on the live page</p>
                    </div>';
        }
        $content  = $settings['general']['content'] ?? [];
        $media    = $settings['general']['media']   ?? [];

        $hero_img = null;
        if (!empty($media['hero_image'])) {
            $val = $media['hero_image'];
            if (is_array($val)) {
                $hero_img = $val['url'] ?? $val['img_url'] ?? null;
                if (empty($hero_img) && !empty($val['id'])) {
                    $d = get_attachment_image_by_id((int) $val['id']);
                    $hero_img = $d['img_url'] ?? null;
                }
            } elseif (is_numeric($val)) {
                $d = get_attachment_image_by_id((int)$val);
                $hero_img = $d['img_url'] ?? null;
            }
        }
        if (empty($hero_img)) {
            $hero_img = global_asset('core/' . theme_assets('images/garage-hero.jpg', 'drivekit'));
        }

        $buttonUrl  = $content['button_url']  ?? '#';
        if (is_array($buttonUrl))  { $buttonUrl  = $buttonUrl['url']  ?? '#'; }
        if (empty($buttonUrl))  { $buttonUrl  = '#'; }

        $button2Url = $content['button2_url'] ?? '#';
        if (is_array($button2Url)) { $button2Url = $button2Url['url'] ?? '#'; }
        if (empty($button2Url)) { $button2Url = '#'; }

        return view('theme-drivekit::widgets.hero_section', [
            'tag'          => $content['tag']          ?? '2M+ Parts in Stock',
            'title'        => $content['title']        ?? 'Parts. Tools.<br>Performance.<br><span>All Here.</span>',
            'subtitle'     => $content['subtitle']     ?? 'OEM and aftermarket parts for every make and model. From oil filters to full engine rebuilds — we stock what mechanics and enthusiasts need.',
            'button_text'  => $content['button_text']  ?? 'Find My Parts',
            'button_url'   => $buttonUrl,
            'button2_text' => $content['button2_text'] ?? 'Browse Tools',
            'button2_url'  => $button2Url,
            'hero_image'   => $hero_img,
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'drivekit';
    }
}
