# Ymnay Team Task Management

This is the authoritative task-management protocol for the Ymnay AI team.

## System of record

- **GitHub Issue** = canonical task record, current owner, status, review routing, and timeline.
- **`.ai/work/<issue-number>-<slug>/`** = persistent work package and task deliverables when files/artifacts are needed.
- **Git branch / PR / commit** = implementation evidence for engineering work.
- **GitHub Projects** = optional visual board/view over Issues; it must not become a second source of truth.
- **ChatGPT** = conversational/reporting interface; reports should be generated from current GitHub Issues and repository artifacts, not stale chat memory.

Do not maintain a parallel Google Sheet as an authoritative task tracker.

## Which work must be tracked

Create or use a GitHub Issue for any substantial task that produces one or more of:

- product requirements or acceptance criteria;
- UI/UX design or review artifacts;
- application code, migration, test, branch, PR, or commit;
- QA/review verdict;
- cross-agent handoff;
- owner decision that blocks or changes implementation.

A simple question, explanation, or read-only discussion does not require an Issue unless the owner asks to track it.

If a task already has an Issue, never create a duplicate. Continue on the existing Issue.

## Canonical Task Control block

Every tracked Issue should keep this block near the top of the Issue body:

```text
TASK CONTROL

Status: BACKLOG
Current Agent: Owner
Next Agent: —
Review Required: NO
Reviewer: —
Work Package: —
Branch / PR: —
Last Updated By: Owner
```

Project-agent handles such as `@Adam` are written in backticks inside GitHub task records (for example `` `@Adam` ``) to avoid pretending they are GitHub user accounts or causing accidental user mentions.

### Field meanings

- **Status** — canonical lifecycle state below.
- **Current Agent** — role/person currently responsible for the next action.
- **Next Agent** — intended next owner after the current step, when known.
- **Review Required** — `YES` or `NO`.
- **Reviewer** — required reviewer when `Review Required: YES`.
- **Work Package** — path under `.ai/work/` when persistent artifacts exist.
- **Branch / PR** — engineering branch/PR when relevant.
- **Last Updated By** — owner or registered project-agent handle that last changed the task state.

## Canonical statuses

Use only these lifecycle states unless the owner explicitly adds another:

| Status | Meaning |
|---|---|
| `BACKLOG` | Task recorded but not started. |
| `IN_PROGRESS` | Current agent is actively working. |
| `NEEDS_REVIEW` | Current output needs review/decision before progressing. |
| `BLOCKED` | Work cannot proceed; blocker and required resolver must be documented. |
| `PRODUCT_READY` | Product/UX requirements are complete and ready for next role. |
| `DESIGN_READY` | UI/UX design package is complete. |
| `READY_FOR_DEVELOPMENT` | Requirements/design are settled enough for engineering. |
| `READY_FOR_QA` | Engineering implementation and its own verification are complete. |
| `QA_FAILED` | QA found at least one blocker; normally routes back to engineering/design/product owner of the defect. |
| `DONE` | Required review is complete and task is accepted for the agreed scope. |
| `CANCELLED` | Owner cancelled the task or it is intentionally not planned. |

`DONE` is normally set after `@Salem` returns `PASS`, or after the owner explicitly accepts a `PASS WITH ISSUES` outcome.

## Starting work

When a registered agent starts a tracked task:

1. read the Issue and existing comments/artifacts;
2. verify the task fits the selected role;
3. update `Status` to `IN_PROGRESS`;
4. set `Current Agent` to that agent;
5. set `Last Updated By` to that agent;
6. add a short `STARTED` Issue comment with what will be produced;
7. create/read the work package only if persistent artifacts are needed.

Do not silently take ownership of a task assigned to another agent unless the owner redirects it or the prior handoff clearly routes it to you.

## Completion and handoff

Completing a role step always requires **both**:

1. update the Issue `TASK CONTROL` block; and
2. add a `HANDOFF` comment using the shared template.

A role is not considered complete merely because an artifact or commit exists.

### Normal forward handoff

Example after Adam finishes product analysis:

```text
Status: PRODUCT_READY
Current Agent: `@Nour`
Next Agent: `@Omar`
Review Required: NO
Reviewer: —
Last Updated By: `@Adam`
```

Adam then posts a handoff comment linking the Product Brief and telling Nour exactly what to produce.

### Review handoff

If Nour needs Adam to review a business-rule conflict:

```text
Status: NEEDS_REVIEW
Current Agent: `@Adam`
Next Agent: `@Nour`
Review Required: YES
Reviewer: `@Adam`
Last Updated By: `@Nour`
```

The handoff comment must state the decision needed. After review, ownership returns to the appropriate next agent.

### Blocked task

Use `BLOCKED` only when work truly cannot continue. The comment must include:

- blocker;
- evidence/context;
- who can unblock it;
- exact decision/access/input required.

## HANDOFF comment contract

Use this structure, omitting irrelevant lines:

```text
HANDOFF

From: `@Agent`
To: `@NextAgent` / Owner
Completed Step:
New Status:

Summary:
- ...

Deliverables / Evidence:
- ...

Decisions Made:
- ...

Open Questions / Risks:
- ...

Review Required: YES / NO
Reviewer: ...

Next Action:
- ...
```

Never claim an attachment, screenshot, test, branch, or artifact exists unless it actually exists and is linked or named precisely.

## Work-package convention

For substantial work that needs persistent artifacts, use:

```text
.ai/work/<issue-number>-<short-slug>/
```

Typical contents:

```text
TASK.md
PRODUCT-BRIEF.md
UI-UX-SPEC.md
HANDOFF-TO-ENGINEER.md
ENGINEERING-REPORT.md
QA-REPORT.md
screenshots/
designs/
```

Create only files that are actually needed. Do not create empty folders or fake artifacts.

`TASK.md` is a local summary/reference; the GitHub Issue remains the canonical task state.

## Role routing

Default team flow when all stages are needed:

```text
Owner
  → `@Adam`  Product / UX
  → `@Nour`  UI / UX design
  → `@Omar`  implementation
  → `@Salem` QA / review
  → Owner / DONE
```

Direct invocation is still allowed. A task can skip roles that are unnecessary for its scope.

Routing rules:

- product/business ambiguity → `@Adam` or Owner;
- interface/design work → `@Nour`;
- implementation/fix → `@Omar`;
- independent verification/retest → `@Salem`;
- strategic/business acceptance or production authorization → Owner.

## QA loop

If Salem returns `FAIL`:

1. set `Status: QA_FAILED`;
2. route `Current Agent` to the role that owns the blocker, usually `@Omar`;
3. Salem posts reproducible blocker evidence;
4. responsible agent fixes/clarifies and sets `READY_FOR_QA` again;
5. Salem retests before changing the verdict.

A code change alone does not close a QA finding.

## Issue closure

Close the GitHub Issue only when:

- task is `DONE` or `CANCELLED`;
- required handoffs/reviews are recorded;
- material deliverables are linked;
- no known blocking issue remains for the agreed scope.

Do not close an Issue simply because one agent finished their stage.

## Reporting

The owner should be able to ask questions such as:

- "ما المهام الموجودة عند عمر؟"
- "ايش المهام المتوقفة؟"
- "ايش ينتظر مراجعتي؟"
- "أعطني تقرير الفريق اليوم."

A repository-aware assistant should answer from current GitHub Issues plus linked work artifacts.

Useful report groupings:

- by `Current Agent`;
- by `Status`;
- `NEEDS_REVIEW` / `BLOCKED` tasks;
- `READY_FOR_QA` and `QA_FAILED` tasks;
- recently completed `DONE` tasks.

Do not infer task state from chat history when the GitHub Issue says otherwise.

## GitHub Projects board (optional view)

If a GitHub Project board is enabled, recommended columns/views are:

```text
Backlog
Product
Design
Development
QA
Needs Review / Blocked
Done
```

The board mirrors Issues. Updating the board must never replace updating the canonical Issue status.

## Data and security

Task records and work packages must not contain secrets, credentials, private keys, production `.env` values, payment credentials, or unnecessary customer data.

Screenshots must avoid or redact sensitive customer/business data where practical.

## Maintenance

This protocol applies to all registered agents. Agent-specific files define how each role uses it. If this protocol changes, update agent guidance only when their role-specific behavior changes; do not duplicate this entire document into every agent file.
