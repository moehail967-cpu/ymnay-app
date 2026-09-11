# Existing coding conventions

All observations below are VERIFIED in the referenced source; they are not an instruction to copy unsafe legacy behavior.

| Area | Observed pattern / placement | Source |
|---|---|---|
| Backend organization | Landlord/Tenant controller trees; module `Http/Controllers`, `Entities`, `Http/Services` or `Services`; helpers/traits used extensively | `core/app/Http`, `core/Modules/Product`, `core/Modules/ShippingModule` |
| Business logic | Controllers may query directly; Product service delegates to ProductGlobalTrait; checkout splits totals/persistence from gateway routing | `core/Modules/Product/Http/Services/Admin/AdminProductServices.php`, `core/app/Http/Services` |
| Validation | FormRequest `validated()` for products/checkout; `$request->validate`/controller validation elsewhere; custom slug validator | `core/Modules/Product/Http/Controllers/ProductController.php`, `core/app/Http/Requests/CheckoutFormRequest.php` |
| Naming | Singular/PascalCase classes, snake_case tables; mixed camelCase/snake_case method names; retain established misspellings in contracts | `core/app/Models`, `core/app/Helpers/funtions.php`, `core/app/Jobs/TenantMigrateDatabseJob.php` |
| Routes | Prefix/name/context middleware groups; controller methods and invokable plugin closures; catch-all slug routes must follow specific paths | `core/routes`, `core/Modules/Product/Routes/web.php`, `core/app/Http/Services/DynamicRouteManager.php` |
| Responses | Blade + redirects/flash for forms; JSON/AJAX for selected CRUD; no single universal response envelope | ProductController, CheckoutPaymentController, landlord admin controllers |
| Authorization | Guard + middleware/controller checks; menu hiding is presentation, not endpoint protection | `core/app/Http/Kernel.php`, `core/app/Helpers/MenuWithPermission.php` |
| Views | Blade extends/sections/includes/components; selected tenant views resolve through theme helpers; admin theme metadata can override product views | `core/resources/views`, `core/app/Helpers/theme-frontend-helpers.php`, ProductController |
| Styling | Existing Bootstrap/legacy plugin classes, theme-scoped CSS, selected Vue/Tailwind utilities; retain AJAX selector contracts | `core/resources/css/app.css`, `core/resources/js/app.js`, `core/themes` |
| Localization | `__()` and related translation helpers; JSON/PHP catalogs; DB language settings and SetLang override base English locale | `core/resources/lang`, `core/config/app.php`, `core/app/Http/Middleware/SetLang.php` |
| PHP formatting | Existing files generally four spaces, framework-style namespaces/imports, mixed legacy spacing; no global reformat for a local fix | `core/app`, `core/.styleci.yml`; composer scripts do not define a formatter |

Recommendation: match the neighboring feature rather than imposing one new style across heterogeneous legacy code. Prefer source theme/module overrides where they genuinely apply. Do not rename selectors, routes, settings keys, widget types or misspelled request fields during an unrelated cleanup.

Source-of-truth routing: [CODE-MAP](CODE-MAP.md). Builder/theme details: [ARCHITECTURE](ARCHITECTURE.md). Required project rules: `../PROJECT-RULES.md`.
