<?php

namespace Themes\Pawhaus;

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
            $this->section('hero',     'pawhaus_hero_section',     '#FBF6EF'),
            $this->section('cats',     'pawhaus_category_grid',    '#FBF6EF'),
            $this->section('products', 'pawhaus_featured_products', '#FBF6EF'),
            $this->section('promo',    'pawhaus_promo_banner',      '#C87040', true),
            $this->section('news',     'pawhaus_newsletter',        '#FBF6EF'),
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

    private function section(string $key, string $widgetType, string $bg = '#FBF6EF', bool $fullWidth = false): array
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
            'id'      => 'section-' . $key . '-' . $h,
            'type'    => 'section',
            'columns' => [[
                'id'       => 'column-' . $key . '-' . $h2,
                'width'    => '100%',
                'widgets'  => [['id' => 'widget-' . $key . '-' . $h3, 'type' => $widgetType]],
                'settings' => [],
            ]],
            'settings' => $settings,
        ];
    }
}
