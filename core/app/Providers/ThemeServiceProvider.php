<?php

namespace App\Providers;

use App\Services\ThemeManager;
use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;
use Stancl\Tenancy\Events\TenancyInitialized;

class ThemeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/theme.php', 'theme');

        $this->app->singleton('theme', function () {
            return new ThemeManager();
        });

        // Keep backward-compatible facade accessor pointing to the same singleton
        $this->app->singleton('ThemeDataFacade', function () {
            return $this->app->make('theme');
        });
    }

    public function boot(): void
    {
        $this->registerViewComposers();

        // Seed theme:: namespace with fallback paths; activate() replaces these once tenancy boots.
        // Use addNamespace on the view factory so the correct finder instance gets the hints.
        app('view')->addNamespace('theme', [
            config('theme.base_path') . '/default/views',
            resource_path('views/tenant'),
        ]);
        // Also register on app('view.finder') for compatibility with code that resolves it directly.
        app('view.finder')->addNamespace('theme', [
            config('theme.base_path') . '/default/views',
            resource_path('views/tenant'),
        ]);

        // Register a stable `theme-{slug}::` namespace for every installed theme's views directory.
        // This lets theme widgets call view('theme-chefhome::widgets.hero_section') without
        // depending on which theme is currently active — enabling zero-core-change theme packaging.
        $this->registerThemeViewNamespaces();

        // Scan themes/{slug}/Widgets/*.php and push discovered classes into xgpagebuilder.custom_widgets.
        // Third-party themes ship their own widgets inside their own directory — no core file changes needed.
        $this->discoverThemeWidgets();

        // Activate theme when tenancy initializes
        app('events')->listen(TenancyInitialized::class, function (TenancyInitialized $event) {
            $slug = $event->tenancy->tenant->theme_slug ?? 'default';
            app('theme')->activate($slug ?: 'default');
        });
    }

    /**
     * Register view composers that inject typed wrapper objects into theme views.
     * Blog composer → all blog::* views (list, single, search, category)
     * Shop composer → all theme::frontend.shop.* views (listing, product detail)
     */
    protected function registerViewComposers(): void
    {
        app('view')->composer('blog::*', \App\Theme\ViewComposers\BlogComposer::class);
        app('view')->composer('theme::frontend.shop.*', \App\Theme\ViewComposers\ShopComposer::class);
    }

    /**
     * Register a `theme-{slug}::` Blade namespace for every theme that has a views/ directory.
     * Skips the _stubs scaffold directory.
     */
    protected function registerThemeViewNamespaces(): void
    {
        $base = config('theme.base_path', base_path('themes'));

        foreach (glob($base . '/*/views') ?: [] as $viewDir) {
            if (!preg_match('#/([^/]+)/views$#', $viewDir, $m)) {
                continue;
            }
            $slug = $m[1];
            if ($slug === '_stubs' || !is_dir($viewDir)) {
                continue;
            }
            app('view')->addNamespace('theme-' . $slug, $viewDir);
        }
    }

    /**
     * Auto-discover widget classes from themes/{slug}/Widgets/*.php.
     *
     * Convention:
     *   - File:      themes/{slug}/Widgets/HeroSection.php
     *   - Namespace: Themes\{StudlySlug}\Widgets\HeroSection
     *
     * No composer.json or xgpagebuilder.php changes needed per theme.
     * Each widget's enable() method gates visibility to the correct theme slug.
     */
    protected function discoverThemeWidgets(): void
    {
        $base  = config('theme.base_path', base_path('themes'));
        $files = glob($base . '/*/Widgets/*.php') ?: [];

        $registered = config('xgpagebuilder.custom_widgets', []);

        foreach ($files as $file) {
            if (!preg_match('#/([^/]+)/Widgets/([^.]+)\.php$#', $file, $m)) {
                continue;
            }

            $fqcn = 'Themes\\' . Str::studly($m[1]) . '\\Widgets\\' . $m[2];

            if (in_array($fqcn, $registered, true)) {
                continue;
            }

            require_once $file;

            if (class_exists($fqcn)) {
                $registered[] = $fqcn;
            }
        }

        config(['xgpagebuilder.custom_widgets' => $registered]);
    }
}
