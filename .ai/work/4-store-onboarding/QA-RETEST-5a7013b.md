# QA RETEST — Issue #4 / PR #5 / 5a7013b

Reviewer: `@Salem`  
Date: 2026-09-15  
Overall verdict: **FAIL**  
Current blocker: **B05 — verification mail failure is reported as success**.  
B04 disposition: **PASS for the original controller-level regression and tested adjacent cases**, not full browser acceptance.  
Next owner: `@Omar`, then `@Salem` for retest. No merge or Production authorization.

## Exact candidate and evidence

- Application/handoff: `5a7013b04c70fbe2ce474f53b491326b2aca89b1`, implementation branch `feat/4-store-onboarding`, [PR #5](https://github.com/moehail967-cpu/ymnay-app/pull/5).
- B04 application change: `3743871950e8d7bd7fa0107283d2beec6ad32517`; the following handoff commit adds its report.
- QA branch: `qa/4-salem-5a7013b`, created from that exact application candidate. [Draft PR #6](https://github.com/moehail967-cpu/ymnay-app/pull/6) targets the implementation branch and exists only to run/retain QA evidence. **Do not merge this QA runner.** Issue #4 remains the canonical task.
- Tested QA head: `e67c1766a59b7ed5150ee94fb5f597ce1edc36a4`; application files unchanged. Only a test file and the isolated test workflow were changed before this run.
- Synthetic PR test merge: `f8531d892f253368936a6c1a5dd9c561cead6c69`. This is not an actual merge into any application branch.
- [Final run 35009713722 / job 104518332586](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35009713722/job/104518332586): **completed / failure**, caused by the two application acceptance assertions below, not environment setup failure.
- [Artifact 10412429959](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35009713722/artifacts/10412429959), created 2026-09-15T18:49:14Z. ZIP was downloaded, its SHA-256 matched GitHub, and JUnit/stage/observation JSON were parsed.
- Verified ZIP SHA-256: `fa0c2c1c9ee58501945e311f827c31e9c2d7c810c1295858bbc1c530f5a7098b`.

The independent tests enforce these Git blob hashes before execution:

| Source | Exact Git blob |
|---|---|
| `core/app/Http/Controllers/Landlord/Frontend/StoreOnboardingController.php` | `b4394b4f079dbf4886c0d013c516e0d642e4a914` |
| `core/app/Helpers/EmailHelpers/VerifyUserMailSend.php` | `138e3f605d53bd2c810addf27a3a0bfa7ea16c79` |

## Work performed and actual results

Read the latest Issue/handoff and implementation diff, dedicated verification methods, current routes, verification view, existing mail helper, and isolated fixture/test workflow. Updated the Issue to IN_PROGRESS and posted STARTED. Added eight independent tests in `tests/SalemB04ReviewTest.php`, without fixing production application code. Ran them together with the engineer's unchanged 28 integration tests on disposable Laravel/Stancl/MySQL infrastructure.

| Check | Final result | Scope |
|---|---|---|
| Selected PHP source and QA test syntax | PASS | Actual CI PHP CLI |
| Original integration suite | **28 tests / 182 assertions / 0 failures / 0 errors / 0 skipped** | Engineer-authored tests rerun on the current candidate |
| Salem independent suite | **8 tests / 76 assertions / 2 failures / 0 errors / 0 skipped** | Six positive/control cases pass; two mail-failure cases fail |
| Combined PHPUnit | **36 tests / 258 assertions / 34 PASS / 2 FAIL / 0 errors / 0 skipped** | Actual JUnit evidence |
| Durable stage decisions | **12 PASS / 0 FAIL** | Actual ProvisioningStages class with in-memory checkpoint storage |

The positive independent cases verify: the disabled generic flag still permits the dedicated verification view; correct code preserves every stored selection and the session request without creating a store; wrong code leaves the account unverified; verification cannot rewrite a provisioning request; resend invalidates the old code and the replacement verifies; final creation remains forbidden after an incorrect code.

The first QA run, [35009510514](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35009510514), had an additional **QA-test comparison mistake**: comparing an in-memory JSON insertion order with a fresh MySQL-normalized snapshot. Salem corrected the test to compare two persisted reads and reran everything. This was not an application defect and is not counted as a blocker. No application code changed between those runs. The final run above is the acceptance evidence.

## B01–B04 disposition

- **B01/B03:** their original state-mutation and final-validation regressions remain green in the fresh original suite, including the fixture's real MySQL stale-tab lock case.
- **B02:** original missing-domain/key and conservative legacy-partial recovery cases remain green within the synthetic tenant-content fixture. Actual module/theme provisioning remains unverified.
- **B04:** the original disabled-global-flag dead end no longer occurs in the tested dedicated controller path. Both flag-value cases in the original suite pass; Salem independently verifies the disabled-flag case and preservation/denial behavior. Source wiring now selects `/create-store/verify-email`, and the generic no-onboarding policy was not modified by the B04 change. Actual route dispatch, middleware and complete rendered browser behavior were not executed.

These repaired regressions are not being returned as still failing. Whole-feature acceptance remains FAIL because B05 violates an explicit in-scope failure-state requirement, and material full-application gates are still unverified.

## B05 — Mail transport rejection still produces verification-send success

Severity: **BLOCKER / P1 — acceptance failure**, conditional on mail-send failure.  
Owner: `@Omar`.  
Requirement: Issue #4, Account And Verification Rules, requires clear wrong/expired-code and mail-send-failure states with a usable resend path. This is not a new product policy or broad security audit.

### Expected

When the mail transport rejects the verification message, the onboarding response must report a clear safe failure rather than claiming successful sending. It must preserve the user's request, keep the account unverified, and offer safe retry. A successfully sent replacement should subsequently verify normally. Sensitive transport/configuration details must not be exposed.

### Actual source and observed behavior

`VerifyUserMailSend::sendMail` writes a replacement `email_verify_token` before sending. It catches mail exceptions. For exception code 553 it returns a danger redirect; other exceptions are swallowed. The new `StoreOnboardingController::resendVerificationEmail` ignores that outcome and always returns a success flash after the helper call. The subsequent verification form does not attempt another initial send because a token already exists.

Independent tests execute those **actual application classes** and replace only mail delivery with a transport adapter throwing a test exception, once with code 550 and once with code 553. Both observed the same failure:

| Observation | 550 | 553 |
|---|---|---|
| Attempted sends | 1 | 1 |
| Flash type | `success` | `success` |
| Flash message | `Verify mail send` | `Verify mail send` |
| Token rotated despite failed sending | true | true |
| Next view | `landlord.frontend.dashboard.email-verify` | same |
| Account verified | 0 | 0 |
| Request status | `draft` | `draft` |
| Session request preserved | true | true |

The view also contains an unconditional message stating a code has been sent; this is a **source observation**, not a screenshot/rendered-browser claim. The observed replacement token is unavailable to a real recipient when its send fails; no token values are written to the evidence artifact.

No account-verification bypass, tenant creation, domain creation or trial effect occurred in these failure cases. There was no real SMTP connection, and no claim is made that Production mail is failing or that all users are affected.

### Reproduction

On the QA branch and using only the existing guarded disposable fixture:

```sh
php .ai/work/4-store-onboarding/tests/stages-regression.php
"$YMNAY_TEST_FIXTURE/vendor/bin/phpunit" --no-configuration --colors=never \
  --log-junit "$RUNNER_TEMP/onboarding-junit.xml" \
  .ai/work/4-store-onboarding/tests
```

The checked-in QA workflow prepares its dependencies and database. Do not substitute a Production `.env`. Test names: `testTransport550FailureMustNotShowSuccess` and `testTransport553FailureMustNotShowSuccess`. The test's pinned source hashes intentionally require explicit review/update when retesting a changed application revision.

Sources on the application candidate:
- [Controller: verificationForm / resendVerificationEmail](https://github.com/moehail967-cpu/ymnay-app/blob/5a7013b04c70fbe2ce474f53b491326b2aca89b1/core/app/Http/Controllers/Landlord/Frontend/StoreOnboardingController.php).
- [VerifyUserMailSend helper](https://github.com/moehail967-cpu/ymnay-app/blob/5a7013b04c70fbe2ce474f53b491326b2aca89b1/core/app/Helpers/EmailHelpers/VerifyUserMailSend.php).
- [Verification view](https://github.com/moehail967-cpu/ymnay-app/blob/5a7013b04c70fbe2ce474f53b491326b2aca89b1/core/resources/views/landlord/frontend/dashboard/email-verify.blade.php).

### Required correction and retest

Propagate an explicit safe send outcome to the onboarding flow and honor it for initial send and resend. Do not report success or grant verification on a failed send. Preserve the saved journey and provide a usable retry after the mail service recovers. Keep changes scoped and verify legacy consumers rather than silently altering their policy. Demonstrate both failure branches, first-send failure, successful retry, invalid/old/used codes, unchanged request ownership and no premature store effects. Exercise current route/view behavior in an isolated application, not only string assertions.

## Remaining verification and operational boundary

This CI uses actual candidate controllers/helpers/models and Laravel/Stancl/MySQL, but auth lookup, cookie behavior, theme data, tenant schema/seed content, file work and mail delivery have explicit fixture adapters. Local PHP/Node are available; no configured complete Laravel/Composer/PDO runtime was available in this session, and direct Git source networking failed. No Production infrastructure was used to compensate.

Full application boot and project suite, HTTP/middleware/rate-limit execution, OTP expiration/session/cookie/password-recovery paths, actual module/theme/admin provisioning, real dashboard token login, native paid/admin regressions, queue completion, full tenant filesystem/cache/queue isolation, and actual Desktop/Mobile/RTL comparison against Nour's references remain unverified. No application screenshot or visual PASS is claimed.

The final artifact also records 22 existing dependency advisory entries across four packages (guzzlehttp/guzzle 9, guzzlehttp/psr7 2, laravel/framework 1, league/commonmark 10). These are separate recorded audit results, not assessed exploitability or a security release decision. No application dependency upgrade occurred.

Set Issue #4 to QA_FAILED, Current Agent `@Omar`, Reviewer/Next Agent `@Salem` after B05 correction and a new exact-candidate handoff. Keep implementation PR #5 open. The QA draft PR #6 is evidence-only and is not merge authorization. No new application migration was added; the feature's existing central migration still requires explicit owner approval before Production. No deployment, merge, Production migration, restart, rollback, customer-data mutation or real email/payment was performed in this review.
