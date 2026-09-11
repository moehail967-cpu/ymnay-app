# Module inventory

VERIFIED: this baseline has **47** directories under `core/Modules`. Inventory is source presence, not enabled plugin status. Purpose grouping and risk ratings are **INFERRED** from handlers/dependencies. No `Modules/YmnayCustom` exists in this baseline; do not revive removed wallet/wizard customizations from historical notes.

## Registration and extension boundary

- Nwidart module: `module.json` provider(s), Routes, controller/entity/service and migrations.
- Custom plugin: `plugin.json` ID/main class, discovered by PluginManager; many modules support both contracts, others only one. Main classes can register routes without a Routes directory.
- Root engine packages under `core/plugins`: PageBuilder, WidgetBuilder and MenuBuilder. They are separate from the 47 module directories and are mapped in ARCHITECTURE. Do not infer an application `Order` module merely from an import in POS.
- Inspect an existing comparable plugin, its actual main class and PluginBase contract before adding one; use manifest requires, existing routes/context guards and hook signatures. Runtime schema/activation behavior is not a safe installer substitute.

Source: `core/config/modules.php`, `core/app/Providers/PluginServiceProvider.php`, `core/app/PluginSystem/PluginManager.php`, `core/app/PluginSystem/PluginBase.php` and manifests below.

## Responsibilities and entry points

Each row root is relative to `core/Modules/`. Full routes, controllers, models, services, views, tables, permission-check sources, jobs, events, dependencies and tests are in [modules.yaml](manifests/modules.yaml). Read only the named module entry. Empty inventories are not proof of an unprotected endpoint or absence of dynamic behavior. Literal schema evidence includes alterations and references; tables may be created by core tenant migrations.

| Module/source root | Responsibility | Registration | Risk (inferred) |
|---|---|---|---|
| `AbandonedCart` | Capture and recover abandoned carts | plugin.json | medium |
| `Affiliate` | Affiliate enrollment, commissions and payouts | plugin.json | medium |
| `AiIntegration` | Provider-backed product/blog/page content generation | plugin.json | medium |
| `Attributes` | Catalog categories, brands, tags and attributes | module.json | medium |
| `Badge` | Catalog badge management | module.json | medium |
| `Blog` | Blog posts and categories | module.json | medium |
| `Campaign` | Campaign pricing and product associations | module.json | medium |
| `CartDiscount` | Cart-level discount rules | plugin.json | medium |
| `CloudStorage` | Configure remote media disks | module.json + plugin.json | medium |
| `CommissionManage` | Order commissions and settlement | module.json + plugin.json | high |
| `CountryManage` | Country/state/city reference data | module.json | medium |
| `CouponManage` | Product coupons and usage | module.json | high |
| `CpanelAutomation` | Control-panel provisioning integration | module.json + plugin.json | high |
| `DigitalProduct` | Digital catalog/download purchase functions | module.json | high |
| `DomainReseller` | Domain availability and purchase integration | module.json + plugin.json | high |
| `FeedSync` | Generate product catalog feeds | plugin.json | medium |
| `FomoNotifications` | Storefront sales/social-proof notifications | plugin.json | medium |
| `GdprExport` | Data export request handling | plugin.json | medium |
| `Integrations` | Integration settings interface | module.json + plugin.json | medium |
| `Inventory` | Stock administration | module.json | high |
| `LoyaltyPoints` | Customer loyalty ledger | plugin.json | medium |
| `MobileApp` | Central and tenant mobile/API handlers | module.json | high |
| `MultiCurrency` | Currency selection/rates and geolocation | plugin.json | medium |
| `MultiLingual` | Stored translations and provider-backed translation | plugin.json | medium |
| `NewsLetter` | Newsletter subscribers and mail management | module.json | medium |
| `PluginManage` | Plugin catalog/lifecycle administration | module.json + plugin.json | high |
| `Pos` | Point-of-sale order operations | module.json + plugin.json | high |
| `Product` | Catalog and inventory persistence | module.json | high |
| `ProductBadges` | Rule-driven storefront product badges | plugin.json | medium |
| `ProductBundles` | Product bundle definitions/items | plugin.json | medium |
| `ProductQA` | Product questions and answers | plugin.json | medium |
| `RefundModule` | Refund request management | module.json | high |
| `SalesReport` | Sales reporting | module.json | medium |
| `Service` | Service content management | module.json | medium |
| `ShippingModule` | Shipping zones, methods and addresses | module.json | medium |
| `ShippingPlugin` | External shipping providers | module.json + plugin.json | high |
| `SiteAnalytics` | Site analytics collection/display | module.json + plugin.json | medium |
| `SiteWayPaymentGateway` | SiteWay payment integration | module.json + plugin.json | high |
| `SmsGateway` | OTP and SMS gateway operations | module.json + plugin.json | medium |
| `SupportTicket` | Support tickets/messages | module.json | medium |
| `TaxModule` | Tax classes/options | module.json | high |
| `ThemeManage` | Theme marketplace/management | module.json | medium |
| `TierPricing` | Quantity-tier product pricing | plugin.json | medium |
| `TrackingPixel` | Client/server marketing conversion events | plugin.json | medium |
| `Wallet` | Wallet balances, ledger and subscription renewal | module.json | high |
| `WebHook` | Webhook configuration and selected event dispatch | module.json + plugin.json | medium |
| `WooCommerce` | WooCommerce store integration/import | module.json + plugin.json | medium |

## Practical ownership checks

Product/Inventory/Attributes/Badge share catalog relations. Checkout also consumes CouponManage, TaxModule, ShippingModule and optional ShippingPlugin; completion affects CommissionManage. Wallet is central subscription/financial logic with context-sensitive consumers. ThemeManage and PluginManage affect presentation/extension lifecycle. MobileApp exposes many of these through a separate API surface. Verify each dependency from the manifest import paths before expanding a change.

Migrations: read `core/config/tenancy.php` ordering as well as module schema sources. Permissions: check the endpoint and guard, not only manifest/menu declarations. Tests: many module Tests folders contain no executable tests; TESTING describes actual baseline coverage.
