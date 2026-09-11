# Authentication and authorization

## VERIFIED — actor/guard map

| Actor/context | Entry | Identity / enforcement |
|---|---|---|
| Central account owner | Central `/login`, `/register`, `/register-store`, OTP endpoints in `core/routes/web.php` | `web` session guard, `App\Models\User`; central DB before tenancy |
| Central administrator | Central `/admin`; `/admin-home` | Shared `Landlord/Admin/Auth/AdminLoginController`; `admin` guard, Admin model, central DB |
| Store administrator | Tenant `/admin`; `/admin-home` | Same admin login controller/model, after domain initialization; tenant DB; package/mail/status middleware on admin group |
| Store customer | Tenant auth and `/user-home` routes | Predominantly `web` guard and User model on tenant DB; distinct from central account with same numeric id |
| Mobile/API customer | Authenticated `user/` groups | `auth:sanctum`; public catalog/auth routes coexist |
| Super Admin role | Gate evaluation | `AuthServiceProvider::Gate::before` grants when `hasRole('Super Admin')`; role is DB-context-dependent, not an automatic cross-tenant superuser |

Source: `core/config/auth.php`, `core/app/Models/Admin.php`, `core/app/Models/User.php`, `core/app/Providers/AuthServiceProvider.php`, `core/routes/web.php`, `core/routes/tenant.php`, `core/routes/admin.php`, `core/routes/tenant_admin.php`, `core/routes/api.php`, `core/routes/tenant_api.php`.

`tenant_user` is also configured against `App\Models\TenantUser`; configuration is not proof every storefront route uses that guard. Do not substitute it for the observed `web` guard.

## VERIFIED — permission mechanism, not a complete capability matrix

- Spatie permission middleware aliases: `role`, `permission`, `role_or_permission`; Admin uses `HasRoles`. Provider policy map is empty. No application-wide policy architecture was established.
- Permissions/roles and pivots exist separately in central and tenant schemas. Tenant seeders: `RolePermissionSeed` and `PermissionSyncSeeder`; command: `SyncTenantPermissions`. Names in menus or seeders do not prove a server endpoint enforces them.
- Example: Attributes controllers use named `permission:product-category-*` constraints. ProductController constructor only adds `auth:admin`; Product routes add tenant context and ProductLimitMiddleware for create/clone. Do not claim a uniform CRUD permission matrix from naming conventions.
- Tenant feature entitlement middleware is separate from role permissions and package expiry. Verify each route's concrete middleware/handler.

Source: `core/app/Http/Kernel.php`, `core/config/permission.php`, `core/database/migrations/2022_04_20_100718_create_permission_tables.php`, `core/database/migrations/tenant/2022_04_20_100718_create_permission_tables.php`, `core/database/seeders/Tenant/RolePermissionSeed.php`, `core/database/seeders/Tenant/PermissionSyncSeeder.php`, `core/Modules/Attributes/Http/Controllers/CategoryController.php`, `core/Modules/Product/Http/Controllers/ProductController.php`, `core/Modules/Product/Routes/web.php`.

## VERIFIED — API/security routing

- Central `/api/v1`: RouteServiceProvider domain groups plus `core/routes/api.php`. Tenant `/api/tenant/v1`: tenant API file and explicit tenant/mobile permission middleware.
- HTTP Kernel API group uses Sanctum stateful middleware, bindings, Demo and the customized tenant resolver. Its `throttle:api` line is commented out; registering a 60/min limiter in RouteServiceProvider does **not** mean it is applied globally.
- Plugin API groups mount under `/api/v1/plugins/{plugin-id}` with `api` middleware. Individual plugin callbacks must establish any additional auth/tenant checks.
- Builder APIs use their custom provider/config middleware rather than assuming the generic mobile API contract.

Source: `core/app/Providers/RouteServiceProvider.php`, `core/app/Http/Kernel.php`, `core/app/PluginSystem/PluginManager.php`, `core/app/Providers/CustomPageBuilderServiceProvider.php`, `core/config/xgpagebuilder.php`.

**UNKNOWN:** complete role grants, route-level coverage, guard compatibility of the Super Admin callback, and live token policies. See UNKNOWN-003 and UNKNOWN-011. A capability matrix claiming all admins/owners can perform every operation would be unsupported.
