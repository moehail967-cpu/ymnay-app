# ENGINEERING HANDOFF — Salem B01–B03 remediation

Engineer: `@Omar`  
Date: 2026-09-15  
Issue: [#4](https://github.com/moehail967-cpu/ymnay-app/issues/4)  
PR: [#5](https://github.com/moehail967-cpu/ymnay-app/pull/5)  
Branch: `feat/4-store-onboarding`  
Recommended next state: **READY_FOR_QA**, reviewer `@Salem`. This is an engineering handoff, not a QA PASS or deployment approval.

## Exact candidate and evidence

- Salem's original reviewed source: `b7d0a52ef23e6b1620509c49e62f1ef86f4bf70b`.
- Historical QA report and original source-pinned reproducer: [d9e0ad8 / QA-REPORT.md](https://github.com/moehail967-cpu/ymnay-app/blob/d9e0ad8c435bb70b9e0eeba37e88c7967cfc840f/.ai/work/4-store-onboarding/QA-REPORT.md). They are unchanged; their 5 PASS / 7 FAIL results belong to the original candidate.
- **Tested application, test and workflow revision: `9f9ce6436152a7147426ecf563dec0cd339de522`.**
- Successful [GitHub Actions run 34921075207](https://github.com/moehail967-cpu/ymnay-app/actions/runs/34921075207), [job 104229129494](https://github.com/moehail967-cpu/ymnay-app/actions/runs/34921075207/job/104229129494), completed successfully on 2026-09-15.
- The pull-request runner tested GitHub's synthetic merge `5985eb4af0cb2be38e20cab6f152140fa085bc29`, with head `9f9ce6436152a7147426ecf563dec0cd339de522`. A synthetic test merge is NOT a merge into main.
- Evidence artifact: [10377549728](https://github.com/moehail967-cpu/ymnay-app/actions/runs/34921075207/artifacts/10377549728), named `onboarding-regression-5985eb4af0cb2be38e20cab6f152140fa085bc29`.
- ZIP SHA-256 verified after download: `ef5c5c0be7d7900974dc68f3eb91aeabc9d4551c2272b0a82d666c07ad28696a`.
- Artifact files: `onboarding-junit.xml`, `stage-results.json`, `dependency-audit.json`. GitHub reports artifact expiration 2026-12-14; rerun the checked-in suite when durable fresh evidence is needed.
- This report is a documentation-only addition after the tested revision. Do not imply the report's own commit was the source of this recorded run.

Source blob fingerprints at the tested revision:

| File | Git blob SHA-1 |
|---|---|
| `core/app/Http/Controllers/Landlord/Frontend/StoreOnboardingController.php` | `0044044738d14d0cfa73235cb97fd8245d02b378` |
| `core/app/Services/Onboarding/StoreOnboardingProvisioner.php` | `bbb2d1585c53a7dfa63aa8d65fb3f607bc9b6136` |
| `.ai/work/4-store-onboarding/tests/OnboardingIntegrationTest.php` | `3b45167e294f7ad37e07713ee3088bd44f004ff6` |

## B01 — preserve request state and identity

All plan, theme, store-detail and plan-acknowledgement mutations now load a fresh request under the same **central row lock** used by completion. They reject in-progress and completed requests, recheck ownership, and cannot overwrite a ready/provisioning request with stale draft data. Selecting a plan no longer silently replaces a completed request. A failed request can be edited only when no linked/owned partial tenant requires preserving the creation identity.

Completion locks the central user first and request second, rechecks stored user verification/eligibility, and continues with the validated locked snapshot rather than the pre-lock model. Cross-request active provisioning for the same account is rejected. The existing NULL-owner conditional claim remains separate from already owned provisioning requests.

Verification includes real two-process MySQL contention: a writer reads draft, waits while another connection claims provisioning, then acquires the lock and rejects the stale write. Ready/provisioning mutation denial covers plan, theme, details and acknowledgement. Repeated completion preserves one tenant and one trial in the fixture.

## B03 — final validation before provisioning effects

The earlier detail step and final completion share server-side store validation. Final validation runs under the claim lock against saved store name/subdomain, current central forbidden-address configuration, current plan/trial/theme data, account verification, trial eligibility and address ownership. The forbidden-address option is read from its central source rather than a cached availability result. Rejected requests do not create tenant/domain/trial effects in the regression cases. The tenant primary key remains the final address-race guard.

Real MySQL testing also exposed an adjacent source defect: JSON storage may reorder object keys, making strict unsorted plan snapshot arrays appear changed. `planHasChanged` now sorts keys while preserving strict value/type comparison, both for review and completion. Genuine price changes still require acknowledgement.

## B02 — stage-aware partial provisioning

The optional onboarding request ID on `TenantRegisterEvent` is saved in the tenant's existing JSON metadata before its synchronous created event. Only these marked self-service tenants use `StoreOnboardingProvisioner`; native paid/admin creation retains its existing pipeline.

The service persists `running`/`done` checkpoints for database, migrations, domain, seed, login key, store title and file dispatch. Completed effects can be reconciled after interrupted checkpoint writes. Safe missing domain/key/title stages are restored; completed seeds are not repeated. Before a trial or ready state, it checks database connectivity, migration bookkeeping, the seeded administrator role, domain, login key, title and stage completion.

Uncertain partially executed seeds, incomplete interrupted migrations, cPanel provisioning or file dispatch require explicit recovery inspection instead of destructive replay. An older unmarked partial tenant is adoptable only after conservative owner/origin/theme/time checks and proof its database is absent or has no application rows apart from migration bookkeeping. The schema inspection is explicitly limited to that tenant database. This is not automatic repair of arbitrary existing stores.

Central checkpoint writes merge existing `tenants.data` under a row lock. They avoid Stancl virtual-attribute shadowing of physical tenant fields. Login-key and trial writes synchronize the physical and JSON representations; unrelated metadata is retained. The original trial action runs in a central transaction, and a matching existing trial is reused without extending its dates. All manual tenant contexts are restored in `finally`. Welcome-mail failure cannot fail or recreate a ready store.

For this flow only, the seed adapter enables strict theme import failures instead of allowing the legacy importer to swallow exceptions. Actual project theme import remains an outstanding full-application check. Existing child file-copy jobs stay asynchronous: successful dispatch is NOT proof all files have been copied.

## Verification actually performed

| Check | Observed result | Scope |
|---|---|---|
| Changed application PHP syntax in CI | PASS | Real PHP 8.4 CLI |
| Durable-stage regression | **12 PASS / 0 FAIL** | Actual `ProvisioningStages` class, explicitly in-memory checkpoint storage |
| PHPUnit integration fixture | **21 tests / 147 assertions / 0 failures / 0 errors / 0 skipped** | Real Laravel components, Stancl switching and MySQL 8 with the adapters below |
| Central onboarding migration | PASS within fixture | Actual existing migration up/down, foreign keys against disposable central schema; tenant fixture schemas checked not to contain the central request table |
| Two-process row-lock/stale-tab test | PASS, not skipped | Actual separate MySQL connections and process barrier |
| Failure before domain / legacy unseeded adoption / missing login key | PASS | Real central and tenant fixture databases; no duplicated trial/seed in tested recovery cases |
| Partial-seed uncertainty and welcome-mail failure | PASS | Deliberate injected failures; no unsafe seed replay, no false ready from partial seed |
| Two tenant title separation/context restoration and foreign request denial | PASS | Targeted fixture checks, NOT full authorization/isolation certification |

The fixture loads actual candidate controller/models/event routing/provisioner/trial action and framework validators/SQL transactions. It explicitly replaces auth lookup, theme-list data, tenant migration/seed CONTENT, file dispatch and mail delivery. Its tenant schema and seed rows are synthetic. It is **not a boot of the complete application**, not the full project PHPUnit suite, not actual module migrations/theme import, not HTTP OTP or browser E2E. A generated dashboard URL is not proof that the real browser token-login succeeds.

Earlier failed runs were inspected and fixed, not relabelled as success: PHP platform preparation, missing fixture bindings/router setup, real plan JSON-order comparison and tenant-schema inspection. The intermediate administrative BLOCKED handoff pinned `51f7076`; this successful newer exact source candidate supersedes that verification blocker, without altering Salem's historical FAIL.

## Dependency audit — separate unresolved maintenance concern

The test dependency closure is pinned to the application's existing `core/composer.lock`; application dependency files were not upgraded. Existing locked packages require PHP 8.4 for this fixture. Production platform compatibility must be verified separately before a release.

To reproduce that existing lock in the disposable fixture, the workflow relaxes advisory-based dependency resolution **only for its temporary test manifest**, with scripts/plugins disabled and no production secrets. It separately saves `composer audit` output. The observed audit has **22 advisories across four existing packages** (9 high, 13 medium): `guzzlehttp/guzzle` 9, `guzzlehttp/psr7` 2, `laravel/framework` 1, `league/commonmark` 10. These are package advisories, not proof every issue is exploitable in this application. They need separately scoped impact assessment/remediation; CI regression success is not a security release approval.

## Changed areas and data impact

- Controller validation/state/ownership and final claim.
- Opt-in event/listener/provider routing; new checkpoint/recovery/strict-import services; scoped strict-seed flag.
- Test-only workflow and checked-in fixture under `.ai/work/4-store-onboarding/tests/`.
- Shared knowledge updated only where affected: `TENANCY.md`, `WORKFLOWS.md`, `manifests/workflows.yaml`.
- **No additional migration** for remediation; checkpoints reuse existing tenant JSON data. The PR's pre-existing `2026_09_14_000001_create_store_onboarding_requests_table.php` is Central and still requires explicit approval before any production execution.
- No frontend redesign, product/pricing policy change, main merge, production deployment, real mail/payment, customer-data operation, service restart or production rollback performed.

## Remaining QA / release gates

`@Salem` should independently retest B01–B03 on the exact new source, updating the old reproducer's source guard explicitly rather than silently accepting another revision. Extend concurrency coverage to parallel completion and cross-account address races in the actual application. Complete full application boot/routes, actual module migration/theme/admin readiness, native paid/admin-path regression, OTP/resend/session/cookie/recovery behavior, real dashboard authentication, queue completion, broader tenant isolation and Nour's desktop/mobile/RTL visual comparisons.

Hard process interruption can leave an active provisioning request or uncertain non-repeatable stage requiring an operational inspection. Do not reset flags, replay seeds or repair production data from this report. No automatic rollback is claimed.

Keep Issue #4 and PR #5 open. Engineering remediation is ready for independent review; B01–B03 are closed only by Salem's retest. Production deployment remains owner-gated.

## Reproduction

The test workflow runs on relevant PR updates. Its checked-in setup prepares a temporary dependency manifest outside the application from the lockfile, starts an isolated MySQL service, checks syntax and runs:

```sh
php .ai/work/4-store-onboarding/tests/stages-regression.php
"$YMNAY_TEST_FIXTURE/vendor/bin/phpunit" --no-configuration --colors=never \
  --log-junit "$RUNNER_TEMP/onboarding-junit.xml" \
  .ai/work/4-store-onboarding/tests/OnboardingIntegrationTest.php
```

Use the workflow's explicit disposable settings, never an application/production `.env`. The fixture refuses execution unless its disposable-database guard is enabled and the exact local test host/database are selected. Its cleanup only targets synthetic fixture databases. Read `tests/bootstrap.php`, `tests/prepare-fixture.py` and `.github/workflows/onboarding-regression.yml` before reproducing.
