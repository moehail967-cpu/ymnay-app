<?php

namespace Themes\Tinynest;

use App\Contracts\ThemeDemoSeederContract;
use App\Models\Page;
use Xgenious\PageBuilder\Models\PageBuilderContent;

class DemoSeeder implements ThemeDemoSeederContract
{
    public function run(): void
    {
        $page = Page::where('slug', 'home')->first();

        if (!$page) {
            return;
        }

        $page->use_page_builder = 1;
        $page->save();

        PageBuilderContent::where('page_id', $page->id)->delete();

        $containers = [
            $this->section('hero',     'tinynest_hero_section',      '#ffffff'),
            $this->section('cats',     'tinynest_category_grid',     '#FFF5F8'),
            $this->section('products', 'tinynest_featured_products', '#FCE4EE'),
            $this->section('promo',    'tinynest_promo_banner',      '#F472B6', true),
            $this->section('news',     'tinynest_newsletter',        '#ffffff'),
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
    }

    private function section(string $key, string $widgetType, string $bg = '#ffffff', bool $fullWidth = false): array
    {
        $h  = substr(md5($key . $widgetType), 0, 8);
        $h2 = substr(md5($key . $widgetType), 8, 8);
        $h3 = substr(md5($key . $widgetType), 16, 8);

        $settings = [
            'margin'          => '0px',
            'padding'         => '0px',
            'backgroundColor' => $bg,
        ];
        if ($fullWidth) {
            $settings['contentWidth'] = 'full_width';
        }

        return [
            'id'      => 'container-tn-' . $key . '-' . $h,
            'type'    => 'section',
            'columns' => [[
                'id'       => 'column-tn-' . $key . '-' . $h2,
                'width'    => '100%',
                'widgets'  => [['id' => 'widget-tn-' . $key . '-' . $h3, 'type' => $widgetType]],
                'settings' => [],
            ]],
            'settings' => $settings,
        ];
    }
}
