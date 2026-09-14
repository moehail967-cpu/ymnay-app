# Engineering report — Issue #4 store onboarding

## Scope delivered

- Added the central `/create-store` five-step flow in the required order: plan, theme, store details, account/email OTP, review/create.
- Kept plan price, cadence, trial availability and trial days dynamic from the active `PricePlan` record. The review step detects a changed plan snapshot and requires explicit acknowledgement.
- Reused the current OTP registration, tenant creation pipeline, trial ledger action and signed tenant-admin login route.
- Removed username from this flow's customer-facing form. A unique internal username is generated when the shared registration endpoint receives none.
- Added responsive Arabic RTL presentation, keyboard-visible controls, server error surfaces, OTP resend cooldown and indeterminate provisioning feedback.

## Central and tenant boundaries

- `store_onboarding_requests` is a new central table. It keeps the UUID request, optional central user/plan references, selected theme, store name, subdomain, resulting tenant ID, plan snapshot and lifecycle status.
- Password hashes and OTP values remain in the existing short-lived registration session and are not copied into the onboarding record.
- Final creation claims the central user and onboarding rows under locks. It rejects another active trial or concurrent provisioning request, reuses an already-owned tenant/trial during retry, and records `ready` only after the tenant has a domain and login key.
- `TenantRegisterEvent` remains the owner of tenant database creation/migration/seeding/domain setup. After it completes, the trial action updates the central payment/tenant records and the onboarding controller sets the tenant `site_title`.

## Resume and failure behavior

- Guest progress is keyed by the onboarding UUID in the server session and a one-week encrypted, HTTP-only, same-site cookie. The cookie contains no form values or credentials. Registration verification or login binds the request to the central user.
- An authenticated user can recover their latest draft, verified, provisioning, failed or ready request after session expiry.
- A ready request exposes the signed dashboard link again. Selecting a plan from a ready request deliberately starts a new draft instead of overwriting the completed record.
- A provisioning exception records `failed`, a safe request reference and the internal error for operations; the customer sees no stack trace, fake percentage or ETA.

## Files and migration

- Central migration: `core/database/migrations/2026_09_14_000001_create_store_onboarding_requests_table.php`
- Controller/model: `core/app/Http/Controllers/Landlord/Frontend/StoreOnboardingController.php`, `core/app/Models/StoreOnboardingRequest.php`
- Shared authentication changes: `core/app/Http/Controllers/Landlord/Frontend/LandlordFrontendController.php`
- Routes/view: `core/routes/web.php`, `core/resources/views/landlord/frontend/onboarding/*`
- Focused markup test: `core/tests/Unit/StoreOnboardingMarkupTest.php`

No migration was run against any environment and nothing was deployed.

## Verification performed

- Parsed every changed PHP file with the PHP tree-sitter grammar; no syntax error nodes were found.
- Extracted the onboarding JavaScript, replaced server-rendered Blade expressions and passed `node --check`.
- Passed `git diff --check`.
- Reviewed route names, plan/theme helpers, OTP response contract, `TenantRegisterEvent`, `TenantTrialPaymentLog` and the existing HMAC tenant dashboard login contract against source.

The workspace does not provide PHP, Composer dependencies, a database or application runtime, so PHPUnit, Laravel route resolution, migrations, browser behavior and end-to-end tenant provisioning were not executed here. QA should run the central migration in a non-production environment, execute the PHP suite, and test the full happy path plus the cases below.

## QA focus for Salem

1. New visitor: all five steps → OTP → zero-SAR review → store provisioning → direct tenant dashboard.
2. Existing verified user login at step four and resumption after session rotation/expiry.
3. Wrong/expired OTP, five-attempt lock, resend cooldown and throttled endpoints.
4. Duplicate/restricted/invalid subdomain and a subdomain claimed between step three and completion.
5. Plan price/trial change between selection and review; disabled plan or removed theme.
6. User who already consumed a trial, two simultaneous completion requests, and retry after a partially created tenant.
7. Responsive widths, keyboard navigation, Arabic RTL copy and screen-reader announcements.

## Integration note for Sara

The new entry route is named `landlord.store.onboarding` and resolves to `/create-store`. Landing page CTA work can point there without changing the paid `/plan-order/{id}` checkout.
