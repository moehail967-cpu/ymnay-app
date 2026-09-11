# Page Builder Vendor Patch Tracker

Track all places where we work around limitations in `vendor/xgenious/xgpagebuilder`.
When the package is updated, review each item and apply the fix upstream.

---

## 1. Widget `enable()` not respected in listing API

**Package file:** `src/Http/Controllers/WidgetController.php`

**Problem:**
`WidgetController::index()`, `grouped()`, and `search()` call `WidgetRegistry::getAllWidgets()` /
`WidgetRegistry::getWidgetsForApi()` which return every registered widget with no `enable()` check.
This means landlord-only widgets appear in the tenant page builder sidebar, and theme-specific widgets
appear for tenants using a different theme.

**Our workaround:**
`app/Http/Controllers/FilteredWidgetController.php` — wraps the same registry calls but filters
each widget through its `enable()` method before returning.

Registered in `app/Providers/CustomPageBuilderServiceProvider::registerRoutes()` **before** the
package's `api.php` is loaded, so Laravel's first-match routing picks our controller for:
- `GET /api/page-builder/widgets`
- `GET /api/page-builder/widgets/grouped`
- `GET /api/page-builder/widgets/search`

**Fix needed in package:**
Add `enable()` filtering inside `WidgetController::index()`, `grouped()`, and `search()`:

```php
// In WidgetController, add a helper:
private function isEnabled(BaseWidget $widget): bool
{
    return !method_exists($widget, 'enable') || $widget->enable();
}
```

Then filter accordingly in each method before building the response.

Also add a static `WidgetRegistry::unregister(string $type)` method so widgets can be
removed from the registry post-boot (currently impossible).

---

## 2. `WidgetRegistry` has no `unregister()` method

**Package file:** `src/Core/WidgetRegistry.php`

**Problem:**
Once a widget is registered it cannot be removed. This prevents context-based filtering at
registration time (e.g. only register tenant widgets during tenant requests).

**Our workaround:**
`enable()` filtering at the API response layer (see item 1).

**Fix needed in package:**
```php
public static function unregister(string $type): void
{
    unset(self::$widgets[$type]);
}
```

---

## 3. No PSR-4 autoloading for theme widget directories

**Problem:**
Theme widget files in `themes/*/Widgets/*.php` are not on Composer's autoload path, so
their classes are not available until explicitly `require_once`'d.

**Our workaround:**
`app/Providers/ThemeServiceProvider::discoverThemeWidgets()` — scans `themes/*/Widgets/*.php`,
`require_once`s each file, derives the FQCN, and pushes to `xgpagebuilder.custom_widgets` config
before the package reads it.

**Fix needed in package / composer.json:**
Add `themes/` to Composer's `classmap` or add a PSR-4 prefix entry so theme namespaces are
autoloaded without manual `require_once`.

Alternatively, provide a `PageBuilderServiceProvider::discoverWidgets(string $path)` hook that
theme providers can call.
