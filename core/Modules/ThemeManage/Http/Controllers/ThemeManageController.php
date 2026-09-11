<?php

namespace Modules\ThemeManage\Http\Controllers;

use App\Contracts\ThemeDemoSeederContract;
use App\Facades\ThemeDataFacade;
use App\Helpers\SeederHelpers\JsonDataModifier;
use App\Models\Page;
use App\Models\PageBuilder;
use App\Services\ThemeDemoImporter;
use Illuminate\Support\Str;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Xgenious\PageBuilder\Models\PageBuilderContent;
use Xgenious\PageBuilder\Models\PageBuilderWidget;

class ThemeManageController extends Controller
{
    const BASE_PATH = 'thememanage::tenant.backend.';

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view(self::BASE_PATH . 'index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('thememanage::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('thememanage::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('thememanage::edit');
    }

    public function update(Request $request, $slug)
    {
//        dd($request->all());
        $request->validate([
            'theme_setting_type' => ['required', Rule::in(['set_theme', 'set_theme_with_demo_data'])],
            'tenant_default_theme' => 'required',
        ], [
            'theme_setting_type.required' => __('Please select theme setting type by clicking on the theme image..!')
        ]);

        $all_theme_slugs = getAllThemeSlug();
        if (!in_array($slug, $all_theme_slugs)) {
            return response()->json([
                'status' => false
            ]);
        }

        $theme_setting_type = $request->theme_setting_type;
        $requested_theme = $request->tenant_default_theme;

        try {
            tenant()->update(['theme_slug' => $requested_theme]);
        } catch (\Exception $exception) {
            \Log::error('Theme update failed: ' . $exception->getMessage());
            return response()->json([
                'status' => false,
                'msg' => __('Failed to save theme. Please try again.')
            ]);
        }


        if ($theme_setting_type == 'set_theme_with_demo_data') {
            $demoJson = config('theme.base_path') . '/' . $requested_theme . '/demo/data.json';

            if (file_exists($demoJson)) {
                // New theme system: import settings/categories/products from demo/data.json
                $data_imported = (new ThemeDemoImporter($requested_theme))->import();

                if (!$data_imported['status']) {
                    return response()->json($data_imported);
                }

                // If demo data didn't set a home page, try PHP DemoSeeder first, then legacy fallback
                if (!get_static_option('home_page')) {
                    $seederClass = 'Themes\\' . Str::studly($requested_theme) . '\\DemoSeeder';
                    if (class_exists($seederClass) && is_a($seederClass, ThemeDemoSeederContract::class, true)) {
                        try {
                            (new $seederClass())->run();
                        } catch (\Throwable $e) {
                            \Log::error('ThemeDemoSeeder failed', ['theme' => $requested_theme, 'error' => $e->getMessage()]);
                        }
                    } else {
                        $this->set_new_home($requested_theme);
                    }
                }
            } else {
                // Legacy fallback: page-layout JSON based import
                $data_imported = $this->set_new_home($requested_theme);

                if (!$data_imported['status']) {
                    return response()->json($data_imported);
                }
            }
        } else {
            // "set_theme" only — no demo data import.
            // Still refresh the home page layout so new theme's addons load correctly.
            $home_page_id = get_static_option('home_page');
            if ($home_page_id) {
                $this->refresh_home_layout($requested_theme, (int) $home_page_id);
            }
        }

        return response()->json([
            'status' => true,
            'msg' => __('Theme selected successfully')
        ]);
    }

    private function refresh_home_layout(string $theme, int $page_id): void
    {
        // Try the new-style layout path first: themes/{slug}/assets/page_layout/home-layout.json
        $newPath = config('theme.base_path') . '/' . $theme . '/assets/page_layout/home-layout.json';

        // Legacy path: assets/tenant/page-layout/home-pages/{theme}-layout.json (web-root relative)
        $legacyFile = $theme . '-layout.json';

        if (file_exists($newPath)) {
            $this->apply_layout_to_page($newPath, $page_id);
        } else {
            $this->upload_layout($legacyFile, $page_id);
        }
    }

    public function set_new_home($requested_theme)
    {
        $current_theme = $requested_theme;

        $object = new JsonDataModifier('', 'dynamic-pages');
        $data = $object->getColumnDataForDynamicPage([
            'id',
            'title',
            'page_content',
            'slug',
            'page_builder',
            'breadcrumb',
            'status',
            'theme_slug'
        ],true, true);

        //For home pages
        $filter_data = array_filter($data,function ($item) use ($current_theme){
            if (in_array($item['theme_slug'],[null,$current_theme])){
                if($item['theme_slug'] == $current_theme){
                    return $item;
                }
            }
        });

        $homepageData = current($filter_data);

        $mapped_data = array_map(function ($item){
            unset($item['theme_slug']);
            return $item;
        },$filter_data);

        $main_data = current($mapped_data);

        $old_page = Page::find($main_data['id']);
        if($old_page)
        {
            $new_page = Page::latest('id')->select('id')->first();
            $new_page_id = $new_page->id + 1;
            $homepageData['id'] = $main_data['id'] = $new_page_id;

            $main_data['slug'] = $old_page->slug.'-'.$new_page_id;
        }

        Page::insert($main_data);

        $homepage_id = $homepageData['id'] ?? null;
        $home_page_layout_file = $current_theme.'-layout.json';
        $this->upload_layout($home_page_layout_file, $homepage_id);

        update_static_option('home_page', $homepage_id);

        return ['status' => true, 'msg' => __('Theme Data Imported Successfully')];
    }

    /**
     * Apply a layout file (any supported format) to a page.
     * Accepts an absolute file path and detects format automatically.
     */
    private function apply_layout_to_page(string $filePath, int $page_id): void
    {
        if (!file_exists($filePath)) {
            return;
        }

        $raw  = file_get_contents($filePath);
        $data = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE || empty($data)) {
            return;
        }

        // ── New React page builder format (xg_page_builder_v2) ──────────
        if (isset($data['format']) && $data['format'] === 'xg_page_builder_v2') {
            $content     = $data['content'] ?? ['containers' => []];
            $widgetsData = $data['widgets'] ?? [];
            $version     = $data['version'] ?? '1.0';

            // Remap all UUIDs to avoid collisions with existing pages
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
                    'updated_by'   => auth('admin')->id(),
                ]
            );

            PageBuilderWidget::where('page_id', $page_id)->delete();

            $adminId = auth('admin')->id();
            foreach ($widgetsData as $oldWidgetId => $w) {
                if (empty($w['type'])) {
                    continue;
                }
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
                    'created_by'          => $adminId,
                    'updated_by'          => $adminId,
                ]);
            }

            \Cache::forget('page_id-' . $page_id);
            Page::findOrFail($page_id)->update(['use_page_builder' => true, 'page_builder' => 0]);
            return;
        }

        // ── Legacy old page builder format ───────────────────────────────
        $fileContents = json_decode($raw);
        $fileContents = $fileContents->data ?? $fileContents;

        if (empty($fileContents)) {
            return;
        }

        $fileContents = (array) $fileContents;
        $first = current($fileContents);

        if (!isset($first->addon_page_type) || $first->addon_page_type !== 'dynamic_page') {
            Page::findOrFail($page_id)->update([
                'page_builder' => 0,
                'page_content' => $first->text ?? '',
            ]);
            return;
        }

        $contentArr = [];
        foreach ($fileContents as $key => $content) {
            unset($content->id);
            $content->addon_page_id = $page_id;
            $content->created_at    = now();
            $content->updated_at    = now();
            foreach ($content as $k => $v) {
                $contentArr[$key][$k] = $v;
            }
        }

        \Cache::forget('page_id-' . $page_id);
        Page::findOrFail($page_id)->update(['page_builder' => 1]);
        PageBuilder::where('addon_page_id', $page_id)->delete();
        PageBuilder::insert($contentArr);
    }

    private function upload_layout($file, $page_id)
    {
        $path = public_path('assets/tenant/page-layout/home-pages/' . $file);
        $this->apply_layout_to_page($path, (int) $page_id);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
