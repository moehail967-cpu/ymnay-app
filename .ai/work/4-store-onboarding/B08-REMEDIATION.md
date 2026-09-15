# B08 REMEDIATION — repeated central password recovery

Engineer: `@Omar`  
Date: 2026-09-15  
Issue: #4  
PR: #5 / `feat/4-store-onboarding`  
Reviewed base: `e2bdaee3a1f8644451b23dd9d41c1234a1b381e6`  
Tested application commit: `f4816dcff241e7958f48a6b0fab1a551e1f2f5fa`

## Result

B08 is corrected in the central user password-recovery path used by store onboarding.

- Every recovery request now persists its newly generated token, replacing the previous token for the same email address.
- A successful password change consumes the matching token, so the link cannot be reused.
- The change is limited to the central user controller; the tenant password-recovery path is unchanged.
- The anonymous onboarding request remains in the same HTTP session throughout the recovery detour. Successful login with the final password claims it and resumes at step 5 with the selected plan, theme, store name and subdomain intact.
- B06 and B07 behavior is unchanged and remains covered by the existing suites.

## Required B08 coverage

- first recovery request creates a usable token;
- a second request before using the first replaces it and invalidates the first link;
- the replacement link changes the password and is consumed;
- a new request after a completed reset creates another usable token;
- login with the final password resumes the preserved onboarding request and choices;
- the same sequence is exercised through the real central forms, SMTP delivery to Mailpit and Chromium.

## Evidence

- [Isolated regression run 35036201722 / job 104605755635](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35036201722/job/104605755635): **PASS**.
- PHP syntax and routes: **PASS**.
- Laravel/Stancl/MySQL regression: **33 tests / 237 assertions / 0 failures / 0 errors / 0 skipped**.
- Durable provisioning stages: **12/12 PASS**.
- [Full-application run 35036201769 / job 104605756300](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35036201769/job/104605756300): **PASS**.
- Full-project Laravel suite: **20 tests / 97 assertions / 0 failures / 0 errors / 0 skipped**.
- Chromium evidence: **28 checks**, including `repeated-password-recovery-replaces-consumes-and-resumes-onboarding`; no browser-report error.
- Provisioning, tenant dashboard, native path, queue drain, storage/cache isolation and parallel-address race: **PASS**.
- [Full-application artifact 10423189323](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35036201769/artifacts/10423189323), GitHub digest: `sha256:8121320a4412bae8388e40edfb4c0a38cc6749bdfcd4be8e6c972b841909dbc2`.
- [Regression artifact 10423501914](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35036201722/artifacts/10423501914), GitHub digest: `sha256:726ca50200cffab3ad7ef9bfd56eba0d856047d29c005cb2f7bb134081782b81`.
- The full-application artifact records exact candidate `f4816dcff241e7958f48a6b0fab1a551e1f2f5fa` and contains only synthetic test data.

## Safety boundary

The workflows use disposable MySQL, Redis and Mailpit services. This remediation does not merge or deploy PR #5, change `main`, run production migrations, access production data, send real email or contact a payment gateway.

## Handoff

B08 is ready for `@Salem` to re-test on PR #5. The PR and Issue #4 remain open; merge and deployment remain owner-gated.
