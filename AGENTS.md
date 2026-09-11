# Ymnay Engineering Operations Guide

This file is the first source to consult before changing Ymnay. It describes the canonical repository layout whose root contains `core/`, `assets/`, `_build/`, and the root `index.php`. The Laravel working directory is `core/`; unless a command explicitly says otherwise, run it there.

Verified against the deployed `develop` repository on 2026-09-04. Facts marked **Needs verification** were not provable from the repository or installed runtime. Do not replace them with assumptions.

## How To Use This Guide

1. Read the relevant row in **Feature Location Map** and **Task Routing Guide**.
2. Confirm whether the request concerns the central/landlord application or an initialized tenant.
3. Inspect the smallest route → controller → service/model → view path named here.
4. Prefer the documented theme, module, plugin, hook, event, or configuration extension point before changing shared code.
5. Match the existing implementation. Do not impose new architectural layers or reorganize unrelated code.
6. After a stable architectural discovery, update this guide under **Keeping This File Current**.

## System Identity

- Product: Ymnay, a branded and customized Nazmart multi-tenant SaaS for creating ecommerce sites/stores.
- Application type: server-rendered Laravel SaaS with a central subscription site and one database per tenant store.
- Backend: PHP `^8.3`, Laravel `12.61.0` from `composer.lock`. The inspected production CLI is PHP `8.4.24`.
- Frontend: Blade is dominant; Bootstrap 5 and legacy jQuery plugins are widely used. Vue 3 is used in selected interactive areas. Tailwind 4 utilities are built without preflight to coexist with Bootstrap.
- Tenancy: `stancl/tenancy` `3.10.0`, database-per-tenant, identified by domain/subdomain.
- Extensibility: Nwidart modules, a repository-specific plugin/hook system, per-tenant themes, and two coexisting Page Builder implementations.
- Authentication: session guards for users/admins and Sanctum for API tokens.
- Data store: MySQL is the configured default. Redis is optional/configured but is not the tenancy bootstrapper.
- Repository state warning: the deployed worktree may contain many unrelated user/server changes. Never reset, clean, overwrite, or include unrelated changes.

## Architecture Overview

These are execution boundaries already present in the project, not a proposal to split the system into new layers:

- **Central/landlord context:** central domain, central users/admins, plans, subscription payments, tenants/domains, global themes/plugins, and central settings.
- **Tenant context:** resolved from the request host, boots a tenant database/cache/filesystem/queue context, then serves the store frontend and tenant admin.
- **Shared application code:** most controllers/models/helpers are reused in both contexts. The active database connection often determines which identically named table is used.
- **Feature packages:** `Modules/` contains most business capabilities. Some modules use Nwidart `module.json`; many also expose the newer `plugin.json` contract.
- **Core plugins:** `plugins/` contains PageBuilder, WidgetBuilder, and MenuBuilder implementations.
- **Presentation:** tenant storefront views resolve through the active theme, then default theme, then core tenant Blade fallbacks.
- **Configuration/data-driven behavior:** many admin settings live in `static_options` rather than `.env` or `config/`.

There is no general repository layer (`app/Repositories/` is absent). Business logic is distributed among controllers, `app/Http/Services`, actions, helpers, module services, and traits. Match the pattern of the feature being changed.

## Project Map

| Path | Purpose / when to inspect |
|---|---|
| `index.php`, `.htaccess`, `web.config` | Root web entry and rewrites; root `index.php` boots `core/`. |
| `assets/` | Web-served legacy/global assets, uploads, tenant media, and generated dynamic styles/scripts. Treat upload/generated subtrees as data. |
| `_build/` | Vite production output. Generated; never hand-edit. |
| `themes` | Root symlink to `core/public/themes`; generated publication surface, not theme source. |
| `core/artisan` | Laravel CLI entry. |
| `core/composer.json`, `core/composer.lock` | PHP runtime, autoload, package versions, Composer lifecycle commands. |
| `core/package.json`, `core/package-lock.json`, `core/vite.config.js` | Frontend dependencies and Vite build. |
| `core/app/Http/Controllers/Landlord/` | Central admin, central frontend, plans, tenants, subscription orders, settings. |
| `core/app/Http/Controllers/Tenant/` | Tenant storefront, customer dashboard, checkout, tenant admin, store orders. |
| `core/app/Http/Services/` | Shared checkout, coupons, dynamic routes, image upload, tax/render helpers. |
| `core/app/Actions/` | Tenant creation/reassignment and payment/SMS actions. |
| `core/app/Models/` | Shared, central, and tenant Eloquent models. Check tenancy traits before assuming connection. |
| `core/app/Jobs/`, `Events/`, `Listeners/`, `Observers/` | Provisioning, mail/file synchronization, stock, commission, and lifecycle side effects. |
| `core/app/Providers/` | Route, tenancy, theme, plugin, Page Builder, event, storage, and application bootstrapping. High-impact. |
| `core/app/PluginSystem/` | New plugin discovery, manifests, hooks, assets, menus, settings, schedules, routes, licensing, updates. |
| `core/app/Helpers/` | Global helpers; several are Composer-autoloaded. `funtions.php` is intentionally misspelled. High fan-out. |
| `core/routes/web.php` | Central storefront/auth/subscription routes and central dynamic page fallback. |
| `core/routes/admin.php` | Central admin routes under `admin-home`. |
| `core/routes/tenant.php` | Tenant storefront/auth/shop routes; includes `tenant_admin.php`. |
| `core/routes/tenant_admin.php` | Tenant admin routes under `admin-home`. |
| `core/routes/api.php` | Central-domain `/api/v1` mobile/API surface. |
| `core/routes/tenant_api.php` | Tenant `/api/tenant/v1` mobile/API surface. |
| `core/config/` | Laravel plus tenancy, theme, modules, permissions, Page Builder, storage, queue, cache, mail. |
| `core/database/migrations/` | Central migrations plus `tenant/` tenant migrations. |
| `core/database/seeders/` | Central and tenant seed entry points, demo/bootstrap data. |
| `core/resources/views/landlord/` | Central frontend/admin Blade views. |
| `core/resources/views/tenant/` | Core tenant admin and storefront fallback views. |
| `core/resources/js/`, `core/resources/css/` | Vite entry points and Vue/Tailwind sources. |
| `core/Modules/` | Business modules: Product, Inventory, Shipping, Tax, Coupon, Campaign, MobileApp, integrations, etc. |
| `core/Modules/YmnayCustom/` | Proven Ymnay-owned business rules: multiple manual wallets and payment review flow. Preferred home for similar local business logic. |
| `core/plugins/` | Core legacy/new plugin packages for PageBuilder, WidgetBuilder, MenuBuilder. |
| `core/themes/{slug}/` | Theme source: `theme.json`, `views/`, `assets/`, `Widgets/`, optional `demo/`. |
| `core/public/` | Published/vendor/public assets. Many files are outputs; identify their source before editing. |
| `core/tests/` | PHPUnit tests. Current coverage is very small. |
| `core/storage/logs/` | Laravel, queue, seeding, file sync, and plugin logs. Never commit. |
| `README.md`, `core/README.md`, `theme-developer-guide.html` | Existing documentation; useful context but some paths/runtime claims are stale. |

Do not crawl `vendor/`, `node_modules/`, `storage/framework/`, uploads/media, backups, logs, compiled output, binary files, or `core/__MACOSX/` without a direct reason.

## Route And Request Topology

- `RouteServiceProvider` maps `routes/web.php` and `routes/api.php` for each `config('tenancy.central_domains')` host.
- `TenancyServiceProvider` maps `routes/tenant.php`; that file requires `routes/tenant_admin.php`.
- Tenant routes use `InitializeTenancyByDomainCustomisedMiddleware` followed by `PreventAccessFromCentralDomains`.
- The web middleware group also contains `TenantConfigMiddleware`, CSRF/session middleware, `Demo`, and `PageBuilderSnapshotMiddleware`.
- Tenant frontend requests commonly add `tenant_glvar`, `set_lang`, `maintenance_mode`, and `package_expire`.
- Tenant admin requests commonly add `auth:admin`, `tenant_admin_glvar`, package/mail/status checks, and `set_lang`.
- Central admin routes use `auth:admin`, `adminglobalVariable`, and `set_lang`.
- Dynamic slugs are catch-all routes and must remain after specific routes. `DynamicRouteManager` dispatches pages, blogs, products, and category slugs.
- Plugin web/API routes may be registered after plugin discovery; module routes are registered by module providers.

## Feature Location Map

| Feature | Routes / entry point | Logic and data | Views, assets, tests |
|---|---|---|---|
| Central user authentication | `routes/web.php` | `LandlordFrontendController`; `User`; `auth:web` | `resources/views/landlord/frontend/user/`; no focused tests |
| Admin authentication, central and tenant | `routes/web.php`, `routes/tenant.php` | shared `Landlord/Admin/Auth/AdminLoginController`; `Admin`; `auth:admin` | `resources/views/landlord/admin/auth/`; tenant route initializes tenancy first |
| Tenant storefront customer auth | `routes/tenant.php` | `TenantFrontendController`; tenant DB `User`; `auth:web` | active theme auth views or `resources/views/tenant/frontend/` fallback |
| Registration and tenant creation | `routes/web.php` register/plan/trial endpoints | `LandlordFrontendController`, `PaymentLogController`, `Actions/Tenant/*`, `TenancyServiceProvider` job pipeline; `User`, `Tenant`, `PaymentLogs`, `Domain` | landlord registration/order views; mail classes in `app/Mail/` |
| Plans/subscriptions | `routes/web.php`, `routes/admin.php`, tenant package routes | `PricePlanController`, `PaymentLogController`, `OrderManageController`, `MyPackageOrderController`; `PricePlan`, `PaymentLogs`, plan feature/theme/gateway/plugin models | `resources/views/landlord/admin/price-plan/`, package order views, tenant package views |
| Tenant management/domains | `routes/admin.php`, tenant custom-domain routes | `TenantManageController`, central/tenant `CustomDomainController`, tenancy jobs/actions; `Tenant`, `domains`, `custom_domains` | landlord tenant/domain views |
| Products | `Modules/Product/Routes/web.php` | `ProductController` → `ProductStoreRequest` → `AdminProductServices` → `ProductGlobalTrait`; `Product` and inventory/category/tag/UOM/meta relations | `Modules/Product/resources/views/`; product JS is often in Blade components; module test dirs are placeholders |
| Product attributes/categories | module routes in `Modules/Attributes` and `Modules/Badge` | module controllers/entities | module views/migrations |
| Inventory/import | `Modules/Inventory/Routes/web.php`; Product import routes | `InventoryController`, product inventory entities, `ProcessProductImport` | module views; import report mail/job |
| Cart/wishlist/compare | tenant `/s/*` routes in `routes/tenant.php` | `TenantFrontendController`, Shoppingcart facade, Product models | active theme `frontend/shop/*`, core fallback; theme JS assets |
| Checkout | tenant `/s/checkout` | `CheckoutPaymentController` → `CheckoutFormRequest` → `ProductCheckoutService::createOrder()` → `CheckoutToPaymentService::checkoutToGateway()` | `themeView('shop.checkout.checkout_page')`; active theme override first, then default/core |
| Store orders | `routes/tenant_admin.php` `order-manage/*`; tenant customer dashboard | `Tenant/Admin/OrderManageController`; `ProductOrder`, `OrderProducts`; order hooks and `OrderCompleted` commission event | `resources/views/tenant/admin/product-order-manage/`, tenant user dashboard order views |
| Subscription payments | central `/order-confirm` and gateway IPNs in `routes/web.php` | `Landlord/Frontend/PaymentLogController`, `Actions/Payment/PaymentGateways`, `PaymentLogs`, `PaymentGatewayCredential` | landlord plan/order/payment views and admin payment logs |
| Tenant store payments | tenant checkout/IPNs | `CheckoutToPaymentService`, `Actions/Payment/Tenant/PaymentGatewayIpn`, `PaymentGateway`, module payment metadata | theme checkout/payment views; admin payment settings |
| Ymnay manual wallets | `Modules/YmnayCustom/Routes/web.php` plus payment metadata hook | `ManualWallet*Controller`, `TenantWalletOrder`, `WalletRepository`, `WalletNotifier`; central `ymnay_manual_wallets`; tenant static option and order/payment snapshots | `Modules/YmnayCustom/resources/views/`; three existing core order-detail adaptations |
| Shipping | `Modules/ShippingModule/Routes/web.php`; checkout AJAX routes | `ShippingMethodController`, `ZoneController`, `ShippingAddressServices`; optional `ShippingPlugin` external API service/observer | module views; `zones`, `zone_regions`, `shipping_methods`, options/addresses |
| Tax/coupons/campaigns | respective module routes | `TaxModule`, `CouponManage`, `Campaign`; checkout coupon/tax services/traits | module views/migrations |
| Tenant admin dashboard | `routes/tenant_admin.php` | `TenantDashboardController`; shared landlord controllers for some settings | `resources/views/tenant/admin/` and module admin views |
| Central admin | `routes/admin.php` | `Landlord/Admin/*` controllers | `resources/views/landlord/admin/` |
| Customer dashboards | central and tenant route groups | landlord or tenant `UserDashboardController`, support controllers | `resources/views/{landlord,tenant}/frontend/user/dashboard/` |
| Pages and dynamic slugs | admin page routes; final dynamic slug routes | `PagesController`, `Page`, `Slug`, `MetaInfo`, `DynamicRouteManager` | landlord dynamic page view; tenant active theme/core fallback |
| Page Builder | admin Page Builder edit/API routes | new Xgenious services/controllers/content models and legacy `plugins/PageBuilder`; see dedicated section | editor assets under published `vendor/page-builder`; theme Widgets and legacy addons |
| Themes | central/tenant theme admin routes | `ThemeManager`, `ThemeServiceProvider`, theme controllers/commands; tenant `theme_slug` | source `themes/{slug}`; published `public/themes/{slug}` is output |
| Plugins/modules | module/plugin admin routes and bootstrap providers | Nwidart module providers plus `app/PluginSystem/*` | `Modules/*`, `plugins/*`, module/plugin-owned views/assets/migrations |
| Media | central and tenant `media-upload/*` routes | shared `MediaUploaderController`, `HandleImageUploadService`, `MediaUploader`, filesystem disks | root `assets/{landlord,tenant}/uploads/media-uploader`; modal/Blade consumers |
| Settings | central/tenant `general-settings`, payment settings, module settings | landlord `GeneralSettingsController` and `PaymentSettingsController`, tenant `OtherSettingsController`, static option helpers | corresponding landlord/tenant settings views |
| Localization/RTL | language admin routes and `set_lang` | `LanguagesController`, `Language`, `LanguageHelper`, global language facade | `resources/lang/*.json`, PHP language files, RTL styles from layouts/theme metadata |
| REST/mobile API | `routes/api.php`, `routes/tenant_api.php` | mainly `Modules/MobileApp/Http/Controllers`; Sanctum user endpoints | JSON responses; no API test coverage found |
| Blog/newsletter/support/refund/digital product/POS | each module's `Routes/`, controllers, entities, migrations | module-local logic plus shared user dashboard/controllers where referenced | module-local views; tests are placeholders |

## Core User Flows

### Central registration and subscription

`routes/web.php` → `LandlordFrontendController` registration/login/plan methods → central `User` → plan order view → `Landlord/Frontend/PaymentLogController::order_payment_form()` → central `PaymentLogs` pending row → manual approval or gateway IPN → `Actions/Payment/PaymentGateways` / tenant creation helpers → `Tenant` creation.

Tables: central `users`, `price_plans`, `plan_features`, `plan_themes`, `plan_payment_gateways`, `plan_plugins`, `payment_logs`, then `tenants` and `domains`. Views live under landlord frontend/admin plan and package-order directories.

### Tenant provisioning

Creating `Tenant` fires Stancl `TenantCreated` → synchronous job pipeline in `TenancyServiceProvider`:

`CreateDatabaseWithFallback` → `TenantMigrateDatabseJob` → `TenantCacheClearJob` → `TenantDomainCreateJob` → `TenantInformationUpdateJob` → `TenantSeedDatabaseJob` → `TenantFileSycnForNewTenant` → `NewShopCreatedEmailNotificationJob`.

The pipeline explicitly uses `shouldBeQueued(false)`. File synchronization work may later use `tenant_file_sync`. Provisioning can also use cPanel automation fallback. Failures are recorded in central `tenant_exceptions` by older helper paths.

### Product creation/update

`Modules/Product/Routes/web.php` → `ProductController::{create,store,edit,update}` → `ProductStoreRequest` plus dynamic slug validation → `AdminProductServices` → `ProductGlobalTrait` → product, slug/meta, categories, gallery, tags/UOM/delivery, inventory, variant details, and specifications. Start debugging persistence inside `ProductGlobalTrait`, not only the controller.

### Store checkout and order review

Tenant `/s/checkout` GET → `CheckoutPaymentController::checkout_page()` → `themeView()` and `nazmart:render_checkout_form` filter. POST → `CheckoutFormRequest` → `ProductCheckoutService` user/address/totals/order creation → `nazmart:before_order_create` → `ProductOrder` + `OrderProducts` → `nazmart:after_order_create` → `CheckoutToPaymentService` stock/campaign updates and gateway dispatch → IPN or pending manual result. Tenant admin `order-manage/change-status` updates order/payment status, invokes order hooks, mail, stock reversal, and commission event behavior.

### Ymnay manual wallet, central plan purchase

Admin catalog CRUD → central `ymnay_manual_wallets` → plan checkout selects `ymnay_manual_wallet` → `ManualWalletPaymentController::landlordCheckout()` stores a snapshot and proof in `PaymentLogs.custom_fields`/`attachments` → pending review → `LandlordWalletReviewController` approve/reject → existing subscription approval/provisioning flow and notifications.

### Ymnay manual wallet, tenant store purchase

Central wallet catalog → tenant admin saves enabled wallet plus account/recipient/instructions into tenant `static_options` → checkout filter renders enabled wallets → `TenantWalletOrder::capture()` validates image, snapshots wallet details into `product_orders.payment_meta`, and leaves order pending → tenant admin review controller approves/rejects → email/SMS plus site response/status views.

### Page render

Specific route or dynamic slug → `DynamicRouteManager` / page controller → `Page`. If `use_page_builder`, resolve bound `PageBuilderRenderService` (local custom implementation) using `page_builder_content`/widgets; otherwise legacy `page_builder` rows or normal `page_content`/shortcodes → landlord Blade or tenant `themeView()` resolution.

### Theme render

Tenant domain middleware initializes tenant → `TenancyInitialized` → `ThemeServiceProvider` calls `ThemeManager::activate(tenant.theme_slug)` → `theme::` paths ordered active theme, default theme, core tenant views → theme metadata injects CSS/RTL/JS/navbar/breadcrumb/footer hooks.

### API request

Central host `/api/v1/*` uses `routes/api.php`. Tenant host `/api/tenant/v1/*` uses `routes/tenant_api.php` with tenant initialization and mobile-app permission middleware. Authentication uses Sanctum. API controllers are mainly in `Modules/MobileApp/Http/Controllers`.

## Database Map

### Central/landlord database

| Tables | Role and important relations |
|---|---|
| `users` | Ymnay account owners; `users.id` → many `tenants.user_id` and `payment_logs.user_id`. |
| `admins`, Spatie permission tables | Central admins/roles/permissions. Equivalent tables also exist in tenant databases. |
| `tenants` | String primary key, normally store/subdomain id; owns domains and points to owner, theme, renewal/payment state. |
| `domains` | Unique host → `tenant_id`; enforced FK to `tenants.id` with cascade. This is Stancl's primary resolver source. |
| `custom_domains`, `tenant_unique_keys`, `tenant_exceptions` | Custom-domain mapping/sync identity and provisioning diagnostics. |
| `price_plans` | Subscription plans and hard limits. |
| `plan_features`, `plan_themes`, `plan_payment_gateways`, `plan_plugins` | Plan entitlements keyed logically by `plan_id`; `plan_plugins` is unique on plan/plugin. Several older tables do not declare DB FKs, so preserve application-level integrity. |
| `payment_logs` | Central subscription purchase/renewal ledger; relates to user, tenant, and plan. `PaymentLogs` explicitly uses `CentralConnection`, including when called during tenant context. |
| `themes`, `theme_marketplace`, plugin option/license/install/hook/tenant-override tables | Theme/plugin catalog, lifecycle, settings, and per-tenant overrides. |
| `pages`, `page_builders`, `page_builder_versions`, `slugs`, `meta_infos` | Central CMS and legacy/new Page Builder history. |
| `static_options`, `static_option_centrals`, `static_option_twos` | Settings/key-value data. Normal helpers cache by option name. |
| `media_uploaders`, support/form/menu/language/blog/newsletter tables | Central content and administration. |
| `ymnay_manual_wallets` | Global wallet types, descriptions, logos, status, display order. Tenant account credentials are not stored here. |

### Tenant database

| Tables | Role and important relations |
|---|---|
| `admins`, `users`, permission pivots | Store administrators and storefront customers. Same model classes often resolve to tenant DB after tenancy bootstrap. |
| `products` | Core physical product row; soft deletes. |
| `product_inventories` | One per product, unique `product_id` and SKU; FK cascades from product. |
| `product_inventory_details` | Variant rows linked to product and inventory; product FK cascades. Attribute pivot tracks variant attributes. |
| category/tag/UOM/gallery/delivery/specification/meta/slug tables | Product classification and presentation relationships. |
| `product_orders` | Store order header, customer/address, totals, gateway/status, serialized order and payment metadata. |
| `order_products` | Order lines; `order_id` FK cascades from `product_orders`. Product/variant ids are not consistently FK constrained. |
| `payment_gateways` | Store gateway definitions/credentials/status; plan entitlement filters visible gateways. |
| `pages`, `page_builders`, `page_builder_content`, `page_builder_widgets`, `page_editing_sessions` | Tenant CMS; new content has FK to page and widget rows are unique by page/widget id. |
| `static_options`, `languages`, `media_uploaders`, `menus`, `widgets` | Tenant settings, localization, media, navigation, legacy widgets. |
| `zones`, `zone_regions`, `shipping_methods`, `shipping_method_options`, address tables | Shipping configuration and checkout addressing. |
| country/state/city and tax tables | Geographic and tax calculation inputs. |
| campaign/coupon/inventory history tables | Pricing campaigns, coupon usage, stock adjustments/history/notifications. |
| digital product, refund, support, blog, newsletter, analytics, SMS tables | Module-owned tenant features. |

Tenant module migrations are listed explicitly in `config/tenancy.php`; `database/migrations/tenant` runs last because many files alter module-created tables. Do not move that ordering casually.

## Multi-Tenancy

- Library: `stancl/tenancy` 3.10.0.
- Tenant model: `App\Models\Tenant` with `HasDatabase` and `HasDomains`.
- Domain model/source: Stancl `Domain`; `domains.domain` uniquely maps to `tenant_id`.
- Identification: `InitializeTenancyByDomainCustomisedMiddleware` normalizes `www`, bypasses configured central domains, resolves the full host, and returns tenant-aware 404/redirect behavior.
- Bootstrappers: database, cache, filesystem, and queue. Redis tenancy bootstrap is disabled.
- Central connection: `config('tenancy.database.central_connection')`, sourced from `DB_CONNECTION`.
- Tenant database name: `TENANT_DATABASE_PREFIX` + tenant id; default prefix in code is `nazmart_tenant_`.
- Storage: local/public roots and `storage_path()` are tenant-suffixed (`tenant{tenant_id}`); `asset()` is tenant-aware. Use `global_asset()` for shared assets.
- Routes/resources: central route files are restricted to central hosts; tenant web/API files initialize tenant before business code. Active theme and most unqualified models then use tenant state.
- Central models used inside tenant context must carry `CentralConnection` or explicitly use the central connection. `PaymentLogs`, `StaticOptionCentral`, and custom-domain sync models do; verify every new cross-boundary query.
- Always end manual tenancy initialization with `tenancy()->end()`, ideally in `finally`.

Hard boundary rules:

- A tenant storefront/admin bug should not change tenant resolver, central subscription logic, or central tables unless evidence crosses that boundary.
- A central plan/provisioning task should not query tenant product/order data without explicitly initializing exactly one tenant.
- Never run a normal central migration expecting it to update tenant databases. Never run tenant migrations against only the central DB.
- Never infer the tenant from a request field when the domain-initialized tenant is available.
- For queue jobs, pass stable tenant identity and initialize/restore context deliberately; queue tenancy bootstrap is active but job behavior still needs verification.

## Themes

- Source directory: `core/themes/{slug}`.
- Required metadata is in `theme.json`; typical subtrees are `views/`, `assets/`, `Widgets/`, `demo/`, and `screenshot.png`.
- Selection source: central `tenants.theme_slug`.
- Activation source: `ThemeServiceProvider` listens to `TenancyInitialized` and activates the selected slug.
- View precedence for `theme::`: selected theme → `themes/default/views` → `resources/views/tenant`.
- Stable per-theme namespaces: `theme-{slug}::` point to each theme's `views/`.
- Module override convention: `themes/{slug}/views/modules/{module-name}/` is prepended to that module's view namespace.
- Theme widgets: `themes/{slug}/Widgets/*.php`, namespace `Themes\{StudlySlug}\Widgets`, discovered by `ThemeServiceProvider` and added to Xgenious Page Builder.
- `theme.json` header/footer hooks define theme CSS, RTL CSS, JS, and navbar/breadcrumb/footer view choices.
- Theme assets publish/symlink from source `themes/{slug}/assets` to `public/themes/{slug}`. The repository-root `themes` symlink exposes those public assets from the root document layout.
- Auto-setup/self-healing may create asset symlinks and writable folders on the first web request after a theme-count change. Marker: `storage/app/.nazmart-themes-published`.

## Theme Modification Rules

- Change storefront markup/styles/scripts in the active theme source first: `themes/{slug}/views` and `themes/{slug}/assets`.
- Check whether the requested page is overridden by the active theme before editing `resources/views/tenant`. For example, VelvetLux overrides checkout at `themes/velvetlux/views/frontend/shop/checkout/checkout_page.blade.php`.
- Put module-specific visual overrides under `themes/{slug}/views/modules/{module-name}` when that namespace is supported.
- Put reusable new Page Builder widgets in the theme's `Widgets/` and matching theme view/assets.
- Use `theme.json` for asset/layout metadata; do not hard-code active-theme paths into shared controllers.
- Do not edit root `themes`, `core/public/themes`, or copied/symlinked published files. They are outputs.
- Keep RTL parity: add/adjust the `rtl_style` asset or direction-aware CSS when the corresponding LTR UI changes.
- If a UI must work across every theme and no theme hook exists, a shared fallback/core change may be necessary; prove it against at least the default and currently active theme.

## Page Builder Architecture

Two implementations coexist. Determine the page's flags/data before touching either.

### New Xgenious builder

- Package: `xgenious/xgpagebuilder` 1.7.1; package auto-discovery is disabled and local providers register it.
- Editor/controller: `CustomPageBuilderController` and routes registered by `AppServiceProvider` for central and tenant admin contexts.
- API routes: `CustomPageBuilderServiceProvider`; widget listing uses `FilteredWidgetController` so `enable()` and active theme/context are respected.
- Renderer: vendor `PageBuilderRenderService` is bound to `App\Services\CustomPageBuilderRenderService`, which fixes percentage column widths.
- Data: `Page.use_page_builder`; `page_builder_content.content` contains layout JSON; `page_builder_widgets` stores normalized widget settings/state/cache/analytics.
- Version snapshots: `PageBuilderSnapshotMiddleware` snapshots save endpoints and throttles to one snapshot per page per five minutes; `PageBuilderVersionController` manages history.
- Widget registry: built-in config in `config/xgpagebuilder.php` plus auto-discovered theme widgets.
- Assets: package route `/vendor/page-builder/{path}` and published package assets. Do not edit published vendor output.

### Legacy builder

- Registry: `plugins/PageBuilder/PageBuilderSetup.php`, with landlord/tenant/theme addon namespaces under `plugins/PageBuilder/Addons/`.
- Data: `page_builders` rows keyed by page/type/location/order with JSON settings and namespace; page flag `page_builder` selects the legacy rendering branch.
- Rendering fallback: `resources/views/tenant/frontend/partials/pages-portion/dynamic-page-builder-part.blade.php` and related plugin addons.

### Builder rules

- `Page.use_page_builder`/`page_builder_content` means new builder. `Page.page_builder`/`page_builders` means legacy builder. Do not write to both for one fix unless implementing an explicit migration/compatibility path.
- For a new theme widget, follow an existing widget in that same theme: extend `BaseWidget`, define fields/render/enable, use the stable theme namespace, and keep assets in the theme.
- For a legacy section, start in `PageBuilderSetup.php` and the exact addon namespace selected for that theme/context.
- Use `resources/views/tenant/frontend/partials/page-builder-content.blade.php` to understand new → legacy → normal content precedence.
- **Needs verification:** only a central migration for `page_builder_versions` was found, while snapshot middleware also runs in tenant requests. Verify the table exists/works in a tenant DB before modifying tenant version history.

## Plugins / Modules / Extensions

### Nwidart modules

- Framework: `nwidart/laravel-modules` 8.6.0.
- Location: `Modules/{Name}` with `module.json`, provider(s), `Routes/`, `Database/Migrations`, entities, controllers/services, resources, and usually placeholder tests.
- Global enabled state: `modules_statuses.json`.
- View namespace normally matches the module alias; published fallback convention is `resources/views/modules/{lower-name}`.
- Tenant module migration order is explicit in `config/tenancy.php`, not automatically inferred from every installed module.
- Payment modules declare `nazmartMetaData.paymentGateway` in `module.json`; `ModuleMetaData`, payment render helpers, and dynamic charge method names consume it.

### Repository plugin system

- Engine: `app/PluginSystem/*`, booted by `PluginServiceProvider`.
- Discovery: `plugin.json` files under both `Modules/*` and `plugins/*`.
- Required manifest fields: kebab-case `id`, `name`, semver `version`, `type` (`landlord|tenant|both`), `pricing`, `min_platform_version`, and `main`.
- Main class extends `PluginBase`, implements `id()` and `boot()`, and can define `routes()`/lifecycle methods.
- Supported extension APIs: actions/filters, admin/frontend assets, menus, settings, shortcodes, scheduled callbacks, web/API routes, migrations, licensing/update metadata, and export-owned tables.
- APIs mount under `/api/v1/plugins/{plugin-id}/`; assets use `/plugins/{plugin-id}/assets/{path}`.
- Activation sources: global `modules_statuses.json`, then tenant `plugin_tenant_overrides`, with central `plan_plugins` entitlement filtering.
- Production manifest cache is 60 seconds (`plugin_manager.manifests`); plan plugin ids cache for 300 seconds.
- Plugin failures are logged to the plugin channel; hook callbacks are isolated by `HookEngine` so a plugin hook exception should not crash the whole app.
- Scaffold command: `php artisan plugin:create vendor-plugin-name` creates under `Modules/`; then review its generated contract and activation status.

### Preferred extension strategy

1. Ymnay-specific business rule or integration spanning central/tenant behavior: extend `Modules/YmnayCustom` if cohesive, otherwise scaffold a separate project-owned plugin/module.
2. Storefront-only visual behavior: active theme source or theme widget/module override.
3. Cross-feature reaction: existing `add_action`/`add_filter`, Laravel event/listener, observer, or service abstraction.
4. Configuration-only behavior: config/static option/plugin setting.
5. Shared core patch only when no real extension point covers the behavior; keep it minimal and document why.

Do not invent hooks or a plugin boundary that the repository does not support. A core change is valid when it is the only correct route, but first search for the exact hook/event/filter used by neighboring features.

## Core Protection Rules

### SAFE TO MODIFY

- A project-owned module/plugin such as `Modules/YmnayCustom`, within its documented context.
- Source files of the specifically active/target theme under `themes/{slug}`.
- Feature-local tests, language JSON/PHP files, module migrations, and module views/assets.
- Configuration when the requested behavior is actually configuration-driven and deployment implications are understood.

### PREFER EXTENSION

- Checkout/order/payment additions: existing payment metadata plus `nazmart:*` hooks, payment module contracts, or a project-owned module.
- New admin/menu/settings/UI capability: plugin/module registration, menu registry, plugin settings, or module routes/views.
- Storefront sections/widgets: theme widgets or legacy addon registry according to page builder type.
- Cross-cutting side effects: events/listeners/jobs/observers or plugin hooks.
- Shared appearance: theme override before core fallback Blade.

### CORE / HIGH RISK

- `TenancyServiceProvider`, tenant initializer middleware, `config/tenancy.php`, `Tenant`, provisioning jobs.
- `AppServiceProvider`, global providers, `Kernel`, shared middleware, autoloaded helpers.
- Central subscription payment/provisioning and tenant checkout/stock logic.
- `ThemeManager`, `ThemeServiceProvider`, Page Builder providers/renderers/snapshot middleware.
- `DynamicRouteManager`, auth guards, permissions/plan-feature middleware, shared base models.
- Core fallback views used by many themes/contexts.

Require a narrow root-cause argument and targeted central/tenant regression check for these files.

### GENERATED / NEVER EDIT

- `vendor/`, `node_modules/`, `_build/`, compiled/cached assets, `storage/framework/`, logs.
- `core/public/themes/*` and root `themes` publication links/copies.
- uploads/media/backups and tenant/user-generated data.
- `core/__MACOSX/` metadata/duplicate package tree.
- Composer/npm lockfiles unless dependency changes are explicitly requested.

## Frontend Architecture

- Blade is the primary UI technology. Landlord and tenant admin templates are separate trees; storefront tenant rendering is theme-aware.
- Bootstrap 5.1.3 is the main layout/component framework; `Paginator::useBootstrap()` is configured.
- jQuery and legacy plugins/scripts are common in Blade layouts/theme assets. Check existing initialization and duplicate inclusion before adding scripts.
- Vue 3 mounts from `resources/js/app.js` to `#app`; current shared components include header/cart and Vue files under `resources/js/vue`. POS also contributes Vue sources.
- Tailwind 4 scans `resources/js/vue/**/*.vue` and `Modules/Pos/vue/**/*.vue`; preflight is intentionally disabled to avoid Bootstrap conflicts.
- Vite inputs: `resources/css/app.css`, `resources/js/app.js`; production output is repository-root `_build/`.
- `vite.config.js` has a development host fixed to `nazmart.test`; adjust only for an explicit local environment task.
- Landlord UI changes start in `resources/views/landlord` and matching `public/assets/landlord` or source assets.
- Tenant admin UI changes start in `resources/views/tenant/admin` or the feature module's views.
- Tenant storefront changes start in the active theme; use core tenant views only as fallback/shared behavior.
- Keep responsive and RTL behavior aligned with the neighboring template/theme. Browser verification is required for interaction/layout changes.

## Localization

- Default config locale/fallback: `en`; runtime language is selected by `SetLang` from the default `languages` row or session.
- Translation sources: JSON files such as `resources/lang/ar.json`, `default.json`, locale JSON files, and Laravel PHP files under `resources/lang/en/`.
- Admin management: central and tenant language routes use `Landlord/Admin/LanguagesController`; language rows hold name, slug, direction, status, default.
- Direction helpers/facade: `LanguageHelper`, `GlobalLanguage`, `get_user_lang_direction()`, `get_lang_direction()`.
- RTL CSS is loaded by landlord/tenant layouts and theme `rtl_style` metadata.
- Add user-visible text through `__()` and the appropriate locale source. Do not hard-code translatable UI strings.
- Keep Arabic and default source keys synchronized using the existing language-management/export workflow. Avoid editing backup files such as `ar.json.backup_*` as a source of truth.
- Clear the `lang_key` cache when changing the default language outside the normal controller workflow.

## Authentication, Authorization, And Validation

- Session guards: `web` → `App\Models\User`; `admin` → `App\Models\Admin`; `tenant_user` is configured but see **Known Gotchas**.
- Tenant customers currently use `auth:web` against tenant DB after tenancy initialization. Tenant admins use `auth:admin` against tenant `admins`.
- API auth uses Sanctum (`auth:sanctum`) and `HasApiTokens` on user/admin models.
- Authorization uses Spatie Permission 6.25.0 on `Admin`, controller `permission:*` middleware, and a Gate-before rule granting Super Admin all abilities.
- Plan entitlements are separate from role permissions: `TenantCheckPermission`, `TenantFeaturePermission`, page/storage/product limits, payment-gateway lists, and plugin/theme assignments.
- Do not rename tenant routes casually: feature middleware derives entitlements from route-name/URL positions.
- Validation is mixed: Form Requests for checkout/products/module operations, controller `$request->validate()`, and module-provided custom rules. Follow the feature's current style and keep validation before side effects.

## Configuration

| Config | Source of behavior |
|---|---|
| `config/app.php` | Providers, locale, application defaults. Theme/plugin/Page Builder providers are manually registered. |
| `config/auth.php`, `sanctum.php`, `permission.php` | Guards, API auth, roles/permissions. |
| `config/tenancy.php` | Central domains, tenant model/resolver bootstrap, DB naming, storage/cache/queue tenancy, tenant migration order. |
| `config/theme.php` | Theme source/stub paths and metadata cache. |
| `config/xgpagebuilder.php` | New Page Builder routes, middleware, widgets, cache/render behavior. |
| `config/modules.php`, `modules_statuses.json` | Nwidart discovery paths and global module/plugin state. |
| `config/database.php` | MySQL/SQLite/Redis connections; MySQL is default fallback. |
| `config/filesystems.php` | Local/public/root, landlord/tenant media, S3/Wasabi/R2 disks. |
| `config/cache.php`, `session.php`, `queue.php` | Defaults come from environment; repository fallbacks are file/file/sync. |
| `config/mail.php`, `services.php`, `broadcasting.php` | SMTP/mail services, OAuth/third-party service config, broadcast drivers. |
| `config/cart.php` | Shoppingcart storage/behavior. |
| `config/logging.php`, `logviewer.php`, `telescope.php` | Application diagnostics. Telescope is registered only for local recognized hosts. |

Many editable settings are not config files. `get_static_option()` reads the current connection's `static_options` with a 24-hour cache; `get_static_option_central()` reads `static_option_centrals` with a short cache. Use the existing update helpers so caches are forgotten.

Never write secret values to this file, source control, logs, or test output. Refer only to environment variable names.

## Environment

- PHP: `>=8.3` according to Composer. Required extensions declared directly: DOM, GD, Intl, JSON, ZIP. MySQL use also requires PDO MySQL. Common package needs may add cURL, OpenSSL, Mbstring, Fileinfo; **Needs verification** against the target server image.
- Composer: lock-compatible Composer 2. The inspected server has Composer 2.10.2.
- Node: Vite 6.4.1 declares Node `^18.0.0 || ^20.0.0 || >=22.0.0`. Exact team-standard Node version is **Needs verification**. Node/npm are not installed in the inspected production shell.
- Package manager: npm; `package-lock.json` exists and no Yarn/pnpm lock was found.
- Database: MySQL is the configured default; exact supported MySQL/MariaDB version is **Needs verification**.
- Redis: optional for cache/queue; current runtime driver values are deployment data and must be checked without recording secrets.
- Writable paths/symlinks: `storage/`, `bootstrap/cache`, root upload/dynamic asset directories, `public/storage`, theme publication links.
- Queue and scheduler: deployment must run Laravel scheduler; repository scheduling currently invokes one-off workers for the default connection and the `tenant_file_sync` connection.
- `.env.example` was not found in the canonical repository. Environment provisioning documentation is therefore **Needs verification**; never reconstruct `.env` from production output.

Important environment variable names only:

- Application: `APP_ENV`, `APP_DEBUG`, `APP_KEY`, `APP_URL`, `APP_TIMEZONE`, `CENTRAL_DOMAIN`.
- Database/tenancy: `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `TENANT_DATABASE_PREFIX`.
- Cache/session/queue: `CACHE_DRIVER`, `SESSION_DRIVER`, `SESSION_DOMAIN`, `QUEUE_CONNECTION`, Redis/SQS variables.
- Mail: `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION`, `MAIL_FROM_*`.
- Storage: `FILESYSTEM_DRIVER`, `FILESYSTEM_CLOUD`, `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET`, `AWS_URL`, `AWS_ENDPOINT`.
- OAuth/security/realtime: `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI`, `RECAPTCHA_SITE_KEY`, `RECAPTCHA_SECRET_KEY`, Pusher/Ably variables.
- Extensions: `PLUGIN_LOG_HOOKS`, `PAGE_BUILDER_*`, `TELESCOPE_*`, `ANALYTICS_ENABLED`, Xgenious update/license variable names.

## Dependencies

Only architecture-significant packages are listed:

| Dependency | Role | Ownership/use |
|---|---|---|
| Laravel 12.61 | HTTP, console, Eloquent, queue, cache, mail | Third-party framework; application uses legacy-style bootstrap/kernel structure. |
| `stancl/tenancy` 3.10 | Domain resolution, tenant DB/cache/fs/queue context | Third-party; customized initializer and provisioning provider/jobs. |
| `nwidart/laravel-modules` 8.6 | Business module organization/lifecycle | Third-party, heavily used by `Modules/`. |
| `xgenious/xgpagebuilder` 1.7.1 | New visual Page Builder | Third-party with local providers/controller/render-service adaptations. |
| `xgenious/paymentgateway` 5.0.5 | Multi-provider charging/IPN support | Third-party with local credential/render/actions and module metadata. |
| Spatie Permission 6.25 | Admin roles and permissions | Third-party, central and tenant permission tables. |
| Laravel Sanctum 4.3 | API tokens | Third-party; mobile/API routes. |
| Shoppingcart 7.0.1 | Cart state | Third-party; tenant shop/checkout. |
| Intervention Image 2.7 | Image processing | Third-party; media upload. |
| Flysystem AWS S3 3.34 | S3-compatible storage | Third-party; S3/Wasabi/R2 paths. |
| Laravel Socialite 5.30 | Social authentication | Third-party; Google service/module. |
| Twilio SDK 7.16 and `SmsGateway` module | SMS/OTP/notifications | Third-party SDK plus project module/config stored via settings. |
| DomPDF/invoice packages | Order/package invoice generation | Third-party; landlord/tenant order controllers/views. |
| Yajra DataTables | Admin table rendering | Third-party; controllers/views. |
| Vue 3, Vite 6, Bootstrap 5, Tailwind 4 | Selected interactive frontend and build | Third-party; Blade remains primary. |

## External Integrations

- Payments: PayPal, Stripe, Razorpay, Mollie, Paystack, Paytm, PayFast, Flutterwave, Midtrans, Cashfree, Instamojo, MercadoPago, Square, CinetPay, KineticPay, PayTabs, Billplz, ZitoPay, ToyyibPay, Iyzico, AWDpay, SSLCommerz, module gateways, manual payment, and Ymnay wallets. Entry points are `PaymentSettingsController`, `PaymentGatewayCredential`, landlord/tenant payment controllers/actions, module metadata, and IPN routes. Credentials are generally admin/static-option/payment-gateway data, not safe to document.
- Email: Laravel Mail via `config/mail.php`; order, provisioning, support, import, stock, and wallet mail classes/actions.
- SMS/OTP: `Modules/SmsGateway`, `app/Actions/Sms`, and wallet notifier; provider settings are stored through the module/admin settings.
- Storage/CDN: local media, S3, Wasabi, Cloudflare R2 via filesystem config, `CloudStorage` module, static options, and `Storage::renderUrl` macro.
- Social login: `Modules/SocialAuth/Services/GoogleAuthService`, Socialite, `GOOGLE_*` variable names.
- Analytics/tracking: `Modules/SiteAnalytics`, `Modules/Integrations` injection points, analytics config, tracking/page view tables.
- Webhooks/WooCommerce: `Modules/WebHook` events/listeners/logs and `Modules/WooCommerce` service/credential middleware.
- Shipping API: `Modules/ShippingPlugin` authorization/service/observer and shipping API order status table.
- Hosting/domain automation: `Modules/CpanelAutomation`, provisioning fallback jobs, `DomainReseller`, custom domain controllers. Treat all credential-bearing static options as secrets.
- Realtime/security: Pusher/Ably broadcasting and reCAPTCHA via config/env names.

For any integration change, trace the admin setting → stored option/credentials → renderer/service → callback/IPN/webhook → status update. Do not add a gateway only to the checkout view.

## Queues, Events, Jobs, And Scheduler

- Queue default: `QUEUE_CONNECTION`, repository fallback `sync`; database jobs and failed-job tables exist.
- Dedicated connection: `tenant_file_sync` (database-backed, queue name `default`) for new-tenant files.
- Provisioning jobs: database creation/deletion, tenant migration/seeding/domain/info/cache, credential/mail, and file sync.
- Other jobs: product import, order mail, account removal mail, package expiry mail, cloud file sync.
- Event mappings in `EventServiceProvider`: registration verification, support mail, tenant registration listeners, and commission creation on `OrderCompleted`.
- Observers: central user/tenant registration, product order, stock notification, product price. Check hooks/events before duplicating side effects in controllers.
- Scheduler source: `app/Console/Kernel.php`. It runs plugin schedules, daily theme update checks/package expiry/account removal/auto-renew, weekly campaign suggestions, and one-off queue workers every minute; logs queue/file sync output separately.
- Production must invoke `php artisan schedule:run` every minute or an equivalent scheduler process. Exact host cron is **Needs verification**.

## Common Commands

Run from `core/` unless noted.

```bash
# PHP install (post-install also runs storage:link and theme:publish --all)
composer install

# Frontend install/build (requires compatible Node; production shell currently lacks Node)
npm ci
npm run dev
npm run build

# Local application server
php artisan serve

# Clear Laravel caches
php artisan optimize:clear

# Central migrations
php artisan migrate
php artisan migrate --force          # production only after review

# Tenant migrations/seeding
php artisan tenants:migrate --force
php artisan tenants:migrate --tenants=TENANT_ID --force
php artisan tenants:seed --tenants=TENANT_ID --force

# Queue/scheduler
php artisan queue:work
php artisan queue:work tenant_file_sync --queue=default
php artisan queue:restart
php artisan queue:failed
php artisan schedule:run
php artisan schedule:list

# Theme/plugin inspection and publication
php artisan theme:list
php artisan theme:publish --all
php artisan plugin:list

# Tests
php artisan test
php artisan test --filter=TestMethodOrClass
./vendor/bin/phpunit tests/Unit/PackageExpireCommandTest.php
./vendor/bin/phpunit --testsuite Unit

# Small PHP syntax check
php -l path/to/ChangedFile.php
```

Theme creation/update/demo and plugin activation/install commands exist, but they mutate broad state. Use `php artisan help <command>` first and invoke them only for an explicit task. No repository-local lint/formatter script or Pint/PHPStan/ESLint/Prettier configuration was verified; `.styleci.yml` declares the Laravel PHP preset and CSS/JS checking.

## Testing Strategy

- Framework: PHPUnit 11.5 via Laravel `php artisan test`; suites are `Unit` and `Feature` in `phpunit.xml`.
- Current meaningful coverage is minimal: example tests and `tests/Unit/PackageExpireCommandTest.php`. Module test directories contain only `.gitkeep` placeholders.
- No Pest, Playwright, or Cypress project/config was found.
- Test environment uses array cache/session/mail and sync queue. SQLite settings are commented, so DB-backed tests need explicit safe test database configuration.
- After a change, run the smallest test or syntax check covering the affected code first.
- Add a focused regression test when behavior can be isolated without unsafe production DB coupling.
- Run a full suite only when the change is broad, touches shared architecture, the user requests it, or a concrete engineering reason requires it.
- Never point automated tests, `migrate:fresh`, seeders, or destructive DB commands at production/tenant data.
- To test tenant behavior, create/use a safe test tenant and verify both tenancy initialization and teardown. Do not use a live customer tenant without explicit scope.

## Browser Testing Rules

Use a browser only when verifying a user flow, UI, JavaScript interaction, responsive behavior, visual result, forms, or navigation. A code-level test is sufficient for isolated backend logic.

When browser testing:

1. Identify central versus tenant host and the exact account/role required.
2. Test only the requested path.
3. Stop at the first material failure and diagnose that path.
4. After the fix, repeat the same path with the same inputs.
5. Check console/network only for that path when JavaScript/API behavior is involved.
6. For theme UI, verify the requested active theme and relevant RTL/mobile breakpoint.
7. Do not turn a targeted check into a general site audit.
8. Do not create purchases, tenants, send messages, or approve payments unless those state changes are explicitly in scope and test data is safe.

## Debugging Map

Default workflow:

`Symptom` → identify central/tenant and feature → consult Feature Location Map → inspect the smallest route/controller/service/model/view path → reproduce → determine root cause → patch → targeted test → browser verification only if needed.

Diagnostic sources:

- Laravel/PHP: `storage/logs/laravel.log`; log channel controlled by `LOG_CHANNEL`/`LOG_LEVEL`.
- Plugins/hooks: daily `storage/logs/plugin-*.log`; optional hook audit controlled by `PLUGIN_LOG_HOOKS` and central `plugin_hook_log`.
- Queue scheduler output: `storage/logs/queue-jobs.log`.
- New-tenant file sync: `storage/logs/new-website-file-sync-jobs.log`.
- Seeding: daily `storage/logs/seed-log-*.log`.
- Failed jobs: `php artisan queue:failed`, `failed_jobs`, `jobs`, `queue_jobs` as applicable.
- Provisioning: `tenant_exceptions`, `cronjob_logs`, provisioning jobs/actions, and central payment log status.
- API exceptions: `app/Exceptions/Handler.php`; debug mode can expose file/trace, so never enable production debug as a routine fix.
- Browser JS: console plus the failing request in Network; then trace to Blade/theme/module source, not compiled output.
- Page Builder: editor API response, new content/widget rows or legacy rows according to page flags, and renderer/widget registry.

Use log tails/searches narrowly and redact tokens, credentials, cookies, personal data, payment proofs, and private paths from reports.

## Coding Conventions

- Match existing feature patterns and naming, including legacy names/typos where changing them would break autoload/routes/data.
- PHP follows Laravel/PSR-style four-space indentation from `.editorconfig`; YAML uses two spaces; UTF-8/LF/final newline.
- Controllers are context-namespaced under Landlord/Tenant; modules keep their own controllers/services/entities/requests/views.
- Use Form Requests where that feature already uses them; otherwise follow its controller validation pattern. Do not introduce a new architecture during a bug fix.
- Models use Eloquent relations and feature-specific tenancy traits. Preserve fillable/casts and connection semantics.
- Use transactions and row locks for multi-row monetary/stock mutations, consistent with checkout inventory/campaign code.
- Use `__()` for UI/notification text.
- Blade commonly contains page-specific jQuery; avoid duplicate global imports/listeners and keep scripts with the owning template/component.
- Use existing helpers (`themeView`, `global_asset`, media render helpers, static option update functions) rather than duplicating path/cache logic.
- Prefer named routes and the current route-name convention because permissions/entitlements may parse route names.
- Do not refactor nearby code merely because it could be cleaner. Keep change scope tied to the request.

## Deployment / Production

- Web root is the repository root. `.htaccess` blocks direct `core/` access and rewrites non-files to root `index.php`, which loads `core/vendor/autoload.php` and `core/bootstrap/app.php`.
- IIS equivalent is `web.config`; no Docker, CI workflow, or deployment script was found.
- Composer lifecycle publishes Laravel assets, creates storage link, and publishes all themes. Review filesystem ownership/symlink support before running with elevated users.
- Vite build writes to repository-root `_build`; production currently cannot build there because Node/npm are absent from the inspected shell. Build-host/process is **Needs verification**.
- Central migration and tenant migration are separate deployment steps. Review migration direction, tenant count, downtime, and rollback before production execution.
- After code/config deployment, use only necessary cache clears; plugin/theme caches and published links may need targeted refresh.
- Restart long-running queue workers after code deployment with `php artisan queue:restart`; ensure scheduler remains active.
- Verify writable `storage`, `bootstrap/cache`, root upload directories, `public/storage`, and theme links.
- There is no verified automated deployment pipeline, backup script, or rollback procedure in the repository. Host-panel backups are operational infrastructure, not repository evidence.

## Known Customizations

Repository history is short, so classification is intentionally conservative:

- `1389f0e` is labeled as the base Nazmart multi-tenant import. Most shared `app/`, legacy modules, themes, and core plugins trace to it; do not assume an individual file is untouched upstream code without diff evidence.
- `706f1e1` records server synchronization of fonts, helpers, language files, landlord views, and CSS.
- `535006a` is the proven Ymnay manual-wallet implementation. It adds `Modules/YmnayCustom`, enables it in `modules_statuses.json`, and makes minimal integration adaptations in `Landlord/Frontend/PaymentLogController` and three order-detail Blade views.
- An earlier separate Yemeni-wallet plugin commit was reverted before `YmnayCustom`; do not revive both implementations or create a second wallet source of truth.
- Local Page Builder/theme/plugin engine adaptations exist in the base repository commit. Treat them as repository core unless upstream comparison later proves otherwise.

Preferred location for new Ymnay business rules is a project-owned module/plugin, with minimal documented hooks/adapters in shared code only when required. Theme-specific presentation remains in themes.

## Source Of Truth

| Subsystem | Source of truth |
|---|---|
| Central web/admin routes | `routes/web.php`, `routes/admin.php`, plus module/plugin providers |
| Tenant web/admin/API routes | `routes/tenant.php`, `routes/tenant_admin.php`, `routes/tenant_api.php` |
| Tenant resolution and DB naming | `config/tenancy.php`, customized initializer middleware, `Tenant`, `domains` |
| Tenant provisioning order | `TenancyServiceProvider` event pipeline |
| Active theme | `tenants.theme_slug`; metadata/source under `themes/{slug}` |
| Theme view precedence/widgets | `ThemeManager`, `ThemeServiceProvider` |
| New Page Builder registry/routes/rendering | `config/xgpagebuilder.php`, `CustomPageBuilderServiceProvider`, theme widget discovery, bound render service |
| Legacy Page Builder registry | `plugins/PageBuilder/PageBuilderSetup.php` |
| Module enabled state | `modules_statuses.json` and Nwidart module manifests/providers |
| New plugin discovery/activation | `PluginManager`, each `plugin.json`, tenant overrides, plan plugin assignments |
| Admin role permissions | Spatie permission tables/config plus controller middleware |
| Plan feature/gateway/theme/plugin entitlement | plan relation tables/models and tenant entitlement middleware/helpers |
| Product persistence | `ProductGlobalTrait` reached through `AdminProductServices` |
| Store order creation | `ProductCheckoutService::createOrder()` and its checkout filter hooks |
| Payment dispatch | central `PaymentLogController` or tenant `CheckoutToPaymentService`, then credentials/module charge/IPN |
| Ymnay wallet catalog/config/order snapshot | central `ymnay_manual_wallets`, tenant wallet static option, `PaymentLogs.custom_fields` or `ProductOrder.payment_meta` |
| General settings | current-context `static_options` through helper/update methods; explicitly central synced settings use `static_option_centrals` |
| Media metadata/files | `media_uploaders`, `MediaUploaderController`, filesystem config/root asset paths |
| Scheduled tasks | `app/Console/Kernel.php` plus plugin scheduler registrations |
| Dependency versions | `composer.lock` and `package-lock.json`, not README claims |

## Known Dangerous Areas

- **Tenancy bootstrap/resolver:** a mistake can route one domain to another database or leak cross-tenant state.
- **Provisioning pipeline/cPanel fallback:** creates/deletes databases, domains, seed data, files, credentials, and notifications.
- **CentralConnection usage:** missing or incorrect connection traits can read/write tenant tables when central data was intended, or vice versa.
- **Auth and shared guards:** the same `web`/`admin` guards operate in different DB contexts.
- **Plan-feature middleware:** it parses route names/URL positions and can silently deny features after route renames.
- **Global providers/autoloaded helpers:** execute on nearly every request and have large blast radius.
- **Payment/checkout/stock/campaign:** combines money, order state, inventory, hooks, gateway callbacks, and notifications.
- **Manual payment approval:** central approval may provision/renew a tenant; tenant approval changes customer orders and stock/commission behavior.
- **Theme resolver/publication:** wrong precedence or editing output can break every tenant theme or lose changes on publish.
- **Dual Page Builders:** changing the wrong registry/data model has no effect or corrupts unrelated pages.
- **Dynamic catch-all routing:** order changes can shadow login/admin/plugin/product routes.
- **Plugin boot/auto schema:** plugins can register routes/hooks and ensure schema during boot; activation/context mistakes affect every request.
- **Media filesystem:** root-level assets, tenant suffixes, and cloud URLs differ; incorrect helper use can expose or orphan files.
- **Package/vendor overrides:** local Page Builder behavior is implemented by bindings/providers; editing `vendor` is temporary and forbidden.

## Known Gotchas

- The real helper filename is `app/Helpers/funtions.php`; it is Composer-autoloaded despite the typo.
- Tenant storefront customers use the `web` guard. `auth.php` also configures `tenant_user` → `App\Models\TenantUser`, but that model was not found. Treat this guard as legacy/unverified and do not build new auth behavior on it without proof.
- Central and tenant admin login routes reuse `Landlord/Admin/Auth/AdminLoginController`; tenancy context changes which admin table is queried.
- Two Page Builders and two extension systems coexist. `module.json` is not interchangeable with `plugin.json`.
- `Modules/YmnayCustom` currently uses Nwidart `module.json`/payment metadata and has no discovered `plugin.json`; it is not booted by the new plugin manifest scan.
- Only a central `page_builder_versions` migration was found although snapshot middleware is in the shared web group. Verify tenant history before relying on it.
- `config/tenancy.php` contains a blank-string key where the template tenant connection key would normally be expected. Do not “clean it up” without validating current Stancl behavior and tenant connections.
- Theme source and published theme assets have similar paths. Edit `core/themes/{slug}`, never root/public publication output.
- Static options cache by option name; direct database changes can appear ineffective until cache expiry/clear. Use update helpers.
- Plugin activation can depend on three states: global status, tenant override, and plan assignment. Changing one source may not make a plugin visible.
- New plugin manifest discovery is cached in production; allow/clear the small manifest cache after adding/removing a manifest.
- The checkout view may be theme-overridden. A patch to the core fallback can pass code review but remain invisible on the active tenant theme.
- Root `README.md` contains useful intent but some referenced docs and runtime claims are absent/stale. Lock/config/code wins.
- `vite.config.js` assumes `nazmart.test` in dev and builds outside `core` into root `_build`.
- `AppServiceProvider` and `ThemeServiceProvider` can mutate symlinks/directories on the first web request; a read-only-looking smoke request may trigger setup filesystem writes.
- On the inspected PHP 8.4 CLI, Artisan bootstrap emits deprecation notices from `nwidart/laravel-modules` 8.6 and `xgenious/xgpagebuilder` 1.7.1 nullable parameters. Do not mistake these notices for the requested command failing; dependency/runtime compatibility should be evaluated separately.
- `core/__MACOSX/` contains package metadata/duplicates and must be excluded from search, packaging, and architecture conclusions.
- Production worktree is intentionally dirty. Never use `git reset --hard`, `git clean`, blanket checkout, or broad formatting.

## Task Routing Guide

| If the task is… | Start here |
|---|---|
| UI change | Identify landlord admin, tenant admin, or tenant storefront; start in matching Blade tree, and for storefront check active theme override first. |
| Theme | `themes/{target}/theme.json`, target view/assets, then `ThemeManager` only if resolution itself is broken. |
| Page Builder section/widget | Check `Page.use_page_builder` vs `Page.page_builder`; new: theme `Widgets/` + Xgenious config/provider; legacy: `plugins/PageBuilder/PageBuilderSetup.php` + exact addon. |
| Plugin | Existing feature's `plugin.json`/main class or `php artisan plugin:create`; then `PluginManager` activation/context only if boot is broken. |
| Module | `Modules/{Feature}/module.json`, provider, routes, controller/service/entity/view/migrations. |
| Product add/edit bug | Product module route → `ProductController` → `ProductStoreRequest` → `AdminProductServices` → `ProductGlobalTrait` → product/inventory relations. |
| Inventory/import | `Modules/Inventory` and Product inventory entities/observers/import job. |
| Order | Tenant admin routes/controller → `ProductOrder`/`OrderProducts`; for creation, trace checkout service and hooks. |
| Checkout/payment | Active theme checkout view → `CheckoutFormRequest` → checkout/order service → gateway module/IPN. Separate central plan payment from tenant store payment. |
| Shipping/tax/coupon | Owning module plus `ProductCheckoutService` total/shipping/coupon calculation path. |
| Tenant/domain/provisioning | Resolver middleware/config for routing; provider job pipeline/actions for creation; central tenant/domain/payment tables. |
| Admin | `routes/admin.php` or `routes/tenant_admin.php` → context controller → corresponding views; check Spatie permission and plan entitlements. |
| Customer dashboard | Central or tenant `UserDashboardController` and matching dashboard view tree. |
| API | Central `routes/api.php` or tenant `routes/tenant_api.php` → MobileApp controller → Sanctum/tenant middleware. |
| Database | Classify central vs tenant → correct migration root/module path → model connection traits → targeted migration/test tenant. |
| Authentication/permission | `config/auth.php`, route guard/middleware, shared login controller, Spatie middleware, tenant context. |
| Media/storage | Media routes/controller → `MediaUploader` → filesystem disk/render helper; identify local vs cloud and tenant vs landlord. |
| Settings/localization | Settings/language controller → static option/language model → cache/helper → matching views/lang JSON. |
| Performance | Measure exact slow route/query first; inspect cache/queries/rendering/asset pipeline only for that path. Avoid speculative global changes. |
| Generic bug | Reproduce → identify feature/context → Feature Location Map → smallest code path/log → targeted patch/test. |

Mental routing check: login UI starts in the correct auth Blade/context; product add starts in Product module/service trait; a new plugin starts from the scaffold/manifest contract; Page Builder starts by selecting new versus legacy; tenant failures start at resolver/bootstrap or the named tenant feature; theme work starts at source theme; checkout follows the active theme → request → order service → gateway chain.

## Codex Efficiency Rules

1. Read `AGENTS.md` before any broad exploration.
2. Use Project Map and Feature Location Map to choose the starting point.
3. Start with the smallest logical task scope.
4. Expand search only when evidence shows the issue is outside the current scope.
5. Do not rediscover documented architecture unless this file is stale or evidence conflicts with it.
6. Do not read `vendor`, `node_modules`, uploads, or generated files without a direct reason.
7. Use targeted `rg`/search instead of broad directory crawling; on hosts without `rg`, use focused `grep`/`find`.
8. Do not use a browser before it is needed.
9. Do not use sub-agents for a small or local task.
10. Do not run broad test suites when a targeted test is sufficient.
11. Do not investigate issues outside the user's request.
12. Do not perform extra refactors merely because they are possible.
13. Do not fix side issues unless they block the requested task.
14. Stop after the requested change and necessary verification succeed.
15. Quality and correctness outweigh token savings; efficiency removes unnecessary work, not necessary verification.

## Pre-Change Product Questions

For a new feature or meaningful behavior change, clarify enough to implement the right outcome before coding:

- Who owns/manages it: Ymnay admin, tenant owner, store customer, or several roles?
- Is it central, tenant, or both? Which data belongs in which database?
- What business result is expected, and why is the change needed?
- Where exactly should it appear, in which theme/admin/API/mobile contexts?
- What are the success, pending, rejection, cancellation, retry, and failure states?
- What validation, permissions, plan entitlements, limits, notifications, audit/history, and file handling apply?
- What should happen when the module/integration is disabled or an external provider fails?
- Which existing feature is the pattern to match?
- What backward compatibility, migration, default behavior, and rollback are required?
- What is the smallest acceptance test that proves the value?

Do not ask every question mechanically when the answer is already explicit. Ask only unresolved questions that materially affect design, safety, or behavior.

## Keeping This File Current

After a future task reveals a stable, important architectural fact or changes architecture, important paths, commands, dependencies, user flows, extension points, database structure, or testing/deployment process, update `AGENTS.md` in the same task.

Do not update it for a temporary bug, user/customer data, a transient test result, a secret/environment value, an unverified guess, or an implementation detail unlikely to help future routing. Keep entries concise: where the source of truth is, how it works, when it matters, and what boundary must be protected.

Before committing an update:

1. Recheck every new path/command against the repository.
2. Label unresolved facts **Needs verification**.
3. Remove secrets, duplication, copied source/schema/routes, and obsolete claims.
4. Confirm the guide still routes UI, product, plugin, Page Builder, tenant, theme, and checkout tasks without a broad rediscovery.
