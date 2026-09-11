<?php

namespace Themes\Maison;

use App\Contracts\ThemeDemoSeederContract;
use App\Models\Page;
use Xgenious\PageBuilder\Models\PageBuilderContent;

class DemoSeeder implements ThemeDemoSeederContract
{
    public function run(): void
    {
        $homeId = (int) get_static_option('home_page');
        $page   = $homeId ? Page::find($homeId) : null;

        if (! $page) {
            $page = Page::where('slug', 'maison-home')->first();
        }

        if (! $page) {
            return;
        }

        $page->use_page_builder = 1;
        $page->save();

        PageBuilderContent::where('page_id', $page->id)->delete();

        $containers = [
            $this->section('hero',     'maison_hero_section',     '#F3EDE0', true),
            $this->section('cats',     'maison_category_grid',    '#FAF7F2'),
            $this->section('products', 'maison_featured_products', '#F3EDE0', true),
            $this->section('promo',    'maison_promo_banner',     '#7D8C5A', true),
            $this->section('news',     'maison_newsletter',       '#FAF7F2'),
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

    private function section(string $key, string $type, string $bg = '#FAF7F2', bool $fullWidth = false): array
    {
        $h  = substr(md5($key . $type), 0, 8);
        $h2 = substr(md5($key . $type), 8, 8);
        $h3 = substr(md5($key . $type), 16, 8);

        $settings = ['margin' => '0px', 'padding' => '0px', 'backgroundColor' => $bg];
        if ($fullWidth) {
            $settings['contentWidth'] = 'full_width';
        }

        return [
            'id'      => 'section-' . $key . '-' . $h,
            'type'    => 'section',
            'columns' => [[
                'id'       => 'column-' . $key . '-' . $h2,
                'width'    => '100%',
                'widgets'  => [['id' => 'widget-' . $key . '-' . $h3, 'type' => $type]],
                'settings' => [],
            ]],
            'settings' => $settings,
        ];
    }
}
