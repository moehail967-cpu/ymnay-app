# Infrastructure

## OWNER-CONFIRMED — supplied 2026-09-12

Production web root: `/home/ymnay/htdocs/ymnay.com`; Laravel root: `/home/ymnay/htdocs/ymnay.com/core`. This foundation does not deploy or alter that checkout.

| Capability | Owner-confirmed value | Evidence limitation |
|---|---|---|
| PHP / DB | PHP 8.4.24; MySQL 8.4.10-10; InnoDB | No live DB inspection performed for this foundation |
| Hosting | DB creation, wildcard subdomains, wildcard SSL, cron supported | Availability does not prove application configuration or successful provisioning |
| PHP settings | memory 768M; execution 600s; upload 512M; POST 256M; display_errors Off | Owner-supplied web/runtime values; CLI may differ; POST limit is lower than upload limit |
| Extensions | redis, memcached, PDO/pdo_mysql/mysqli, curl, openssl, mbstring, intl, imagick, gd, sockets, sodium, xml, zip, OPcache | Installed capability only, not proof each is used |

## OWNER-CONFIRMED — Production SSH connection profile — 2026-09-16

This section records non-secret connection metadata only. It does not prove that every execution environment can reach the server or possesses the matching private key.

| Field | Value | Operational boundary |
|---|---|---|
| SSH alias | `ymnay-production` | Convenience alias; the active runtime must have its own secure SSH configuration |
| Host | `148.230.114.69` | Production VPS |
| Port | `22` | Direct outbound TCP/22 is blocked in the current ChatGPT cloud runtime |
| User | `root` | Administrative account; Adam/Nour/Salem are limited by role policy to read actions, while only Omar may perform approved live changes |
| Authorized key identity | `codex-ymnay-deploy-2026-09-16` | Public fingerprint: `SHA256:UrphjzYtIVL6DV898caxiqBNuXttrTHJklXv5EEscJs` |
| Web root | `/home/ymnay/htdocs/ymnay.com` | Root HTTP entry remains the repository root `index.php` |
| Laravel root | `/home/ymnay/htdocs/ymnay.com/core` | Run Laravel commands from this directory only when specifically authorized |
| Health URL | `https://ymnay.com/` | Public post-deployment health target |
| Current verified deployment channel | `.github/workflows/deploy-production.yml` | Manual GitHub Actions workflow; successful run `35156339983` deployed commit `e1a4e6ebc522f2e8f2961424222d0c4803c04add` |

The private key, passwords, tokens, and Production environment values must remain outside Git and `.ai/`. Before claiming direct SSH access, perform a read-only connection check from the active runtime. Follow `../deployment/READ-ONLY-PRODUCTION-SSH.md` for Adam/Nour/Salem and `../deployment/OMAR-DIRECT-SSH.md` for Omar's approved live changes; connection metadata alone is not deployment authorization.

## OWNER-CONFIRMED — team read-only inspection decision — 2026-09-17

The owner permits `@Adam`, `@Nour`, and `@Salem` to use the existing general SSH connection for task-scoped read actions on live project files and relevant errors/logs. Adam records product requirements and acceptance criteria for Nour; Nour designs the interface and obtains owner acceptance of the specific artifacts before handing them with Adam's plan to Omar; Salem reviews the implemented candidate. Omar alone builds application code and may modify or deploy to the live server under existing owner approval gates. Source: owner clarification recorded in GitHub Issue #24 and `../deployment/READ-ONLY-PRODUCTION-SSH.md`.

**Connection status:** the existing general SSH connection was verified from one desktop session on 2026-09-17; reachability from a future agent session is not guaranteed. The account may be write-capable, but Adam, Nour, and Salem are authorized only to read. Verify connectivity from the active runtime before claiming live inspection, and never store credentials in this repository.

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

Node 22.23.2/npm 10.9.8 successfully built the approved baseline in isolated preparation; no repository `engines` or team-pinned Node policy was found. Lock uses Vite 6.4.1/plugin-vue 5.2.4. A tracked manual Production deployment workflow now exists at `.github/workflows/deploy-production.yml` and was verified by successful run `35156339983`. Deployment still requires the release gate and explicit owner authorization in `.ai/deployment/README.md`; migrations, `.env` changes, rollback, cache clearing beyond the approved workflow, and service restarts remain separately scoped operations. Direct SSH reachability depends on the active runtime's network and secure key availability.
