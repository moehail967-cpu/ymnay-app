<?php

namespace Themes\Velvetlux;

use App\Contracts\ThemeDemoSeederContract;
use App\Models\MediaUploader;
use Xgenious\PageBuilder\Models\PageBuilderWidget;
use App\Models\Page;

class DemoSeeder implements ThemeDemoSeederContract
{
    public function run(): void
    {
        $homePageId = (int) get_static_option('home_page');
        
        if (!$homePageId) {
            $page = Page::where('slug', 'velvetlux-home')->first();
            if ($page) {
                $homePageId = $page->id;
                update_static_option('home_page', $homePageId);
            } else {
                return;
            }
        }

        $this->patchHeroImage($homePageId);
    }

    /**
     * Ensure the hero section widget has the hero image.
     */
    private function patchHeroImage(int $pageId): void
    {
        $widget = PageBuilderWidget::where('page_id', $pageId)
            ->where('widget_type', 'velvetlux_hero_section')
            ->first();

        if (!$widget) {
            return;
        }

        $settings = $widget->general_settings ?? [];

        if (!empty($settings['media']['hero_image'])) {
            return; // Already set — nothing to patch
        }

        $media = MediaUploader::where('title', 'velvetlux-hero.jpg')
            ->orWhere('title', 'velvetlux-hero')
            ->latest('id')
            ->first();

        if (!$media) {
            return;
        }

        $settings['media']['hero_image'] = $media->id;
        $widget->general_settings = $settings;
        $widget->save();
    }
}
