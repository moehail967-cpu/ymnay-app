<?php

namespace App\Providers;

use App\Helpers\LanguageHelper;
use App\Helpers\ModuleMetaData;
use App\Helpers\SidebarMenuHelper;
use App\Http\Services\RenderImageMarkupService;
use App\Services\CustomPageBuilderRenderService;
use App\Models\Themes;
use Xgenious\PageBuilder\Services\PageBuilderRenderService;
use App\Models\User;
use App\Observers\TenantRegisterObserver;
use App\Observers\WalletBalanceObserver;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Modules\Blog\Entities\BlogCategory;
use Modules\Wallet\Entities\Wallet;
use function Psy\bin;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        app()->singleton('LandlordAdminMenu',function (){
           return  new SidebarMenuHelper();
        });
        app()->singleton('GlobalLanguage',function (){
           return  new LanguageHelper();
        });

        $this->app->singleton('ModuleDataFacade', function (){
            return new ModuleMetaData();
        });
        $this->app->singleton('ImageRenderFacade', function (){
            return new RenderImageMarkupService();
        });

        /* LARAVEL TELESCOPE */
        if ($this->app->environment('local') && in_array(request()->getHost(), ['nazmart.test','127.0.0.1','localhost'])) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->ensureSymlinksExist();

        // Override vendor's PageBuilderRenderService with our custom version
        // (column width fix: editor saves "width" %, vendor PHP reads "size" int)
        // Must be in boot() so it runs after vendor's PageBuilderServiceProvider::register()
        $this->app->bind(PageBuilderRenderService::class, CustomPageBuilderRenderService::class);

        Paginator::useBootstrap();

        // Wrap in try-catch to prevent facade errors during bootstrap
        try {
            if (get_static_option('site_force_ssl_redirection') === 'on'){
                URL::forceScheme('https');
            }
        } catch (\Exception $e) {
            // Silently fail if called before facades are ready
        }

        // Register Page Builder routes with landlord/tenant prefixes
        $this->registerPageBuilderRoutes();

        /**
         * Setup micros for mediauploader url or cloudlflareurl
         * */

        Storage::macro("renderUrl", function ($filepath, $size = null, $load_from = 0)
        {
            $prefix = !empty($size) ? '' : $size."/".$size."-";
            if ($size == ""){
                $prefix = "";
            }

            if ($prefix == "full"){
                $prefix = "";
            }

            $driver = Storage::getDefaultDriver();

            if ($load_from === 0 && !is_null(tenant())){
                $driver = "TenantMediaUploader";
            }else{
                $driver = "LandlordMediaUploader";
            }

            $file_url = Storage::disk($driver)->url($prefix.$filepath);

            if ($load_from == 0 && !is_null(tenant())){
                return str_replace("/storage",url("/assets/tenant/uploads/media-uploader/".tenant()->getTenantKey().$prefix),$file_url);
            }elseif($load_from == 0 && is_null(tenant())){
                return str_replace("/storage",url("/assets/landlord/uploads/media-uploader").$prefix,$file_url);
            }


            if (Storage::getDefaultDriver() == "TenantMediaUploader"){
                return str_replace("/storage",url("/assets/tenant/uploads/media-uploader/".tenant()->getTenantKey().$prefix),$file_url);
            }

            if (Storage::getDefaultDriver() == "LandlordMediaUploader"){
                return str_replace("/storage",url("/assets/landlord/uploads/media-uploader").$prefix,$file_url);
            }

            $folder_prefix = "";
            if (!is_null(tenant())){
                $folder_prefix = tenant()->getTenantKey()."/";
            }

            if (cloudStorageExist() && Storage::getDefaultDriver() == "wasabi"){
//                $bucket = get_static_option_central('wasabi_bucket') ?? '';
//                $endpoint = get_static_option_central('wasabi_url') ?? '';

//                $path = str_replace("https://".$bucket.".".str_replace("https://","",$endpoint."/"),"",$file_url);
                $filepath = tenant() ? tenant()->id.'/'.$filepath : $filepath;
                $finalUrl = renderWasabiCloudFile($filepath);

                return $finalUrl;
            }


            if (cloudStorageExist() && Storage::getDefaultDriver() == "s3"){
                $tempUrl = Storage::temporaryUrl($folder_prefix.$prefix.$filepath,Carbon::now()->addMinutes(20));
                return $tempUrl;
            }

//            $tempUrl = Cache::remember($filepath,Carbon::now()->addMinutes(15),function ()use($filepath){
//                Storage::temporaryUrl($filepath,Carbon::now()->addMinutes(20));
//            });

            $tempUrl = Storage::temporaryUrl($folder_prefix.$prefix.$filepath,Carbon::now()->addMinutes(20));

            //cloudflare temporary url
            $finalUrl = str_replace([
                "https://".get_static_option_central('cloudflare_r2_bucket').".".str_replace("https://","",get_static_option_central('cloudflare_r2_endpoint'))
            ],[
                "https://".get_static_option_central('cloudflare_r2_url')
            ],$tempUrl);

            return $finalUrl;
        });
    }

    /**
     * Automatically create public/storage and public/themes/* symlinks
     * if they are missing. Runs once on the first web request after
     * a fresh server installation.
     */
    protected function ensureSymlinksExist(): void
    {
        if ($this->app->runningInConsole()) {
            return;
        }

        try {
            // public/storage symlink
            if (!file_exists(public_path('storage')) && !is_link(public_path('storage'))) {
                Artisan::call('storage:link');
            }

            // public/themes/* symlinks
            $themesPublic = public_path('themes');
            if (!is_dir($themesPublic) || count(glob($themesPublic . '/*', GLOB_ONLYDIR)) === 0) {
                Artisan::call('theme:publish', ['--all' => true]);
            }

            // Root-level themes symlink (project_root/themes -> core/public/themes)
            // Valet/Herd serves static files from the project root (where index.php lives),
            // not from core/public/, so theme assets must be reachable at root/themes/.
            $projectRoot = dirname(base_path());
            $rootThemesLink = $projectRoot . '/themes';
            if (!file_exists($rootThemesLink) && !is_link($rootThemesLink)) {
                symlink(public_path('themes'), $rootThemesLink);
            }
        } catch (\Throwable $e) {
            // Silently fail — symlinks are non-critical at bootstrap time
        }
    }

    /**
     * Register Page Builder routes with landlord/tenant context
     */
    protected function registerPageBuilderRoutes()
    {
        // Import required classes
        $pageBuilderController = \App\Http\Controllers\CustomPageBuilderController::class;
        $assetController = \Xgenious\PageBuilder\Http\Controllers\AssetController::class;

        // Serve page builder assets (shared for both landlord and tenant)
        \Illuminate\Support\Facades\Route::get('/vendor/page-builder/{path}', [$assetController, 'serve'])
            ->where('path', '.*')
            ->name('page-builder.assets');

        // Landlord Page Builder Routes
        \Illuminate\Support\Facades\Route::middleware(['web', 'auth:admin'])
            ->prefix('admin-home/landlord')
            ->name('landlord.')
            ->group(function () use ($pageBuilderController) {
                \Illuminate\Support\Facades\Route::get('/page-builder/edit/{pageId}', [$pageBuilderController, 'edit'])
                    ->name('admin.page-builder.edit')
                    ->whereNumber('pageId');
            });

        // Tenant Page Builder Routes (only if tenant exists)
        \Illuminate\Support\Facades\Route::middleware([
                'web',
                \App\Http\Middleware\Tenant\InitializeTenancyByDomainCustomisedMiddleware::class,
                \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
                'auth:admin',
            ])
            ->prefix('admin-home/tenant')
            ->name('tenant.')
            ->group(function () use ($pageBuilderController) {
                \Illuminate\Support\Facades\Route::get('/page-builder/edit/{pageId}', [$pageBuilderController, 'edit'])
                    ->name('admin.page-builder.edit')
                    ->whereNumber('pageId');
            });
    }
}
