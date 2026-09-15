# Tenancy

## VERIFIED — database per tenant

Stancl tenancy 3.10.0 uses `App\Models\Tenant` (`HasDatabase`, `HasDomains`) and Stancl Domain. A central database holds accounts/subscriptions/tenant metadata; each store has a separate database. Shared model classes are reused across connections. This is **database-per-tenant**, not a shared products table filtered by `tenant_id`.

Source: `core/composer.lock`, `core/config/tenancy.php`, `core/app/Models/Tenant.php`.

| Concern | Implemented behavior | Source |
|---|---|---|
| Resolution | Normalize leading `www`; bypass configured central hosts; resolve full host through `DomainTenantResolver` | `core/app/Http/Middleware/Tenant/InitializeTenancyByDomainCustomisedMiddleware.php` |
| Central hosts | `CENTRAL_DOMAIN`; central connection derives from `DB_CONNECTION` | `core/config/tenancy.php` |
| Domain data | Unique `domains.domain`, string `tenant_id` foreign key with cascade to tenants | `core/database/migrations/2019_09_15_000020_create_domains_table.php` |
| DB naming | `TENANT_DATABASE_PREFIX` + tenant key + suffix; tenant internal DB credentials may override | `core/config/tenancy.php`, `core/app/Jobs/CreateDatabaseWithFallback.php` |
| Isolation boot | Database, cache, filesystem, queue bootstrappers; direct Redis bootstrap commented out | `core/config/tenancy.php` |
| Filesystem | Tenant-suffixed local/public roots and `storage_path`; tenant-aware asset helper | `core/config/tenancy.php` |
| Failure | Tenant API returns 404 JSON; web redirects to app URL, with loop guard | `core/app/Providers/TenancyServiceProvider.php` |
| Central access inside tenant | `PaymentLogs` explicitly uses CentralConnection; ordinary User/Admin/OrderProducts models do not | `core/app/Models/PaymentLogs.php`, `User.php`, `Admin.php`, `OrderProducts.php` in that directory |
| Explicit tenant model | `ProductOrder` uses TenantConnection | `core/app/Models/ProductOrder.php` |

## VERIFIED — native lifecycle

Tenant creation without a W10 onboarding marker triggers this **synchronous** pipeline (`shouldBeQueued(false)`), even though several classes implement ShouldQueue:

`CreateDatabaseWithFallback → TenantMigrateDatabseJob → TenantCacheClearJob → TenantDomainCreateJob → TenantInformationUpdateJob → TenantSeedDatabaseJob → TenantFileSycnForNewTenant → NewShopCreatedEmailNotificationJob`.

The database job chooses cPanel only when the central option enables it; otherwise calls the Stancl database manager. It is not an automatic retry through both providers. File-sync job dispatches individual copy jobs to `tenant_file_sync` separately.

Tenant migrations use explicit module paths followed by `database/migrations/tenant`. Several module migrations alter tenant-core tables, so configuration order alone is not proof a blank database can be built successfully. Seeding runs `TenantDatabaseSeeder` then theme import. `TenancyEnded` reverts context. Tenant deletion runs `DeleteDatabaseWithFallback` synchronously and can destroy the tenant DB.

Source: `core/app/Providers/TenancyServiceProvider.php`, `core/app/Jobs`, `core/config/tenancy.php`, `core/database/seeders/TenantDatabaseSeeder.php`.

## VERIFIED source — W10 checkpointed onboarding exception

The optional fourth `TenantRegisterEvent` argument identifies a `StoreOnboardingRequest`. `TenantDomainCreate` persists that origin in the new tenant's existing JSON data before the synchronous `TenantCreated` event. Only marked tenants use `StoreOnboardingProvisioner`; native purchase/admin creation retains the pipeline above.

W10 records `running`/`done` checkpoints for database, migrations, domain, seed, login key, store title and file dispatch. Before recording readiness it checks the database connection, required migration repository entries, seeded administrator role, domain, matching login key and title. The original trial action runs afterward inside a central transaction; retries reuse a matching trial without extending its dates. Welcome mail failure is independent of ready state.

A verifier can reconcile a completed side effect whose checkpoint was interrupted. Safe domain/key/title stages can be retried. Uncertain seeding, incomplete interrupted migrations, cPanel user/grant operations or partial file dispatch require inspection rather than blind replay. Legacy unmarked partial tenants are adoptable only when origin/ownership checks pass and the database is absent or has no application rows outside migration bookkeeping. This is not automatic recovery of arbitrary existing tenant data and is not an authorization to repair Production.

Checkpoint writes merge the existing central `tenants.data` under a row lock. Where Stancl virtual attributes coexist with physical columns, login-key and trial writes keep both representations synchronized instead of saving a stale model over them. No additional migration is introduced for these checkpoint fields. Manual tenant context switches are restored in `finally` blocks. The existing child file-copy queue remains asynchronous; dispatch completion is not proof all files were copied.

Source: `core/app/Services/Onboarding/StoreOnboardingProvisioner.php`, `ProvisioningStages.php`, `RecoveryRequired.php`, `OnboardingThemeDemoImporter.php` in that directory; `core/app/Events/TenantRegisterEvent.php`, `core/app/Listeners/TenantDomainCreate.php`, `core/app/Providers/TenancyServiceProvider.php`, `core/database/seeders/TenantDatabaseSeeder.php`. Full application migration/theme/queue/browser verification remains a QA gate; source behavior and isolated fixture checks are not an end-to-end certification.

## VERIFIED — access and domains

- Expiry middleware checks tenant/trial expiry and a qualifying completed/trial payment-log fallback before redirecting. Tenant admin status middleware separately allows active, paid **or** trial states. These are different conditions; do not document one universal suspension flag.
- Custom-domain management lives in separate landlord/tenant controllers and `CustomDomain`; Stancl `domains` still resolves incoming hosts. Domain reseller purchase is another module, not DNS provisioning itself.
- Wildcard subdomain/SSL support is owner-confirmed infrastructure, not proof every custom domain has DNS/certificates installed.

Source: `core/app/Http/Middleware/Tenant/PackageExpireMiddleware.php`, `TenantAccountStatus.php` in the same directory; `core/app/Http/Controllers/Landlord/Admin/CustomDomainController.php`, `core/app/Http/Controllers/Tenant/Admin/CustomDomainController.php`.

## Boundary rules derived from these contracts

Never choose tenant context from a user-submitted ID when domain context already exists. Explicitly scope and restore manual tenancy initialization (prefer `try/finally`). Preserve central connection markers. Do not add shared-table tenant filtering as a substitute for database isolation. Never test creation/deletion against a customer tenant. Queue/context restoration and live schema parity remain [UNKNOWN-002 / UNKNOWN-005](UNKNOWNS.md).
