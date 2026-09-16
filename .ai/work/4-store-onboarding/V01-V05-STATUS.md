# V01–V05 status — V01–V04 implemented; V05 not complete

Engineer: `@Omar`  
Date: 2026-09-16  
Issue: #4 / PR: #5 / branch: `feat/4-store-onboarding`  
Status: **IN_PROGRESS** — current agent remains Omar. **Not a handoff to Nour and not deployment approval.**

## Scope implemented

- V01: native radio selection plus a 2px indigo card border and `✓ محددة`, including keyboard/change/pageshow synchronization.
- V02: correct verification heading, edit-email and back actions; refresh restores safe name/email/phone fields and cooldown, never passwords or the OTP. Store choices remain on the existing draft.
- V03: configured Central terms/privacy pages open in safe new tabs without losing the form. An actual HTTP regression also covers public policy rendering on a fresh Central database without a legacy widgets table; installed legacy footers are preserved.
- V04: human-readable theme label, billing period with price, and four direct edit links. Immutable provisioning/ready/failed summaries do not expose edit actions.

No controller, service, route, migration, payment or provisioning implementation was changed. B08 application source is unchanged. The original E2E theme-preservation assertion now compares the selected display label rather than the raw slug.

## Exact tested candidate and evidence

Candidate: `dbbfc6abe0e60d9494ecc5c04bb83b71004566bf`. This status-file commit is documentation only; it does not make the failed V05 gate pass.

### Isolated regression — SUCCESS

Run: https://github.com/moehail967-cpu/ymnay-app/actions/runs/35138166558  
Artifact: `10464341881`  
Downloaded SHA-256, verified against GitHub: `873791da57f4ca322f93d14210ebc71d48113bd6c5713cd5b73bd5b2a4e21956`.

XML: **33 tests / 237 assertions / 0 failures, errors or skips**. Stage report: **12 PASS**, explicitly using the actual ProvisioningStages class with in-memory checkpoint storage, not SQL/E2E. The dependency audit remains a separate review input; dependencies were not updated or comprehensively security-assessed here.

### Full application — functional checks passed; overall run FAILED at V05

Run: https://github.com/moehail967-cpu/ymnay-app/actions/runs/35138166389  
Artifact: https://github.com/moehail967-cpu/ymnay-app/actions/runs/35138166389/artifacts/10463769516  
Downloaded SHA-256, verified against GitHub: `f54a11aeae8899145eb6ef97b8f4cf1747043ad029c9be48e918c17c533af9aa`.

`candidate-sha.txt` matches the candidate. Full-project XML: **24 tests / 144 assertions / 0 failures, errors or skips**. The original browser report completed **28 checks with no error**. Native creation and queue/provisioned-state verification passed, without a gateway or real charge.

The new visual report completed both device flows and recorded 25 observations/checkpoints, but its final result is **FAIL**, not PASS. `identity.json` has `verified: false`: `The public inner-page logo could not be observed.` Both device brand/font gates are unsatisfied. Recorded observations must not be misrepresented as passing brand checks.

## V05 blocker

All 22 state viewport screenshots were captured: 11 at 1440×900 and 11 at 390×844, plus 18 full-page companions. They show the implemented interactions but fall back to a text brand/default font. Manual inspection confirmed that this is **not a final brand evidence set**.

The public login template uses `.auth-logo`, while the capture script searched navbar/header logo containers. A local correction was prepared, but the connector write attempting to save that correction was blocked by OpenAI safety checks. It was **not applied in GitHub**. No alternate write route was used to bypass that block.

The remaining work is to resolve the unsuccessful script update, rerun the exact-candidate visual gate, and inspect the actual primary/white logo and loaded-font evidence. Only then should the task return to **Nour** for an explicit visual verdict; **Salem** follows only after Nour PASS. Previous independent B08 acceptance remains historical and is not replaced by this engineering run.

## Image index and data provenance

Inside the full artifact: `_temp/g01-visual-review/`.

For both `desktop-` and `mobile-` prefixes:

1. `01-landing.png` and `01-landing-full.png`
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

Full-page companions are supplied for below-fold content where recorded. See `visual-review-report.json`, `identity.json` and `fixture.json` for the failure and provenance. The account/store/legal/plan data is explicitly synthetic or representative, not a Production database copy. The Arabic preview is an actual repository theme with declared review content, not a generated mockup.

The fixture retains the fresh Central schema without a fabricated widgets table. Unchanged distribution assets are mirrored only to the disposable Artisan document root. There are no mocked browser responses or suppressed JavaScript error assertions. Font binaries are not included in the user image bundle.

## Deployment

No merge, deployment, Production migration, Production mutation, real-recipient mail or gateway payment occurred. Issue #4 and PR #5 remain open. No visual approval, comprehensive security approval, pixel-perfect claim or production-readiness claim is made.
