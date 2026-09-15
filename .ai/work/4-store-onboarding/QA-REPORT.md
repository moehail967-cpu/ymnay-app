# QA RESULT — Issue #4 / PR #5

Reviewer: `@Salem`  
Date: 2026-09-15  
Verdict: **FAIL**  
Recommended task state: **QA_FAILED**, implementation handoff to `@Omar`, retest by `@Salem`.

## Exact target

- Issue: https://github.com/moehail967-cpu/ymnay-app/issues/4
- PR: https://github.com/moehail967-cpu/ymnay-app/pull/5
- Implementation branch: `feat/4-store-onboarding`
- Reviewed candidate: `b7d0a52ef23e6b1620509c49e62f1ef86f4bf70b`
- Base: `215020153b1bd8346a79cf5349679dc09a7ad6c9`
- QA artifacts branch: `qa/4-salem-b7d0a52`, based on that exact candidate. QA artifacts do not change application source, the implementation branch, or main.

Scope: the full PR diff (25 files), official landing L01–L08, the owner-approved five-step onboarding requirements, affected central/tenant provisioning and authentication contracts. This first review cycle establishes source/control-flow blockers; it does not certify the full application or visual implementation.

## Environment and method — do not overstate these results

PHP CLI 8.4.23 and Node 22.16.0 are available. Chromium is installed. Composer is not installed and PHP reports **no PDO drivers**. There is no configured, dependency-installed Laravel/isolated-database runtime for this candidate in this workspace. Direct source-download networking failed; the GitHub connector supplied the source. No production environment, customer database, real email or payment was used.

The reviewer copied the controller from the connector into the isolated workspace and verified the complete Git blob hash against GitHub: `ce4dc666de41d21266acbf549533b6ab36a30365`. The unmodified controller was then executed with an explicitly documented in-memory dependency harness.

**The harness is not PHPUnit, a Laravel feature suite, SQL concurrency testing, or end-to-end testing.** Eloquent, the session/auth facades, event pipeline, trial action, response factory and tenancy services are doubles. It exercises the real controller's branch/state/dispatch decisions, not those dependencies' implementations. Validation doubles are intentionally limited; mutation cases supply otherwise-valid values. No claim is made that duplicate rows or cross-tenant leakage were observed in a real database.

Artifacts:
- `qa/controller-harness.php` — executable, source-hash-pinned control-flow reproducer.
- `qa/controller-results.json` — actual results from the run.

Run from repository root, using the reviewed candidate's controller:

```sh
php .ai/work/4-store-onboarding/qa/controller-harness.php core/app/Http/Controllers/Landlord/Frontend/StoreOnboardingController.php
```

Exit code 1 is expected on the reviewed candidate because regression expectations fail. The source-hash guard intentionally rejects another revision; verify and update the expected hash as part of an explicit retest, not silently.

## Automated checks actually performed

| Check | Result | Qualification |
|---|---|---|
| `php -l` StoreOnboardingController.php | PASS | Real PHP CLI; exact GitHub blob verified |
| `php -l` StoreOnboardingRequest.php | PASS | Source copy matches PR blob prefix `5f7b2a30` |
| `php -l` new central migration | PASS | Source copy matches PR blob prefix `3fdebdea`; migration NOT executed |
| `node --check` ymnay-public.js | PASS | Source copy matches PR blob prefix `e9a671f1`; syntax only |
| Controller control-flow harness | **5 PASS / 7 FAIL / 0 harness errors** | 12 cases; seven failing cases map to three blocker groups below |
| Laravel/PHPUnit, frontend build, database migration, actual browser E2E | NOT RUN | Required dependencies/database runtime unavailable; no false PASS |

The positive control-flow cases were: rejecting an unverified account; detecting a changed plan snapshot; avoiding duplicate dispatch/trial calls on sequential ready-request completion; rejecting a foreign-account bound request; and rejecting an existing trial on another store. These are limited harness results, not full acceptance or tenant-isolation certification.

## BLOCKING ISSUES

### B01 — Provisioning/ready request state can be overwritten by earlier-step mutations

Severity: BLOCKER / P1  
Owner: `@Omar`  
Cases: C06-selectPlan, C06-selectTheme, C06-storeDetails, C07.

Expected: once creation starts, an earlier tab or repeated request cannot change its plan/theme/store data or erase its in-progress state. A completed request must remain a reliable reference to the created store. A deliberately new creation must use a separate request and the normal eligibility checks.

Actual: `selectPlan`, `selectTheme`, and `storeDetails` write `status = draft` without rejecting `provisioning`. `selectTheme` also rewrites a `ready` request to `draft` while retaining its `tenant_id`. Only `selectPlan` has a special case for `ready`. The transaction in `complete` protects the claim only; it has already returned before tenant provisioning executes.

Observed in the harness: each of the three earlier-step methods changed `provisioning` to `draft`; theme/store mutations changed those fields too. After successful simulated completion, selecting a theme changed `ready` to `draft` while retaining `tenant_id = qa-store`.

Why blocking: the state used to prevent repeated work is mutable during that work, and the summary/request can diverge from the creation already started or completed. Real SQL duplicate effects were not tested; the actual state-overwrite defect is directly reproduced.

Safe runtime reproduction after a QA environment is prepared: pause provisioning immediately after its claim commits; in the same authenticated test account use a second tab to submit `/create-store/plan`, `/theme` or `/details`. Verify the saved request is not rewritten and no second creation can start. Repeat earlier-step submission after a completed request.

Source: [StoreOnboardingController lines 73–158](https://github.com/moehail967-cpu/ymnay-app/blob/b7d0a52ef23e6b1620509c49e62f1ef86f4bf70b/core/app/Http/Controllers/Landlord/Frontend/StoreOnboardingController.php#L73-L158), [claim and provisioning lines 220–276](https://github.com/moehail967-cpu/ymnay-app/blob/b7d0a52ef23e6b1620509c49e62f1ef86f4bf70b/core/app/Http/Controllers/Landlord/Frontend/StoreOnboardingController.php#L220-L276).

Required before retest: enforce and atomically preserve the allowed state transitions for every mutation path, including plan acknowledgement; keep completed requests immutable or use a separate explicit request. Add database-backed parallel-request and stale-tab tests. Do not implement a policy change as the fix.

### B02 — A partially created tenant cannot resume missing provisioning through this retry path

Severity: BLOCKER / P1  
Owner: `@Omar`  
Case: C09.

Expected: a transient failure after a tenant row exists must have a safe resumable/recoverable path, without consuming duplicate trials or indefinitely repeating the same failure after the dependency is restored.

Actual: `complete` dispatches `TenantRegisterEvent` only when no tenant row exists. On retry of a partially created owned tenant, the row is reused, the trial action can run, and missing domain/login-key checks fail again. The missing provisioning stages are never dispatched by this retry path.

This condition is supported by the real implementation: `TenantDomainCreate::handle` creates the tenant with its user ID; the synchronous TenantCreated pipeline runs as part of that creation; the listener writes `unique_key` only after `Tenant::create` returns. A pipeline exception can therefore leave an owned tenant row without its domain/key. This is not a guessed alternative architecture.

Observed with one injected failure before the domain stage: three attempts returned `422 / failed`; pipeline dispatch count stayed **1**; the trial action was called once during retry; the remaining error was `Tenant provisioning did not produce a domain and login key.` The fixture does not exercise actual database creation/migration.

Source: [controller retry path](https://github.com/moehail967-cpu/ymnay-app/blob/b7d0a52ef23e6b1620509c49e62f1ef86f4bf70b/core/app/Http/Controllers/Landlord/Frontend/StoreOnboardingController.php#L245-L277), [TenantDomainCreate](https://github.com/moehail967-cpu/ymnay-app/blob/b7d0a52ef23e6b1620509c49e62f1ef86f4bf70b/core/app/Listeners/TenantDomainCreate.php), [TenancyServiceProvider](https://github.com/moehail967-cpu/ymnay-app/blob/b7d0a52ef23e6b1620509c49e62f1ef86f4bf70b/core/app/Providers/TenancyServiceProvider.php), [TenantTrialPaymentLog](https://github.com/moehail967-cpu/ymnay-app/blob/b7d0a52ef23e6b1620509c49e62f1ef86f4bf70b/core/app/Actions/Tenant/TenantTrialPaymentLog.php).

Required before retest: provide a stage-aware, safe continuation/reconciliation path for existing partial tenants, including the login key and actual database/theme/admin readiness. Avoid blindly rerunning destructive migrations/seeds. Demonstrate recovery after injected failures at relevant stages, one trial record, correct tenant/plan/theme and working dashboard. Recovering/rolling back Production data is NOT authorized by this report.

### B03 — Final creation can dispatch without complete store data and ignores a newly forbidden subdomain

Severity: BLOCKER / P1  
Owner: `@Omar`  
Cases: C08, C10.

Expected: the server must validate the complete saved store request and current subdomain rules at the final submission before any provisioning side effect. UI step limiting is not the server-side creation gate.

Actual: `complete` validates `terms_condition` but does not validate required `store_name`/`subdomain` or re-read `forbidden_subdomains`. `maxStep` only limits rendering. The full domain rules are in `storeDetails`, not the final creation gate.

Observed:
1. A verified test user's selected-plan/theme request without store data dispatched `TenantRegisterEvent` with a **null subdomain** before returning an error. No real orphan database was created; that consequence remains a risk, not an observed fact.
2. After saving a valid address and then marking it forbidden in the test configuration, final completion returned `200 / ready` in the harness, with one event dispatch and **no read** of the updated forbidden-address option. The actual listener does not add that missing validation.

Source: [store-details validation](https://github.com/moehail967-cpu/ymnay-app/blob/b7d0a52ef23e6b1620509c49e62f1ef86f4bf70b/core/app/Http/Controllers/Landlord/Frontend/StoreOnboardingController.php#L122-L153), [complete](https://github.com/moehail967-cpu/ymnay-app/blob/b7d0a52ef23e6b1620509c49e62f1ef86f4bf70b/core/app/Http/Controllers/Landlord/Frontend/StoreOnboardingController.php#L178-L254), [maxStep](https://github.com/moehail967-cpu/ymnay-app/blob/b7d0a52ef23e6b1620509c49e62f1ef86f4bf70b/core/app/Http/Controllers/Landlord/Frontend/StoreOnboardingController.php#L360-L368).

Required before retest: final validation of complete saved data and current allowed/reserved/claimed address rules, using the same intended product contract as the earlier step. Test direct final submission before step three, an address newly reserved after step three, and a competing account claiming the address. Assert no tenant/database/domain/trial side effects for rejected input.

## Material checks still unverified

- Actual Laravel boot/route resolution and the repository PHPUnit tests.
- Executing the new migration on a disposable central DB, verifying its FKs and ensuring tenant databases are untouched.
- Real HTTP registration/email OTP, expiry/retry throttles, session/cookie transitions, password recovery and resumption.
- Database-backed provisioning, true parallel requests, failure recovery and dashboard authentication.
- Two-tenant authorization/isolation, cache/filesystem context and teardown after exceptions.
- Full frontend build and actual landing/onboarding desktop/mobile/RTL screenshots against Nour's four image references. No actual application screenshots were captured and no visual-match PASS is claimed. Chromium availability alone is not a running application.

These unknowns remain release gates even after B01–B03 are fixed. No unrelated observation was promoted into a blocker, and no production feature code was patched during review.

## Deployment impact and handoff

- A future application release is needed to ship the feature; **no release is approved**.
- New migration: `core/database/migrations/2026_09_14_000001_create_store_onboarding_requests_table.php`, intended for Central. Model uses `CentralConnection`. Syntax was checked; application/FK/rollback behavior was not run. No tenant migration is added in this PR.
- Provisioning reuses the existing synchronous TenantCreated pipeline; file-copy jobs remain an existing downstream effect. Runtime queue/worker requirements are not certified here; no restart/scheduler change was made or authorized.
- `@Omar`: fix B01–B03, add meaningful safe regression coverage, and provide a configured non-production candidate for the outstanding E2E/visual checks. Return the same Issue to `READY_FOR_QA` with the new exact commit.
- `@Salem`: rerun each failed reproduction and adjacent regression checks, then complete the outstanding material verification before any PASS.
- Keep Issue #4 and PR #5 open. Do not merge, deploy, run Production migrations or roll back anything on the basis of this report.
