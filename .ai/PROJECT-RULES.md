# Global project rules

Evidence baseline: clean `main` at `60ba2c68e09ed367026ac62c55a666a72fba900d`, inspected 2026-09-12. These rules describe existing contracts, not a proposed redesign.

## Current owner mandates

- Canonical repository: `moehail967-cpu/ymnay-app`; canonical branch: `main`. A task branch is not deployment authority. Do not merge or deploy without the current task authorizing it.
- `.ai/` is the sole repository AI authority; `AGENTS.md` stays a short entry pointer. No specialized agents in this foundation.
- Preserve existing architecture and user-facing behavior outside the requested scope. No unsolicited refactor, schema repair, UI change, or dependency upgrade.
- Never commit secrets, production environment files, customer uploads/proofs, generated invoices, logs, caches, sessions, dumps, backups, installed dependencies, or runtime storage. Preserve the sanitized `core/.env.example` and existing `.gitignore` exclusions.

## VERIFIED PROJECT RULE

| Contract to preserve | Code evidence |
|---|---|
| Repository web entry is root `index.php`; Laravel commands run in `core/`. Do not assume `core/public` is the deployed document root. | `index.php`, `core/artisan`, `core/bootstrap/app.php` |
| Preserve legacy Kernel/provider bootstrapping despite Laravel 12. Do not migrate to another application skeleton incidentally. | `core/bootstrap/app.php`, `core/config/app.php` |
| Resolve tenant from its domain before tenant data access. Central and tenant connections are not interchangeable. | `core/config/tenancy.php`, `core/app/Providers/TenancyServiceProvider.php`, `core/app/Models/PaymentLogs.php`, `core/app/Models/ProductOrder.php` |
| Retain route-group guards and contextual middleware when extending an endpoint; validate feature-specific permission enforcement separately. | `core/routes/admin.php`, `core/routes/tenant_admin.php`, `core/app/Http/Kernel.php` |
| Apply server-side validation in the feature's existing FormRequest or controller pattern, not only in JavaScript. | `core/app/Http/Requests/CheckoutFormRequest.php`, `core/Modules/Product/Http/Controllers/ProductController.php` |
| Product persistence fans into `ProductGlobalTrait`; store checkout fans into checkout services. Follow those existing owners. No general repository layer is present. | `core/Modules/Product/Http/Services/Admin/AdminProductServices.php`, `core/Modules/Product/Http/Traits/ProductGlobalTrait.php`, `core/app/Http/Services/ProductCheckoutService.php` |
| Preserve separate central and tenant migrations and the configured migration order; prove a migration's target before running it. | `core/config/tenancy.php`, `core/app/Jobs/TenantMigrateDatabseJob.php` |
| Keep subscription and product-order payment states distinct (`complete` versus `success`); callbacks, commissions and UI consume these exact values. | `core/app/Models/PaymentLogs.php`, `core/app/Models/ProductOrder.php`, `core/app/Http/Controllers/Tenant/Admin/OrderManageController.php` |
| Theme source is `core/themes`; theme view resolution precedes fallback views. Published asset locations are not the editing source. | `core/app/Services/ThemeManager.php`, `core/app/Providers/ThemeServiceProvider.php`, `.gitignore` |
| Preserve both Page Builder data formats and their flags; do not treat similarly named builders as interchangeable. | `core/app/Http/Controllers/CustomPageBuilderController.php`, `core/plugins/PageBuilder/PageBuilderSetup.php`, `core/config/xgpagebuilder.php` |
| Preserve module/provider registration and plugin lifecycle separation. A discovered plugin route does not prove activation or permission. | `core/config/modules.php`, `core/app/PluginSystem/PluginManager.php` |
| Use existing translation calls/catalogs for new translatable UI text; maintain the affected layout's RTL handling. | `core/app/Http/Middleware/SetLang.php`, `core/resources/lang`, `core/app/Helpers/theme-frontend-helpers.php` |
| Keep Vite's current entries and Bootstrap-compatible Tailwind setup. Do not enable Tailwind preflight casually. | `core/vite.config.js`, `core/resources/css/app.css`, `core/resources/js/app.js` |

## RECOMMENDATION — not a claim of existing implementation

- Prefer an actual plugin hook, theme override, event/listener or existing service seam before a shared-core change. If none meets the requirement, explain the necessary core edit.
- Test the smallest affected behavior first; expand only for a justified shared impact. Browser verification is for UI/interaction flows, not a default site audit.
- Never run migrations, seeders, scheduler jobs, or tests against customer databases merely to investigate. Use isolated, explicitly configured test infrastructure.
- Use targeted search, not repeated whole-repository discovery. Skip dependencies, generated outputs and media unless directly relevant. Do not create additional agents for a small local task.
- Add idempotency/transaction or privilege controls only when required by the task; their desirability is not proof that existing flows already implement them.

Evidence labels and maintenance: [knowledge/INDEX.md](knowledge/INDEX.md), [AGENT-BOOTSTRAP.md](AGENT-BOOTSTRAP.md).
