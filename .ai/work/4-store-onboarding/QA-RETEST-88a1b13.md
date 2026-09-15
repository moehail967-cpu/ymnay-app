# QA RETEST — Issue #4 / PR #5 / 88a1b136

Reviewer: `@Salem`  
Date: 2026-09-15  
Overall verdict: **FAIL**  
Next owner: `@Omar` for B04, then `@Salem` for retest and outstanding full-application verification.  
No merge or Production authorization.

## Exact target and provenance

- Reviewed handoff head: `88a1b1368b82406cafccb6b8309938494a847e5e`, branch `feat/4-store-onboarding`, [PR #5](https://github.com/moehail967-cpu/ymnay-app/pull/5).
- Tested application/test/workflow head: `9f9ce6436152a7147426ecf563dec0cd339de522`.
- GitHub compare was checked independently: the handoff head is exactly one commit ahead, with only `.ai/work/4-store-onboarding/QA-REMEDIATION.md` added. Application, tests and workflow are unchanged between these two revisions.
- Historical QA FAIL and its original frozen harness belong to `b7d0a52ef23e6b1620509c49e62f1ef86f4bf70b`; they have not been rewritten or silently applied to the new source.
- This report and its reproducer are on separate branch `qa/4-salem-88a1b13`, based on the handoff head. No application source, main, or implementation branch was changed by Salem.

## Work actually performed

1. Read the current Issue control block, remediation handoff, candidate controller, provisioning service, relevant authentication method/routes/view, integration tests and their bootstrap/workflow. Reviewed the relevant remediation diff and source fingerprints.
2. Independently requested a fresh execution of the existing isolated GitHub Actions regression job. This is a new execution of the engineer-authored tests, not a claim that Salem authored those tests.
3. Verified the new job completed successfully, downloaded its new artifact, checked the ZIP digest against GitHub, and parsed its JUnit and stage JSON locally.
4. Independently traced a previously uncovered existing-account verification path and executed the verbatim relevant method excerpts in a local PHP control-flow reproducer.

Local workspace: PHP 8.4.23, Node and Chromium are available; no PDO drivers or Composer-installed complete Laravel runtime is configured. Direct source-download DNS resolution failed. The GitHub connector supplied source and CI evidence. Production was not used for tests. No actual application/browser screenshots, live mail, real payment, customer data, full application boot or Production database operation was used.

## Fresh regression evidence

[Run 34921075207, new job 104233431067](https://github.com/moehail967-cpu/ymnay-app/actions/runs/34921075207/job/104233431067): **completed / success**.

[New artifact 10378817046](https://github.com/moehail967-cpu/ymnay-app/actions/runs/34921075207/artifacts/10378817046), created 2026-09-15T02:48:19Z. Downloaded ZIP SHA-256 matched GitHub: `20240c3f6d86735553db0fcf8582755698b3def3749272e5f1574bbb0a748a2f`.

The job used synthetic PR merge `5985eb4af0cb2be38e20cab6f152140fa085bc29` with head `9f9ce6436152a7147426ecf563dec0cd339de522`. This is test infrastructure, not a merge into main.

| Check | Fresh result | Actual scope |
|---|---|---|
| PHP syntax step | PASS | Changed PHP files selected by the workflow |
| PHPUnit integration fixture | **21 tests / 147 assertions / 0 failures / 0 errors / 0 skipped** | Actual candidate controller/provisioner/models with Laravel/Stancl and MySQL, with explicit adapters below |
| Durable stage decisions | **12 PASS / 0 FAIL** | Actual ProvisioningStages class with in-memory checkpoint persistence |
| Independent verification-gate reproducer | **4 PASS / 1 FAIL**, expected exit code 1 | Verbatim method excerpts and dependency doubles; NOT HTTP, full class loading, Laravel or E2E |

Artifacts parsed: `onboarding-junit.xml`, `stage-results.json`, `dependency-audit.json`. A machine-readable summary and the independent reproducer are under `qa-retest-88a1b13/` beside this report.

### Important test limitations

The integration bootstrap explicitly substitutes auth lookup, cookies, theme data, tenant schema/migration/seed CONTENT, file dispatch and mail delivery. Only its configured framework providers are booted; the complete application is not. Its migration fixture executes the actual new central onboarding migration, not every application/module migration. The MySQL stale-tab case is a genuine two-process row-lock test; it is not a full parallel HTTP completion/address-race test. The generated dashboard URL is not a successful browser token-login. Only database tenancy bootstrapping is configured in the fixture; full filesystem/cache/queue isolation remains unverified.

These limits were checked in `tests/bootstrap.php` and the actual test methods, not inferred from a green status.

## Original blockers: independent retest disposition

| Finding | Disposition on new candidate |
|---|---|
| B01 — earlier-step mutations overwrite provisioning/ready requests | **PASS for the original regression.** Fresh tests reject plan, theme, details and acknowledgement writes for both states without changing attributes or request count. The real MySQL stale-tab writer waits on the lock and is rejected after provisioning is claimed. |
| B02 — retry skips missing domain/key provisioning | **PASS for the reproduced domain/key/legacy-unseeded recovery cases.** Fresh tests recover the missing stages, preserve one trial and do not repeat completed seed/database work. Uncertain partial seeding is deliberately blocked for reviewed recovery. This does not certify actual module/theme import, hard-process interruption recovery or full dashboard readiness. |
| B03 — final creation omits complete/current saved-data validation | **PASS for the original regression.** Missing/malformed data, newly forbidden addresses and an address owned by another account are rejected without new tenant/trial effects in the fixture. |

The original reproduced defects are not being sent back as still failing. Remaining full-application release gates are separate. This cycle fails because of new B04 below and does not grant whole-feature acceptance.

## B04 — Existing unverified account cannot reach verification when the general verification flag is disabled

Severity: **BLOCKER / P1**, conditional on the documented configuration/account state.  
Owner: `@Omar`.  
Discovery: independent source review plus local method-excerpt execution. Not observed against Production; the current Production flag has not been inspected.

### Required behavior

Issue #4 explicitly requires an existing unverified account to complete email verification before step five, and prevents store creation before verification. Disabling the general legacy email-verification setting must not leave the new onboarding flow unable to satisfy its own mandatory verification gate. Preserve approved behavior outside onboarding; do not solve this by falsely marking an account verified or bypassing OTP.

### Actual control flow

For an authenticated account with `email_verified = 0`, a complete plan/theme/store selection, a bound onboarding request in the session, and an empty/disabled `user_email_verify_status`:

1. `StoreOnboardingController::maxStep()` limits the wizard to step four.
2. `store-setup.blade.php` offers the `landlord.user.email.verify` link to `/verify-email`.
3. `LandlordFrontendController::verify_user_email()` sees the disabled general flag and redirects to onboarding with `step = 5`, without returning the verification view.
4. The wizard limits that request to step four again. Clicking the verification link repeats the same dead end. The final creation endpoint still correctly rejects the unverified account.

This is a repeated **user-action loop**, not a claimed automatic HTTP redirect loop. It prevents the affected user from reaching the verification form through the provided flow. It is not proof all users or the current Production configuration are affected.

### Reproduction and results

Local command:

```sh
php .ai/work/4-store-onboarding/qa-retest-88a1b13/verification-gate-repro.php
```

The reproducer executes verbatim `verify_user_email()` and `maxStep()` excerpts with explicit auth/config/session/view/redirect doubles. It is frozen historical control-flow evidence, not a test that automatically reads future application edits. The wizard clamp and next-link trace correspond to the inspected candidate source/view.

Five cases ran on PHP 8.4.23: guest, verified onboarding, unverified onboarding with flag enabled, unverified onboarding with flag disabled, and unverified account outside onboarding with flag disabled. Four controls passed; the onboarding/disabled-flag case failed. Three simulated verification clicks each requested step five but returned to rendered step four. No mail or database effects were run.

Sources at the reviewed head:
- [LandlordFrontendController](https://github.com/moehail967-cpu/ymnay-app/blob/88a1b1368b82406cafccb6b8309938494a847e5e/core/app/Http/Controllers/Landlord/Frontend/LandlordFrontendController.php#L120-L138), blob `ba1b4afbafed30f0bcd5b4ee24b4259137235e7b`.
- [StoreOnboardingController](https://github.com/moehail967-cpu/ymnay-app/blob/88a1b1368b82406cafccb6b8309938494a847e5e/core/app/Http/Controllers/Landlord/Frontend/StoreOnboardingController.php), `index`, `maxStep`, final verification check; blob `0044044738d14d0cfa73235cb97fd8245d02b378`.
- [Step-four view](https://github.com/moehail967-cpu/ymnay-app/blob/88a1b1368b82406cafccb6b8309938494a847e5e/core/resources/views/landlord/frontend/onboarding/store-setup.blade.php#L115-L126), blob `d0344575209814575469190b99146f78c1b32498`.
- [Registered verification route](https://github.com/moehail967-cpu/ymnay-app/blob/88a1b1368b82406cafccb6b8309938494a847e5e/core/routes/web.php#L117-L137).

### Exact correction/retest expectation

Make mandatory onboarding verification reachable for this account state independently of the general legacy flag, without changing unrelated verification policy or granting unverified access. Add a regression exercising current application code and the actual route/view, not a hardcoded repaired excerpt. Test both global flag values, verified/unverified and guest users, no-onboarding behavior, actual code submission/resend and safe return to the preserved request. Only a verified account may reach final creation.

## Material acceptance still unverified

- Full Laravel application boot, route/middleware behavior and full project PHPUnit suite.
- Actual tenant module migrations, real theme import/admin readiness, native paid/admin creation regression and queue completion.
- HTTP OTP/resend/expiry/rate limits, cookie/session/password-recovery resumption and browser token-login.
- Full parallel completion and cross-account address races in the application; broader tenant filesystem/cache/queue isolation.
- Actual landing/onboarding desktop/mobile/RTL views and comparison with Nour's four references. No visual PASS or actual application screenshots are claimed.

The green fixture does not remove these gates. The focused review stopped after establishing the new material account-flow blocker; unexecuted checks have not been marked passed.

## Separate dependency observation

The fresh artifact contains 22 advisory entries across four existing packages: guzzlehttp/guzzle 9, guzzlehttp/psr7 2, laravel/framework 1 and league/commonmark 10. These are recorded audit entries, not an application exploitability assessment. No dependency upgrade or security release approval was performed; assess separately rather than relabelling them as B01–B04.

## Handoff and operational boundary

- Set the same Issue #4 to `QA_FAILED`, Current Agent `@Omar`, Next Agent/Reviewer `@Salem` after fixes. Do not create a duplicate development task.
- Omar: fix B04, provide source-backed regression and an isolated full-application candidate/evidence for the outstanding checks, then return with an exact new head.
- Salem: retest B04 and adjacent identity/resumption paths, keep B01–B03 regression coverage, and complete material acceptance before any whole-feature PASS.
- Existing central migration remains the only migration added by the feature. Its isolated fixture test does not authorize Production execution. No tenant migration, deploy, merge, restart or rollback was performed by this review.
