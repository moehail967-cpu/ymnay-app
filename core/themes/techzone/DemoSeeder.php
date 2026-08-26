<?php

namespace Themes\Techzone;

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

        $this->patchHeroImage($homePageId);
        $this->patchPromoBannerImage($homePageId);
    }

    /**
     * Ensure the hero section widget has the right-side product/device image.
     * ThemeDemoImporter resolves @media tokens from home-layout.json, but on
     * older installs or theme-switch without re-import the media group may be
     * missing. This patches it using the media already uploaded.
     */
    private function patchHeroImage(int $pageId): void
    {
        $widget = PageBuilderWidget::where('page_id', $pageId)
            ->where('widget_type', 'techzone_hero_section')
            ->first();

        if (!$widget) {
            return;
        }

        $settings = $widget->general_settings ?? [];

        if (!empty($settings['media']['product_image']) || !empty($settings['media']['hero_image'])) {
            return; // Already set — nothing to patch
        }

        $media = MediaUploader::where('title', 'techzone-hero.jpg')
            ->latest('id')
            ->first();

        if (!$media) {
            return;
        }

        $settings['media']['product_image'] = $media->id;
        $widget->general_settings = $settings;
        $widget->save();
    }

    /**
     * Ensure the promo banner widget has a right-side image.
     */
    private function patchPromoBannerImage(int $pageId): void
    {
        $widget = PageBuilderWidget::where('page_id', $pageId)
            ->where('widget_type', 'techzone_promo_banner')
            ->first();

        if (!$widget) {
            return;
        }

        $settings = $widget->general_settings ?? [];

        if (!empty($settings['media']['promo_image'])) {
            return; // Already set — nothing to patch
        }

        $media = MediaUploader::where('title', 'promo-banner.jpg')
            ->orWhere('title', 'smartwatch.jpg')
            ->latest('id')
            ->first();

        if (!$media) {
            return;
        }

        $settings['media']['promo_image'] = $media->id;
        $widget->general_settings = $settings;
        $widget->save();
    }
}
