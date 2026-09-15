# QA RETEST — Issue #4 / PR #5 / e2bdaee3

Reviewer: `@Salem`  
Review date: 2026-09-16 (Asia/Aden; CI timestamps 2026-09-15 UTC)  
Application candidate: `e2bdaee3a1f8644451b23dd9d41c1234a1b381e6`  
Application parent: `d27c619b7d6ad6a9463bc34a5c524204a5054edf`  
Final tested QA-only head: `178b547d8a4458961166dbccb317151093d28a22`  
Verdict: **FAIL — new B08 blocks required password-recovery acceptance. B06 and B07 PASS in the independently retested scope.**

The full application environment remains available. There is no renewed G01 environment blocker and no claim that B01–B07 are failing again. No production or merge authorization.

## Fresh executions and provenance

| Execution | Observed result | Evidence |
| --- | --- | --- |
| Unchanged-candidate full application, run 35033066854 / fresh job 104597969403 | SUCCESS; project PHPUnit **19 tests / 75 assertions / 0 failures / 0 errors / 0 skipped**; build, real browser journey, native creation and file-queue verification succeeded | [Run/job](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35033066854/job/104597969403), [artifact 10423100338](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35033066854/artifacts/10423100338) |
| Isolated regression, run 35033066874 / fresh job 104597996878 | SUCCESS; **33 tests / 237 assertions / 0 failures / 0 errors / 0 skipped**, plus **12 stage checks PASS** | [Run/job](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35033066874/job/104597996878), [artifact 10422537192](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35033066874/artifacts/10422537192) |
| Independent Chromium supplement, run 35034376672 / job 104599982938 | FAILURE from the repeated-reset assertion: historical UI suite **12 PASS**, added acceptance suite **13 PASS / 1 FAIL**, both with **0 instrumentation errors** | [Run/job](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35034376672/job/104599982938), [artifact 10423240465](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35034376672/artifacts/10423240465) |

The two independent browser suites total **26 checks: 25 PASS / 1 FAIL**, not 26 PHPUnit tests. The existing engineer browser suite runs unchanged before the supplement. Native/queue/reference steps AFTER the supplement were skipped in its failing run; those steps passed in the separate unchanged-candidate rerun above. These results must not be conflated.

All three ZIP files were downloaded; SHA-256 digests matched GitHub, and retained XML/JSON were parsed:

- Full application: `51f9922135abe2db1bfe391ca7752adef6f7bf45ad91dd8e10f7aed538ac8ffe`.
- Isolated regression: `3a078795f432a3822e0f26b0f39a79eb8ef1771cd0c976727398e0ef38fba371`.
- Final independent browser: `66cc7051ccd5747374f7f1841141239685550bd6ecebc793e9386fba911ab8a7`.

The unchanged full-app artifact's `candidate-sha.txt` is the application head above. The independent artifact records the final QA-only head. QA PR #9 changes only four G01 test-harness files; it was closed without merge after execution. Current engineer browser source is preserved as `engineer-e2e.mjs`, blob `e5d103a508b54fe0c9dd8c74423f4b1d58916842`. The historical Salem UI script is preserved as blob `b09f0fa87fe1b36b45706c23c9267f54be1444eb`; only its candidate metadata is repinned at runtime, with all 12 assertions unchanged. The wrapper verifies application view blob `c1a3273e8bf4d51cfe3b1f2b217b99a7103cad30` and writes provenance.

## B06 — PASS

All five progress markers are below the fixed header and unobstructed at 1440×900 and 390×844 in the independent visibility/hit tests. The unchanged-candidate browser evidence records desktop bounds y=104–138 below a 72px header, and mobile y=88–122 below a 64px header. Completed-step links work and preserve the selected plan on both sizes. The historical failing assertions now pass without hiding or changing the header in the test.

## B07 — PASS

Independent DOM assertions verified each displayed limit by its label on each of the three synthetic plans, rather than accepting a number anywhere in a card:

| Synthetic plan | Products | Pages | Blog | Storage | Trial |
| --- | ---: | ---: | ---: | --- | ---: |
| Test | 100 | 10 | 10 | 512 MB | 37 days |
| Growth | 350 | 25 | 40 | 2048 MB | 45 days |
| Business | غير محدود | غير محدود | غير محدود | 5120 MB | 60 days |

SAR is displayed with `ر.س`, without `$`, on desktop/mobile plan cards. Actual theme-form radio choices are aromatic, bakerco and hexfashion. No horizontal page overflow was measured on the tested plan screens. Current source includes limits in the plan snapshot; the current passing regression suite includes changed-limit acknowledgement coverage. These are synthetic test values, not a statement of production plan configuration.

### QA harness correction, not an application defect

Initial independent run 35033984307 also reported two theme-count failures because the global selector counted three theme radios plus an unrelated hidden `theme_slug` input in `/order-confirm`. The final test selects the actual theme form's radios and compares their exact three values. Both checks pass. The recovery test was not weakened or changed; it failed in both runs. The correction and retained initial run are part of the QA trail, not extra product findings.

## B08 — Repeated password recovery mails an unusable replacement link

**BLOCKER / P1 for the explicitly required onboarding recovery flow. Owner: `@Omar`.**

### Expected

A central account owner can request a new password-reset link, use the latest valid link to change their password, log in with it, and resume the same owned onboarding request. An earlier reset request or successful reset must not cause the next emailed link to lack a corresponding reset record.

### Actual reproduction — real browser/HTTP/Mailpit

Using only the synthetic existing G01 account and its saved draft:

1. Log in, record the owned request reference and selections, then log out.
2. Request reset through the actual form and retrieve its email from local Mailpit.
3. Use the first link and set a new test password. Redirect to login succeeds, the new password is accepted, and the same draft/reference/selections are preserved: PASS.
4. Log out and request another reset link. A new email is captured.
5. Use that new link and submit another test password. The page displays a reset error and does not redirect to login. Login with the proposed password returns HTTP 200 with `status=invalid`: FAIL.
6. Login with the previous password still returns `status=valid`: the failed second reset did not overwrite it.

Evidence: `salem-acceptance-report.json`, and `salem-second-password-reset-rejected.png` in the final independent artifact. There are zero instrumentation errors. No production mailbox, account, reset link or customer data was used. No account takeover or loss of the prior password is claimed.

### Source diagnosis and regression attribution

In `LandlordFrontendController::sendUserForgetPasswordMail`, the result of deleting an existing `password_resets` row is assigned to `$existing_token`. The new token is inserted only when that deletion result is empty. When a prior row exists, deletion succeeds and the replacement insert is skipped, but a link containing the newly generated token is still mailed. `UserResetPassword` accepts only a matching stored email/token and leaves the old row after a successful reset. This explains the reproduced second-request failure.

- [Candidate source, request and consume methods](https://github.com/moehail967-cpu/ymnay-app/blob/e2bdaee3a1f8644451b23dd9d41c1234a1b381e6/core/app/Http/Controllers/Landlord/Frontend/LandlordFrontendController.php#L605-L677), blob `ba1b4afbafed30f0bcd5b4ee24b4259137235e7b`.
- [Same logic in the PR base](https://github.com/moehail967-cpu/ymnay-app/blob/215020153b1bd8346a79cf5349679dc09a7ad6c9/core/app/Http/Controllers/Landlord/Frontend/LandlordFrontendController.php#L543-L610).

**This is pre-existing code exposed by the required real recovery review, not a defect introduced by B06/B07.** It is in scope because Issue #4 and the previous G01 handoff explicitly require working password-recovery resumption. The engineer's previous recovery check only visited the recovery page and returned; it did not submit or consume a reset link. No inference of live production incidents is made.

### Correction and retest expectation

Repair the central reset-record replacement/consumption lifecycle in its existing pattern; the successfully issued latest link must have a matching usable record even when a prior record existed. Keep unrelated authentication, tenant and business policies unchanged. Preserve the owned onboarding request and selections through recovery. Add regression cases for first reset, repeated issuance before use, repeat after a completed reset, correct new-password login and same-request resumption. Rerun the failed real-browser sequence plus adjacent B01–B07 checks on a new pinned candidate. Do not use production data to reproduce or fix it.

## Other acceptance evidence and limits

The fresh unchanged-candidate execution completed registration, invalid/valid registration OTP and resend cooldown via Mailpit, provisioning, actual tenant dashboard login, same-request HTTP completion and a different-account address race. Native creation without a gateway, real file-queue drain, distinct tenant database/title checks and targeted Redis cache/storage isolation probes also passed. Those targeted checks do not constitute a complete security audit. The project PHPUnit count includes markup/unit/HTTP tests, not 19 complete user journeys; isolated regression retains explicit test adapters.

Actual desktop/mobile plan/theme/review images and Nour's four references were inspected. Progress and plan-limit findings are resolved; no pixel-identical or whole-interface acceptance is claimed. Synthetic branding/assets are not evidence of production branding. The written specification remains authoritative over generated-image artifacts and does not require the disputed long marketing side panel.

First actual password recovery and request preservation are now verified; repeated recovery is the concrete remaining blocker. Any final broad visual/authorization or release sign-off must remain explicit rather than inferred from these passing samples. The earlier migration inventory remains relevant: B06/B07 add no migration; production schema state/upgrade/rollback was not exercised. Existing dependency advisories were not upgraded or certified safe in this review.

## Handoff and retained artifacts

Set the same Issue #4 to `QA_FAILED`, Current Agent `@Omar`, Next Agent/Reviewer `@Salem` after B08 remediation and exact-candidate handoff. Do not ask for the G01 environment again or re-open resolved B06/B07 as failures. No duplicate development issue.

Tests remain on `qa/4-salem-e2bdaee3` in closed-without-merge PR #9. Implementation PR #5 and Issue #4 remain open. Only test/evidence files were changed by Salem; application source, implementation branch, main and production were unchanged. No merge, deploy, production migration, restart, real-recipient mail or gateway payment was performed.

The curated evidence bundle retains report/JSON/XML/provenance, selected screenshots and reference images. Raw runtime/server logs, .env, dependencies, mail payloads, password values and reset-token URLs are excluded from that bundle. Refer to the checked-in QA scripts for reproducible synthetic test setup.
