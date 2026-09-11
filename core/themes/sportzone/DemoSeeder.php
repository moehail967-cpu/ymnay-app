<?php

namespace Themes\sportzone;

use App\Contracts\ThemeDemoSeederContract;
use App\Models\MediaUploader;
use Xgenious\PageBuilder\Models\PageBuilderWidget;

class DemoSeeder implements ThemeDemoSeederContract
{
    public function run(): void
    {
        $homePageId = (int) get_static_option('home_page');
        if (!$homePageId) {
            return;
        }

        $this->patchBannerImage($homePageId);
        $this->patchHeroImage($homePageId);
    }

    /**
     * Ensure the banner section widget has a right-side image.
     * ThemeDemoImporter resolves @media tokens from home-layout.json, but if the
     * shop was created before that JSON fix the widget settings will be missing the
     * media group entirely. This patches it using the media already imported.
     */
    private function patchBannerImage(int $pageId): void
    {
        $widget = PageBuilderWidget::where('page_id', $pageId)
            ->where('widget_type', 'pharmacy_banner_section')
            ->first();

        if (!$widget) {
            return;
        }

        $settings = $widget->general_settings ?? [];

        if (!empty($settings['media']['banner_image'])) {
            return; // Already set — nothing to patch
        }

        $media = MediaUploader::where('title', 'prescription-pad.jpg')
            ->latest('id')
            ->first();

        if (!$media) {
            return;
        }

        $settings['media']['banner_image'] = $media->id;
        $widget->general_settings = $settings;
        $widget->save();
    }

    /**
     * Ensure the hero section widget has a product/hero image.
     */
    private function patchHeroImage(int $pageId): void
    {
        $widget = PageBuilderWidget::where('page_id', $pageId)
            ->where('widget_type', 'pharmacy_hero_section')
            ->first();

        if (!$widget) {
            return;
        }

        $settings = $widget->general_settings ?? [];

        if (!empty($settings['media']['product_image']) || !empty($settings['media']['hero_image'])) {
            return; // Already set — nothing to patch
        }

        $media = MediaUploader::where('title', 'pharmacy-hero.jpg')
            ->latest('id')
            ->first();

        if (!$media) {
            return;
        }

        $settings['media']['product_image'] = $media->id;
        $widget->general_settings = $settings;
        $widget->save();
    }
}
