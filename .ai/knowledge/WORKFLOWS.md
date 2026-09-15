# Business workflows

VERIFIED control paths below; successful end-to-end execution and provider delivery are not certified. Actors/steps/states also have compact records in `manifests/workflows.yaml`. All source paths are repository-relative. Failures described are source branches or unresolved risks, not claims of observed customer incidents.

## W01 — central registration and subscription purchase

- Actor/trigger: visitor or central user enters native registration/plan checkout in `core/routes/web.php`; selected plan and request validation are prerequisites.
- Chain: LandlordFrontendController registration/OTP/user checks → plan view → PaymentLogController::order_payment_form → PaymentLogs → gateway/manual result → subscription approval/provisioning. `/plan-order` redirects to pricing; `/plan-order/{id}` is the native paid purchase entry. The separate self-service trial wizard is W10.
- Effects/states: central users, payment_logs and selected price_plans; paid flows start pending, completed gateway result uses payment_status `complete`. Manual payment remains pending for review; free/trial branches differ.
- Notifications/side effects: core mail and tenant registration event when eligible; tenant creation is W02. Failed validation, gateway failure or pending approval must not be equated with an activated store.
- Source: `core/app/Http/Controllers/Landlord/Frontend/LandlordFrontendController.php`, `core/app/Http/Controllers/Landlord/Frontend/PaymentLogController.php`, `core/app/Actions/Payment/PaymentGateways.php`.

## W02 — provision a tenant/store

- Actor/trigger: qualified central purchase/trial/admin path creates Tenant; central owner/domain/plan inputs precede creation. W10 marked self-service requests use the checkpointed exception documented below and in TENANCY.
- Chain and exact synchronous native job order: TENANCY. The native TenantCreated pipeline creates DB, migrates, sets domain/information, seeds and dispatches media/mail work.
- Effects: central tenants/domains and a new tenant database with users/admins/settings/schema/demo data; cloud file-copy child job uses tenant_file_sync connection.
- Failure: DB permissions, migration ordering, seeding, provider or cloud copy failure. Parent sync pipeline and later queued copy are not one atomic operation. Do not claim automatic full rollback.
- Source: `core/app/Providers/TenancyServiceProvider.php`, `core/app/Jobs`, `core/database/seeders/TenantDatabaseSeeder.php`.

## W03 — create/update a product

- Actor/trigger: tenant admin submits Product create/update route, with tenancy/admin/package/product-limit checks and ProductStoreRequest validation.
- Chain: ProductController → AdminProductServices → ProductGlobalTrait → product, inventory/variants, gallery/category/tag/specification/slug/meta records. Creation status uses product status IDs; update preserves distinct inventory quantities.
- Events: product-price and inventory observers may react. Failure: request/slug/stock relation errors; transaction coverage must be checked for the exact variant path, not assumed from a service name.
- Source: `core/Modules/Product/Routes/web.php`, `core/Modules/Product/Http/Controllers/ProductController.php`, `core/Modules/Product/Http/Services/Admin/AdminProductServices.php`, `core/Modules/Product/Http/Traits/ProductGlobalTrait.php`, `core/app/Providers/EventServiceProvider.php`.

## W04 — tenant checkout and payment

- Actor/trigger: storefront buyer POSTs checkout; domain context, cart, billing/shipping/payment validation precede order creation. Digital-product guest restriction is enforced in the checkout path.
- Chain: CheckoutPaymentController → CheckoutFormRequest → ProductCheckoutService::createOrder → before/after order hooks → ProductOrder + OrderProducts → CheckoutToPaymentService::checkoutToGateway → provider IPN or pending manual result.
- Effects/states: user/address where applicable, order totals/lines, stock and campaign counters; pending payment until successful gateway/admin transition. Legacy manual gateway requests transaction ID; do not restore the deleted custom wallet proof workflow from old notes.
- Failure: validation/cart/gateway failures, stock/coupon/shipping changes, duplicate callbacks. Provider idempotency/replay safety remains UNKNOWN-004. Checkout creates persisted effects before all downstream work finishes.
- Source: `core/routes/tenant.php`, `core/app/Http/Controllers/Tenant/Frontend/CheckoutPaymentController.php`, `core/app/Http/Requests/CheckoutFormRequest.php`, `core/app/Http/Services/ProductCheckoutService.php`, `core/app/Http/Services/CheckoutToPaymentService.php`, `core/app/Actions/Payment/Tenant/PaymentGatewayIpn.php`.

## W05 — tenant order review/status

- Actor/trigger: tenant admin changes order status through tenant_admin.php; admin context and validated status input required.
- Chain: OrderManageController::change_status → order/payment update → order hooks → stock restoration on cancellation → OrderCompleted/commission behavior → mail.
- State rules: payment `success` is distinct from fulfillment `complete`; current successful payment is protected against subsequent payment-status edits in this handler. Cancellation has stock side effects; retrying a transition needs exact-path testing.
- Effects: product_orders, inventory/campaign state, commission records; notification errors may be caught independently. Never approve central subscriptions through this tenant order handler.
- Source: `core/app/Http/Controllers/Tenant/Admin/OrderManageController.php`, `core/Modules/CommissionManage`, `core/app/Providers/EventServiceProvider.php`.

## W06 — subscription expiry, renewal and access

- Actor/trigger: scheduled package checks or a request to tenant frontend/admin. Existing subscription/trial/payment state drives middleware decisions.
- Chain: package:expire sends expiry notices; package:auto-renew calls WalletService renewal; PackageExpireMiddleware and TenantAccountStatus decide request access using expiry/payment/status conditions.
- Effects: renewal may consume wallet/update ledger and expiry; expired request redirects or is blocked according to route middleware. `account:remove` currently sends notices, **not account deletion**. Actual Tenant deletion uses a separate destructive lifecycle (TENANCY).
- Failure: insufficient wallet/provider failure, stale expiry or unexpected historical logs; delivery/cron activation UNKNOWN-001/005.
- Source: `core/app/Console/Kernel.php`, `core/app/Console/Commands`, `core/Modules/Wallet`, `core/app/Http/Middleware`.

## W07 — custom domain management

- Actor/trigger: central or tenant admin requests/manages domain mapping; authenticated owner/context and unique host validation matter.
- Chain: matching Landlord/Admin or Tenant/Admin CustomDomainController → custom_domains / tenant domain records → resolver on subsequent requests. Optional DomainReseller/cPanel integrations are separate capability paths.
- Failure: conflicting mapping, absent DNS or certificate. Wildcard support supplied by owner does not prove a newly requested custom domain is provisioned. Exact operational DNS/SSL validation remains UNKNOWN-009.
- Source: `core/app/Http/Controllers/Landlord/Admin/CustomDomainController.php`, `core/app/Http/Controllers/Tenant/Admin/CustomDomainController.php`, `core/app/Http/Middleware/Tenant/InitializeTenancyByDomainCustomisedMiddleware.php`.

## W08 — theme selection and page editing

- Actor/trigger: authenticated admin selects/imports theme or saves a Page Builder page; tenant/admin context and owning page required.
- Chain: theme metadata/tenant theme_slug → ThemeServiceProvider activation → ThemeManager resolution. ThemeDemoImporter can mutate demo pages/menus. Builder editor/API saves page content/widget data; page flags choose legacy/new renderer.
- Effects: CMS content, theme selection, settings and published assets; save/import failure can leave incomplete content and is not certified atomic. Tenant theme ZIP upload and tenant demo import are different operations.
- Source: `core/app/Services/ThemeManager.php`, `core/app/Services/ThemeDemoImporter.php`, `core/app/Http/Controllers/CustomPageBuilderController.php`, `core/app/Providers/CustomPageBuilderServiceProvider.php`.

## W09 — plugin activation / tenant boot

- Actor/trigger: plugin management or tenancy initialization; discovered manifest and context activation checks precede boot.
- Chain: PluginServiceProvider → PluginManager registration/status/overrides → plugin main class → hooks/routes/assets/settings/scheduler. Some schema checks execute during tenant boot.
- Effects: module/plugin status/options/tenant override and plugin-owned schema where applicable. A manifest/route existing is not proof of active entitlement. Compatibility/failure-cache issues: UNKNOWN-007.
- Source: `core/app/Providers/PluginServiceProvider.php`, `core/app/PluginSystem/PluginManager.php`, `core/app/PluginSystem/PluginBase.php`, `core/Modules/PluginManage`.

## W10 — self-service store onboarding trial

- Actor/trigger: visitor or verified central user enters `/create-store`; an active trial-enabled plan, allowed theme, valid available subdomain and trial-eligible central account are prerequisites.
- Chain: StoreOnboardingController records plan/theme/store data in StoreOnboardingRequest → LandlordFrontendController OTP registration or login claims the request → review compares saved/current plan values independently of JSON key order → completion locks the central user/request and revalidates all saved store data, current reserved/claimed address rules, verification and trial eligibility → marked TenantRegisterEvent selects StoreOnboardingProvisioner → checkpointed readiness → central trial transaction → signed tenant dashboard URL.
- Effects/states: central `store_onboarding_requests` uses `draft`, `account_verified`, `provisioning`, `ready`, `failed`. Every earlier-step mutation and plan acknowledgement uses the same central request row lock. Provisioning and ready requests are immutable through these endpoints; selecting a plan does not silently replace a completed request. Failed requests with an owned partial tenant retain their creation identity. No passwords or OTPs are stored in the onboarding row.
- Recovery: existing `tenants.data` stores request origin and stage checkpoints. Only matching owned partial tenants can resume. Missing safe stages are retried or reconciled; uncertain seeds, interrupted incomplete migrations or provider operations require inspection. Unmarked legacy partial tenants are only adopted when origin checks pass and their DB is absent or contains no application rows. This is not general automatic repair or rollback of tenant databases.
- Validation and integrity: final data validation happens under the claim lock, before provisioning effects; forbidden subdomains are read from the central option source, not a previously cached availability result. The tenant primary key remains the final address-race guard. A matching existing trial is reused without extending its dates. Physical tenant key/trial fields and their virtual JSON representation are synchronized under a central row lock.
- Readiness and boundaries: the service checks database/schema bookkeeping, seeded administrator role, domain, login key, store title and checkpoint completion. Manual tenancy switches end in `finally`. Existing file-copy child jobs remain asynchronous; dispatch completion does not certify copied media. Welcome mail failure does not turn a ready store into failed. Cross-database work is not one atomic transaction; hard interruption and uncertain non-repeatable stages still need an explicit recovery review.
- Source: `core/routes/web.php`, `core/app/Http/Controllers/Landlord/Frontend/StoreOnboardingController.php`, `core/app/Http/Controllers/Landlord/Frontend/LandlordFrontendController.php`, `core/app/Models/StoreOnboardingRequest.php`, `core/app/Actions/Tenant/TenantTrialPaymentLog.php`, `core/app/Services/Onboarding/`, `core/app/Events/TenantRegisterEvent.php`, `core/app/Listeners/TenantDomainCreate.php`, `core/app/Providers/TenancyServiceProvider.php`, `core/database/seeders/TenantDatabaseSeeder.php`.
- Verification boundary: isolated tests use real framework components and MySQL with explicit synthetic tenant migration/seed/auth/mail adapters. Full application boot, actual themes/modules, OTP/cookie flows, queue completion and desktop/mobile/RTL browser acceptance remain separate QA gates.
