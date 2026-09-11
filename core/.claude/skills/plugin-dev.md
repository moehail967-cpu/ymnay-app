# Plugin Development Guide (Nazmart)

Use this skill when the user asks to build, scaffold, or work on a Nazmart plugin.

## Quick Reference

- Plugins live in `Modules/` (first-party) or `plugins/` (third-party)
- Every plugin needs `plugin.json` at its root
- Main class extends `App\PluginSystem\PluginBase`
- Scaffold: `php artisan plugin:create plugin-id`
- Full docs: `docs/plugin-developer-guide.html`

---

## Directory Structure

```
Modules/MyPlugin/
├── plugin.json                  # REQUIRED manifest
├── src/
│   └── MyPlugin.php             # REQUIRED main class
├── resources/
│   ├── views/                   # Blade templates
│   │   ├── admin/               # Tenant admin panel views
│   │   └── frontend/            # Storefront views (injected via hooks)
│   └── lang/                    # Translation files
├── database/
│   └── migrations/              # Standard Laravel migrations
└── routes/
    └── web.php                  # Optional additional routes
```

---

## plugin.json — All Fields

```json
{
  "id":                   "my-plugin",        // kebab-case, globally unique
  "name":                 "My Plugin",
  "version":              "1.0.0",            // semver
  "description":          "What it does",
  "type":                 "tenant",           // landlord | tenant | both
  "pricing":              "free",             // free | paid
  "min_platform_version": "2.5.0",
  "main":                 "src/MyPlugin.php",
  "author":               "Nazmart"
}
```

---

## Main Class Skeleton

```php
namespace Modules\MyPlugin\Src;

use App\PluginSystem\PluginBase;

class MyPlugin extends PluginBase
{
    public function id(): string
    {
        return 'my-plugin';
    }

    public function on_activate(): void
    {
        $this->run_migrations();
        $this->update_option('enabled', '1');
    }

    public function on_deactivate(): void
    {
        // Clear caches only — never drop tables here
    }

    public function on_update(string $from_version): void
    {
        $this->run_migrations();
    }

    /**
     * Called for ALL discovered plugins (active or not).
     * ALWAYS call registerSettings() here so the Settings button on the
     * plugin card is visible even before boot() runs.
     */
    public function routes(): void
    {
        $this->registerSettings(); // must be first — guarantees hasDefinitions() is true
        $this->registerRoutes();
    }

    public function boot(): void
    {
        // register_settings() MUST come first — it is pure in-memory and must run even
        // in CLI/landlord context so the Settings button appears on the plugin card.
        $this->registerSettings();

        // Guards CLI and landlord contexts from DB-dependent hooks and menus.
        if (!function_exists('tenant') || !tenant()) return;

        $this->registerMenus();
        $this->registerHooks();
        // Note: route registration moved to routes() — do NOT call registerRoutes() here
    }
}
```

---

## Settings API

Call `register_settings()` inside `boot()`. Platform auto-generates the settings UI.

```php
private function registerSettings(): void
{
    $this->register_settings([
        [
            'key'         => 'enabled',
            'label'       => 'Enable Plugin',
            'type'        => 'toggle',
            'default'     => true,
        ],
        [
            'key'         => 'api_key',
            'label'       => 'API Key',
            'type'        => 'text',
            'required'    => true,
            'description' => 'Get this from your account dashboard',
        ],
        [
            'key'     => 'mode',
            'label'   => 'Mode',
            'type'    => 'select',
            'default' => 'test',
            'options' => ['test' => 'Test', 'live' => 'Live'],
        ],
    ]);
}

// Retrieve anywhere:
$key = $this->get_option('api_key');
```

**Available field types:** `text`, `email`, `url`, `number`, `textarea`, `code`, `select`, `multiselect`, `toggle`, `color`, `image`, `repeater`

---

## Admin Menu Registration

```php
private function registerMenus(): void
{
    $this->add_menu([
        'id'      => 'my-plugin-menu',
        'label'   => __('My Plugin'),
        'icon'    => 'mdi-puzzle',           // MDI icon class
        'route'   => 'tenant.admin.my-plugin.index',
        'order'   => 80,
        'context' => 'tenant',
    ]);

    $this->add_submenu('my-plugin-menu', [
        'id'    => 'my-plugin-settings',
        'label' => __('Settings'),
        'icon'  => 'mdi-cog',
        'route' => 'tenant.admin.my-plugin.settings',
    ]);
}
```

Use MDI icon names: `mdi-chart-bar`, `mdi-tag`, `mdi-bell`, `mdi-cart`, `mdi-package`, `mdi-cog`, `mdi-eye`, `mdi-star`, `mdi-percent`.

---

## Hook System

### Commerce Hooks (most commonly used)

| Hook | Type | Args | Use |
|------|------|------|-----|
| `nazmart:after_order_create` | Action | `$order` | Fire tracking on new order |
| `nazmart:order_completed` | Action | `$order` | Fire purchase conversion |
| `nazmart:payment_success` | Action | `$payment, $order` | Server-side conversion API |
| `nazmart:cart_total` | Filter | `$total` | Modify cart total (discounts) |
| `nazmart:after_add_to_cart` | Action | `$item, $cart` | Track add-to-cart events |
| `nazmart:product_price` | Filter | `$price, $product` | Override displayed price |

### Frontend Layout Hooks

| Hook | Fires at |
|------|---------|
| `nazmart:frontend_head_end` | Just before `</head>` — inject scripts/pixels |
| `nazmart:frontend_body_start` | Just after `<body>` — inject noscript tags |
| `nazmart:frontend_footer` | Just before `</body>` — inject deferred scripts |

### Usage

```php
private function registerHooks(): void
{
    // Inject pixel into <head>
    $this->add_action('nazmart:frontend_head_end', function () {
        $pixel_id = $this->get_option('pixel_id');
        if (!$pixel_id) return;
        echo view($this->plugin_path('resources/views/frontend/pixel-head.blade.php'),
            compact('pixel_id'))->render();
    });

    // Server-side conversion on order complete
    $this->add_action('nazmart:order_completed', function ($order) {
        if (!$this->get_option('server_side_enabled')) return;
        dispatch(new \Modules\MyPlugin\Jobs\SendConversionJob($order));
    });

    // Modify cart total
    $this->add_filter('nazmart:cart_total', function (float $total) {
        $discount = $this->calculateDiscount($total);
        return $total - $discount;
    });
}
```

---

## Route Registration

```php
private function registerRoutes(): void
{
    // Tenant admin routes (standard web routes)
    $this->register_web_routes(function () {
        \Route::middleware(['auth:admin', 'tenantadmin'])
            ->prefix('admin/my-plugin')
            ->name('tenant.admin.my-plugin.')
            ->group(function () {
                \Route::get('/', [MyPluginController::class, 'index'])->name('index');
                \Route::post('/settings', [MyPluginController::class, 'settings'])->name('settings');
            });
    });

    // REST API endpoints (auto-mounted at /api/v1/plugins/my-plugin/)
    $this->register_api_routes(function ($router) {
        $router->get('/status', fn() => response()->json(['ok' => true]));
        $router->post('/event', [MyPluginApiController::class, 'event']);
    });
}
```

---

## Asset Injection

The platform has a full WordPress-style asset manager. All assets registered here are
automatically rendered by `@pluginFrontendStyles` (inside `<head>`) and
`@pluginFrontendScripts` (before `</body>`) — both are already wired into every theme's
master layout. No changes to theme files are ever required.

### File-based assets (via PluginBase helpers)

```php
// In boot():
$this->enqueue_frontend_script(
    'my-plugin-js',
    $this->plugin_url('resources/js/plugin.js'),
    [],    // deps: ['jquery', 'alpine', 'toastr', 'sweetalert2', 'tailwind']
    true   // true = footer (default), false = <head>
);

$this->enqueue_frontend_style(
    'my-plugin-css',
    $this->plugin_url('resources/css/plugin.css')
);

$this->enqueue_admin_style(
    'my-plugin-admin-css',
    $this->plugin_url('resources/css/admin.css')
);

$this->enqueue_admin_script(
    'my-plugin-admin-js',
    $this->plugin_url('resources/js/admin.js'),
    ['jquery'],
    true
);
```

### Inline styles and scripts

For dynamic configuration or small CSS tweaks that depend on plugin settings:

```php
// In boot(), after fetching settings:
$color = $this->get_option('brand_color', '#6366f1');

plugin_enqueue_inline_style(
    'my-plugin-vars',
    ":root { --mp-brand: {$color}; }",
    'frontend'  // 'frontend' or 'admin'
);

plugin_enqueue_inline_script(
    'my-plugin-config',
    "window.MyPluginConfig = " . json_encode(['key' => $this->get_option('api_key')]) . ";",
    'frontend',
    false  // false = in <head>, true = before </body> (default)
);
```

### Global helper functions (use anywhere, not just in plugins)

```php
plugin_enqueue_style('handle', $url, 'frontend');          // CSS file
plugin_enqueue_script('handle', $url, 'frontend');         // JS file
plugin_enqueue_inline_style('handle', $css, 'frontend');   // inline CSS
plugin_enqueue_inline_script('handle', $js, 'frontend');   // inline JS
```

### Dependency resolution

Deps declared with `'deps' => ['jquery']` are sorted automatically — platform handles
(`jquery`, `alpine`, `toastr`, `sweetalert2`, `tailwind`) are always available and never
re-enqueued. First registration wins; duplicate handles are silently ignored.

---

## Migrations

File: `database/migrations/2026_05_10_000001_create_my_plugin_table.php`

```php
public function up(): void
{
    Schema::create('my_plugin_data', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('tenant_id')->index();
        $table->string('key');
        $table->text('value')->nullable();
        $table->timestamps();
        $table->unique(['tenant_id', 'key']);
    });
}
```

Always call `$this->run_migrations()` in `on_activate()` and `on_update()`.

---

## View Rendering (Admin Pages)

Controller pattern:

```php
namespace Modules\MyPlugin\Http\Controllers;

use App\Http\Controllers\Controller;

class MyPluginController extends Controller
{
    public function index()
    {
        return view('my-plugin::admin.index', [
            'data' => MyPluginModel::paginate(20),
        ]);
    }
}
```

Blade view namespace prefix is the plugin's `id` value with `::` separator.

---

## Frontend Injection via Blade Partials

For injecting HTML into the storefront, use action hooks + a simple Blade snippet (not a full template):

```php
$this->add_action('nazmart:frontend_footer', function () {
    if (!$this->get_option('enabled')) return;
    $options = [
        'delay'    => (int) $this->get_option('delay', 5),
        'interval' => (int) $this->get_option('interval', 15),
    ];
    // Encode options as JSON for JS consumption
    echo '<div id="my-plugin-root" data-options="' . e(json_encode($options)) . '"></div>';
    echo '<script src="' . $this->plugin_url('resources/js/frontend.js') . '"></script>';
});
```

---

## Scheduled Tasks

```php
// In boot():
$this->schedule('daily', function () {
    \Modules\MyPlugin\Jobs\DailySync::dispatch();
});

$this->schedule('0 * * * *', function () {  // hourly via cron expression
    \Modules\MyPlugin\Jobs\HourlyRefresh::dispatch();
});
```

---

## Checklist: New Plugin

- [ ] `plugin.json` — id, name, version, type, pricing, min_platform_version, main
- [ ] Main class extends `PluginBase`, implements `id()`
- [ ] `routes()` calls `registerSettings()` FIRST, then `registerRoutes()` — never skip this
- [ ] `boot()` calls `registerSettings()` again (idempotent) before the tenant null-check
- [ ] `boot()` has null-check for tenant context (if type=tenant)
- [ ] `on_activate()` calls `run_migrations()`, sets default options
- [ ] All settings registered via `register_settings()` not hardcoded
- [ ] Admin menus registered via `add_menu()` / `add_submenu()`
- [ ] MDI icons used throughout (no emoji, no Themify)
- [ ] Frontend injection via `nazmart:frontend_head_end` / `nazmart:frontend_footer` **or** `plugin_enqueue_frontend_*` helpers (prefer helpers for simple CSS/JS files)
- [ ] Server-side API calls dispatched via queued Jobs, not inline in hooks
- [ ] Migrations use `unique(['tenant_id', 'key'])` pattern for tenant-scoped data
- [ ] `on_deactivate()` clears caches only, does NOT drop tables
- [ ] Sprint ticket created + SPRINT.md updated at repo root

---

## Common Pitfalls

- **`register_settings()` must be called in `routes()`** — if you only call it in `boot()`, the Settings button on the plugin card will be hidden for inactive plugins because `boot()` never runs for them. `routes()` runs for all discovered plugins regardless of active state.
- **`register_web_routes()` and `register_api_routes()` belong in `routes()`** — calling them in `boot()` causes route registration timing issues; routes may not be available when the router needs to match the incoming request.
- **Never call `tenant()` directly** in `boot()` without null-check — CLI contexts crash
- **Never drop tables in `on_deactivate()`** — data must survive disable/re-enable cycles
- **Filter hooks must return a value** — forgetting `return` passes `null` down the chain
- **Use queued Jobs for HTTP calls** — never make outbound HTTP requests inline in action hooks
- **Use `insertOrIgnore()` for seed data** — `on_activate()` can be called multiple times
- **Plugin settings are tenant-scoped automatically** — `get_option()` returns per-tenant value
