# Feature → code map

VERIFIED source entry points. Paths in the Logic column are relative to `core/`; controller short names resolve under the explicitly named namespace. Route files, not remembered URLs, are authoritative. Middleware abbreviations: **C** = central web/session context; **CA** = central `auth:admin`; **T** = domain-initialized tenant; **TA** = tenant admin guard/package checks; **U** = relevant context's `auth:web`. See AUTHORIZATION for limitations. Unlisted tests mean no focused test established, not verified behavior.

| Feature | Route source / context | Logic → data | Presentation / verification |
|---|---|---|---|
| Central login/register | `core/routes/web.php`, C/U | `app/Http/Controllers/Landlord/Frontend/LandlordFrontendController.php` → User, OTP/registration and token login | `core/resources/views/landlord/frontend/user`; inspect actual return view before editing |
| Admin login | web.php / tenant.php, C or T then admin session | `app/Http/Controllers/Landlord/Admin/Auth/AdminLoginController.php` → Admin | `core/resources/views/landlord/admin/auth`; shared even in tenant context |
| Tenant customer login | tenant.php, T/U | `app/Http/Controllers/Tenant/Frontend/TenantFrontendController.php` → tenant User | Active theme first, core tenant frontend fallback |
| Plans / subscription checkout | web.php: `/plan-order/{id}`, `/order-confirm`, C/U | LandlordFrontendController → `app/Http/Controllers/Landlord/Frontend/PaymentLogController.php` → `app/Actions/Payment/PaymentGateways.php` → PricePlan, PaymentLogs | Landlord frontend package views; native `/plan-order` redirects to `/pricing-plan`; no old multi-step Ymnay wizard |
| Tenant creation/lifecycle | web.php / admin.php, C/CA | `app/Actions/Tenant`, `app/Providers/TenancyServiceProvider.php`, `app/Jobs` → Tenant, domains, per-store DB | Central tenant admin views; WORKFLOWS W02 |
| Products | `core/Modules/Product/Routes/web.php`, TA | ProductController → ProductStoreRequest → AdminProductServices → ProductGlobalTrait → Product/inventory/meta/slug relations | `core/Modules/Product/resources/views`; admin theme metadata can override; no focused product test |
| Inventory / classification | Inventory / Attributes / Badge module Routes, TA | Module controllers/entities, Product inventory entities | Module views; product persistence is shared with ProductGlobalTrait |
| Cart / checkout | tenant.php `/s/*`, T | TenantFrontendController; `app/Http/Controllers/Tenant/Frontend/CheckoutPaymentController.php` → CheckoutFormRequest → ProductCheckoutService → CheckoutToPaymentService → ProductOrder/OrderProducts | `themeView('shop.checkout.checkout_page')`; theme override and hook `nazmart:render_checkout_form`; no E2E test |
| Order review / payment state | tenant_admin.php `order-manage/*`, TA | `app/Http/Controllers/Tenant/Admin/OrderManageController.php` → ProductOrder → stock restoration / hooks / OrderCompleted commission event | `core/resources/views/tenant/admin/product-order-manage`; payment success differs from fulfillment complete |
| Customer dashboard | web.php / tenant.php user groups, U | Context's `Frontend/UserDashboardController.php` → account/order records | `core/resources/views/tenant/frontend/user/dashboard`, landlord counterpart; `core/tests/Unit/DashboardMarkupTest.php` only checks legacy markup |
| Shipping / tax / coupons | Module routes + tenant checkout, TA/T | ShippingAddressServices, ShippingModule / TaxModule / CouponManage entities; optional ShippingPlugin remote rates/orders | Module views and checkout; inspect totals/recalculation path |
| Domains | admin.php / tenant_admin.php, CA/TA | Context's `Admin/CustomDomainController.php`; TenantManageController → Tenant/domain mapping | Central/tenant domain admin views; TENANCY before modifying resolver |
| Pages / slugs | admin.php and terminal dynamic routes, CA/TA/T/C | Landlord/Admin/PagesController, `app/Http/Services/DynamicRouteManager.php` → Page/Slug/MetaInfo | Landlord page view or active tenant theme |
| Page Builder section | CustomPageBuilder providers + legacy admin routes | `app/Http/Controllers/CustomPageBuilderController.php`, `app/Http/Controllers/FilteredWidgetController.php`, `app/Services/CustomPageBuilderRenderService.php` | `core/plugins/WidgetBuilder`, `core/plugins/PageBuilder`, theme Widgets; check page flags/format before choosing engine |
| Theme | ThemeManage + central/tenant theme routes | `app/Services/ThemeManager.php`, `app/Services/ThemeDemoImporter.php`, ThemeServiceProvider → tenant.theme_slug | `core/themes/{slug}` source; never published asset symlinks |
| Plugin / module | `plugin.json`, `module.json`, owning Routes or main class | `app/PluginSystem/PluginManager.php`, PluginBase, HookEngine; module controller/service | MODULES + modules.yaml; do not create an absent YmnayCustom module implicitly |
| Media / general settings | admin.php / tenant_admin.php, CA/TA | Landlord/Admin/MediaUploaderController and GeneralSettingsController; Tenant/Admin/OtherSettingsController → MediaUploader/static_options | Shared uploader modal / settings views; upload data excluded from source |
| API / mobile | `core/routes/api.php`, `core/routes/tenant_api.php` | `core/Modules/MobileApp/Http/Controllers`; Sanctum on selected protected groups | JSON resources under MobileApp; route-by-route auth and context validation |
| Mail / SMS / external providers | Core config plus module routes | `core/app/Mail`, `core/Modules/SmsGateway`, payment helpers and integration modules | INTEGRATIONS and BACKGROUND-PROCESSING |

## Source of truth

- Request contract: owning route file/provider **plus** guard/middleware and request validation.
- Tenant identity: domains resolver + `core/config/tenancy.php`, not a posted tenant ID.
- Permissions: actual endpoint checks + Spatie seed/config, not sidebar visibility.
- Plugin registration: manifests, providers, PluginManager status/override lookup; module inventory is not activation state.
- Theme rendering: ThemeManager and active tenant theme slug. Builder registry: providers/config/theme Widgets, not compiled editor bundles.
- Schema: creation and subsequent alteration migrations, with live parity UNKNOWN-002.

Debugging route: symptom → affected row → smallest code path → reproduce safely → root cause → scoped patch → targeted test → browser only if required. Laravel exceptions: `core/app/Exceptions/Handler.php`; log routing: `core/config/logging.php`; runtime logs: excluded `core/storage/logs`. JS starts at owning Blade/theme/source entry, not minified output. Queue failures: config/queue.php and job/dispatch site.
