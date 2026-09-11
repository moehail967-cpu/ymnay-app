# System

## VERIFIED — source identity

| Aspect | Current fact | Source |
|---|---|---|
| Purpose/type | Nazmart-derived Ymnay SaaS: central site sells store subscriptions; tenant sites sell products | `core/routes/web.php`, `core/routes/tenant.php`, `core/app/Models/Tenant.php` |
| Actors | Central administrators, central account/store owners, tenant administrators, storefront customers, guests, external payment callbacks | `core/config/auth.php`, route files, `core/app/Models/Admin.php`, `core/app/Models/User.php` |
| Backend | Laravel 12.61.0; Composer PHP requirement `^8.3`; retained Kernel-based skeleton | `core/composer.lock`, `core/composer.json`, `core/bootstrap/app.php` |
| Frontend | Blade; jQuery/Bootstrap assets; selected Vue 3 screens; Vite 6.4.1 and plugin-vue 5.2.4; Tailwind utilities without preflight | `core/package-lock.json`, `core/resources/js/app.js`, `core/resources/css/app.css`, `core/resources/views` |
| Boundaries | Central subscription/control plane; domain-initialized tenant commerce databases; shared PHP implementation; modules/plugins; theme rendering | `core/config/tenancy.php`, `core/app/Providers`, `core/Modules`, `core/themes` |
| Entrypoint | Root web entry boots `core/`; repository is not just a Laravel `public/` folder | `index.php`, `.htaccess`, `core/bootstrap/app.php` |

## INFERRED — subsystem grouping

Central identity/subscriptions/provisioning; tenant commerce/catalog/orders; content/themes/builders; plugin/integration administration; scheduled operations. These are existing execution responsibilities, not new services or a proposed layer split.

## Canonical scope

Owner-designated canonical branch is `main`; source baseline is the SHA in [INDEX](INDEX.md). The prior custom multi-wallet module and multi-page order wizard are absent. Native `Wallet` is present and must not be confused with removed `YmnayCustom` behavior. `/plan-order` redirects to `/pricing-plan`; `/plan-order/{id}` uses native ordering. Source: `core/routes/web.php`, `core/Modules/Wallet`, module inventory.

Production runtime specifications supplied by the owner, actual-config uncertainty and runtime exclusions belong in [INFRASTRUCTURE](INFRASTRUCTURE.md), not inferred from package availability.
