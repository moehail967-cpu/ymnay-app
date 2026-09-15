# QA RETEST — B05 / Issue #4 / PR #5

Reviewer: `@Salem`  
Date: 2026-09-15  
**B05 remediation verdict: PASS for the independently retested delivery-failure and recovery scope.**  
**Whole Issue #4 acceptance: NOT APPROVED; remaining full-application QA is BLOCKED.**  
Next owner: `@Omar` to supply the isolated full-application candidate/evidence described below, then `@Salem` for the remaining acceptance checks. No new application defect is asserted merely because that environment is missing.

## Exact target and provenance

- Application/handoff: `5663407c3c83ad51210312e80beeb6ba1a6fa1a2`, implementation branch `feat/4-store-onboarding`, [PR #5](https://github.com/moehail967-cpu/ymnay-app/pull/5).
- B05 application parent: `583ddeeb186f8bc9b12e7c27e078ae0e08e9dc69`.
- QA-only tested head: `69a02cef252ef6d4bd6dd99cdef90b8e0c6d1d22`, branch `qa/4-salem-5663407`, [evidence PR #7](https://github.com/moehail967-cpu/ymnay-app/pull/7).
- QA head is based directly on the application candidate. Its three changed files are two test files and the isolated regression workflow. Application source is not changed.
- The runner explicitly checked out the QA head, not a moving synthetic PR merge. Its artifact records the tested head.
- QA PR #7 was closed after the completed test run, **without merging**. Branch and evidence remain available. Implementation PR #5 and canonical Issue #4 remain open.

Verified application blobs used by the tests:

| File | Git blob SHA-1 |
|---|---|
| `core/app/Http/Controllers/Landlord/Frontend/StoreOnboardingController.php` | `12c2f66c90c828cffa187c81cf7b82468e658764` |
| `core/app/Helpers/EmailHelpers/VerifyUserMailSend.php` | `c0e448422a448dbf62bc4ea5df1b3af7c98dfc6c` |
| `core/resources/views/landlord/frontend/dashboard/email-verify.blade.php` | `4dc505f420e3c27e41f20fa156033b07dbc7f7fe` |

## Work actually performed

Read the current Issue/handoff, current registered QA role and project rules, B05 remediation report and application/test diff, strict sender, controller and delivery-notice template. Continued the previously established B01–B04 acceptance context rather than treating the new engineering report as independent approval.

Restored the original independent eight-case QA suite from Git blob `7f35e9c339e076b3828979d38b737d057fdeb373`. Its two source-fingerprint constants and candidate metadata were **explicitly** repinned in the QA runner to the current verified blobs. No test assertion was removed or weakened. The original blob check, replacement map and resulting guard-only diff are retained in the CI artifact. The two tests that discovered B05 were rerun against the new controller/helper.

Added four independent tests for initial-send failure and successful recovery, safe feedback and continued usability of the old delivered code, rollback when both mail and logging fail, and the actual Blade delivery-notice branch. The Blade test compiles and renders the current notice fragment with success/failure values; it excludes the surrounding layout and flash component and is not a full-page browser test.

Ran these suites together with the engineer's current regression suite in the existing disposable Laravel/Stancl/MySQL fixture. Downloaded the completed run artifact, independently verified its SHA-256, and parsed its JUnit, stage results, observations and guard-only diff locally.

## Fresh execution evidence

- [Run 35014669177 / job 104534964232](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35014669177/job/104534964232): **completed / success**.
- [Artifact 10414702416](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35014669177/artifacts/10414702416), name `salem-b05-69a02cef252ef6d4bd6dd99cdef90b8e0c6d1d22`, 7,463 bytes.
- Downloaded ZIP SHA-256 matched GitHub: `149bb33a1cc89107db107e894643077e49dc0c9c54825c55377aa1c1a828b385`.
- Artifact includes `onboarding-junit.xml`, `stage-results.json`, `dependency-audit.json`, `salem-b04-observations.json`, `salem-b05-observations.json`, `salem-guard-repin.json` and `salem-guard-repin.diff`.

| Check | Fresh result |
|---|---|
| Selected source and QA PHP syntax | PASS |
| Current engineer PHPUnit suite | 32 tests / 230 assertions / 0 failures / 0 errors / 0 skipped |
| Historical independent Salem suite | 8 tests / 76 assertions / 0 failures / 0 errors / 0 skipped |
| Supplementary independent B05 suite | 4 tests / 59 assertions / 0 failures / 0 errors / 0 skipped |
| **Combined PHPUnit** | **44 tests / 365 assertions / 0 failures / 0 errors / 0 skipped** |
| Durable-stage decisions | **12 PASS / 0 FAIL**, actual class with in-memory checkpoint persistence; not additional PHPUnit cases |

These are the results of this new run, not copied from Omar's earlier 32-test success.

## B05 — independently verified disposition

The original defect is resolved for both previously failing injected transport rejection cases:

| Observation | 550 case | 553 case |
|---|---|---|
| Mail attempts | 1 | 1 |
| Feedback type | `danger` | `danger` |
| Feedback text | `We could not send the verification code. Please try again.` | Same safe message |
| Token changed despite failed send | false | false |
| Account marked verified | no | no |
| Request retained | yes | yes |
| Store/domain/trial created | no | no |

The supplementary/current tests also verified:

- Initial-send failure makes failure visible and leaves a null token rather than retaining a newly generated unsent token. The request and its selections are unchanged.
- A subsequent successful resend permits code verification while preserving the request; verification itself does not create a store.
- A previously delivered code remains usable after rejected resend. Successful replacement invalidates the old code, while wrong codes cannot verify or create a store.
- Logging failure cannot convert a mail failure into success or commit the replacement token.
- The actual Blade failure-notice fragment contains the failure message and alert role, and does not contain the sent-success claim. Its success branch remains distinct.
- B01–B04 tests in the retained suites remain passing within their previously documented fixture scope.

The new strict sender is opt-in for onboarding; source review shows the legacy `sendMail()` callers retain their existing contract. This is not a claim that all native/legacy application flows were exercised end-to-end.

No new code blocker was established in this focused retest. Do not relabel B05 or the previous reproduced B01–B04 cases as still failing merely because whole-feature verification is incomplete.

## Central/tenant and verification limits

B05 changes central-account code issuance and central onboarding feedback. The new sender uses the user's connection and a row-locked transaction. The MySQL tests verify rollback/preserved tokens and no new tenant/domain/trial effects during verification failure or success. The retained suite includes its existing targeted two-tenant/context checks and central onboarding migration tests.

The fixture still substitutes auth lookup, cookies, theme data, tenant migration/seed **content**, file dispatch and mail transport. It is not the complete Laravel application, real middleware/session/cookie behavior, actual SMTP delivery, module migrations/theme import or browser token-login. SQL/Blade-fragment success is not full application or visual acceptance. Full filesystem/cache/queue tenant isolation and parallel HTTP creation/address races are not certified.

Local preflight in this session found PHP 8.4.23 but no PDO drivers and no Composer installation; a complete candidate runtime is not configured. Direct raw-source download failed. A read-only attempt to list Opera tabs returned **Browser not connected**. No production page or database was used as a substitute test environment, and no actual application screenshots were captured.

## Remaining blocker G01 — full-application acceptance environment/evidence

This is an execution/verification blocker, **not a newly discovered B06 application bug**. B05 scoped PASS does not satisfy the original whole-feature acceptance criteria.

Required engineering handoff from `@Omar`, on the same Issue:

1. A reproducible disposable local/CI/staging setup pinned to the candidate SHA, booting the **actual application**, with synthetic central/test-user data, safely scoped central and tenant databases, no Production .env/customer data, and outbound mail captured by a local test transport/sink rather than real customer delivery. Provide startup commands and a test URL when interactive review is supported, or retained HTTP/browser evidence from the isolated CI run.
2. Actual route/middleware/session/cookie registration and existing-account verification, invalid/expired code/resend/rate-limit/recovery cases, preserved selections, review and exactly-once store creation. Keep the existing rejection/ownership and B01–B05 tests; do not replace them with markup-only assertions.
3. Actual module/schema/theme/admin provisioning, real token-login into the correct tenant dashboard, native paid/admin-path regression without real payments, queue/file completion and central/two-tenant isolation; include parallel completion/address-race evidence in the real flow.
4. Full frontend build and real desktop/mobile/RTL screenshots and interaction evidence against Nour's four linked references, including the landing L01–L08 and the five-step flow. Label viewport and candidate SHA. No screenshot of Production or a reimplemented mock counts as this candidate's UI evidence.

`@Salem` resumes the full acceptance review after this delivery. Interactive browser access may require the owner to reconnect the browser, but a reproducible isolated CI/browser evidence package can remove dependence on that connection. Do not request Production credentials in an Issue or report.

Until G01 and the outstanding material checks are resolved, set Issue #4 **BLOCKED**, Current Agent `@Omar`, Next Agent/Reviewer `@Salem`; keep it open and do not move to READY_FOR_DEPLOYMENT. Do not use PASS WITH ISSUES to hide unverified material acceptance.

## Non-blocking / out-of-scope observations

The retained dependency audit again lists 22 advisory records across four existing packages: guzzlehttp/guzzle 9, guzzlehttp/psr7 2, laravel/framework 1, league/commonmark 10. This records tool output only, not exploitability or security release approval. No dependency upgrades were performed.

PR #5's description still contains an older 88a1b136 handoff despite its newer actual head; the Issue control block and current report identify the candidate unambiguously. This is a tracking discrepancy, not an application failure. The current QA outcome is also posted to the implementation PR conversation.

## Operational boundary

No application source, implementation branch or main was modified by this review. Test/evidence changes are isolated to the QA branch. No merge, auto-merge, Production deployment/migration, service restart, rollback, actual mail send, payment or customer-data operation occurred. B05 adds no migration. The feature's existing central onboarding migration and any later Production operation remain separately owner-gated.

Historical QA reports/results are preserved at their original commits. This report records a scoped B05 PASS and the precise remaining acceptance blocker, not a whole-feature release approval.
