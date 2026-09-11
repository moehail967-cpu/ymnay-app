<?php

namespace Plugins\WidgetBuilder\Widgets\Tenant;

use Xgenious\PageBuilder\Core\BaseWidget;
use Xgenious\PageBuilder\Core\ControlManager;
use Xgenious\PageBuilder\Core\FieldManager;
use Xgenious\PageBuilder\Core\WidgetCategory;

class CarouselWidget extends BaseWidget
{
    protected function getWidgetType(): string        { return 'tenant_carousel'; }
    protected function getWidgetName(): string        { return 'Carousel'; }
    protected function getWidgetIcon(): string|array  { return 'las la-images'; }
    protected function getWidgetDescription(): string { return 'CSS-only image carousel with dot navigation.'; }
    protected function getCategory(): string          { return WidgetCategory::MEDIA; }
    protected function getWidgetTags(): array         { return ['carousel', 'slider', 'images']; }

    public function enable(): bool { return !is_null(tenant()); }

    public function getGeneralFields(): array
    {
        $cm = new ControlManager();
        $cm->addGroup('slides', 'Slides')
            ->registerField('items', FieldManager::REPEATER()
                ->setLabel('Slides')
                ->setItemLabel('Slide')
                ->setAddButtonText('Add Slide')
                ->setMin(1)
                ->setMax(10)
                ->setFields([
                    'image'   => FieldManager::IMAGE()->setLabel('Image'),
                    'caption' => FieldManager::TEXT()->setLabel('Caption'),
                    'link'    => FieldManager::URL()->setLabel('Link (optional)'),
                ]))
            ->endGroup();
        return $cm->getFields();
    }

    public function getStyleFields(): array
    {
        $cm = new ControlManager();
        $cm->addGroup('display', 'Display')
            ->registerField('height', FieldManager::NUMBER()
                ->setLabel('Slide Height (px)')->setDefault(420)->setMin(100)->setMax(900))
            ->registerField('border_radius', FieldManager::NUMBER()
                ->setLabel('Border Radius (px)')->setDefault(0)->setMin(0)->setMax(40))
            ->registerField('show_caption', FieldManager::TOGGLE()
                ->setLabel('Show Caption')->setDefault(true))
            ->endGroup();
        $cm->addGroup('spacing', 'Spacing')
            ->registerField('padding_top', FieldManager::NUMBER()
                ->setLabel('Padding Top (px)')->setDefault(60)->setMin(0)->setMax(300))
            ->registerField('padding_bottom', FieldManager::NUMBER()
                ->setLabel('Padding Bottom (px)')->setDefault(60)->setMin(0)->setMax(300))
            ->endGroup();
        return $cm->getFields();
    }

    public function render(array $settings = []): string
    {
        $slides_group = $settings['general']['slides'] ?? [];
        $display      = $settings['style']['display'] ?? [];
        $spacing      = $settings['style']['spacing'] ?? [];

        $rawSlides    = $slides_group['items'] ?? [];
        $height       = (int) ($display['height'] ?? 420);
        $radius       = (int) ($display['border_radius'] ?? 0);
        $show_caption = (bool) ($display['show_caption'] ?? true);

        $slides = [];
        foreach ($rawSlides as $slide) {
            // IMAGE field returns {img_url, url, id, ...} array
            $imageField = $slide['image'] ?? null;
            if (is_array($imageField)) {
                $imgUrl = $imageField['img_url'] ?? $imageField['url'] ?? '';
            } elseif (!empty($imageField)) {
                $img_data = get_attachment_image_by_id($imageField);
                $imgUrl = $img_data['img_url'] ?? '';
            } else {
                $imgUrl = '';
            }

            // URL field returns {url, target, ...} array
            $linkField = $slide['link'] ?? '';
            $link = is_array($linkField) ? ($linkField['url'] ?? '') : $linkField;

            $slides[] = [
                'img_url' => $imgUrl ?: global_asset('assets/common/img/placeholder.jpg'),
                'caption' => $slide['caption'] ?? '',
                'link'    => $link,
            ];
        }

        if (empty($slides)) {
            $slides[] = [
                'img_url' => global_asset('assets/common/img/placeholder.jpg'),
                'caption' => '',
                'link'    => '',
            ];
        }

        return view('widgetbuilder::tenant.carousel', [
            'id'             => uniqid('xgpbcr'),
            'slides'         => $slides,
            'height'         => $height,
            'radius'         => $radius,
            'show_caption'   => $show_caption,
            'padding_top'    => (int) ($spacing['padding_top'] ?? 60),
            'padding_bottom' => (int) ($spacing['padding_bottom'] ?? 60),
        ])->render();
    }
}
