<?php

namespace Themes\Pharmacy\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class BannerSection extends BaseWidget
{
    protected function getWidgetType(): string { return 'pharmacy_banner_section'; }
    protected function getWidgetName(): string { return 'Pharmacy: Banner Section'; }
    protected function getWidgetIcon(): string|array { return 'las la-prescription'; }
    protected function getWidgetDescription(): string { return __('Dark prescription service banner with 3-step process'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['banner', 'prescription', 'pharmacy']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'Content')
            ->registerField('eyebrow', FieldManager::TEXT()->setLabel('Badge Text')->setDefault('Prescription Service'))
            ->registerField('title', FieldManager::TEXTAREA()->setLabel('Title')->setDefault("Upload Your Prescription, We'll Handle the Rest"))
            ->registerField('subtitle', FieldManager::TEXTAREA()->setLabel('Description')->setDefault('Send us your NHS or private prescription and our pharmacists will dispense and deliver directly to your door. Secure, confidential, and fast.'))
            ->registerField('button_text', FieldManager::TEXT()->setLabel('Primary Button Text')->setDefault('Upload Prescription'))
            ->registerField('button_url', FieldManager::URL()->setLabel('Primary Button URL')->setDefault('#'))
            ->registerField('button2_text', FieldManager::TEXT()->setLabel('Secondary Button Text')->setDefault('Learn More'))
            ->registerField('button2_url', FieldManager::URL()->setLabel('Secondary Button URL')->setDefault('#'))
            ->endGroup();

        $control->addGroup('media', 'Media')
            ->registerField('banner_image', FieldManager::IMAGE()
                ->setLabel('Right Side Image (optional — falls back to step icons)'))
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'Spacing')
            ->registerField('padding_top', FieldManager::NUMBER()->setLabel('Padding Top (px)')->setDefault(72)->setMin(0)->setMax(300))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('Padding Bottom (px)')->setDefault(72)->setMin(0)->setMax(300))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#0B1C2D;border:1px dashed #ccc;border-radius:8px;color:#888;font-family:sans-serif;">
                        <p style="margin:0;font-size:14px;color:#aaa;">Pharmacy: Banner Section — preview on the live page</p>
                    </div>';
        }
        $content = $settings['general']['content'] ?? [];
        $media   = $settings['general']['media']   ?? [];
        $spacing = $settings['style']['spacing']   ?? [];

        $btnUrl  = $content['button_url']  ?? '#';
        $btn2Url = $content['button2_url'] ?? '#';
        if (is_array($btnUrl))  $btnUrl  = $btnUrl['url']  ?? '#';
        if (is_array($btn2Url)) $btn2Url = $btn2Url['url'] ?? '#';

        $bannerImageUrl = null;
        if (!empty($media['banner_image'])) {
            $raw = $media['banner_image'];
            $id  = is_array($raw) ? ($raw['id'] ?? null) : $raw;
            if ($id) {
                $d = get_attachment_image_by_id($id);
                $bannerImageUrl = is_array($raw) ? ($raw['url'] ?? $d['img_url'] ?? null) : ($d['img_url'] ?? null);
            }
        }

        return view('theme-pharmacy::widgets.banner_section', [
            'eyebrow'      => $content['eyebrow']     ?? 'Prescription Service',
            'title'        => $content['title']        ?? "Upload Your Prescription, We'll Handle the Rest",
            'subtitle'     => $content['subtitle']     ?? 'Send us your NHS or private prescription and our pharmacists will dispense and deliver directly to your door. Secure, confidential, and fast.',
            'button_text'  => $content['button_text']  ?? 'Upload Prescription',
            'button_url'   => $btnUrl,
            'button2_text' => $content['button2_text'] ?? 'Learn More',
            'button2_url'  => $btn2Url,
            'banner_image' => $bannerImageUrl,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 72),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 72),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'pharmacy';
    }
}
