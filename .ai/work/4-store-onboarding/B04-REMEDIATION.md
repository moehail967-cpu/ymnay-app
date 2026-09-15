# ENGINEERING HANDOFF — B04 remediation

Engineer: `@Omar`  
Date: 2026-09-15  
Issue: [#4](https://github.com/moehail967-cpu/ymnay-app/issues/4)  
PR: [#5](https://github.com/moehail967-cpu/ymnay-app/pull/5)  
Branch: `feat/4-store-onboarding`  
Tested application/test head: `3743871950e8d7bd7fa0107283d2beec6ad32517`

## Correction

B04 affected an authenticated existing central account that was still unverified and owned a saved onboarding request. When the optional legacy `user_email_verify_status` flag was empty, the generic `/verify-email` entry redirected toward onboarding step five without rendering a verification form; onboarding correctly clamped the unverified account back to step four.

The onboarding flow now owns dedicated verification endpoints under `/create-store/verify-email`. They require the central `web` guard and a saved request owned by the authenticated user. They remain available for that mandatory onboarding gate independently of the optional legacy flag, while the generic verification route and its policy outside onboarding are unchanged.

The dedicated flow:

- renders the existing verification view with onboarding-specific submit/resend URLs;
- creates and sends a code when the eligible unverified account has no current token;
- rate-limits verification submissions and resends at the route;
- validates the stored token under central user/request row locks, using the same lock order as completion;
- clears the used token, marks a draft request `account_verified`, preserves the request ID, and returns to step five;
- rejects guests, absent/foreign requests, invalid codes, and requests whose creation has already progressed;
- does not create a tenant, grant an unverified account access, or change legacy verification policy outside onboarding.

No schema change or migration was added for B04.

## Verification evidence

[GitHub Actions run 35005707364](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35005707364), [job 104504796811](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35005707364/job/104504796811): **completed / success**.

- PR head: `3743871950e8d7bd7fa0107283d2beec6ad32517`.
- Synthetic test merge: `b024229932b4ab59b2e29d04056edf7bd43cb599`; this is not a merge into `main`.
- PHP syntax, including `core/routes/web.php` and the changed controller: PASS.
- PHPUnit integration fixture: **28 tests / 182 assertions / 0 failures / 0 errors / 0 skipped**.
- Durable provisioning-stage regression: **12 PASS / 0 FAIL**.
- Evidence artifact: [10412120263](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35005707364/artifacts/10412120263), `onboarding-regression-b024229932b4ab59b2e29d04056edf7bd43cb599`.
- Downloaded ZIP SHA-256 matched GitHub: `8f70264f1b1cd7899a05471b4f694c4c39ddde38b211ddb056f186618b4bf9bb`.

New regression coverage executes the changed `StoreOnboardingController` against Laravel components and MySQL. It covers both values of the legacy flag, initial code delivery, valid and invalid submission, resend/token rotation, verified-account redirect, guest/missing/foreign request denial, route/view wiring, preserved session request, and the existing final unverified-account denial. The previous B01–B03 and provisioning regressions remain green.

## Scope boundary and QA request

The CI fixture uses real candidate controller/models, Laravel validation/transactions, Stancl switching and MySQL, with explicit test adapters for auth lookup, theme data, tenant migration/seed content, file work and mail delivery. It does not certify live mail delivery, full application/browser HTTP execution, OTP expiry behavior, every session/cookie recovery path, actual module/theme provisioning, dashboard token login, queue completion, or Desktop/Mobile/RTL visual conformance.

`@Salem` should independently retest B04 on the exact new candidate, including the real route/middleware/session behavior and adjacent legacy no-onboarding path, while retaining the green B01–B03 regression coverage and the remaining acceptance gates from the prior QA report.

No merge, Production deployment, Production migration, service restart, customer-data operation, or rollback was performed or authorized.
