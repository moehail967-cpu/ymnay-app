# G01 — Full-application QA handoff

## Candidate and evidence

- Application candidate: `6cf2957d1a5579bc09f607754dd2b4b0c45c2d72`
- Full-application workflow: [run 35027323242](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35027323242), job `104577347956` — **PASS**
- Isolated regression workflow: [run 35027323215](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35027323215), job `104577347675` — **PASS**
- Full-application artifact: [artifact 10420366550](https://github.com/moehail967-cpu/ymnay-app/actions/runs/35027323242/artifacts/10420366550)
- Artifact digest: `sha256:901ea641c1192e3f771d9a29007c936c3f61baf3c72ae74832b6a308a1ffb718`
- Nour visual reference commit: `dfc56d81afb448a8432675d64d22c16340502164`

The evidence was produced in a disposable GitHub Actions environment using MySQL 8, Redis 7, Mailpit, PHP 8.4 and Node 22. The workflow checked out the exact candidate SHA and used only synthetic data. It did not access production configuration or data, send real email, contact a payment gateway, merge, deploy, or run production migrations.

## Executed coverage

### Existing regression suites

- Laravel full-project suite: **18 tests, 55 assertions**, no failures, errors or skips.
- Store-onboarding isolated regression: **32 tests, 230 assertions**, no failures or errors.
- Durable provisioning-stage checks: **12/12 PASS**.
- B01–B05 regression coverage remained green.

The isolated workflow records the repository's existing dependency advisories as a non-release informational step. That step is intentionally not claimed as a security-release PASS.

### Browser, HTTP and session path

The real application was started over HTTP with concurrent PHP workers and exercised through Chromium. The retained `browser-report.json` proves:

- landing page and onboarding render in RTL on desktop and mobile;
- plan, theme and store details survive navigation between all five steps;
- registration creates a real HTTP session and sends verification mail to Mailpit;
- resend throttling returns HTTP 429;
- an invalid OTP is rejected;
- a valid OTP establishes the authenticated session and reaches review;
- review values remain intact;
- provisioning completes and token login reaches `http://g01-browser-store.localhost/admin-home`;
- the tenant dashboard renders `.dash-card` without an exception page;
- a parallel address race has exactly one winner (`200/ready`) and one rejection (`422`).

### Provisioning, isolation and files

The retained provisioning reports prove:

- all stages are `done`: seed, domain, database, login key, migrations, store title and file dispatch;
- central database: `ymnay_g01_central`;
- browser tenant database: `ymnay_g01_tenant_g01-browser-store`;
- native/admin-path tenant database: `ymnay_g01_tenant_g01-native-store`;
- tenant admin, roles, selected `hexfashion` theme, title, pages and seeded records are available;
- three queued tenant-file copy jobs completed and `file_queue_drained=true`;
- the copied synthetic media is present in tenant storage and tenant data remains isolated;
- the native paid/admin creation path completes without a gateway or charge.

## Visual comparison with Nour's references

| Surface | Result | Evidence and remaining variance |
| --- | --- | --- |
| Landing desktop/mobile | Structural PASS | RTL direction, violet/white system, responsive layout and major sections are present. Hero imagery, branding and content differ from the perfume-oriented reference. |
| Onboarding desktop | Functional/structural PASS; not pixel-identical | Five-step flow, controls, review and RTL work. The implementation lacks the reference's illustrated side rail, dense card imagery/icons and compact two-column composition; it has larger empty areas. |
| Onboarding mobile | Responsive PASS; not pixel-identical | Single-column flow, readable controls and full-width primary action work. The synthetic fixture exposes one plan, so it does not reproduce the reference's four-plan density or template imagery. |
| Tenant dashboard | PASS | The dashboard is the real tenant admin screen, not an Ignition/error page. Synthetic fixture images such as the logo/avatar may be absent, which does not block the onboarding flow. |

The artifact contains the actual desktop/mobile screenshots alongside Nour's four approved reference images so QA can inspect the differences directly.

## Fixes required to make the full-app evidence pass

- Added an isolated, repeatable full-application workflow and browser/provisioning evidence harness.
- Ensured the tenant dashboard disk-size helper uses the configured disk and tolerates a file disappearing during enumeration.
- Made tenant media seed paths absolute and the queued copy job read central content before tenancy initialization.
- Added retryable local seed-file discovery without caching an empty list.
- Made the evidence pipeline fail on PHP/JSON verification errors instead of allowing a piped command to produce a false green result.

## Handoff status

G01 is ready for Salem's QA review. PR #5 and Issue #4 must remain open; no merge or deployment is included in this handoff.
