# Background processing

## VERIFIED — schedule declarations, not proof of running cron

Source: `core/app/Console/Kernel.php`. Actual host crontab/worker supervision and effective queue driver are UNKNOWN-001/005.

| Invocation | Cadence / behavior |
|---|---|
| PluginScheduler::scheduleAll | Adds active plugin-declared schedules |
| theme:check-updates | Daily; theme update discovery |
| package:expire | Daily; expiry notices, not a universal suspension job |
| campaigns:suggest --days=60 --min-stock=10 | Weekly suggestions |
| account:remove | Daily; inspected handler sends notices, does not itself delete accounts |
| package:auto-renew | Daily; wallet package renewal logic |
| queue:work --timeout=60 --tries=1 --once | Every minute, without overlap, default configured connection |
| queue:work tenant_file_sync --timeout=60 --tries=1 --once | Every minute, without overlap, named connection |
| telescope:prune | Every minute only in local environment |

Sources for handler behavior: `core/app/Console/Commands`, `core/Modules/Wallet`, `core/app/PluginSystem/PluginScheduler.php`. A command name is not its business specification.

## VERIFIED — queues and synchronization

- `core/config/queue.php`: default QUEUE_CONNECTION fallback `sync`; database connection uses jobs; tenant_file_sync is a **connection name**, database driver/file_sync_jobs table on mysql; failed_jobs configured separately. Redis support is configuration capability, not evidence it runs.
- TenantCreated provisioning is explicitly `shouldBeQueued(false)` (TENANCY). The parent `TenantFileSycnForNewTenant` dispatches `TenanFileCopyFromCloudForNewTenant` on tenant_file_sync with a short delay; parent completion does not certify files arrived.
- Stancl queue bootstrapper restores tenant context for supported dispatched work. Manual jobs that initialize tenants need explicit cleanup; don't presume every plugin job is context-safe.
- `core/Modules/TrackingPixel/Jobs/SendServerConversionJob.php` declares tries/backoff but catches provider errors; swallowed failures cannot be assumed to retry. Scheduled workers request one attempt; assess effective behavior at the dispatch/worker boundary.
- `core/Modules/FeedSync/Jobs/RegenerateFeedJob.php` handles feed regeneration. Other module jobs/events are inventoried in modules.yaml, including those with no async worker guarantee.

Sources: config/queue.php, TenancyServiceProvider, the named job files under `core/app/Jobs`, and the named module job files.

## VERIFIED — events and observers

| Source | Consumers / consequences |
|---|---|
| Registered | Framework email verification listener |
| SupportMessage | Admin/user support mail listeners |
| TenantRegisterEvent | TenantDomainCreate; tenant lifecycle then provisioning pipeline |
| CommissionManage OrderCompleted | CreateCommissionRecord |
| User / ProductOrder / ProductInventory / Product observers | Tenant registration/order handling, stock notification, product-price reactions |
| WebhookEventFire | Wallet, user and subscription webhook listeners |

Sources: `core/app/Providers/EventServiceProvider.php`, `core/Modules/WebHook/Providers/EventServiceProvider.php`, `core/Modules/WebHook/Listeners`. Registration alone does not prove every event is emitted on every workflow branch; follow dispatch sites.

## Operations boundary

Runtime log destinations include queue-jobs.log and new-website-file-sync-jobs.log under storage/logs; tenant context may suffix storage paths. Inspect failed jobs and relevant logs without printing customer payloads/tokens. Do not invoke schedule:run, queue workers, tenant deletion, renewal or seed commands on Production as diagnostic probes. Safe operational commands/settings are in INFRASTRUCTURE.
