<?php

namespace Themes\Trailco;

use App\Contracts\ThemeDemoSeederContract;
use App\Models\MediaUploader;
use App\Models\Page;
use Xgenious\PageBuilder\Models\PageBuilderContent;
use Xgenious\PageBuilder\Models\PageBuilderWidget;

class DemoSeeder implements ThemeDemoSeederContract
{
    public function run(): void
    {
        // Find the home page created by ThemeDemoImporter (slug: trailco-home)
        // or fall back to whichever page is currently set as home.
        $page = Page::where('slug', 'trailco-home')->first()
            ?? Page::where('id', (int) get_static_option('home_page'))->first();

        if (!$page) {
            return;
        }

        // Ensure page builder is enabled on this page
        $page->use_page_builder = 1;
        $page->save();

        // If ThemeDemoImporter already built PageBuilderContent for this page
        // (via home-layout.json), only patch the hero image and exit.
        $existingContent = PageBuilderContent::where('page_id', $page->id)->first();
        if ($existingContent && !empty($existingContent->content['containers'])) {
            $this->patchHeroWidget($page->id);
            return;
        }

        // ── Fallback: ThemeDemoImporter did not create the layout.
        // Build it entirely in PHP so the theme always gets a working home page. ──

        PageBuilderContent::where('page_id', $page->id)->delete();
        PageBuilderWidget::where('page_id', $page->id)->delete();

        $containers = [
            $this->section('hero',     'trailco_hero_section',      '#2C3B20'),
            $this->section('cats',     'trailco_category_grid',     '#F4F1EC'),
            $this->section('products', 'trailco_featured_products', '#F4F1EC'),
            $this->section('promo',    'trailco_promo_banner',      '#4A5E3A'),
            $this->section('news',     'trailco_newsletter',        '#EAF0E3'),
        ];

        PageBuilderContent::create([
            'page_id'      => $page->id,
            'content'      => ['containers' => $containers],
            'version'      => '1.0',
            'is_published' => true,
            'published_at' => now(),
            'created_by'   => 1,
        ]);

        update_static_option('home_page', $page->id);

        // Patch hero image from uploaded media
        $this->patchHeroWidget($page->id);
    }

    /**
     * Patch the hero widget: inject uploaded hero image ID and default content
     * if they are missing (happens when home-layout.json general settings
     * were stored as an empty array rather than a keyed object).
     */
    private function patchHeroWidget(int $pageId): void
    {
        $widget = PageBuilderWidget::where('page_id', $pageId)
            ->where('widget_type', 'trailco_hero_section')
            ->first();

        if (!$widget) {
            return;
        }

        $settings = $widget->general_settings ?? [];
        $changed  = false;

        // Inject hero image if not already set
        if (empty($settings['media']['hero_image'])) {
            $media = MediaUploader::where('title', 'trailco-hero.jpg')
                ->orWhere('title', 'trailco-hero')
                ->orderByDesc('id')
                ->first();

            if ($media) {
                $settings['media']['hero_image'] = $media->id;
                $changed = true;
            }
        }

        // Inject default content text if widget was created without settings
        if (empty($settings['content'])) {
            $settings['content'] = [
                'category_tag' => 'Adventure Awaits',
                'title'        => "Built for the<br>Wild. Tested on<br><span>Every Trail.</span>",
                'subtitle'     => "Professional outdoor gear for hikers, climbers, and adventurers. From day hikes to multi-day expeditions — we've got you covered.",
                'button_text'  => 'Shop Gear',
                'button_url'   => '#',
                'button2_text' => 'Find Your Trail',
                'button2_url'  => '#',
            ];
            $changed = true;
        }

        if ($changed) {
            $widget->general_settings = $settings;
            $widget->save();
        }
    }

    private function section(string $key, string $widgetType, string $bg = '#F4F1EC'): array
    {
        $h  = substr(md5($key . $widgetType), 0, 8);
        $h2 = substr(md5($key . $widgetType), 8, 8);
        $h3 = substr(md5($key . $widgetType), 16, 8);

        return [
            'id'      => 'container-tc-' . $key . '-' . $h,
            'type'    => 'section',
            'columns' => [[
                'id'       => 'column-tc-' . $key . '-' . $h2,
                'width'    => '100%',
                'widgets'  => [['id' => 'widget-tc-' . $key . '-' . $h3, 'type' => $widgetType]],
                'settings' => [],
            ]],
            'settings' => [
                'margin'          => '0px',
                'padding'         => '0px',
                'backgroundColor' => $bg,
                'contentWidth'    => 'full_width',
            ],
        ];
    }
}
