# YMNAY Stitch-to-Code Design Workflow

**Owner mandate:** Google Stitch is YMNAY's preferred AI design environment for applicable UI/UX exploration and redesign work, with `@Nour` owning the design stage and `@Omar` owning application-code implementation.

**Scope:** This document governs design work that starts from an existing YMNAY interface, code/design context, screenshots, or an approved Product Brief and proceeds through Stitch to an owner-approved engineer handoff. It does not replace YMNAY source code, project rules, business requirements, QA, or deployment controls.

**Authority:** GitHub remains the technical source of truth. Current executable YMNAY code and current project rules outrank generated designs. Stitch output is a design artifact until the owner approves it and Omar implements it in the application.

## Why this workflow exists

The intended operating model is:

```text
Current YMNAY app / approved Product Brief
        ↓
@Nour inspects the real current UI and relevant source
        ↓
Google Stitch receives the smallest useful design context
        ↓
@Nour iterates UI/UX + design system + prototype
        ↓
Owner reviews and approves exact design artifacts
        ↓
@Omar / Codex implements the approved design in existing YMNAY code
        ↓
@Salem verifies behavior + design conformance
        ↓
Owner-controlled release process when deployment is in scope
```

The purpose is to separate **design intent** from **software implementation** while keeping both synchronized.

## Verified external Stitch capabilities

The following are external Google Stitch capabilities, not claims about YMNAY runtime behavior. Re-verify them when a task depends materially on a capability because Stitch is an evolving Google Labs product.

### VERIFIED — Google, March 18, 2026

Google states that Stitch can:

- accept images, text, and code as canvas context;
- create and iterate high-fidelity UI through an AI-native design canvas;
- extract a design system from a URL;
- import/export design rules using `DESIGN.md`;
- turn screens into interactive prototypes and connect flows;
- integrate with external development workflows through a Stitch MCP server and SDK.

Source: https://blog.google/innovation-and-ai/models-and-research/google-labs/stitch-ai-ui-design/

### VERIFIED — Google, April 21, 2026

Google states that the Stitch `DESIGN.md` format can export/import design rules between projects and tools, and published its draft specification for cross-platform use.

Source: https://blog.google/innovation-and-ai/models-and-research/google-labs/stitch-design-md/

### VERIFIED — Google, May 19, 2026

Google states that users can bring an existing codebase and design files into Stitch as design context, in addition to text/voice prompts.

Source: https://blog.google/innovation-and-ai/models-and-research/google-labs/stitch-updates/

## Critical interpretation rule

Do **not** convert an external Stitch capability into an assumed local integration.

Examples:

- Google publishing a Stitch MCP server does **not** prove the current Codex environment is connected to it.
- A Stitch share link does **not** prove Omar can access the underlying project.
- A generated `DESIGN.md` does **not** automatically become YMNAY's canonical design system.
- Imported code in Stitch is design context; Stitch is not the authoritative YMNAY code editor.

For every task, distinguish:

- **VERIFIED TOOL CAPABILITY** — supported by current Google documentation;
- **VERIFIED TASK ACCESS** — confirmed working in the active environment/session;
- **NOT VERIFIED / UNAVAILABLE** — use artifact-based fallback and record the limitation.

## Role boundaries

### `@Nour` — Stitch design owner

Nour owns:

- current-state visual/UX inspection;
- selecting the smallest useful context for Stitch;
- establishing or applying design rules;
- Stitch design iterations;
- responsive, RTL/LTR, accessibility, component and state design;
- prototypes/user-flow design;
- real visual artifacts/screenshots/references;
- owner design-review package;
- implementation-ready handoff to Omar after owner approval;
- optional post-implementation design-conformance review.

Nour does **not** implement Laravel, Blade, Vue, JavaScript application logic, database changes, backend APIs, migrations, deployment, or production code changes.

### `@Omar` — implementation owner

Omar owns:

- translating the approved design into YMNAY's existing implementation patterns;
- mapping design components to existing Blade/Vue/theme/Page Builder/frontend structures;
- preserving backend/business behavior unless an approved requirement changes it;
- engineering validation and implementation evidence;
- handing the implementation to Salem for QA.

Generated HTML/code from Stitch is a reference or acceleration artifact, not a command to replace YMNAY architecture blindly.

### `@Salem` — verification owner

Salem verifies the implemented candidate against:

- approved requirements;
- approved Nour/Stitch design artifacts;
- responsive/RTL/state behavior;
- regression risk and agreed QA scope.

## When Nour must use this workflow

Use this workflow when the owner or task asks for any of the following and Stitch access is available:

- redesign or improvement of an existing YMNAY page/flow;
- "same functions, better UI/UX";
- a new UI based on an existing YMNAY design language;
- a multi-screen customer journey that benefits from an interactive prototype;
- creation/refinement of a reusable YMNAY design system;
- explicit use of Google Stitch;
- a design package intended for Omar/Codex implementation.

For a tiny cosmetic request, Stitch may be unnecessary. Do not expand a small task merely to force this workflow.

## Phase 1 — Ground in the current application

Before designing, Nour must establish what users actually see and what must not change.

When access is available:

1. Open the task-relevant current page/flow in an authorized browser.
2. Record route/page, actor/context, language and direction.
3. Inspect the relevant desktop and mobile layouts; include tablet only when it materially affects the feature.
4. Capture important current states relevant to the task.
5. Inspect the smallest relevant source path/components when needed to understand reuse/constraints.
6. Separate visual problems from business/product problems.
7. List preserved functions explicitly.

Do not infer the visual baseline only from source code when the real interface can be inspected.

## Phase 2 — Build the Stitch context package

Give Stitch enough context to design accurately, but do not dump the entire repository unnecessarily.

Preferred context order:

1. approved Product Brief / owner requirements;
2. current screenshots of the exact flow;
3. current live URL when safe and useful for design-system extraction;
4. relevant design files;
5. relevant frontend/code context when Stitch import is useful;
6. current approved design rules / `DESIGN.md`, if one already exists;
7. explicit preserved functions and do-not-change constraints.

Never provide production secrets, `.env` values, credentials, private keys, tokens, customer data, payment proofs, logs containing sensitive information, or unrelated repository content.

## Phase 3 — Establish the design system

### Existing approved `DESIGN.md`

If an owner-approved YMNAY `DESIGN.md` exists for the applicable surface, use it as the starting design language unless the task explicitly changes it.

### No approved `DESIGN.md` yet

Nour may use Stitch to extract/derive design rules from the current site, URL, screenshots, or design artifacts. The generated file remains **PROPOSED** until owner approval.

A usable YMNAY design-system artifact should cover, when applicable:

- brand colors and semantic color roles;
- typography hierarchy and Arabic/Latin implications;
- spacing scale;
- radii and elevation/shadows;
- buttons and action hierarchy;
- forms and validation treatment;
- cards, tables, navigation and dialogs;
- iconography style;
- container/grid rules;
- responsive breakpoints/behavior;
- RTL/LTR rules;
- accessibility/contrast expectations;
- reusable state patterns.

Do not change the whole product identity just because Stitch generates a visually attractive alternative.

## Phase 4 — Design in Stitch

The default redesign instruction should make preservation explicit.

Recommended pattern:

```text
Redesign this existing YMNAY interface while preserving its current approved functions,
field meanings, business rules and user journey unless the supplied requirements explicitly
change them. Improve information hierarchy, spacing, readability, interaction clarity,
responsive behavior, accessibility and visual consistency. Follow the supplied YMNAY design
rules. Produce realistic desktop and mobile views and correct RTL behavior for Arabic.
Do not invent product rules, pricing, permissions, fields or backend behavior.
```

For a multi-step flow add:

```text
Keep the approved step order and validation/business requirements. Design the transition,
loading, validation, success and failure states, then connect the screens as an interactive
prototype so the complete user journey can be reviewed before engineering.
```

For exploration, Nour may ask Stitch for materially different visual directions, but each direction must respect the same fixed requirements. Do not generate superficial variants that differ only in color.

## Phase 5 — Required screen/state coverage

For substantial Stitch tasks, cover what is relevant from:

- desktop;
- mobile;
- tablet when materially different;
- Arabic RTL;
- English/LTR when the feature supports both and direction affects layout;
- default;
- hover/focus/active where material;
- loading/skeleton;
- empty;
- validation error;
- server/general error;
- success;
- disabled;
- confirmation/destructive confirmation;
- permission-restricted/locked when applicable.

Do not mark a design complete merely because the "happy path" screen looks polished.

## Phase 6 — Prototype the user journey

For multi-screen or multi-step work, Nour should use Stitch prototypes when the capability is available.

The prototype must test the intended sequence rather than merely connect arbitrary screens.

Review at least:

- entry point;
- primary action;
- next/back behavior;
- validation failure;
- loading/processing;
- completion/success;
- cancellation/exit where relevant;
- mobile navigation behavior.

A prototype demonstrates interaction intent; it does not prove backend behavior.

## Phase 7 — Owner design review gate

Nour must present **actual visual evidence**, not only a written description.

The review package should include, when produced:

- Stitch project/share reference;
- exact screens proposed for approval;
- desktop visuals;
- mobile visuals;
- RTL visuals;
- prototype reference for flows;
- proposed/updated `DESIGN.md` when applicable;
- `UI-UX-SPEC.md`;
- list of preserved functions;
- list of intentional changes;
- unresolved product decisions;
- known Stitch/access limitations.

The owner approves the **specific artifact/version**, not the general idea.

Before owner approval:

```text
Status: NEEDS_REVIEW
Current Agent: Owner
Review Required: YES
Reviewer: Owner
```

After the owner explicitly approves the exact design package, Nour may hand it to Omar.

## Phase 8 — Handoff to Omar / Codex

The handoff should make implementation possible without visual guessing.

Include:

- approved Stitch project/share reference if available;
- approved screen identifiers/names;
- approved prototype reference;
- approved `DESIGN.md` or scoped design rules;
- desktop/mobile/RTL visuals;
- `UI-UX-SPEC.md`;
- Product Brief / acceptance criteria when one exists;
- current page/route/source references;
- preserved functions;
- explicit changes;
- do-not-change constraints;
- component/state behavior;
- responsive behavior;
- accessibility expectations;
- likely existing YMNAY components to reuse;
- open blockers only if implementation truly cannot proceed.

### MCP / direct tool handoff

If the active Codex environment has an authorized, verified Stitch MCP/SDK integration, Nour/Omar may use it to retrieve the approved design context directly.

Before relying on MCP, verify in the active task/session:

1. the Stitch connector/server is actually available;
2. authentication/authorization is working;
3. the correct Stitch project can be opened;
4. the approved screen/version can be identified;
5. no secret is being committed to GitHub.

If any of these are not verified, use the artifact handoff instead. Never write "Codex is connected to Stitch" based only on Google publishing an MCP server.

## Phase 9 — Omar implementation rules

Omar should implement the design in the existing YMNAY architecture rather than replace the architecture with generated output.

Omar must:

- inspect the current implementation before changing it;
- reuse existing components/patterns where appropriate;
- preserve routes, form semantics, validation, permissions, tenancy and business logic unless explicitly changed;
- treat generated frontend code as reference until reconciled with YMNAY source;
- keep translations and RTL behavior intact;
- avoid unrelated refactors;
- verify the affected behavior before handing to Salem.

If a Stitch design requires a feature YMNAY does not currently support, Omar must flag the engineering/product delta; do not silently fake the behavior in frontend-only code.

## Phase 10 — QA and design conformance

Salem verifies functional acceptance and agreed regression scope.

When requested, Nour may separately conduct design-conformance review and report differences such as:

- typography;
- spacing;
- component sizing;
- hierarchy;
- alignment;
- responsive layout;
- RTL mirroring/order;
- states;
- interaction affordances.

A design mismatch is not automatically a functional defect, and a functional PASS is not automatically proof of design conformance. Record both distinctly when both matter.

## Persistent artifact convention

For a tracked substantial task, prefer:

```text
.ai/work/<issue-number>-<slug>/
├── PRODUCT-BRIEF.md              # when applicable
├── UI-UX-SPEC.md                 # Nour's design specification
├── HANDOFF-TO-ENGINEER.md        # approved implementation handoff
├── STITCH-REFERENCE.md           # project/share/screen/version references + access notes
├── DESIGN.md                     # only when produced/approved for this scope
├── screenshots/
│   ├── current/
│   └── approved/
└── designs/
    ├── desktop/
    ├── mobile/
    └── states/
```

Create only artifacts that actually exist. Never create fake screenshot paths or pretend a Stitch link/version was captured.

## `STITCH-REFERENCE.md` contract

When Stitch materially drives the task, record:

```text
STITCH REFERENCE

Task / Issue:
Stitch Project:
Share Reference:
Access Verified In Current Session: YES / NO
Design System Source:
DESIGN.md Status: NONE / PROPOSED / OWNER-APPROVED
Approved Screen(s):
Approved Prototype:
Owner Approval Record:
Export / Capture Date:

Context Supplied To Stitch:
- ...

Preserved Functions:
- ...

Known Tool / Access Limitations:
- ...
```

Do not place authentication tokens or secrets in this file.

## Nour completion gate for Stitch work

A substantial Stitch design stage is complete only when all applicable items are true:

- current UI baseline was inspected or the access limitation is explicit;
- fixed requirements and preserved functions are documented;
- Stitch received task-relevant context rather than unrelated repository data;
- design rules are identified and `DESIGN.md` status is clear when applicable;
- actual visual output exists;
- desktop/mobile behavior is designed where relevant;
- RTL is explicitly reviewed where relevant;
- important states are covered;
- a prototype exists for multi-step flows when useful and Stitch supports it;
- design does not invent unresolved business rules;
- exact owner-approved artifacts are recorded;
- Omar receives enough information to implement without material UI guessing;
- MCP/direct integration status is stated as verified or unavailable rather than assumed.

## Fallback when Stitch is unavailable

Stitch availability must not block ordinary UI/UX work unnecessarily.

If Stitch is unavailable or inaccessible:

1. record the limitation;
2. inspect the current UI normally;
3. use screenshots, owner references, repository evidence, and another supported visual/mockup path;
4. produce the same `UI-UX-SPEC` and engineer-handoff quality;
5. do not fabricate Stitch project links, prototypes, exports, or MCP access.

## Security and privacy

Never put the following into Stitch context, GitHub artifacts, screenshots, or prompts unless they are sanitized and genuinely required:

- passwords;
- SSH private keys;
- API keys/tokens;
- production `.env` values;
- customer payment proofs;
- sensitive customer records;
- private production logs/data;
- unrelated tenant data.

Use representative/sanitized content for design whenever possible.

## Summary operating rule

For YMNAY, the intended relationship is:

```text
Stitch = design exploration + design system + visual screens + prototypes
Nour   = design judgment, task grounding, approval package and handoff
Codex  = engineering assistant available to Omar when configured
Omar   = application-code implementation owner
Salem  = independent QA/review
GitHub = technical/task source of truth
Owner  = design acceptance + Production release authority
```

Stitch improves the quality and speed of design. It never overrides YMNAY business rules, source code authority, agent role boundaries, owner approval, QA, or deployment controls.
