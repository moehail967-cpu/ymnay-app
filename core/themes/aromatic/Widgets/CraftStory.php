<?php

namespace Themes\Aromatic\Widgets;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class CraftStory extends BaseWidget
{
    protected function getWidgetType(): string { return 'aromatic_craft_story'; }
    protected function getWidgetName(): string { return 'Aromatic: حكاية الصنعة'; }
    protected function getWidgetIcon(): string|array { return 'las la-seedling'; }
    protected function getWidgetDescription(): string { return __('صور وخطوات صناعة المنتجات'); }
    protected function getCategory(): string { return WidgetCategory::THEME; }
    protected function getWidgetTags(): array { return ['aromatic', 'about', 'story']; }

    public function getGeneralFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('content', 'المحتوى')
            ->registerField('title', FieldManager::TEXT()->setLabel('العنوان')->setDefault('شغف الصنعة وفنون الحرفيين'))
            ->registerField('subtitle', FieldManager::TEXTAREA()->setLabel('الوصف')->setDefault('من أول قطرة إلى اللمسة الأخيرة، نصنع تجربة تبقى في الذاكرة'));
        for ($i = 1; $i <= 3; $i++) {
            $control->registerField("item_{$i}_title", FieldManager::TEXT()->setLabel("عنوان الخطوة {$i}"))
                ->registerField("item_{$i}_description", FieldManager::TEXTAREA()->setLabel("وصف الخطوة {$i}"));
        }
        $control->endGroup();
        $control->addGroup('media', 'الصور');
        for ($i = 1; $i <= 3; $i++) {
            $control->registerField("image_{$i}", FieldManager::IMAGE()->setLabel("صورة الخطوة {$i}"));
        }
        $control->endGroup();
        return $control->getFields();
    }

    public function getStyleFields(): array
    {
        $control = new ControlManager();
        $control->addGroup('spacing', 'المسافات')
            ->registerField('padding_top', FieldManager::NUMBER()->setLabel('المسافة العلوية')->setDefault(90)->setMin(0)->setMax(300))
            ->registerField('padding_bottom', FieldManager::NUMBER()->setLabel('المسافة السفلية')->setDefault(90)->setMin(0)->setMax(300))
            ->endGroup();
        return $control->getFields();
    }

    public function render(array $settings = []): string
    {
        $content = $settings['general']['content'] ?? [];
        $media = $settings['general']['media'] ?? [];
        $spacing = $settings['style']['spacing'] ?? [];
        $items = [];
        for ($i = 1; $i <= 3; $i++) {
            $image = $media["image_{$i}"] ?? null;
            $url = is_array($image) ? ($image['img_url'] ?? $image['url'] ?? null) : null;
            $id = is_array($image) ? ($image['id'] ?? null) : $image;
            if (!$url && is_numeric($id)) {
                $attachment = get_attachment_image_by_id((int) $id);
                $url = $attachment['img_url'] ?? null;
            }
            $items[] = [
                'image' => $url,
                'title' => $content["item_{$i}_title"] ?? '',
                'description' => $content["item_{$i}_description"] ?? '',
            ];
        }
        return view('theme-aromatic::widgets.craft_story', [
            'title' => $content['title'] ?? '',
            'subtitle' => $content['subtitle'] ?? '',
            'items' => $items,
            'padding_top' => (int) ($spacing['padding_top'] ?? 90),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 90),
        ])->render();
    }

    public function enable(): bool
    {
        return function_exists('tenant') && tenant() && (tenant()->theme_slug ?? '') === 'aromatic';
    }
}
