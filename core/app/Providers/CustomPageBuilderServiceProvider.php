<?php

namespace App\Providers;

use App\Http\Controllers\FilteredWidgetController;
use Illuminate\Support\Facades\Route;
use Xgenious\PageBuilder\PageBuilderServiceProvider;

class CustomPageBuilderServiceProvider extends PageBuilderServiceProvider
{
    /**
     * Override route configuration to use the published config's middleware
     * instead of the hardcoded ['api'] in the vendor service provider.
     * This prevents InitializeTenancyByDomainCustomisedMiddleware from running
     * on page-builder API routes when accessed from the landlord domain.
     */
    protected function routeConfiguration(): array
    {
        return [
            'prefix' => 'api/' . config('xgpagebuilder.route_prefix', 'page-builder'),
            'middleware' => config('xgpagebuilder.route_middleware', ['web', 'auth:admin']),
        ];
    }

    /**
     * Override the parent's registerRoutes to prevent loading default routes
     * We'll register our own routes in AppServiceProvider with landlord/tenant prefixes
     */
    protected function registerRoutes(): void
    {
        // We register ALL API routes manually here instead of loading api.php.
        // This gives us full control: the widget listing routes use FilteredWidgetController
        // (which respects enable() to hide landlord-only and wrong-theme widgets in tenant context),
        // while all other routes proxy to the package's controllers unchanged.
        //
        // Vendor patch tracking: see docs/page-builder-vendor-patches.md
        $pkg = \Xgenious\PageBuilder\Http\Controllers\PageBuilderController::class;
        $wgt = \Xgenious\PageBuilder\Http\Controllers\WidgetController::class;
        $ses = \Xgenious\PageBuilder\Http\Controllers\EditingSessionController::class;
        $col = \Xgenious\PageBuilder\Http\Controllers\ColumnCSSController::class;

        Route::group($this->routeConfiguration(), function () use ($pkg, $wgt, $ses, $col) {
            // Content Management
            Route::get('/pages/{pageId}/content', [$pkg, 'getContent'])->name('api.page-builder.get-content')->whereNumber('pageId');
            Route::get('/pages/{pageId}/history', [$pkg, 'getHistory'])->name('api.page-builder.history')->whereNumber('pageId');
            Route::get('/pages/{pageId}/widgets/{widgetId}', [$pkg, 'getWidgetData'])->name('api.page-builder.widget-data')->whereNumber('pageId');

            // Save / Publish
            Route::post('/save', [$pkg, 'saveContent'])->name('api.page-builder.save-content');
            Route::post('/publish', [$pkg, 'publish'])->name('api.page-builder.publish');
            Route::post('/unpublish', [$pkg, 'unpublish'])->name('api.page-builder.unpublish');

            // Widget Settings
            Route::get('/pages/{pageId}/widgets/{widgetId}/settings/{tab}', [$pkg, 'getWidgetSettings'])
                ->name('api.page-builder.get-widget-settings')->whereNumber('pageId')->whereIn('tab', ['general', 'style', 'advanced']);

            // Settings Save
            Route::post('/pages/{pageId}/widgets/{widgetId}/save-all-settings', [$pkg, 'saveWidgetAllSettings'])->name('api.page-builder.save-widget-all-settings')->whereNumber('pageId');
            Route::post('/pages/{pageId}/sections/{sectionId}/save-all-settings', [$pkg, 'saveSectionAllSettings'])->name('api.page-builder.save-section-all-settings')->whereNumber('pageId');
            Route::post('/pages/{pageId}/columns/{columnId}/save-all-settings', [$pkg, 'saveColumnAllSettings'])->name('api.page-builder.save-column-all-settings')->whereNumber('pageId');
            Route::post('/pages/{pageId}/widgets/{widgetId}/save-general-settings', [$pkg, 'saveWidgetGeneralSettings'])->name('api.page-builder.save-widget-general-settings')->whereNumber('pageId');
            Route::post('/pages/{pageId}/widgets/{widgetId}/save-style-settings', [$pkg, 'saveWidgetStyleSettings'])->name('api.page-builder.save-widget-style-settings')->whereNumber('pageId');
            Route::post('/pages/{pageId}/widgets/{widgetId}/save-advanced-settings', [$pkg, 'saveWidgetAdvancedSettings'])->name('api.page-builder.save-widget-advanced-settings')->whereNumber('pageId');

            // CSS Generation
            Route::post('/columns/css/generate', [$col, 'generateCSS'])->name('api.page-builder.column-css.generate');
            Route::post('/css/generate', [$pkg, 'generateCSS'])->name('api.page-builder.css.generate');
            Route::post('/css/generate-bulk', [$pkg, 'generateBulkCSS'])->name('api.page-builder.css.generate-bulk');
            Route::get('/defaults/{type}', [$pkg, 'getDefaultSettings'])->name('api.page-builder.defaults')->where('type', 'section|column|widget');

            // Editing Sessions
            Route::post('/pages/{pageId}/start-editing', [$ses, 'startSession'])->name('api.page-builder.start-editing')->whereNumber('pageId');
            Route::put('/editing-sessions/{sessionToken}/heartbeat', [$ses, 'heartbeat'])->name('api.page-builder.heartbeat');
            Route::delete('/editing-sessions/{sessionToken}', [$ses, 'endSession'])->name('api.page-builder.end-session');
            Route::post('/pages/{pageId}/takeover', [$ses, 'takeover'])->name('api.page-builder.takeover')->whereNumber('pageId');
            Route::get('/pages/{pageId}/editors', [$ses, 'getEditors'])->name('api.page-builder.get-editors')->whereNumber('pageId');
            Route::post('/editing-sessions/cleanup', [$ses, 'cleanup'])->name('api.page-builder.cleanup-sessions');

            // Widget API — listing routes use FilteredWidgetController to respect enable()
            Route::get('/widgets', [FilteredWidgetController::class, 'index'])->name('api.page-builder.widgets.index');
            Route::get('/widgets/grouped', [FilteredWidgetController::class, 'grouped'])->name('api.page-builder.widgets.grouped');
            Route::get('/widgets/search', [FilteredWidgetController::class, 'search'])->name('api.page-builder.widgets.search');
            Route::post('/widgets/{type}/preview', [$wgt, 'preview'])->name('api.page-builder.widgets.preview');
            Route::get('/widgets/{type}', [$wgt, 'show'])->name('api.page-builder.widgets.show');
            Route::get('/widgets/{type}/fields/{tab}', [$wgt, 'getWidgetFields'])->name('api.page-builder.widgets.fields');
        });

        // Don't load web.php - handled in AppServiceProvider with landlord/tenant prefixes
    }

    public function boot(): void
    {
        parent::boot();

        if ($this->app->runningInConsole()) {
            // Publish page-builder assets to root assets/ directory instead of core/public/
            // Usage: php artisan vendor:publish --tag=page-builder-root-assets --force
            $this->publishes([
                base_path('vendor/xgenious/xgpagebuilder/public') => base_path('../assets/vendor/page-builder'),
            ], 'page-builder-root-assets');
        }
    }
}
