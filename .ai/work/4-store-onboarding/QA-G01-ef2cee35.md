# G01 QA result — Issue #4 / PR #5

Reviewer: `@Salem`
Application candidate: `ef2cee35d42d6834d3ca8acda019d79628faf68b`
QA-only test commit: `330854ad159fb56fce2cdff888c6a9a408a5645f`
Verdict: **FAIL — B06 and B07 require correction.**

**G01 environment availability is resolved.** The actual application boots, builds and completes the tested registration-to-tenant-dashboard flow. B05 is not being returned as failing. Overall acceptance is not granted because of the findings and remaining evidence below.

## Fresh execution evidence

The reviewer requested new executions, downloaded their artifacts, verified ZIP SHA-256 values and parsed their XML/JSON. All fixtures are synthetic and isolated.

- Full application: [run 35028050058 / fresh job 104583570764](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35028050058/job/104583570764), SUCCESS. Project JUnit: **18 tests / 55 assertions / zero failures, errors or skips**. Artifact [10421315402](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35028050058/artifacts/10421315402), SHA-256 `18a3aeb38d8cae6cb21c994431845257822d0338281bd63c7055b562dff2aae1`.
- Isolated regression: [run 35028050127 / fresh job 104583579038](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35028050127/job/104583579038), SUCCESS. **32 tests / 230 assertions / zero failures, errors or skips**, plus **12 stage checks PASS**. Artifact [10420083922](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35028050127/artifacts/10420083922), SHA-256 `677d45866f446474eb485bf0b305c60f480730b255335f6593834137733bca96`.
- Independent Chromium supplement: [run 35029830955 / job 104585469804](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35029830955/job/104585469804), FAILURE from visibility assertions. **12 checks: 10 PASS / 2 FAIL / zero instrumentation errors**. Artifact [10420988254](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35029830955/artifacts/10420988254), SHA-256 `9ad558116dc7f2469c6ee0f8beae6995b215828670938eae6d38e7f8d19d26a7`.

Candidate-sha.txt in the fresh full-app artifact contains the application head above. The supplement checks out the QA-only commit; GitHub compare confirms only three G01 test files differ. The original browser script is preserved unchanged as `engineer-e2e.mjs`, blob `f512420d2a6cf166047ca9959394530f0c7a2b08`, and runs before the independent checks.

The supplement failed at its browser step; subsequent native/queue/reference steps were skipped in THAT run. They passed in the separate full-app rerun. The 18 project tests include markup/example tests and four G01 HTTP tests; they are not 18 browser journeys. Stage checks use in-memory checkpoint storage. The isolated regression retains its documented adapters.

## Verified behavior

Real Chromium completed plan, theme, details, registration, Mailpit OTP, review, provisioning and actual tenant dashboard login. Wrong OTP was rejected; early resend returned 429. The address race produced one 200/ready and one 422. Separate verification checked completed provisioning stages, actual theme/admin/pages, distinct tenant databases and titles, copied synthetic files and a drained file queue. The native creation fixture used no gateway or charge.

The actual HTTP feature suite covers registration-OTP expiry/removal, existing-account verification with fake mail, and unauthorized/foreign-request denial. Do not keep these tests labelled wholly unexecuted.

Independent checks passed at both 1440x900 and 390x844 for the configured 37-day trial, preview Escape/focus return, reserved-address rejection, the five approved account fields and Back preserving store details.

## B06 — Fixed header obscures the five-step navigation

**BLOCKER: explicit visible-progress requirement. Owner: `@Omar`.**

Expected: five clear markers below the header and usable completed-step navigation, as required by Issue #4 and section 5 of [Nour's specification](https://github.com/moehail967-cpu/ymnay-app/issues/4#issuecomment-5671359390).

Actual at scroll zero:
- Desktop 1440x900: all markers top=48, bottom=82, center y=65; hit testing reaches `.ym-public-container.ym-nav-row` rather than the markers. The 72px fixed header covers most of them.
- Mobile 390x844: all markers top=28, bottom=62, center y=45; header content is above all five. The 64px header completely hides them.

Evidence: `g01-browser-evidence/salem-ui-report.json`, `salem-desktop-step-1.png`, `salem-mobile-step-1.png` in the independent artifact. Both failing assertions concern this same defect. DOM presence alone is insufficient.

Source: `.ym-site-header` in `core/public/assets/new-landlord/css/ymnay-public.css`; `.ym-onboarding` padding 48px/28px and `.ym-progress` in `core/resources/views/landlord/frontend/onboarding/store-setup.blade.php`.

Reproduce by opening /create-store at both viewports and comparing marker rectangles/hit tests against the header. Correct the shared layout without hiding the header in tests. Retest the same visibility assertions and completed-step navigation on later steps.

## B07 — Plan selection omits configured limits

**BLOCKER: missing required selection information, not a security finding. Owner: `@Omar`.**

Issue #4's primary flow explicitly requires actual plan limits at step one. The card template renders only title, price/cadence and trial text, with no relevant limits or details link. G01 prepare.php sets product=100, pages=10 and blog=10, but actual browser card text in both viewports is only the plan name, `149$ / شهريا` and `37 يوم تجربة مجانية`.

This is confirmed by the complete step-one template and captured DOM text/screenshots. It is a separate source/acceptance finding, not another failing automated visibility assertion, and is not inferred from the fixture having only one plan.

Sources: step-one card in `core/resources/views/landlord/frontend/onboarding/store-setup.blade.php`; `.ai/work/4-store-onboarding/g01/prepare.php`; approved Issue #4 Primary User Flow step 1.

Render actual relevant limits with readable labels and add data-driven assertions using several synthetic plans with different limits. Preserve dynamic prices, trial days and saved choices.

## Visual interpretation and remaining evidence

All four reference images at `dfc56d81afb448a8432675d64d22c16340502164` were opened and relevant actual screenshots inspected. No complete visual PASS is granted.

Absence of the illustrated marketing side rail is NOT itself a blocker: the written specification says plan/theme selection uses full width and no long marketing panel should consume form space. Generated logo/placeholder names/dates/perfume imagery are not literal requirements. Larger whitespace and the one-plan fixture alone do not establish defects; representative multi-plan/multi-template evidence is still needed.

The tested wizard/review shows dollars rather than SAR. Fixture preparation sets currency-related options, but this review has not established the formatter's effective keys. This is an unresolved configuration/evidence gate, NOT proof that Production currency is wrong. Provide correctly configured SAR evidence without hardcoding or changing tenant currency policy.

Remaining material coverage includes browser session expiry/password-recovery resumption, the complete existing-account resend path, same-request parallel HTTP completion, broader cross-tenant HTTP/session/cache/storage denial, and remaining mobile OTP/review/loading/success states. Registration OTP expiry already has the passing HTTP test noted above. Do not equate distinct database names with complete authorization certification.

The G01 diff also modifies existing permission/newsletter/widget migration files plus the feature's new central onboarding migration. Fresh-schema execution passed; existing-installation upgrade/rollback impact is not thereby certified. Inventory Central/Tenant impact before a later release. No live migration is authorized by this review.

## Handoff

Set the same Issue #4 to QA_FAILED, Current Agent `@Omar`, Reviewer `@Salem`. Correct B06 and B07 together and supply a new exact candidate, regression results and representative SAR/plan/theme screenshots. Use Nour for visual clarification and complete the specifically outstanding acceptance paths in the NOW WORKING environment. Do not request another environment from scratch or relabel B05 as failing.

QA-only PR #8 is an evidence runner and should be closed without merge after retaining evidence. PR #5 and Issue #4 remain open. Salem changed test/evidence files only; application source, implementation branch, main and Production were unchanged. No merge, deployment, real-recipient mail, gateway charge or service operation was performed.
