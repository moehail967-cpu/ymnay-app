# V01–V05 engineering handoff — complete, awaiting Nour review

Engineer: `@Omar`  
Date: 2026-09-16  
Issue: #4 / PR: #5 / branch: `feat/4-store-onboarding`  
Status: **NEEDS_REVIEW** — engineering evidence is complete; the next independent visual verdict belongs to `@Nour`.

## Implemented scope

- V01: native radio selection plus a 2px indigo card border and `✓ محددة`, including keyboard/change/pageshow synchronization.
- V02: correct «تحقق من بريدك» state, edit-email and back actions; refresh restores only safe name/email/phone fields and cooldown while preserving the store draft.
- V03: configured Central terms/privacy pages open in safe new tabs without losing the form. Public policy pages render on a fresh Central schema without fabricating a legacy widgets table.
- V04: human-readable theme name, billing period and post-trial price, plus four preserving edit actions. Immutable provisioning/ready/failed summaries expose no edit actions.
- V05: final 1440×900 and 390×844 evidence for the landing page, selected plan, selected/previewed Arabic theme, registration, restored OTP, edit email, review, provisioning, dashboard success and ready state.

## Exact candidate

Application/evidence candidate: `ff5f1e1afe8bb311babae5714922c04c74f4f968`.

The visual fixture uses the real public YMNAY primary and white logos, the observed `29LT Bukra` body font and `Tajawal` heading font, an actual repository Aromatic tenant provisioned through the native pipeline, and declared Arabic review content. Account, store, legal and plan records are synthetic/representative; they are not a Production database copy.

## Verification

### Full application — SUCCESS

Run: https://github.com/moehail967-cpu/ymnay-app/actions/runs/35141708620

Artifact: https://github.com/moehail967-cpu/ymnay-app/actions/runs/35141708620/artifacts/10466210103

Artifact SHA-256: `2a74214a5246ca33b980d1fc7e0bc9e2bf3291031cc9407e6f13f06f09f073f0`.

- Full-project tests: **24 tests / 144 assertions / 0 failures or errors**.
- Existing browser suite: **28 checks completed**, including real session/OTP mail, rate limiting, parallel completion, provisioning and tenant dashboard.
- Native paid/admin path without a gateway: PASS.
- Tenant file queue and provisioned-state verification: PASS.
- V05 report: **PASS**, **25 checks**, no limitations.
- Images: **22 viewport captures** — 11 desktop at 1440×900 and 11 mobile at 390×844 — plus **18 full-page companions**.
- `identity.json`: `verified: true`; primary and white logo hashes and loaded font observations are recorded.

### Isolated regression — SUCCESS

Run: https://github.com/moehail967-cpu/ymnay-app/actions/runs/35141708631

Artifact: https://github.com/moehail967-cpu/ymnay-app/actions/runs/35141708631/artifacts/10465960189

Artifact SHA-256: `d58ad584fba83b26cbe9848e23789f24b04f659bca3759dc620ff03121b2959e`.

- **33 tests / 237 assertions** without failure or error.
- **12 provisioning-stage checks PASS** within the documented isolated fixture limits.

## Image index

Inside the full artifact: `_temp/g01-visual-review/`.

For both `desktop-` and `mobile-` prefixes:

1. `01-landing.png`
2. `02-package-selected.png`
3. `03-theme-selected.png`
4. `04-theme-preview.png`
5. `05-registration.png`
6. `06-otp-restored.png`
7. `07-edit-email.png`
8. `08-review.png`
9. `09-provisioning.png`
10. `10-dashboard-success.png`
11. `11-ready.png`

Full-page companions are included where the content extends below the viewport. `arabic-template-source.png`, `public-brand-source.png`, `public-inner-brand-source.png`, `visual-review-report.json`, `identity.json`, `fixture.json`, the four approved Nour reference images and the tracked evidence sources are included in the same artifact.

## Boundaries and next action

This is engineering evidence, not Nour's independent visual approval and not Salem's final acceptance. `@Nour` must compare this exact package with the approved references and return PASS or reproducible changes. After Nour PASS, route the exact accepted candidate/package to `@Salem`.

No merge, deployment, Production migration, Production write, real-recipient mail or gateway payment occurred. No Production authorization is implied.
