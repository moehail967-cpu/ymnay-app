<?php

namespace Database\Seeders\Tenant;

use App\Helpers\SanitizeInput;
use App\Mail\TenantCredentialMail;
use App\Models\Admin;
use App\Models\Language;
use App\Models\Menu;
use App\Models\Page;
use App\Models\PageBuilder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Xgenious\PageBuilder\Models\PageBuilderContent;
use Xgenious\PageBuilder\Models\PageBuilderWidget;

class PageSeed extends Seeder
{
    public function run()
    {
        Log::driver('seed-log')->info('Starting PageSeed seeder');
        $page_data = new Page();
        $page_data->slug = Str::slug('home');
        $page_data->title = __('Home');
        $page_data->page_content = __('Home');
        $page_data->visibility = 0;
        $page_data->status = 1;
        $page_data->navbar_variant = '01';
        $page_data->footer_variant = '01';
        $page_data->page_builder = 1;
        $page_data->breadcrumb = 0;

        $Metas = [
            'title' => SanitizeInput::esc_html(''),
            'description' => SanitizeInput::esc_html('Demo meta desc'),
            'image' => null,
            //twitter
            'tw_image' => null,
            'tw_title' => SanitizeInput::esc_html('tw title'),
            'tw_description' => SanitizeInput::esc_html('tw desc'),
            //facebook
            'fb_image' => null,
            'fb_title' =>  SanitizeInput::esc_html('fb title'),
            'fb_description' =>  SanitizeInput::esc_html('fb desc'),
        ];

        $page_data->save();
        Log::driver('seed-log')->info('Home page created', ['page_id' => $page_data->id]);

        $page_data->slug()->create(['slug' => $page_data->slug]);
        $page_data->metainfo()->create($Metas);

        // Uploading page layout — prefer demo/home-layout.json (xg_page_builder_v2) if available
        $theme_slug = tenant()->theme_slug ?: 'hexfashion';
        $demo_layout = $theme_slug . '/demo/home-layout.json';
        $legacy_layout = $theme_slug . '/assets/page_layout/home-layout.json';
        $file_name = file_exists(base_path('themes/' . $demo_layout)) ? $demo_layout : $legacy_layout;

        Log::driver('seed-log')->info('Uploading home layout', ['file' => $file_name]);
        $this->upload_layout($file_name, $page_data->id);


        $page_data_2 = new Page();
        $page_data_2->slug = Str::slug('about');
        $page_data_2->title = __('About Us');
        $page_data_2->page_content = __('About us content');
        $page_data_2->visibility = 0;
        $page_data_2->status = 1;
        $page_data_2->navbar_variant = '01';
        $page_data_2->footer_variant = '01';
        $page_data_2->page_builder = 1;
        $page_data_2->breadcrumb = 1;

        $Metas_2 = [
            'title' => SanitizeInput::esc_html('Demo Meta Title'),
            'description' => SanitizeInput::esc_html('Demo meta desc'),
            'image' => null,
            //twitter
            'tw_image' => null,
            'tw_title' => SanitizeInput::esc_html('tw title'),
            'tw_description' => SanitizeInput::esc_html('tw desc'),
            //facebook
            'fb_image' => null,
            'fb_title' =>  SanitizeInput::esc_html('fb title'),
            'fb_description' =>  SanitizeInput::esc_html('fb desc'),
        ];

        $page_data_2->save();
        Log::driver('seed-log')->info('About Us page created', ['page_id' => $page_data_2->id]);

        $page_data_2->slug()->create(['slug' => $page_data_2->slug]);
        $page_data_2->metainfo()->create($Metas_2);

        // Uploading page layout — prefer demo/about-layout.json (xg_page_builder_v2) if available
        $about_slug   = tenant()->theme_slug ?: 'hexfashion';
        $demo_about   = $about_slug . '/demo/about-layout.json';
        $legacy_about = $about_slug . '/assets/page_layout/about-layout.json';
        $file_name    = file_exists(base_path('themes/' . $demo_about)) ? $demo_about : $legacy_about;
        Log::driver('seed-log')->info('Uploading about layout', ['file' => $file_name]);
        $this->upload_layout($file_name, $page_data_2->id);

        $page_data_5 = new Page();
        $page_data_5->slug = Str::slug('contact');
        $page_data_5->title = SanitizeInput::esc_html('Contact');
        $page_data_5->page_content = __('contact content');
        $page_data_5->visibility = 0;
        $page_data_5->status = 1;
        $page_data_5->navbar_variant = '01';
        $page_data_5->footer_variant = '01';
        $page_data_5->page_builder = 1;
        $page_data_5->breadcrumb = 1;

        $Metas_5 = [
            'title' => SanitizeInput::esc_html('Demo Meta Title'),
            'description' => SanitizeInput::esc_html('Demo meta desc'),
            'image' => null,
            //twitter
            'tw_image' => null,
            'tw_title' => SanitizeInput::esc_html('tw title'),
            'tw_description' => SanitizeInput::esc_html('tw desc'),
            //facebook
            'fb_image' => null,
            'fb_title' =>  SanitizeInput::esc_html('fb title'),
            'fb_description' =>  SanitizeInput::esc_html('fb desc'),
        ];

        $page_data_5->save();
        Log::driver('seed-log')->info('Contact page created', ['page_id' => $page_data_5->id]);

        $page_data_5->slug()->create(['slug' => $page_data_5->slug]);
        $page_data_5->metainfo()->create($Metas_5);

        // Uploading page layout — prefer demo/contact-layout.json (xg_page_builder_v2) if available
        $contact_slug   = tenant()->theme_slug ?: 'hexfashion';
        $demo_contact   = $contact_slug . '/demo/contact-layout.json';
        $legacy_contact = $contact_slug . '/assets/page_layout/contact-layout.json';
        $file_name      = file_exists(base_path('themes/' . $demo_contact)) ? $demo_contact : $legacy_contact;
        Log::driver('seed-log')->info('Uploading contact layout', ['file' => $file_name]);
        $this->upload_layout($file_name, $page_data_5->id);

        $page_data_6 = new Page();
        $page_data_6->slug = Str::slug('shop');
        $page_data_6->title = SanitizeInput::esc_html('Shop');
        $page_data_6->page_content = __('contact content');
        $page_data_6->visibility = 0;
        $page_data_6->status = 1;
        $page_data_6->navbar_variant = '01';
        $page_data_6->footer_variant = '01';
        $page_data_6->page_builder = 0;
        $page_data_6->breadcrumb = 1;

        $Metas_6 = [
            'title' => SanitizeInput::esc_html('Demo Meta Title'),
            'description' => SanitizeInput::esc_html('Demo meta desc'),
            'image' => null,
            //twitter
            'tw_image' => null,
            'tw_title' => SanitizeInput::esc_html('tw title'),
            'tw_description' => SanitizeInput::esc_html('tw desc'),
            //facebook
            'fb_image' => null,
            'fb_title' =>  SanitizeInput::esc_html('fb title'),
            'fb_description' =>  SanitizeInput::esc_html('fb desc'),
        ];

        $page_data_6->save();
        Log::driver('seed-log')->info('Shop page created', ['page_id' => $page_data_6->id]);

        $page_data_6->slug()->create(['slug' => $page_data_6->slug]);
        $page_data_6->metainfo()->create($Metas_6);

        \App\Models\StaticOption::updateOrCreate(
            ['option_name' => 'shop_page'],
            ['option_value' => $page_data_6->id]
        );

        $page_data_7 = new Page();
        $page_data_7->slug = Str::slug('blog');
        $page_data_7->title = SanitizeInput::esc_html('Blog');
        $page_data_7->page_content = __('Blog content');
        $page_data_7->visibility = 0;
        $page_data_7->status = 1;
        $page_data_7->navbar_variant = '01';
        $page_data_7->footer_variant = '01';
        $page_data_7->page_builder = 0;
        $page_data_7->breadcrumb = 1;

        $Metas_7 = [
            'title' => SanitizeInput::esc_html('Demo Meta Title'),
            'description' => SanitizeInput::esc_html('Demo meta desc'),
            'image' => null,
            //twitter
            'tw_image' => null,
            'tw_title' => SanitizeInput::esc_html('tw title'),
            'tw_description' => SanitizeInput::esc_html('tw desc'),
            //facebook
            'fb_image' => null,
            'fb_title' =>  SanitizeInput::esc_html('fb title'),
            'fb_description' =>  SanitizeInput::esc_html('fb desc'),
        ];

        $page_data_7->save();
        Log::driver('seed-log')->info('Blog page created', ['page_id' => $page_data_7->id]);

        $page_data_7->slug()->create(['slug' => $page_data_7->slug]);
        $page_data_7->metainfo()->create($Metas_7);

        \App\Models\StaticOption::updateOrCreate(
            ['option_name' => 'blog_page'],
            ['option_value' => $page_data_7->id]
        );

        $page_data_8 = new Page();
        $page_data_8->slug = Str::slug('digital-product');
        $page_data_8->title = SanitizeInput::esc_html('Digital Product');
        $page_data_8->page_content = __('Shop content');
        $page_data_8->visibility = 0;
        $page_data_8->status = 1;
        $page_data_8->navbar_variant = '01';
        $page_data_8->footer_variant = '01';
        $page_data_8->page_builder = 0;
        $page_data_8->breadcrumb = 1;

        $Metas_8 = [
            'title' => SanitizeInput::esc_html('Demo Meta Title'),
            'description' => SanitizeInput::esc_html('Demo meta desc'),
            'image' => null,
            //twitter
            'tw_image' => null,
            'tw_title' => SanitizeInput::esc_html('tw title'),
            'tw_description' => SanitizeInput::esc_html('tw desc'),
            //facebook
            'fb_image' => null,
            'fb_title' =>  SanitizeInput::esc_html('fb title'),
            'fb_description' =>  SanitizeInput::esc_html('fb desc'),
        ];

        $page_data_8->save();
        Log::driver('seed-log')->info('Digital Product page created', ['page_id' => $page_data_8->id]);

        $page_data_8->slug()->create(['slug' => $page_data_8->slug]);
        $page_data_8->metainfo()->create($Metas_8);

        \App\Models\StaticOption::updateOrCreate(
            ['option_name' => 'digital_shop_page'],
            ['option_value' => $page_data_8->id]
        );

        Log::driver('seed-log')->info('PageSeed seeder completed successfully');

    }

    private function upload_layout($file, $page_id)
    {
        $full_path = base_path('themes/' . $file);

        if (!file_exists($full_path)) {
            Log::driver('seed-log')->warning('Layout file not found', ['file' => $file]);
            return;
        }

        DB::beginTransaction();
        try {
            $raw  = file_get_contents($full_path);
            $data = json_decode($raw, true);
            Log::driver('seed-log')->info('Layout file loaded', ['file' => $file, 'page_id' => $page_id]);

            // ── New xg_page_builder_v2 format ────────────────────────────
            if (isset($data['format']) && $data['format'] === 'xg_page_builder_v2') {
                $content     = $data['content'] ?? ['containers' => []];
                $widgetsData = $data['widgets'] ?? [];
                $version     = $data['version'] ?? '1.0';

                // Remap all UUIDs to avoid ID collisions
                $widgetIdMap    = [];
                $containerIdMap = [];
                $columnIdMap    = [];

                foreach (array_keys($widgetsData) as $oldId) {
                    $widgetIdMap[$oldId] = (string) Str::uuid();
                }
                foreach ($content['containers'] as &$container) {
                    if (!empty($container['id'])) {
                        $newCId = (string) Str::uuid();
                        $containerIdMap[$container['id']] = $newCId;
                        $container['id'] = $newCId;
                    }
                    foreach ($container['columns'] as &$column) {
                        if (!empty($column['id'])) {
                            $newColId = (string) Str::uuid();
                            $columnIdMap[$column['id']] = $newColId;
                            $column['id'] = $newColId;
                        }
                        foreach ($column['widgets'] as &$wRef) {
                            if (isset($wRef['id'], $widgetIdMap[$wRef['id']])) {
                                $wRef['id'] = $widgetIdMap[$wRef['id']];
                            }
                        }
                        unset($wRef);
                    }
                    unset($column);
                }
                unset($container);

                PageBuilderContent::updateOrCreate(
                    ['page_id' => $page_id],
                    [
                        'content'      => $content,
                        'version'      => $version,
                        'is_published' => true,
                        'published_at' => now(),
                        'updated_by'   => null,
                    ]
                );

                PageBuilderWidget::where('page_id', $page_id)->delete();

                foreach ($widgetsData as $oldWidgetId => $w) {
                    if (empty($w['type'])) continue;
                    PageBuilderWidget::create([
                        'page_id'             => $page_id,
                        'widget_id'           => $widgetIdMap[$oldWidgetId],
                        'widget_type'         => $w['type'],
                        'container_id'        => $containerIdMap[$w['container_id'] ?? ''] ?? $w['container_id'] ?? null,
                        'column_id'           => $columnIdMap[$w['column_id'] ?? '']    ?? $w['column_id']    ?? null,
                        'sort_order'          => $w['sort_order']  ?? 0,
                        'is_visible'          => $w['is_visible']  ?? true,
                        'is_enabled'          => $w['is_enabled']  ?? true,
                        'version'             => $w['version']     ?? '1.0.0',
                        'general_settings'    => $w['settings']['general']    ?? null,
                        'style_settings'      => $w['settings']['style']      ?? null,
                        'advanced_settings'   => $w['settings']['advanced']   ?? null,
                        'responsive_settings' => $w['settings']['responsive'] ?? null,
                        'created_by'          => null,
                        'updated_by'          => null,
                    ]);
                }

                Page::findOrFail($page_id)->update(['use_page_builder' => true, 'page_builder' => 0]);
                Log::driver('seed-log')->info('xg_page_builder_v2 layout imported', ['page_id' => $page_id]);

            // ── Legacy addon page builder format ─────────────────────────
            } else {
                $file_contents = json_decode($raw);
                $contentArr = [];
                if (!empty($file_contents) && is_array($file_contents) && isset(current($file_contents)->addon_page_type) && current($file_contents)->addon_page_type === 'dynamic_page') {
                    foreach ($file_contents as $key => $content) {
                        unset($content->id);
                        $content->addon_page_id = (int) trim($page_id);
                        $content->created_at = now();
                        $content->updated_at = now();
                        foreach ($content as $key2 => $con) {
                            $contentArr[$key][$key2] = $con;
                        }
                    }
                    Page::findOrFail($page_id)->update(['page_builder' => 1]);
                    PageBuilder::where('addon_page_id', $page_id)->delete();
                    PageBuilder::insert($contentArr);
                    Log::driver('seed-log')->info('Legacy layout imported', ['page_id' => $page_id, 'sections' => count($contentArr)]);
                } else {
                    Page::findOrFail($page_id)->update([
                        'page_builder' => 0,
                        'page_content' => current($file_contents)->text ?? '',
                    ]);
                    Log::driver('seed-log')->info('Static page content updated', ['page_id' => $page_id]);
                }
            }

            DB::commit();
            Log::driver('seed-log')->info('Layout upload committed', ['page_id' => $page_id]);

        } catch (\Exception $exception) {
            DB::rollBack();
            Log::driver('seed-log')->error('Layout upload failed', [
                'page_id' => $page_id,
                'file'    => $file,
                'error'   => $exception->getMessage(),
            ]);
        }
    }
}
