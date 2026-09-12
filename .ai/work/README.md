# Team Work Packages

Persistent deliverables for tracked team tasks live here when needed.

Canonical task state remains in the GitHub Issue. This directory stores only real task artifacts that help the next role work without guessing.

## Naming

Use:

```text
.ai/work/<issue-number>-<short-slug>/
```

Example:

```text
.ai/work/42-manual-payment/
```

## Typical files

Create only what the task needs:

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

Do not create empty folders, fake screenshot paths, or placeholder artifacts that were never produced.

## Rules

- GitHub Issue = canonical status, owner, review routing, and timeline.
- Work package = persistent deliverables/evidence.
- Branch/PR/commit = implementation evidence.
- Link artifacts back from the Issue handoff comment.
- Never store secrets, credentials, production `.env` values, private keys, payment credentials, or unnecessary customer data here.
- Prefer redacted screenshots when visible data is sensitive.

Full protocol: `../task-management/README.md`.
