# QA RETEST — B08 / Issue #4 / PR #5

Reviewer: `@Salem`  
Date: 2026-09-16  
Application candidate: `525d2a3e2d5143179a53e220bddcb44fd8e016b7`  
Application parent: `f4816dcff241e7958f48a6b0fab1a551e1f2f5fa`  
Tested QA-only head: `4de6a5713cbff9d71b0931c39f7808083c4057ad`  
**Verdict: PASS for B08 remediation and the independently retested adjacent scope. No new blocking application defect was established in this round.**

This closes the reproduced B08 finding on the exact candidate. It is not an unqualified whole-feature visual/security release certification. The existing final visual-conformance/owner acceptance item from prior reports is not silently waived. Issue #4 moves to NEEDS_REVIEW for that final review, not QA_FAILED and not READY_FOR_DEPLOYMENT. Do not send B08 back for another repair merely because whole-task acceptance is separate.

## Fresh executions

All evidence below was produced by new runs requested during this review. The ZIPs were downloaded, SHA-256 checked, and XML/JSON parsed. Actual plan, mobile review and recovered step-five screenshots were inspected.

| Execution | Result | Evidence |
|---|---|---|
| Exact candidate full application | SUCCESS; project PHPUnit 20 tests / 97 assertions / 0 failures / 0 errors / 0 skipped; 28 original Chromium check records without a browser error | [Run 35036619841 / fresh job 104612223782](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35036619841/job/104612223782), [artifact 10423704545](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35036619841/artifacts/10423704545) |
| Exact candidate isolated regression | SUCCESS; 33 tests / 237 assertions / 0 failures / 0 errors / 0 skipped; 12 stage checks PASS | [Run 35036619971 / fresh job 104612257028](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35036619971/job/104612257028), [artifact 10423742838](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35036619971/artifacts/10423742838) |
| QA-only independent browser supplement | SUCCESS; original engineer browser sequence plus historical Salem UI 12/12 PASS, historical acceptance/recovery 13/13 PASS, added lifecycle 6/6 PASS; zero instrumentation errors | [Run 35038481267 / job 104612822121](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35038481267/job/104612822121), [artifact 10424626140](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35038481267/artifacts/10424626140) |

The independent Salem suites total **31 checks, all PASS**, separate from the 28 engineer browser check records and PHPUnit counts. PHPUnit includes unit/markup/HTTP tests, not 20 complete browser journeys. The historical recovery diagnostic checking that a FAILED reset preserved the prior password is conditional: it did not run because the second reset now succeeds. No assertion was removed or weakened. The separate new lifecycle test explicitly verifies rejected links do not overwrite the current password.

Unlike the earlier failing supplementary runs, this supplement also completed the subsequent native creation, file-queue and provisioning/isolation verification steps. These have retained JSON evidence; they are not inherited by assumption.

### Verified ZIP digests

- Full application: `66051f970ef7602ed58e7203caaf04317bbe598594779d3dcb350923bb84c446`.
- Isolated regression: `e25127d21bb4a57d204087af81d9bc5854d12ca18a4045bae41de7dd58d4a9e7`.
- Independent browser: `6b7e560153415642684cf9f389a636e1c208dae15743d5a7ee459294f1c6cd75`.

## Provenance and inspected change

The candidate's last commit adds only B08-REMEDIATION.md. Its parent changes the central recovery controller and test/fixture code: updateOrInsert always persists the replacement token and created_at; successful password change deletes the matching email/token row. No B08 migration, tenant recovery implementation or visual application file was changed.

The full artifact's candidate-sha.txt is `525d2a3e...`; the independent artifact records the QA-only head above. Its wrapper verifies there is no diff in core/ or .github/workflows/ against the application candidate.

The copied engineer browser script is unchanged, blob `4a74ffd29786398821067eec33edea997cc6779b`. Historical Salem UI blob `b09f0fa87fe1b36b45706c23c9267f54be1444eb` and recovery blob `1650e236241b574a5d26d060da944a0e5a5795e3` are verified before execution. Only their candidate metadata is repinned. Provenance is recorded in salem-b08-provenance.json. The added six-check script is new independent QA, not an application fix.

## B08 results — actual HTTP/Chromium/Mailpit

1. The historical failing sequence now succeeds: first reset, new-password login, preserved owned request/choices, then another reset and login with that second password.
2. Issuing a new link before using the prior link produces a distinct replacement. The superseded link is rejected; its proposed password cannot log in; the existing password still works.
3. The latest replacement changes the password and authenticates successfully.
4. Reusing that consumed link is rejected and cannot overwrite the current password.
5. A further request after completed recovery creates a usable link and changes the password again.
6. The verified fixture account returns to `/create-store?step=5` with the same request reference, state and summary choices. Its draft remains a draft; password recovery does not itself create a store. The historical unverified fixture retains its required verification gate rather than being granted verification by recovery.

Evidence: salem-acceptance-report.json, salem-b08-lifecycle-report.json and salem-b08-recovered-step-five.png in the independent artifact. Reports do not retain passwords, reset URLs, mail bodies or session tokens.

## Adjacent regression and boundaries

B06 marker visibility/navigation and B07 exact plan limits/unlimited/SAR checks remain green on desktop/mobile. The actual full application also completed the configured registration/OTP journey, tenant provisioning and dashboard login, same-request completion, address contention, native no-gateway creation, file copying and the targeted database/cache/storage isolation probes. The isolated suite retains its declared adapters; its 12 stage checks use in-memory checkpoint storage. No broad security audit or all-theme/all-browser certification is implied.

The new password-link tests are sequential; no claim is made here about all possible concurrent reset requests, production SMTP delivery, or production configuration. These limitations are not newly invented B09 findings. Existing dependency advisories are not certified safe by a functional regression PASS.

## Whole-task final review and release boundary

B08 was the reproduced functional blocker in the immediately prior review and is now resolved in its tested scope. No additional implementation repair is handed back in this round.

Prior reports explicitly left final whole-interface visual conformance against Nour's package and real brand/content acceptance unapproved. The synthetic G01 screenshots are valid test evidence, not a substitute for final brand/visual acceptance. Route the task to **Owner / NEEDS_REVIEW**, with `@Nour` for that final visual review and `@Salem` for the consolidated exact-candidate acceptance record. This is not permission to waive a mandatory acceptance criterion. Do not move to READY_FOR_DEPLOYMENT until that record is complete.

B08 adds no migration. The whole PR still includes the earlier central store_onboarding_requests migration and documented historical migration compatibility changes. Production schema/worker requirements must follow the existing inventory and owner-gated deployment protocol. No live migration, merge, deployment, restart or rollback was performed or authorized.

## Handoff

Record B08 PASS, retain B01–B07 history without rewriting old failures, and keep Issue #4 and implementation PR #5 open. QA PR #10 is an evidence runner only; close it without merge after retaining this report. QA source changes are restricted to tests/evidence on qa/4-salem-b08-525d2a3e; implementation branch, main and Production are unchanged.
