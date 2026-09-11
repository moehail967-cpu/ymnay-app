# Change impact map

INFERRED risk ranking over VERIFIED call/dependency relationships. Begin at CODE-MAP, then inspect only the affected row's consumers. These are minimum impact candidates, not proof every consumer fails.

| Change owner | Likely blast radius | Risk / first verification |
|---|---|---|
| Tenant model/resolver/bootstrap | Domain routing → DB/cache/storage/queue context → all tenant users/admin/products/orders | Critical; central-vs-two-tenant isolation and context cleanup |
| Provisioning jobs/migration order | New DB → module tables → seeds/theme demo → domain/mail/media | Critical; disposable tenant lifecycle including failure path |
| Guards / Gate / shared middleware | Central admin + tenant admin/customer + API access | Critical; actor/guard matrix, deny cases and cross-tenant access |
| PaymentLogController / central payment actions | Subscription ledger → approval → store provisioning/renewal | Critical; pending/failure/success/repeated callback; no tenant-order assumptions |
| ProductCheckoutService / CheckoutToPaymentService | Totals/coupon/tax/shipping → order lines → inventory → gateway/IPN | High; one targeted checkout including failure/retry case |
| Tenant order status | Payment/fulfillment → stock reversal → commissions → notification | High; prevent duplicate effects and test exact old/new state |
| ProductGlobalTrait | Catalog → variants/inventory → category/meta/slug → storefront/API/POS | High; simple and affected variant persistence, safe deletion |
| ThemeManager / theme helpers | All themed views → module overrides → assets/RTL/widget namespaces | High; default and affected theme; namespace/cache resolution |
| Builder registry/renderer/content schema | Editor listing/save → page JSON/widgets → central/tenant render/demo import | High; known-format edit/save/render round trip, preserve IDs |
| PluginManager / HookEngine / shared provider | Registration/activation → routes/menus/options/migrations/scheduler | High; active/inactive central/tenant paths and no unrelated plugin regression |
| Static option helpers / TenantConfigMiddleware | Cache keys → language/mail/storage/payment/plugin settings | High; isolate tenant/central settings and verify cache invalidation |
| Shared User/Admin model | Session guard → ownership → password/reset → API serialization → provisioning | High; hidden fields, connection, permissions and login/logout |
| Theme-local view/CSS | Affected tenant view plus shared includes and responsive/RTL variants | Medium; smallest browser flow/viewport needed |
| Knowledge-only files | Agent routing and future interpretation, no runtime source | Low runtime risk; path/symbol/evidence/scope validation |

Sources: `core/app/Providers/TenancyServiceProvider.php`, `core/app/Providers/ThemeServiceProvider.php`, `core/app/Providers/EventServiceProvider.php`, `core/app/PluginSystem`, `core/app/Http/Services`, `core/Modules/Product/Http/Traits/ProductGlobalTrait.php`, `core/app/Http/Middleware/Tenant/TenantConfigMiddleware.php`, and paths in CODE-MAP. Before broadening a task, show a concrete dependency or failure; this map is not permission to refactor all consumers.
