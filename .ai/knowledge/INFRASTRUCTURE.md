# Infrastructure

## OWNER-CONFIRMED — supplied 2026-09-12

Production web root: `/home/ymnay/htdocs/ymnay.com`; Laravel root: `/home/ymnay/htdocs/ymnay.com/core`. This foundation does not deploy or alter that checkout.

| Capability | Owner-confirmed value | Evidence limitation |
|---|---|---|
| PHP / DB | PHP 8.4.24; MySQL 8.4.10-10; InnoDB | No live DB inspection performed for this foundation |
| Hosting | DB creation, wildcard subdomains, wildcard SSL, cron supported | Availability does not prove application configuration or successful provisioning |
| PHP settings | memory 768M; execution 600s; upload 512M; POST 256M; display_errors Off | Owner-supplied web/runtime values; CLI may differ; POST limit is lower than upload limit |
| Extensions | redis, memcached, PDO/pdo_mysql/mysqli, curl, openssl, mbstring, intl, imagick, gd, sockets, sodium, xml, zip, OPcache | Installed capability only, not proof each is used |

## VERIFIED — source/lock configuration

| Concern | Actual source contract | Effective use |
|---|---|---|
| Framework | Laravel 12.61.0 in `core/composer.lock`; PHP `^8.3` in composer.json | PHP 8.4.24 boot passed during clean-main preparation; not a full HTTP certification |
| Database | MySQL default in `core/config/database.php`; central + tenant DB bootstrapping | Database-per-tenant proven in code; live schema parity UNKNOWN |
| Cache | `CACHE_DRIVER`, default file; Redis and memcached configured alternatives | Live driver UNKNOWN; direct Redis tenancy bootstrap disabled |
| Sessions | `SESSION_DRIVER`, default file, tenant-suffixed storage; same_site lax | Live cookie/domain/secure settings UNKNOWN |
| Queues | `QUEUE_CONNECTION`, default sync; named database/file-sync connections | Actual worker configuration UNKNOWN; scheduler invokes one-shot workers |
| Storage | `FILESYSTEM_DRIVER`, default local; named landlord/tenant media disks, S3/Wasabi/R2 alternatives | TenantConfigMiddleware can override default/credentials from DB options; actual provider UNKNOWN |
| Mail | `core/config/mail.php`, environment options and per-tenant DB SMTP overrides | Actual transport/delivery UNKNOWN |
| HTTP entry | Root `index.php`, `.htaccess`, `web.config`; Laravel under core | Server vhost/proxy config not versioned here |
| Assets | Vite builds to root `_build`; themes publish to `core/public/themes` | Outputs excluded by `.gitignore`; bundled public libraries are not PHP vendor dependencies |

Sources: `core/config/database.php`, `cache.php`, `session.php`, `queue.php`, `filesystems.php`, `mail.php` in the same directory; `core/app/Http/Middleware/Tenant/TenantConfigMiddleware.php`, `core/config/tenancy.php`, `core/app/Console/Kernel.php`, `core/vite.config.js`.

## Verified commands / operating boundaries

Run from `core/` in an authorized non-production environment:

| Purpose | Command | Caveat |
|---|---|---|
| Dependencies | `composer install` | Lifecycle scripts boot/discover/publish assets; use `--no-scripts --no-plugins` for a deliberately inert install, not as a deployment replacement |
| Metadata/platform | `composer validate --no-check-publish`; `composer check-platform-reqs` | Existing exact-version/deprecation/PSR-4 warnings are not automatically a nonzero exit |
| Frontend | `npm ci`; `npm run build`; `npm run dev` | Vite dev host is `nazmart.test`; no legacy peer-deps needed at baseline |
| Console boot | `php artisan --version` | Does not prove database-backed pages work |
| Tests | `php vendor/bin/phpunit tests/Unit/DashboardMarkupTest.php` | See TESTING for isolated DB setup |
| Central schema | `php artisan migrate` | Mutating; not a tenant migration |
| Tenant schema | `php artisan tenants:migrate --tenants=TENANT_ID` | Mutating; configured force/path options; requires explicit target/safe environment |
| Tenant seeds | `php artisan tenants:seed --tenants=TENANT_ID` | Mutating demo/accounts/settings; never a diagnostic on live data |
| Worker/scheduler | `php artisan queue:work`; `php artisan schedule:run` | Can send mail, charge wallets, change data; not a read-only health check |

Node 22.23.2/npm 10.9.8 successfully built the approved baseline in isolated preparation; no repository `engines` or team-pinned Node policy was found. Lock uses Vite 6.4.1/plugin-vue 5.2.4. No tracked CI/deployment workflow or production supervisor configuration was established. Deployment, cache clearing and worker restart need a separate scoped task, not automatic execution from these examples.
