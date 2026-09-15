# Engineering report — Issue #4 store onboarding

## Scope delivered

- Replaced the central source landing with the owner-approved L01–L08 structure: official navigation, hero, site/store choices, real theme previews, administration explanation, five-step explanation, dynamic pricing, FAQ/final CTA and official footer.
- Read active plans with their current prices, cadence, limits, features, badge and trial data. Each plan CTA posts the selected plan into `/create-store`; no price, trial duration or popularity label is invented in the landing.
- Read published themes from the current ThemeManager registry and use their actual screenshots, custom names and configured preview URLs. Preview dialogs have desktop/mobile modes and do not select a theme or start payment.
- Added the central `/create-store` five-step flow in the required order: plan, theme, store details, account/email OTP, review/create.
- Kept plan price, cadence, trial availability and trial days dynamic from the active `PricePlan` record. The review step detects a changed plan snapshot and requires explicit acknowledgement.
- Reused the current OTP registration, tenant creation pipeline, trial ledger action and signed tenant-admin login route.
- Removed username from this flow's customer-facing form. A unique internal username is generated when the shared registration endpoint receives none.
- Added responsive Arabic RTL presentation, keyboard-visible controls, a mobile step counter/collapsible summary, debounced subdomain feedback with final server validation, password reveal controls, accessible theme previews, server error surfaces, OTP resend cooldown and indeterminate provisioning feedback.

## Nour design reference review

All four reference images from design commit `dfc56d81afb448a8432675d64d22c16340502164` were opened at original resolution before detailed implementation. They remain references and were not copied into public assets.

- `01-landing-desktop.png` — SHA-256 `ac57ea4a17d97344402a595c0973a309231dd334242158dedc7803c76432e541`: established the desktop L01–L08 hierarchy and restrained indigo visual language.
- `02-landing-mobile.png` — SHA-256 `53552cac313e4c8873df233f753ca64f4fc171b30a23247520f8b029c0e30f70`: established single-column stacking, 16px margins and mobile CTA behavior.
- `03-onboarding-desktop.png` — SHA-256 `65b93c0fa988f9d02f12c33c96947971539906380b8442924920a91b10f1043e`: established full-width selection grids followed by the form/summary split.
- `04-onboarding-mobile.png` — SHA-256 `6fd2841b9faa189c0b5cd34504086c5c196b8acf45aca3b71dc5d5c8c1df043c`: established the five-marker progress pattern, compact summary and provisioning/success states.

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
- Landing source/assets: `core/resources/views/landlord/frontend/frontend-home.blade.php`, `core/resources/views/landlord/frontend/partials/ymnay-*`, `core/public/assets/new-landlord/{css,js}/ymnay-public.*`
- Focused markup tests: `core/tests/Unit/StoreOnboardingMarkupTest.php`, `core/tests/Unit/LandingPageMarkupTest.php`

No migration was run against any environment and nothing was deployed.

## Verification performed

- The original onboarding change set was parsed with the PHP tree-sitter grammar before this visual expansion; no syntax error nodes were found in that pass.
- Extracted the onboarding JavaScript, replaced server-rendered Blade expressions and passed `node --check`.
- Passed `node --check` for the shared landing/navigation/dialog JavaScript.
- Passed static assertions for L01–L08 order, runtime plan/theme markers, onboarding controls, balanced CSS braces and unique static element IDs.
- Passed `git diff --check`.
- Reviewed route names, plan/theme helpers, OTP response contract, `TenantRegisterEvent`, `TenantTrialPaymentLog` and the existing HMAC tenant dashboard login contract against source.

The workspace does not provide PHP, Composer dependencies, a database or application runtime, so PHPUnit, Laravel route resolution, migrations and end-to-end tenant provisioning were not executed here. Playwright is installed but its browser executable is absent, so actual rendered desktop/mobile screenshots could not be captured in this workspace. QA should run the central migration in a non-production environment, execute the PHP suite, capture the requested viewports and test the full happy path plus the cases below.

## QA focus for Salem

1. Landing at 360/390/768/1024/1440 widths: L01–L08 order, real logo/theme images, no horizontal scroll, FAQ/menu/dialog keyboard behavior and plan-to-wizard preselection.
2. New visitor: all five steps → OTP → zero-current-charge review → store provisioning → direct tenant dashboard.
3. Existing verified user login at step four and resumption after session rotation/expiry.
4. Wrong/expired OTP, five-attempt lock, resend cooldown and throttled endpoints.
5. Duplicate/restricted/invalid subdomain and a subdomain claimed between step three and completion.
6. Plan price/trial change between selection and review; disabled plan or removed theme.
7. User who already consumed a trial, two simultaneous completion requests, and retry after a partially created tenant.
8. Keyboard navigation, Arabic RTL copy, zoom at 200%, reduced motion and screen-reader announcements.

## Handoff contract

The entry route is named `landlord.store.onboarding` and resolves to `/create-store`. The official central landing and onboarding are now one Omar-owned source implementation on PR #5. Paid `/plan-order/{id}` behavior remains separate and unchanged. No Production migration or deployment was performed.
