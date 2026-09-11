<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Contracts\ThemeDemoSeederContract;
use App\Helpers\ResponseMessage;
use App\Models\StaticOption;
use App\Models\Tenant;
use App\Services\AdminTheme\MetaDataHelpers;
use App\Services\ThemeDemoImporter;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TenantDashboardThemeManageController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $themes = MetaDataHelpers::Init();
        $all_themes = $themes->getThemesInfo();

        /* Experiment  */

        $themes->getThemesStyles('metronic','header');
        $themes->getThemesScriptsJs('metronic','footer');

        /* Experiment  */

        return view('tenant.admin.pages.dashboard.index',compact('all_themes'));
    }

    public function create()
    {
        return view('thememanage::create');
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        return view('thememanage::show');
    }


    public function edit($id)
    {
        return view('thememanage::edit');
    }

    public function update(Request $request, $slug)
    {
        $themes = MetaDataHelpers::Init();
        $all_themes = $themes->getThemesInfo();

        foreach ($all_themes as $theme_slug) {
//            dd($theme_slug);
            if ($slug == $theme_slug->slug) {
                update_static_option('tenant_admin_dashboard_theme', $slug);
                return response()->success(ResponseMessage::SettingsSaved());
            }
            return response()->error('Invalid Theme');
        }


        $theme_setting_type = $request->theme_setting_type;
        $requested_theme = $request->tenant_default_theme;

//        try {
//            $tenant_id = \tenant()->id;
//            Tenant::where('id', $tenant_id)->update([
//                'theme_slug' => $requested_theme
//            ]);
//        } catch (\Exception $exception) {}


//        if($theme_setting_type == 'set_theme_with_demo_data'){
//            $data_imported = $this->set_new_home($requested_theme);
//
//            if (!$data_imported['status'])
//            {
//                return response()->json($data_imported);
//            }
//        }

    }

    public function destroy($id)
    {
        //
    }

    public function activateFrontendTheme(Request $request)
    {
        $request->validate([
            'theme_slug'     => 'required|string',
            'with_demo_data' => 'nullable|boolean',
        ]);

        $themeSlug    = $request->input('theme_slug');
        $withDemoData = (bool) $request->input('with_demo_data', false);

        // Verify theme exists on disk
        if (! file_exists(theme_path($themeSlug) . '/theme.json')) {
            return response()->json(['status' => false, 'msg' => __('Theme not found.')], 404);
        }

        // Update theme_slug on the central Tenant record
        $tenantId = tenant()->id;
        Tenant::where('id', $tenantId)->update(['theme_slug' => $themeSlug]);

        if ($withDemoData) {
            // Step 1 — import categories, products, settings from demo/data.json
            try {
                $importer = new ThemeDemoImporter($themeSlug);
                $result   = $importer->import();
                if (! $result['status']) {
                    \Log::warning('ThemeDemoImporter skipped', ['theme' => $themeSlug, 'msg' => $result['msg']]);
                }
            } catch (\Throwable $e) {
                \Log::error('ThemeDemoImporter failed', ['theme' => $themeSlug, 'error' => $e->getMessage()]);
                return response()->json(['status' => false, 'msg' => __('Demo data import failed: ') . $e->getMessage()]);
            }

            // Step 2 — build page builder home layout via PHP DemoSeeder
            $class = 'Themes\\' . Str::studly($themeSlug) . '\\DemoSeeder';

            if (class_exists($class) && is_a($class, ThemeDemoSeederContract::class, true)) {
                try {
                    (new $class())->run();
                } catch (\Throwable $e) {
                    \Log::error('ThemeDemoSeeder failed', ['theme' => $themeSlug, 'error' => $e->getMessage()]);
                    return response()->json(['status' => false, 'msg' => __('Demo layout import failed: ') . $e->getMessage()]);
                }
            }
        }

        return response()->json(['status' => true, 'msg' => __('Theme activated successfully.')]);
    }
}
