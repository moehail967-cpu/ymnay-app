<?php

namespace Themes\Aromatic\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class InstagramWidget extends BaseWidget
{
    protected function getWidgetType(): string  { return 'aromatic_instagram_widget'; }
    protected function getWidgetName(): string  { return 'Aromatic: معرض الصور'; }
    protected function getWidgetIcon(): string|array { return 'lab la-instagram'; }
    protected function getWidgetDescription(): string { return 'معرض صور وروابط إنستغرام قابلة للتعديل'; }
    protected function getCategory(): string    { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array   { return ['instagram', 'social', 'aromatic']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'المحتوى')
            ->registerField('title',        FieldManager::TEXT()->setLabel('العنوان')->setDefault('مجتمع أثير على إنستغرام'))
            ->registerField('instagram_url', FieldManager::URL()->setLabel('رابط إنستغرام')->setDefault('#'))
            ->endGroup();
        $control->addGroup('media', 'الصور')
            ->registerField('image_one', FieldManager::IMAGE()->setLabel('الصورة الأولى'))
            ->registerField('image_two', FieldManager::IMAGE()->setLabel('الصورة الثانية'))
            ->registerField('image_three', FieldManager::IMAGE()->setLabel('الصورة الثالثة'))
            ->registerField('image_four', FieldManager::IMAGE()->setLabel('الصورة الرابعة'))
            ->endGroup();

        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'المسافات')
            ->registerField('padding_top',    FieldManager::NUMBER()->setLabel('المسافة العلوية (بكسل)')->setDefault(50)->setMin(0)->setMax(200))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('المسافة السفلية (بكسل)')->setDefault(50)->setMin(0)->setMax(200))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        if (!function_exists('tenant') || !tenant()) {
            return '<div style="padding:40px;text-align:center;background:#f9f9f9;border:1px dashed #ccc;color:#888;">Aromatic: Instagram — preview on the live page</div>';
        }

        $content = $settings['general']['content'] ?? [];
        $media = $settings['general']['media'] ?? [];
        $spacing = $settings['style']['spacing']   ?? [];

        $rawUrl = $content['instagram_url'] ?? '#';
        $images = [];
        foreach (['image_one', 'image_two', 'image_three', 'image_four'] as $field) {
            $image = $media[$field] ?? null;
            $url = is_array($image) ? ($image['img_url'] ?? $image['url'] ?? null) : null;
            $id = is_array($image) ? ($image['id'] ?? null) : $image;
            if (!$url && is_numeric($id)) {
                $attachment = get_attachment_image_by_id((int) $id);
                $url = $attachment['img_url'] ?? null;
            }
            if ($url) {
                $images[] = $url;
            }
        }

        return view('theme-aromatic::widgets.instagram_widget', [
            'title'          => $content['title'] ?? 'مجتمع أثير على إنستغرام',
            'instagram_url'  => is_array($rawUrl) ? ($rawUrl['url'] ?? '#') : $rawUrl,
            'images'         => $images,
            'padding_top'    => (int) ($spacing['padding_top']    ?? 50),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 50),
        ])->render();
    }

    public function enable(): bool
    {
        return !is_null(tenant()) && (tenant()->theme_slug ?? '') === 'aromatic';
    }
}
