<?php

namespace App\Http\Controllers\Landlord\Admin;

use App\Enums\SlugMorphableTypeEnum;
use App\Helpers\ResponseMessage;
use App\Helpers\SanitizeInput;
use App\Http\Controllers\Controller;
use App\Http\Services\DynamicCustomSlugValidation;
use App\Models\Page;
use App\Models\PageBuilder;
use App\Models\PaymentLogs;
use App\Models\PricePlan;
use Artesaos\SEOTools\SEOMeta;
use Artesaos\SEOTools\SEOTools;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Xgenious\PageBuilder\Models\PageBuilderContent;
use Xgenious\PageBuilder\Models\PageBuilderWidget;
use Xgenious\PageBuilder\Services\PageBuilderRenderService;
use function GuzzleHttp\Promise\all;

class PagesController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:page-list|page-edit|page-delete',['only' => ['all_pages','page_builder']]);
        $this->middleware('permission:page-create',['only' => ['create_page','store_new_page']]);
        $this->middleware('permission:page-edit',['only' => ['edit_page','update']]);
        $this->middleware('permission:page-delete',['only' => ['delete']]);
    }

    public function all_pages()
    {
        $all_pages = Page::orderBy('id','desc')->get();
        return view('landlord.admin.pages.index',compact('all_pages'));
    }

    public function create_page()
    {
        return view('landlord.admin.pages.create');
    }

    public function page_builder($id)
    {
        $page = Page::with('metainfo')->findOrfail($id);
        return view('landlord.admin.pages.page-builder',compact('page'));
    }

    public function edit_page($id)
    {
        $page = Page::with('metainfo')->findOrfail($id);
        return view('landlord.admin.pages.edit',compact('page'));
    }

    public function store_new_page(Request $request)
    {
        $validatedData = $this->validate($request, [
            'status' => 'required|integer',
            'visibility' => 'required|integer',
            'title' => 'required|string',
            'page_content' => 'nullable|string',
            'navbar_variant' => 'nullable|string',
            'footer_variant' => 'nullable|string',
            'slug' => 'required|string',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_image' => 'nullable|integer',
            'tw_image' => 'nullable|integer',
            'fb_image' => 'nullable|integer',
            'meta_tw_title' => 'nullable|string',
            'meta_tw_description' => 'nullable|string',
            'meta_fb_title' => 'nullable|string',
            'meta_fb_description' => 'nullable|string',
        ]);

        DynamicCustomSlugValidation::validate(
            slug: $validatedData['slug']
        );

        $page_data = new Page();
        $slug = $request->slug ?? $request->name;
        $page_data->slug = create_slug($slug, 'Slug');
        $page_data->title = esc_html($request->title);
        $page_data->page_content = str_replace('script','',$request->page_content);
        $page_data->visibility = $request->visibility;
        $page_data->status = $request->status;

        if(tenant()){
            $page_data->navbar_variant = $request->navbar_variant;
            $page_data->footer_variant = $request->footer_variant;
        }

        $page_data->page_builder = is_null( $request->page_builder) ? 0 : 1;
        $page_data->breadcrumb = is_null( $request->breadcrumb) ? 0 : 1;

        $Metas = [
            'title' => esc_html($request->meta_title),
            'description' => esc_html($request->meta_description),
            'image' => $request->meta_image,
            //twitter
            'tw_image' => $request->tw_image,
            'tw_title' => esc_html($request->meta_tw_title),
            'tw_description' => esc_html($request->meta_tw_description),
            //facebook
            'fb_image' => $request->fb_image,
            'fb_title' =>  esc_html($request->meta_fb_title),
            'fb_description' =>  esc_html($request->meta_fb_description),
        ];

        $page_data->save();
        $page_data->metainfo()->create($Metas);
        $page_data->slug()->create(['slug' => $page_data->slug]);
        do_action('nazmart:after_page_saved', $page_data, 'create');

        return response()->success(ResponseMessage::SettingsSaved());
    }

    public function update(Request $request)
    {
        $validateData = $this->validate($request, [
            'id' => 'required|integer',
            'status' => 'required|integer',
            'visibility' => 'required|integer',
            'title' => 'required|string',
            'page_content' => 'nullable|string',
            'navbar_variant' => 'nullable|string',
            'footer_variant' => 'nullable|string',
            'slug' => ['required','string'],
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_image' => 'nullable|integer',
            'tw_image' => 'nullable|integer',
            'fb_image' => 'nullable|integer',
            'meta_tw_title' => 'nullable|string',
            'meta_tw_description' => 'nullable|string',
            'meta_fb_title' => 'nullable|string',
            'meta_fb_description' => 'nullable|string',
        ]);

        DynamicCustomSlugValidation::validate(
            slug: $validateData['slug'],
            id: $validateData['id'],
            type: SlugMorphableTypeEnum::PAGE
        );

        $page_data = Page::find($request->id);
        Cache::forget('page_id-'.$page_data->id);

        if ($request->slug !== $page_data->slug)
        {
            $page_data->slug = create_slug($request->slug ?? $request->name, 'Slug');
        }

        $page_data->title = esc_html($request->title);
        $page_data->page_content = str_replace('script','',$request->page_content);
        $page_data->visibility = $request->visibility;
        $page_data->status = $request->status;

        if(tenant()){
            $page_data->navbar_variant = $request->navbar_variant;
            $page_data->footer_variant = $request->footer_variant;
        }

        $page_data->page_builder = is_null( $request->page_builder) ? 0 : 1;
        $page_data->breadcrumb = is_null( $request->breadcrumb) ? 0 : 1;
        $page_data->save();
        $page_data->slug()->update(['slug' => $page_data->slug]);

        $meta_data = [
            'title' => esc_html($request->meta_title),
            'description' => esc_html($request->meta_description),
            'image' => $request->meta_image,
            //twitter
            'tw_image' => $request->tw_image,
            'tw_title' =>  esc_html($request->meta_tw_title),
            'tw_description' => esc_html($request->meta_tw_description),
            //facebook
            'fb_image' => $request->fb_image,
            'fb_title' => esc_html($request->meta_fb_title),
            'fb_description' => esc_html($request->meta_fb_description),
        ];

        $page_data->metainfo()->updateOrCreate(["metainfoable_id" => $page_data->id] ,$meta_data);
        $page_data->slug()->update(['slug' => $page_data->slug]);
        do_action('nazmart:after_page_saved', $page_data, 'update');

        return response()->success(ResponseMessage::SettingsSaved());
    }

    public function delete($id){
        $page = Page::find($id);
        $page->metainfo()->delete();
        $page->delete();
        return response()->danger(ResponseMessage::delete());
    }

    public function download($id)
    {
        if (class_exists(\Barryvdh\Debugbar\Facades\Debugbar::class)) {
            \Debugbar::disable();
        }

        $page = Page::findOrFail($id);

        // New React page builder
        if ($page->use_page_builder) {
            $pbContent = PageBuilderContent::where('page_id', $page->id)->first();
            $widgets   = PageBuilderWidget::where('page_id', $page->id)->ordered()->get();

            $widgetsExport = [];
            foreach ($widgets as $w) {
                $widgetsExport[$w->widget_id] = [
                    'type'                => $w->widget_type,
                    'container_id'        => $w->container_id,
                    'column_id'           => $w->column_id,
                    'sort_order'          => $w->sort_order,
                    'is_visible'          => $w->is_visible,
                    'is_enabled'          => $w->is_enabled,
                    'version'             => $w->version,
                    'settings'            => [
                        'general'    => $w->general_settings    ?? new \stdClass(),
                        'style'      => $w->style_settings      ?? new \stdClass(),
                        'advanced'   => $w->advanced_settings   ?? new \stdClass(),
                        'responsive' => $w->responsive_settings ?? new \stdClass(),
                    ],
                ];
            }

            $export = [
                'format'  => 'xg_page_builder_v2',
                'version' => $pbContent?->version ?? '1.0',
                'content' => $pbContent?->content ?? ['containers' => []],
                'widgets' => $widgetsExport,
            ];

            $page_contents = json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        // Old page builder
        } elseif ($page->page_builder) {
            $page_contents = PageBuilder::where('addon_page_id', $page->id)->orderBy('id', 'ASC')->get()->toJson();

        // Simple page
        } else {
            $page_contents = json_encode([[
                'text'            => $page->page_content,
                'addon_page_type' => 'simple_page',
            ]]);
        }

        $fileName = $page->slug . '-layout.json';

        header('Content-Disposition: attachment; filename=' . $fileName);
        header('Content-Type: application/json');
        echo $page_contents;
        exit;
    }

    public function upload(Request $request)
    {
        $request->validate([
            'page_layout' => 'required|mimes:json',
            'page_id'     => 'required|integer',
        ]);

        DB::beginTransaction();
        try {
            $raw  = file_get_contents($request->file('page_layout'));
            $data = json_decode($raw, true);

            if (json_last_error() !== JSON_ERROR_NONE || empty($data)) {
                throw new \Exception('Invalid JSON file.');
            }

            $pageId = (int) $request->page_id;
            Page::findOrFail($pageId); // ensure page exists

            // ── New React page builder format ────────────────────────────
            if (isset($data['format']) && $data['format'] === 'xg_page_builder_v2') {

                $content = $data['content'] ?? ['containers' => []];
                $version = $data['version'] ?? '1.0';
                $widgetsData = $data['widgets'] ?? [];

                // Remap widget & container/column IDs to avoid collisions
                $widgetIdMap    = [];
                $containerIdMap = [];
                $columnIdMap    = [];

                // Build fresh IDs for every widget
                foreach (array_keys($widgetsData) as $oldWidgetId) {
                    $widgetIdMap[$oldWidgetId] = (string) Str::uuid();
                }

                // Remap IDs inside the content structure
                foreach ($content['containers'] as &$container) {
                    $oldContainerId = $container['id'] ?? null;
                    $newContainerId = $oldContainerId ? (string) Str::uuid() : null;
                    if ($oldContainerId) {
                        $containerIdMap[$oldContainerId] = $newContainerId;
                        $container['id'] = $newContainerId;
                    }

                    foreach ($container['columns'] as &$column) {
                        $oldColumnId = $column['id'] ?? null;
                        $newColumnId = $oldColumnId ? (string) Str::uuid() : null;
                        if ($oldColumnId) {
                            $columnIdMap[$oldColumnId] = $newColumnId;
                            $column['id'] = $newColumnId;
                        }

                        // Remap widget references inside columns
                        foreach ($column['widgets'] as &$widgetRef) {
                            if (isset($widgetRef['id']) && isset($widgetIdMap[$widgetRef['id']])) {
                                $widgetRef['id'] = $widgetIdMap[$widgetRef['id']];
                            }
                        }
                        unset($widgetRef);
                    }
                    unset($column);
                }
                unset($container);

                // Save layout structure
                PageBuilderContent::updateOrCreate(
                    ['page_id' => $pageId],
                    [
                        'content'      => $content,
                        'version'      => $version,
                        'is_published' => false,
                        'published_at' => null,
                        'updated_by'   => auth('admin')->id(),
                    ]
                );

                // Replace widgets
                PageBuilderWidget::where('page_id', $pageId)->delete();

                $adminId = auth('admin')->id();
                foreach ($widgetsData as $oldWidgetId => $w) {
                    if (empty($w['type'])) {
                        continue;
                    }

                    PageBuilderWidget::create([
                        'page_id'             => $pageId,
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
                        'created_by'          => $adminId,
                        'updated_by'          => $adminId,
                    ]);
                }

                Page::findOrFail($pageId)->update([
                    'use_page_builder' => true,
                    'page_builder'     => 0,
                ]);

            // ── Old page builder format ──────────────────────────────────
            } elseif (is_array($data) && isset($data[0]['addon_page_type'])) {

                $file_contents = (object) array_map(fn($item) => (object) $item, $data);

                if (current($file_contents)->addon_page_type === 'dynamic_page') {
                    $contentArr = [];
                    foreach ($file_contents as $key => $content) {
                        unset($content->id);
                        $content->addon_page_id = $pageId;
                        $content->created_at    = now();
                        $content->updated_at    = now();

                        foreach ($content as $k => $v) {
                            $contentArr[$key][$k] = $v;
                        }
                    }

                    Page::findOrFail($pageId)->update(['page_builder' => 1, 'use_page_builder' => false]);
                    PageBuilder::where('addon_page_id', $pageId)->delete();
                    PageBuilder::insert($contentArr);

                } else {
                    Page::findOrFail($pageId)->update([
                        'page_builder'     => 0,
                        'use_page_builder' => false,
                        'page_content'     => current($file_contents)->text,
                    ]);
                }

            } else {
                throw new \Exception('Unrecognised layout file format.');
            }

            DB::commit();
            $type    = 'success';
            $message = 'Page layout uploaded successfully.';

        } catch (\Exception $exception) {
            DB::rollBack();
            $type    = 'danger';
            $message = 'Please upload a valid layout JSON file. (' . $exception->getMessage() . ')';
        }

        return back()->with(['type' => $type, 'msg' => $message]);
    }
//    public function show($slug)
//    {
//        $page = Page::where('slug', $slug)->firstOrFail();
//
//        if ($page->use_page_builder) {
//            $pageBuilderService = app(PageBuilderRenderService::class);
//            $page->rendered_content = $pageBuilderService->renderPage($page);
//        }
//
//        return view('frontend.pages.show', compact('page'));
//    }
}
