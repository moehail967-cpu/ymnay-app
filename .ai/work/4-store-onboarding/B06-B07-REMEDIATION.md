# B06/B07 remediation handoff

## Candidate and retained evidence

- Application candidate: `d27c619b7d6ad6a9463bc34a5c524204a5054edf`
- [Full-application run 35032594407 / job 104594293473](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35032594407/job/104594293473): **PASS**, 19 tests / 75 assertions.
- [Isolated regression run 35032594470 / job 104594293575](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35032594470/job/104594293575): **PASS**, 33 tests / 237 assertions and 12/12 provisioning-stage checks.
- [Full-application artifact 10422172092](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35032594407/artifacts/10422172092), SHA-256 `7ddb533bf2e3d929d0acba15ac90bba92fc95c092fefeb24ada37de41f5fb0e6`.
- [Regression artifact 10421684272](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35032594470/artifacts/10421684272), SHA-256 `0d7b4fe57395a4944acdb64e05580c8470babe20161fc9489b2db6e58e9c058a`.

Both artifacts were downloaded, their ZIP digests were checked, and the retained JSON, XML and screenshots were inspected. The run used a disposable GitHub Actions environment with synthetic data, MySQL 8, Redis 7 and Mailpit. It did not use production configuration, customer data, real email or a payment gateway, and performed no merge, deployment or production migration.

## B06 — fixed-header clearance and completed-step navigation

The onboarding container now reserves the fixed header's height before rendering the five progress markers. The full-browser check reproduces Salem's visible-rectangle and `elementFromPoint` hit test at scroll position zero.

| Viewport | Header bottom | All five marker bounds | Marker center | Result |
| --- | ---: | ---: | ---: | --- |
| Desktop 1440 px | 72 px | 104–138 px | 121 px | All five visible and unobstructed |
| Mobile 390 px | 64 px | 88–122 px | 105 px | All five visible and unobstructed |

The same check also passed on the mobile OTP and review states. Clicking the completed first step from step three returned to it, kept the selected plan, and allowed the journey to continue without losing the remaining request data.

## B07 — actual, data-driven plan limits

Step one now renders each active plan's actual product, page, blog and storage limits. `-1` is rendered as `غير محدود`; null limits are omitted rather than invented. These values are also part of the saved plan snapshot, so a material limit change before completion requires acknowledgement just like a price or trial change.

The application fixture contains three distinct synthetic plans and the browser asserted the rendered cards, not source strings:

| Plan | Price | Trial | Products | Pages | Blogs | Storage |
| --- | ---: | ---: | ---: | ---: | ---: | ---: |
| Test | 149 SAR | 37 days | 100 | 10 | 10 | 512 MB |
| Growth | 249 SAR | 45 days | 350 | 25 | 40 | 2048 MB |
| Business | 399 SAR | 60 days | Unlimited | Unlimited | Unlimited | 5120 MB |

All three cards rendered the configured `ر.س` symbol with no dollar symbol. Three actual theme choices (`hexfashion`, `bakerco`, `aromatic`) were rendered and captured for the selected plan.

## Additional acceptance evidence completed

The retained `browser-report.json`, screenshots and provisioning verification now prove:

- the encrypted HTTP-only request reference resumes the same draft after the anonymous browser session cookie is cleared;
- a password-recovery detour preserves the same onboarding request and resumes step five;
- registration OTP invalid/valid behavior and resend throttling remain intact;
- an existing unverified account receives its initial verification email and a successful resend (Mailpit count 1 → 2);
- two parallel HTTP completions of the same request both resolve idempotently (`200`, `200`) without duplicate provisioning;
- a foreign request-reference cookie cannot cross the authenticated-account boundary;
- the different-account address race still has exactly one ready winner and one `422` rejection;
- tenant cache and storage probes are isolated between the browser tenant and the race tenant;
- all durable provisioning stages are `done`, the file queue is drained, and both the browser and native/admin tenants have working admins;
- mobile screenshots cover plan selection, OTP, review, provisioning/loading and the resulting tenant dashboard.

## Existing-install migration impact inventory

B06/B07 add no migration. The complete PR diff against `main` currently contains the following migration files from the earlier onboarding work:

| File | Scope | Existing-install impact |
| --- | --- | --- |
| `2026_09_14_000001_create_store_onboarding_requests_table.php` | Central | New pending migration creates `store_onboarding_requests`; rollback drops only that table. Foreign keys point to central `users` and `price_plans`. |
| `2022_04_20_100718_create_permission_tables.php` | Central/fresh install compatibility | Historical migration uses configured pivot-key names instead of removed package statics. Already-migrated installations do not rerun it and receive no schema mutation. |
| `2022_07_04_125618_create_newsletters_table.php` | Central/fresh install compatibility | Historical migration skips creation if the module already created the central table. Already-migrated installations do not rerun it. |
| `Modules/NewsLetter/.../2020_02_04_010636_create_newsletters_table.php` | Central/fresh install compatibility | Equivalent duplicate-table guard plus PHP migration-class compatibility. Already-migrated installations do not rerun it. |
| `2022_09_11_084331_add_widget_namespace_column_to_widgets_table.php` | Tenant-owned table / central loader compatibility | Historical migration now checks table existence before altering it. Already-migrated installations do not rerun it; no new tenant migration is introduced. |

Fresh central and tenant schema creation passed in the full-application run. For an existing installation, the only new pending schema operation in this PR is the central onboarding-request table. Production execution and rollback rehearsal remain release operations and were intentionally not performed in this remediation.

## Handoff status

B06 and B07 are ready for Salem's independent retest on the exact application candidate above. PR #5 and Issue #4 remain open. This is `READY_FOR_QA`, not merge, deployment or production-migration authorization.
