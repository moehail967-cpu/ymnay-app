<?php

namespace Modules\MultiLingual\Src;

use App\PluginSystem\PluginBase;
use App\Models\Language;
use Illuminate\Support\Facades\Route;
use Modules\MultiLingual\Http\Controllers\MultiLingualController;
use Modules\MultiLingual\Services\TranslationService;

class MultiLingualPlugin extends PluginBase
{
    // Maps form field names to DB column names where they differ
    // (product sends 'summery' in the form but stores it in the 'summary' DB column)
    private array $fieldAliases = [
        'product' => ['summery' => 'summary'],
    ];

    // Translatable field definitions per model type
    private array $translatableModels = [
        'product'        => ['name', 'summery', 'description'],
        'category'       => ['name', 'description'],
        'sub_category'   => ['name', 'description'],
        'child_category' => ['name', 'description'],
        'brand'          => ['name', 'description'],
        'blog'           => ['title', 'blog_content', 'excerpt'],
        'page'           => ['title', 'page_content'],
    ];

    public function id(): string
    {
        return 'multi-lingual';
    }

    // ── Boot ──────────────────────────────────────────────────────────────────

    public function boot(): void
    {
        view()->addNamespace('multi-lingual', $this->plugin_path('resources/views'));

        $this->register_settings([
            ['key' => 'enabled',       'label' => 'Enable Translations',  'type' => 'toggle',   'default' => '1'],
            ['key' => 'ai_source',     'label' => 'AI Credentials Source','type' => 'dropdown', 'default' => 'ai-integration'],
            ['key' => 'ai_provider',   'label' => 'AI Provider',          'type' => 'dropdown', 'default' => 'openai'],
            ['key' => 'ai_key',        'label' => 'API Key',              'type' => 'text',     'default' => ''],
            ['key' => 'ai_model',      'label' => 'Model',                'type' => 'text',     'default' => ''],
            ['key' => 'ai_temperature','label' => 'Temperature',          'type' => 'text',     'default' => '0.3'],
        ]);

        $this->add_menu([
            'id'      => 'ml-menu',
            'label'   => __('Multi-Lingual'),
            'icon'    => 'mdi-translate',
            'route'   => 'tenant.admin.multi-lingual.settings',
            'order'   => 91,
            'context' => 'tenant',
        ]);

        $this->registerObservers();
        $this->registerSaveHooks();

        // Defer asset injection to a View composer so it runs AFTER tenancy middleware
        // has connected the tenant DB — otherwise Language::where() hits the central DB.
        // We only set $done=true once a real tenant connection is confirmed; if a view
        // renders before tenancy middleware (e.g. during error handling), we retry next time.
        \Illuminate\Support\Facades\View::composer('*', function () {
            static $done = false;
            if ($done) return;
            try {
                if (!function_exists('tenant') || tenant() === null) return;
            } catch (\Throwable) {
                return;
            }
            $done = true;
            $this->enqueueInlineAssets();
        });

        // Inject frontend language switcher — CSS in <head>, widget in navbar icons row
        $this->add_action('nazmart:frontend_head_end', [$this, 'injectFrontendCss'], 10);
        $this->add_action('nazmart:navbar_icons',      [$this, 'injectLangSwitcher'], 20);
    }

    // ── Routes ────────────────────────────────────────────────────────────────

    public function routes(): void
    {
        view()->addNamespace('multi-lingual', $this->plugin_path('resources/views'));

        $this->register_web_routes(function () {
            Route::middleware([
                    'web',
                    \App\Http\Middleware\Tenant\InitializeTenancyByDomainCustomisedMiddleware::class,
                    \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
                    'auth:admin',
                    'tenant_admin_glvar',
                    'package_expire',
                    'tenantAdminPanelMailVerify',
                    'tenant_status',
                    'set_lang',
                ])
                ->prefix('admin-home/multi-lingual')
                ->name('tenant.admin.multi-lingual.')
                ->group(function () {
                    Route::get('/settings',  [MultiLingualController::class, 'settings'])->name('settings');
                    Route::post('/settings', [MultiLingualController::class, 'saveSettings'])->name('settings.save');
                    Route::get('/translations', [MultiLingualController::class, 'fetchTranslations'])->name('translations');
                    Route::post('/translate',  [MultiLingualController::class, 'translate'])->name('translate');
                });
        });
    }

    // ── Lifecycle ─────────────────────────────────────────────────────────────

    public function on_activate(): void
    {
        $this->run_migrations();
    }

    public function on_update(string $from_version): void
    {
        $this->run_migrations();
    }

    public function on_deactivate(): void {}

    // ── Observers ─────────────────────────────────────────────────────────────

    private function registerObservers(): void
    {
        $modelClasses = [
            'product'        => \Modules\Product\Entities\Product::class,
            'category'       => \Modules\Attributes\Entities\Category::class,
            'sub_category'   => \Modules\Attributes\Entities\SubCategory::class,
            'child_category' => \Modules\Attributes\Entities\ChildCategory::class,
            'brand'          => \Modules\Attributes\Entities\Brand::class,
            'blog'           => \Modules\Blog\Entities\Blog::class,
            'page'           => \App\Models\Page::class,
        ];

        foreach ($modelClasses as $type => $class) {
            if (!class_exists($class)) {
                continue;
            }

            $fields = $this->translatableModels[$type];

            // Use closure listener instead of observer class — avoids container
            // re-resolution (Eloquent stores class name internally and would call
            // app(TranslationObserver::class), which fails for primitive constructor args).
            $aliases = $this->fieldAliases[$type] ?? [];

            $class::retrieved(static function ($model) use ($type, $fields, $aliases) {
                $locale   = app()->getLocale();
                $fallback = config('app.fallback_locale', 'en');

                if ($locale === $fallback) {
                    return;
                }

                $translations = TranslationService::forModel($type, (int) $model->getKey(), $locale);

                foreach ($fields as $field) {
                    if (isset($translations[$field]) && $translations[$field] !== '') {
                        $model->setAttribute($field, $translations[$field]);
                        // Also set the DB column name if this form field has an alias
                        if (isset($aliases[$field])) {
                            $model->setAttribute($aliases[$field], $translations[$field]);
                        }
                    }
                }
            });
        }
    }

    // ── Save Hooks ────────────────────────────────────────────────────────────

    private function registerSaveHooks(): void
    {
        $this->add_action('nazmart:after_product_save', [$this, 'handleProductSaved'], 10, 2);
        $this->add_action('nazmart:after_blog_saved',   [$this, 'handleBlogSaved'],    10, 2);
        $this->add_action('nazmart:after_page_saved',   [$this, 'handlePageSaved'],    10, 2);
        $this->add_action('nazmart:after_category_saved',      [$this, 'handleCategorySaved'],      10, 2);
        $this->add_action('nazmart:after_sub_category_saved',  [$this, 'handleSubCategorySaved'],   10, 2);
        $this->add_action('nazmart:after_child_category_saved',[$this, 'handleChildCategorySaved'], 10, 2);
        $this->add_action('nazmart:after_brand_saved',  [$this, 'handleBrandSaved'],   10, 2);
    }

    public function handleProductSaved($product, string $op): void
    {
        $this->persistTranslations('product', (int) $product->id, ['name', 'summery', 'description']);
    }

    public function handleBlogSaved($blog, string $op): void
    {
        $this->persistTranslations('blog', (int) $blog->id, ['title', 'blog_content', 'excerpt']);
    }

    public function handlePageSaved($page, string $op): void
    {
        $this->persistTranslations('page', (int) $page->id, ['title', 'page_content']);
    }

    public function handleCategorySaved($model, string $op): void
    {
        $this->persistTranslations('category', (int) $model->id, ['name', 'description']);
    }

    public function handleSubCategorySaved($model, string $op): void
    {
        $this->persistTranslations('sub_category', (int) $model->id, ['name', 'description']);
    }

    public function handleChildCategorySaved($model, string $op): void
    {
        $this->persistTranslations('child_category', (int) $model->id, ['name', 'description']);
    }

    public function handleBrandSaved($model, string $op): void
    {
        $this->persistTranslations('brand', (int) $model->id, ['name', 'description']);
    }

    private function persistTranslations(string $type, int $id, array $fields): void
    {
        $locales = $this->activeLocales();
        $fallback = config('app.fallback_locale', 'en');

        foreach ($locales as $locale) {
            // Don't store translations for the default locale
            if ($locale === $fallback) {
                continue;
            }

            $data = [];
            foreach ($fields as $field) {
                $value = request()->input("{$field}__{$locale}");
                if ($value !== null && $value !== '') {
                    $data[$field] = $value;
                }
            }

            if (!empty($data)) {
                TranslationService::saveIfPresent($type, $id, $locale, $data);
            }
        }
    }

    private function activeLocales(): array
    {
        try {
            return Language::where('status', 1)->pluck('slug')->toArray();
        } catch (\Throwable) {
            return [];
        }
    }

    // ── Frontend Switcher ─────────────────────────────────────────────────────

    public function injectFrontendCss(): void
    {
        echo <<<CSS
        <style>
        .ml-navbar-switcher{display:inline-flex;align-items:center;gap:5px;cursor:pointer;position:relative;}
        .ml-navbar-switcher i{font-size:18px;line-height:1;opacity:.85;}
        .ml-navbar-switcher select{appearance:none;-webkit-appearance:none;border:none;outline:none;background:transparent;font-size:13px;font-weight:600;cursor:pointer;color:inherit;padding:0;max-width:80px;}
        .ml-navbar-switcher select option{color:#111;background:#fff;}
        </style>
        CSS;
    }

    public function injectLangSwitcher(): void
    {
        try {
            $langs = Language::where('status', 1)->get();
        } catch (\Throwable) {
            return;
        }

        if ($langs->count() < 2) {
            return;
        }

        $currentLocale = app()->getLocale();
        try {
            $switchBase = route('tenant.frontend.langchange');
        } catch (\Throwable) {
            return;
        }

        $options = '';
        foreach ($langs as $lang) {
            $selected = $currentLocale === $lang->slug ? ' selected' : '';
            $options .= '<option value="' . e($lang->slug) . '"' . $selected . '>' . e($lang->name) . '</option>';
        }

        echo <<<HTML
        <div class="ml-navbar-switcher">
            <i class="mdi mdi-translate"></i>
            <select onchange="window.location.href='{$switchBase}?lang='+this.value" aria-label="Language">
                {$options}
            </select>
        </div>
        HTML;
    }

    // ── Admin Assets ──────────────────────────────────────────────────────────

    private function enqueueInlineAssets(): void
    {
        $cssFile = $this->plugin_path('resources/css/lang-tabs.css');
        $jsFile  = $this->plugin_path('resources/js/lang-tabs.js');

        if (file_exists($cssFile)) {
            app(\App\PluginSystem\AssetManager::class)
                ->enqueueInlineStyle('admin', 'ml-lang-tabs-css', file_get_contents($cssFile));
        }

        if (file_exists($jsFile)) {
            try {
                $locales = Language::where('status', 1)
                    ->get(['slug', 'name'])
                    ->map(fn($l) => ['slug' => $l->slug, 'name' => $l->name])
                    ->values()
                    ->toJson();
            } catch (\Throwable) {
                $locales = '[]';
            }

            $fallback = config('app.fallback_locale', 'en');
            try {
                $translateUrl  = route('tenant.admin.multi-lingual.translate');
                $fetchUrl      = route('tenant.admin.multi-lingual.translations');
            } catch (\Throwable) {
                $translateUrl  = '';
                $fetchUrl      = '';
            }

            $config   = 'window.mlConfig=' . json_encode([
                'locales'      => json_decode($locales, true),
                'fallback'     => $fallback,
                'fields'       => ['name', 'summery', 'description', 'title', 'page_content', 'blog_content', 'excerpt'],
                'translateUrl' => $translateUrl,
                'fetchUrl'     => $fetchUrl,
                // URL segment → model type mapping for edit pages
                'editPatterns' => [
                    ['segment' => 'product/update',   'type' => 'product'],
                    ['segment' => 'blog/edit',         'type' => 'blog'],
                    ['segment' => 'pages/edit',        'type' => 'page'],
                ],
            ]) . ';';

            app(\App\PluginSystem\AssetManager::class)
                ->enqueueInlineScript('admin', 'ml-lang-tabs-js', $config . file_get_contents($jsFile), true);
        }
    }
}
