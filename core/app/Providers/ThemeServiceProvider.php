<?php

namespace App\Providers;

use App\Services\ThemeManager;
use Illuminate\Support\Facades\Log;
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
        // Auto-setup on first request: publish all theme symlinks + fix asset permissions.
        // Uses a flag file so it only runs once per deploy (or when theme count changes).
        // No SSH, no artisan command needed — triggered automatically by the first web request.
        $this->autoSetup();

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

    // ── Auto Setup ───────────────────────────────────────────────────────────

    /**
     * Automatically publish all theme symlinks and fix asset folder permissions
     * on the first web request after deploy. Uses a flag file storing the theme
     * count — re-runs automatically when a new theme is added.
     *
     * No SSH, no artisan command, no manual steps needed.
     */
    protected function autoSetup(): void
    {
        try {
            $base      = config('theme.base_path', base_path('themes'));
            $assetDirs = glob($base . '/*/assets', GLOB_ONLYDIR) ?: [];

            // Filter out _stubs
            $assetDirs = array_values(array_filter(
                $assetDirs,
                fn($d) => basename(dirname($d)) !== '_stubs'
            ));

            $themeCount = count($assetDirs);
            $flagFile   = storage_path('app/.nazmart-themes-published');

            // Read stored count from flag file
            $storedCount = file_exists($flagFile) ? (int) trim(file_get_contents($flagFile)) : -1;

            // Skip if already set up with same number of themes
            if ($storedCount === $themeCount) {
                return;
            }

            // Fix writable asset folder permissions first
            $this->fixAssetPermissions();

            // Publish symlinks for all themes
            $this->publishAllThemes($assetDirs);

            // Store theme count so we re-run only when a new theme is added
            @file_put_contents($flagFile, (string) $themeCount);

        } catch (\Throwable $e) {
            Log::error('Nazmart autoSetup failed: ' . $e->getMessage());
        }
    }

    /**
     * Ensure writable asset directories exist and have correct permissions.
     * Uses umask(0) to bypass server permission mask — works on most live servers
     * without any manual chmod or chown.
     */
    protected function fixAssetPermissions(): void
    {
        $writableDirs = [
            base_path('assets/uploads'),
            base_path('assets/tenant'),
            base_path('assets/tenant/frontend'),
            base_path('assets/tenant/frontend/themes'),
            base_path('assets/tenant/frontend/themes/css'),
            base_path('assets/tenant/frontend/themes/css/dynamic-styles'),
            base_path('assets/tenant/frontend/themes/js'),
            base_path('assets/tenant/frontend/themes/js/dynamic-scripts'),
            base_path('assets/tenant/uploads'),
            base_path('assets/tenant/uploads/media-uploader'),
        ];

        $old = umask(0);
        foreach ($writableDirs as $dir) {
            if (!is_dir($dir)) {
                @mkdir($dir, 0775, true);
            } else {
                @chmod($dir, 0775);
            }
        }
        umask($old);
    }

    /**
     * Create symlinks (Unix) or copy (Windows) for all theme asset directories
     * into public/themes/{slug}/. Skips themes already published.
     */
    protected function publishAllThemes(array $assetDirs): void
    {
        $parent = public_path('themes');
        $old    = umask(0);

        if (!is_dir($parent)) {
            @mkdir($parent, 0775, true);
        }

        foreach ($assetDirs as $source) {
            $slug        = basename(dirname($source));
            $destination = $parent . DIRECTORY_SEPARATOR . $slug;

            // Already published — skip
            if (is_link($destination) || is_dir($destination)) {
                continue;
            }

            if (!is_dir($source)) {
                continue;
            }

            try {
                if (PHP_OS_FAMILY === 'Windows') {
                    $this->copyDirectoryRecursive($source, $destination);
                } else {
                    $relative = $this->buildRelativeSymlinkPath($parent, $source);
                    symlink($relative, $destination);
                }
            } catch (\Throwable $e) {
                Log::warning("Theme auto-publish failed [{$slug}]: " . $e->getMessage() .
                    ' — Try: chmod 775 ' . $parent);
            }
        }

        umask($old);
    }

    /**
     * Build a relative symlink path from $fromDir to $toTarget.
     */
    protected function buildRelativeSymlinkPath(string $fromDir, string $toTarget): string
    {
        $from   = explode(DIRECTORY_SEPARATOR, rtrim($fromDir, DIRECTORY_SEPARATOR));
        $to     = explode(DIRECTORY_SEPARATOR, rtrim($toTarget, DIRECTORY_SEPARATOR));
        $common = 0;

        foreach ($from as $i => $segment) {
            if (isset($to[$i]) && $segment === $to[$i]) {
                $common++;
            } else {
                break;
            }
        }

        $upCount = count($from) - $common;
        $downs   = array_slice($to, $common);

        return str_repeat('..' . DIRECTORY_SEPARATOR, $upCount) . implode(DIRECTORY_SEPARATOR, $downs);
    }

    /**
     * Recursively copy a directory (used on Windows where symlinks may not work).
     */
    protected function copyDirectoryRecursive(string $source, string $destination): void
    {
        if (!is_dir($destination)) {
            mkdir($destination, 0775, true);
        }
        $dir = opendir($source);
        while (($file = readdir($dir)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $src  = $source . DIRECTORY_SEPARATOR . $file;
            $dest = $destination . DIRECTORY_SEPARATOR . $file;
            is_dir($src) ? $this->copyDirectoryRecursive($src, $dest) : copy($src, $dest);
        }
        closedir($dir);
    }

    // ── View Composers ───────────────────────────────────────────────────────

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
