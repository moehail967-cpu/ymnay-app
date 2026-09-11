# Testing

## VERIFIED — current coverage and setup

- PHPUnit 11.5.55 (lock); root test suites in `core/phpunit.xml`. Unit: `ExampleTest.php`, `DashboardMarkupTest.php`. Feature: `ExampleTest.php` GET `/` expects 200. Module Test directories are mostly placeholders, not broad coverage.
- DashboardMarkupTest is a **static Blade markup** regression: balanced forms, hidden legacy container, expected form ID. It is not a browser, checkout, permissions or database test.
- `core/tests/CreatesApplication.php` boots the console kernel. `core/tests/TestCase.php` does not create a database fixture. Feature ExampleTest imports RefreshDatabase but does not use the trait.
- Test config selects array mail/cache/session and sync queue, but SQLite DB overrides are commented out. Never assume it cannot connect to a configured production database.
- `core/database/factories/UserFactory.php` exists. TenantDatabaseSeeder builds tenant/demo content; default DatabaseSeeder is not evidence of a complete central-site fixture.
- No tracked Playwright/Cypress/Pest suite or CI workflow was found. Historical notes reference `PackageExpireCommandTest.php`, which is absent from this baseline.

Source: `core/composer.lock`, `core/phpunit.xml`, `core/tests`, `core/database/factories`, `core/database/seeders/DatabaseSeeder.php`, `core/database/seeders/TenantDatabaseSeeder.php`.

## Verification scope

The approved clean-main preparation passed normal `npm ci`, `npm run build`, Composer validation/platform checks, console boot, two Unit tests and 67 targeted PHP syntax checks. This is a prior point-in-time result for the baseline, not a claim that this documentation task reran every application check or that all user flows pass.

Known fresh-test limitation: empty SQLite cannot serve a configured homepage/languages. The NewsLetter migration `core/Modules/NewsLetter/Database/Migrations/2020_02_04_010636_create_newsletters_table.php` returns a class without `extends Migration`; a blank-schema install needs separate investigation. This foundation documents it, not repairs it.

## Task-sized checks (recommendation)

| Change | First check |
|---|---|
| PHP local fix | `php -l path/to/file.php`, then targeted PHPUnit case |
| Dashboard legacy form | `php vendor/bin/phpunit tests/Unit/DashboardMarkupTest.php` |
| Shared isolated utility | `php vendor/bin/phpunit --testsuite Unit` |
| Specific test | `php vendor/bin/phpunit --filter test_method_name` |
| Frontend build/dependency | `npm ci` then `npm run build` |
| Tenant/auth/order change | Safe database fixture and explicit actor/context tests; do not substitute a markup check |
| Knowledge-only change | Resolve source links/symbols; parse five YAML manifests; compare inventory/IDs; scan secrets; ensure no application diff |

For DB-backed execution, explicitly supply isolated DB connection/credentials, a throwaway APP_KEY, array mail/cache/session and safe queue settings; deny outbound network where practical. Do not copy `.env` from Production. Do not run scheduler, migrations or seeds on live data as a test setup.

Browser rules: use it when UI, navigation, JavaScript, responsive behavior or an actual user flow needs verification. Test only the requested flow; stop at the first material blocker; after a fix repeat that flow. A documentation-only change does not justify browsing/admin mutations or a full site audit.
