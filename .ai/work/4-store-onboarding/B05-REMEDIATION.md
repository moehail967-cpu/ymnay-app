# B05 REMEDIATION — verification mail delivery outcome

Engineer: `@Omar`  
Date: 2026-09-15  
Issue: #4  
PR: #5 / `feat/4-store-onboarding`  
Reviewed base: `5a7013b04c70fbe2ce474f53b491326b2aca89b1`  
Tested application commit: `583ddeeb186f8bc9b12e7c27e078ae0e08e9dc69`

## Result

B05 is corrected in the onboarding-owned verification path.

- `VerifyUserMailSend::sendMailForOnboarding()` returns a boolean delivery outcome without changing the legacy `sendMail()` contract used outside this flow.
- The candidate user row is locked and token rotation occurs in the same database transaction as the synchronous mail attempt. A thrown delivery error rolls the transaction back, retaining the previous usable token (or `null` for an initial send).
- Both the first verification-form send and explicit resend honor the result. Failure returns a safe generic message with `type=danger`; success alone returns the existing sent confirmation.
- The verification view suppresses its sent notice when delivery failed and keeps the resend action available.
- Logs contain only user id, exception class and numeric code; transport messages, credentials, addresses and verification tokens are not exposed.
- Request ownership/status guards and the pre-verification provisioning gate are unchanged. No store, domain or trial is created in the failure tests.

## Regression coverage

The engineer suite now covers:

- initial delivery rejection with no persisted unusable token;
- resend rejection codes 550 and 553 with danger feedback;
- preserved request/session/status and the previously delivered token after rejection;
- successful resend after service recovery, invalidation of the old token and acceptance of the replacement;
- use of a previously delivered token after a failed resend;
- wrong-code denial, verified-user behavior, ownership guards and no premature provisioning;
- all existing B01-B04 and provisioning regressions.

## Evidence

- [Run 35012013713 / job 104526039684](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35012013713/job/104526039684): `completed / success` on application commit `583ddeeb186f8bc9b12e7c27e078ae0e08e9dc69`.
- PHP syntax: PASS for routes, controller, strict mail helper, onboarding services/events/listener/provider and tenant seeder.
- PHPUnit: **32 tests / 230 assertions / 0 failures / 0 errors / 0 skipped**.
- Durable provisioning stages: **12 PASS / 0 FAIL**.
- [Artifact 10413678628](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35012013713/artifacts/10413678628) contains JUnit, stage results and dependency audit. Downloaded ZIP SHA-256 matched GitHub: `3955304c5e92fc1a099d8a50a8985f3324a67597f5ed165eb9cc80af4f53c329`.

## Verification boundary

The PHPUnit fixture executes the actual candidate controller/helper/models with Laravel, Stancl and disposable MySQL. Authentication, tenant content and mail transport are explicit test adapters; the rejected delivery cases are injected exceptions, not real SMTP incidents. Full application HTTP/middleware/session execution, actual SMTP delivery, OTP expiry, real module/theme provisioning, dashboard login, queues/isolation and Desktop/Mobile/RTL visual acceptance remain `@Salem` gates.

This remediation does not merge or deploy the PR, run a Production migration, send real mail, or change `main`. The existing central onboarding migration remains owner-gated for Production.
