<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\View\Factory as ViewFactory;

/**
 * Central theme engine. Bound as singleton 'theme' via ThemeServiceProvider.
 * - activate($slug)  called once per request when tenancy boots
 * - asset($path)     resolves theme asset URLs via public symlinks
 * - loadMeta($slug)  reads and caches themes/{slug}/theme.json
 */
class ThemeManager
{
    protected string  $activeSlug      = 'default';
    protected ?object $activeThemeMeta = null;

    // Tracks which slugs have had their assets verified this PHP process lifecycle.
    // Static so the check runs once per worker, not on every tenant request.
    private static array $ensured = [];

    // ── Activation ──────────────────────────────────────────────────────────

    public function activate(string $slug): void
    {
        $this->activeSlug      = $slug ?: 'default';
        $this->activeThemeMeta = null; // lazy-load on first use

        // Self-heal: if symlink is missing for this tenant's theme, create it now.
        // Covers server migration, symlink deletion, and edge cases ThemeServiceProvider missed.
        $this->ensureAssetsPublished($this->activeSlug);

        $this->registerThemeViewNamespace($this->activeSlug);
        $this->registerModuleViewOverrides($this->activeSlug);
    }

    protected function registerThemeViewNamespace(string $slug): void
    {
        $paths = [];

        if ($slug !== 'default') {
            $p = config('theme.base_path') . '/' . $slug . '/views';
            if (is_dir($p)) {
                $paths[] = $p;
            }
        }

        $default = config('theme.base_path') . '/default/views';
        if (is_dir($default)) {
            $paths[] = $default;
        }

        // Core tenant views as last-resort fallback.
        // Must be views/tenant (not views/tenant/frontend) because renderThemeView()
        // prepends 'frontend.' — so theme::frontend.pages.x resolves to
        // resources/views/tenant/frontend/pages/x.blade.php correctly.
        $paths[] = resource_path('views/tenant');

        // Use the factory's finder — app('view.finder') is a different singleton instance
        // that the factory does not use, so changes to it have no effect on view resolution.
        $finder = app('view')->getFinder();

        if (method_exists($finder, 'replaceNamespace')) {
            $finder->replaceNamespace('theme', $paths);
            // Flush the resolved-path cache so previously resolved views get re-looked-up
            // with the updated namespace hints.
            if (method_exists($finder, 'flush')) {
                $finder->flush();
            }
        } else {
            $finder->addNamespace('theme', $paths);
        }
    }

    /**
     * Prepend theme-specific module view overrides to each module's namespace.
     * Convention: themes/{slug}/views/modules/{module-name}/ → prepended to the
     * "{module-name}" view namespace so module controllers resolve theme views first.
     * E.g. themes/chefhome/views/modules/blog/ overrides blog:: views for ChefHome.
     */
    protected function registerModuleViewOverrides(string $slug): void
    {
        if ($slug === 'default') {
            return;
        }

        $base = config('theme.base_path') . '/' . $slug . '/views/modules';
        if (!is_dir($base)) {
            return;
        }

        $finder = app('view')->getFinder();

        foreach (glob($base . '/*', GLOB_ONLYDIR) ?: [] as $moduleDir) {
            $moduleNamespace = basename($moduleDir);
            // Prepend so theme views take priority over the module's own views.
            $finder->prependNamespace($moduleNamespace, $moduleDir);
        }

        if (method_exists($finder, 'flush')) {
            $finder->flush();
        }
    }

    // ── Asset Self-Heal ──────────────────────────────────────────────────────

    /**
     * Ensure theme assets are published (symlinked) for the given slug.
     * Uses a static array so the filesystem check runs at most once per
     * PHP-FPM worker process per slug — zero overhead on subsequent requests.
     *
     * This is a safety net for server migrations, symlink deletions, or any
     * slug that ThemeServiceProvider::autoSetup() may have missed.
     */
    protected function ensureAssetsPublished(string $slug): void
    {
        if ($slug === 'default' || isset(self::$ensured[$slug])) {
            return;
        }
        self::$ensured[$slug] = true;

        $destination = public_path('themes' . DIRECTORY_SEPARATOR . $slug);

        // Already published — nothing to do.
        if (is_link($destination) || is_dir($destination)) {
            return;
        }

        $source = config('theme.base_path') . DIRECTORY_SEPARATOR . $slug . DIRECTORY_SEPARATOR . 'assets';

        if (!is_dir($source)) {
            return;
        }

        $parent = public_path('themes');
        $old    = umask(0);

        if (!is_dir($parent)) {
            @mkdir($parent, 0775, true);
        }

        try {
            if (PHP_OS_FAMILY === 'Windows') {
                $this->copyThemeDirectory($source, $destination);
            } else {
                $relative = $this->buildThemeSymlinkPath($parent, $source);
                symlink($relative, $destination);
            }
            \Illuminate\Support\Facades\Log::info("Theme self-heal: published assets for [{$slug}].");
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error(
                "Theme self-heal failed [{$slug}]: " . $e->getMessage() .
                ' — Fix: chmod 775 ' . $parent
            );
        }

        umask($old);
    }

    /**
     * Build a relative path from $fromDir to $toTarget for use in symlink().
     */
    protected function buildThemeSymlinkPath(string $fromDir, string $toTarget): string
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
     * Recursively copy a directory (Windows fallback — no symlink support).
     */
    protected function copyThemeDirectory(string $source, string $destination): void
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
            is_dir($src) ? $this->copyThemeDirectory($src, $dest) : copy($src, $dest);
        }
        closedir($dir);
    }

    // ── Public API ───────────────────────────────────────────────────────────

    public function getSelectedThemeData(): ?object
    {
        if ($this->activeThemeMeta !== null) {
            return $this->activeThemeMeta;
        }

        // Resolve slug: use activated slug, or fall back to tenant
        $slug = $this->activeSlug !== 'default'
            ? $this->activeSlug
            : (tenant()?->theme_slug ?? 'default');

        $this->activeThemeMeta = $this->loadMeta($slug);
        return $this->activeThemeMeta;
    }

    public function getSelectedThemeSlug(): ?string
    {
        $data = $this->getSelectedThemeData();
        return !empty($data) ? $data->slug : null;
    }

    public function getHeaderHook(): mixed
    {
        $themeMeta = $this->getSelectedThemeData();
        if (!empty($themeMeta) && property_exists($themeMeta, 'headerHook')) {
            return current($themeMeta->headerHook);
        }
        return [];
    }

    public function getFooterHook(): mixed
    {
        $themeMeta = $this->getSelectedThemeData();
        if (!empty($themeMeta) && property_exists($themeMeta, 'footerHook')) {
            return current($themeMeta->footerHook);
        }
        return [];
    }

    public function getHeaderHookCssFiles(): array
    {
        $headerHook = $this->getHeaderHook();
        if (!empty($headerHook) && property_exists($headerHook, 'style')) {
            return (array) $headerHook->style;
        }
        return [];
    }

    public function getHeaderHookRtlCssFiles(): array
    {
        $headerHook = $this->getHeaderHook();
        if (!empty($headerHook) && property_exists($headerHook, 'rtl_style')) {
            return (array) $headerHook->rtl_style;
        }
        return [];
    }

    public function getHeaderHookJsFiles(): array
    {
        $headerHook = $this->getHeaderHook();
        if (!empty($headerHook) && property_exists($headerHook, 'script')) {
            return (array) $headerHook->script;
        }
        return [];
    }

    public function getFooterHookCssFiles(): array
    {
        $footerHook = $this->getFooterHook();
        if (!empty($footerHook) && property_exists($footerHook, 'style')) {
            return (array) $footerHook->style;
        }
        return [];
    }

    public function getFooterHookJsFiles(): array
    {
        $footerHook = $this->getFooterHook();
        if (!empty($footerHook) && property_exists($footerHook, 'script')) {
            return (array) $footerHook->script;
        }
        return [];
    }

    public function renderHeaderHookBladeFile(): string
    {
        $headerHook = $this->getHeaderHook();
        $output     = '';

        if (!empty($headerHook) && property_exists($headerHook, 'blade')) {
            foreach ($headerHook->blade as $blade) {
                $viewName = 'theme::header.' . $blade;
                if (view()->exists($viewName)) {
                    $output .= view($viewName)->render() . "\n";
                }
            }
        }

        return $output;
    }

    public function renderFooterHookBladeFile(): string
    {
        $footerHook = $this->getFooterHook();
        $output     = '';

        if (!empty($footerHook) && property_exists($footerHook, 'blade')) {
            foreach ($footerHook->blade as $blade) {
                $viewName = 'theme::footer.' . $blade;
                if (view()->exists($viewName)) {
                    $output .= view($viewName)->render() . "\n";
                }
            }
        }

        return $output;
    }

    public function getFooterWidgetArea(): string
    {
        $footerHook = $this->getFooterHook();
        if (!empty($footerHook) && property_exists($footerHook, 'widgetArea') && !empty($footerHook->widgetArea)) {
            $area = $footerHook->widgetArea;
            return str_contains($area, '/') ? $area : 'footer/' . $area;
        }
        return '';
    }

    public function getHeaderNavbarArea(): string
    {
        $headerHook = $this->getHeaderHook();
        if (!empty($headerHook) && property_exists($headerHook, 'navbarArea') && !empty($headerHook->navbarArea)) {
            $area = $headerHook->navbarArea;
            return str_contains($area, '/') ? $area : 'header/' . $area;
        }
        return '';
    }

    public function getHeaderBreadcrumbArea(): string
    {
        $headerHook = $this->getHeaderHook();
        if (!empty($headerHook) && property_exists($headerHook, 'breadcrumbArea') && !empty($headerHook->breadcrumbArea)) {
            $area = $headerHook->breadcrumbArea;
            return str_contains($area, '/') ? $area : 'header/' . $area;
        }
        return '';
    }

    public function renderThemeView(string $view = '', array $data = []): \Illuminate\Contracts\View\View
    {
        return view('theme::frontend.' . $view, $data);
    }

    public function getAllThemeDataForAdmin(): array
    {
        return $this->scanThemeDirectories(includeDefault: false);
    }

    public function getAllThemeData(): array
    {
        $all = $this->scanThemeDirectories(includeDefault: false);
        return array_filter($all, fn($t) => property_exists($t, 'status') && $t->status);
    }

    public function getAllThemeSlug(): array
    {
        $slugs = [];
        foreach ($this->getAllThemeData() as $data) {
            if (property_exists($data, 'slug')) {
                $slugs[] = $data->slug;
            }
        }
        return array_values($slugs);
    }

    public function getDefaultThemeData(): mixed
    {
        $all = $this->scanThemeDirectories(includeDefault: true);
        $defaults = array_filter($all, fn($t) => property_exists($t, 'slug') && $t->slug === 'default');
        return !empty($defaults) ? current($defaults) : [];
    }

    public function getIndividualThemeDetails(string $themeName): array
    {
        $meta        = $this->loadMeta($themeName);
        $defaultMeta = $this->getDefaultThemeData();
        $details     = [];

        if (!empty($meta)) {
            $details['name']        = $meta->name        ?? ($defaultMeta->name ?? '');
            $details['slug']        = $meta->slug        ?? ($defaultMeta->slug ?? '');
            $details['description'] = $meta->description ?? ($defaultMeta->description ?? '');
        }

        return $details;
    }

    public function getIndividualScreenshotMeta(string $themeSlug): mixed
    {
        $meta = $this->loadMeta($themeSlug);
        if (!empty($meta) && property_exists($meta, 'screenshot')) {
            $screenshot = $meta->screenshot;
            return !empty($screenshot) ? current((array)$screenshot) : [];
        }
        return [];
    }

    public function getIndividualThemeScreenshot(string $themeSlug): array
    {
        $screenshotList = [];
        $screenshotDir  = $this->resolveScreenshotPath($themeSlug);

        if (is_dir($screenshotDir)) {
            $files = \File::allFiles($screenshotDir);
            foreach ($files as $file) {
                $ext = pathinfo($file, PATHINFO_EXTENSION);
                if (in_array($ext, ['jpg', 'png', 'jpeg'])) {
                    $meta = $this->getIndividualScreenshotMeta($themeSlug);
                    if (!empty($meta) && pathinfo($file, PATHINFO_BASENAME) == ($meta->primary ?? '')) {
                        $screenshotList['primary'] = pathinfo($file);
                    } else {
                        $screenshotList['secondary'][] = pathinfo($file);
                    }
                }
            }
        }

        return $screenshotList;
    }

    public function renderPrimaryThemeScreenshot(string $themeSlug): string
    {
        $imageData = $this->getIndividualThemeScreenshot($themeSlug);

        if (empty($imageData)) {
            return '';
        }

        if (array_key_exists('primary', $imageData)) {
            $slug    = $themeSlug;
            $primary = $imageData['primary'];
        } else {
            $defaultTheme = $this->getDefaultThemeData();
            $slug         = property_exists($defaultTheme, 'slug') ? $defaultTheme->slug : 'default';
            $defaultShot  = $this->getIndividualThemeScreenshot($slug);
            if (!array_key_exists('primary', $defaultShot)) {
                return '';
            }
            $primary = $defaultShot['primary'];
        }

        // Serve via the theme.primary.screenshot route — it checks new + legacy locations
        return route('theme.primary.screenshot', $slug);
    }

    public function loadCoreStyle(): array
    {
        $allStyles = [
            'bootstrap.min', 'animate', 'slick', 'nice-select', 'line-awesome.min',
            'jquery.ihavecookies', 'odometer', 'common', 'magnific-popup', 'helpers',
            'toastr', 'loader',
        ];

        $hook = $this->getHeaderHook();
        if (!property_exists($hook, 'loadCoreStyle')) {
            return $allStyles;
        }

        $coreStyles = $hook->loadCoreStyle;
        return array_values(array_filter($allStyles, function ($value) use ($coreStyles) {
            $flag = $coreStyles->$value ?? 'not-found';
            return $flag !== false;
        }));
    }

    public function loadCoreScript(): array
    {
        $allScripts = [
            'jquery-3.6.1.min', 'jquery-migrate-3.4.0.min', 'bootstrap.bundle.min',
            'jquery.lazy.min', 'slick', 'odometer', 'viewport.jquery', 'wow',
            'jquery.nice-select', 'jquery.syotimer.min', 'sweetalert2', 'toastr.min',
            'jquery.nicescroll.min', 'nouislider-8.5.1.min', 'custom-alert-message',
            'main', 'star-rating.min', 'md5',
        ];

        $hook = $this->getFooterHook();
        if (!property_exists($hook, 'loadCoreScript')) {
            return $allScripts;
        }

        $coreScript = $hook->loadCoreScript;
        return array_values(array_filter($allScripts, function ($value) use ($coreScript) {
            $flag = $coreScript->$value ?? 'not-found';
            return $flag !== false;
        }));
    }

    // ── Asset Resolution (NEW) ───────────────────────────────────────────────

    /**
     * Resolve a theme asset URL with fallback chain:
     * 1. public/themes/{active}/{path}
     * 2. public/themes/default/{path}
     * Returns empty string if neither exists.
     */
    public function asset(string $path, ?string $slug = null): string
    {
        $slug ??= $this->activeSlug;

        foreach ([$slug, 'default'] as $candidate) {
            $publicRelative = "themes/{$candidate}/{$path}";
            if (file_exists(public_path($publicRelative))) {
                return global_asset($publicRelative);
            }
        }

        return '';
    }

    // ── Internal ─────────────────────────────────────────────────────────────

    public function loadMeta(string $slug): ?object
    {
        if (empty($slug)) {
            return null;
        }

        return Cache::remember('theme_meta_' . $slug, config('theme.cache_ttl', 3600), function () use ($slug) {
            $path = config('theme.base_path') . '/' . $slug . '/theme.json';
            return file_exists($path) ? json_decode(file_get_contents($path)) : null;
        });
    }

    protected function scanThemeDirectories(bool $includeDefault): array
    {
        $themes = [];
        $base   = config('theme.base_path');

        if (!is_dir($base)) {
            return $themes;
        }

        foreach (glob($base . '/*/theme.json') as $jsonFile) {
            $slug = basename(dirname($jsonFile));
            if (!$includeDefault && $slug === 'default') {
                continue;
            }
            $meta = json_decode(file_get_contents($jsonFile));
            if ($meta && property_exists($meta, 'slug')) {
                $themes[$slug] = $meta;
            }
        }

        return $themes;
    }

    protected function resolveScreenshotPath(string $themeSlug): string
    {
        return config('theme.base_path') . '/' . $themeSlug . '/screenshot';
    }
}
