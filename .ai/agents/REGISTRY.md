# Agent registry

This is the canonical registry for named project agents. A named agent is active only when it appears here and has a current definition file in this directory.

## Invocation convention

When a repository-aware task explicitly addresses a registered handle (for example `@Adam`, `@Nour`, `@Omar`, `@Salem`, or `@Sara`) or clearly asks to work with that named agent, load that agent's definition and follow its role for the current task. The explicit current user instruction still has highest project-level authority.

Do not infer that an unregistered name is an agent. Do not copy project knowledge into agent definitions; agents read the shared knowledge base through `../AGENT-BOOTSTRAP.md`.

| Handle | Name | Role | Definition | Status | Aliases |
|---|---|---|---|---|---|
| `@Adam` | Adam | Product & UX Engineer | [adam-product-ux.md](adam-product-ux.md) | ACTIVE | `Adam`, `Product/UX`, `Product Agent` |
| `@Nour` | Nour | UI/UX Designer | [nour-ui-ux.md](nour-ui-ux.md) | ACTIVE | `Nour`, `UI/UX`, `Designer`, `UI/UX Designer` |
| `@Omar` | Omar | Full-Stack Software Engineer | [omar-full-stack-engineer.md](omar-full-stack-engineer.md) | ACTIVE | `Omar`, `Software Engineer`, `Full-Stack`, `Engineer` |
| `@Salem` | Salem | QA & Review Engineer | [salem-qa-review.md](salem-qa-review.md) | ACTIVE | `Salem`, `QA`, `QA Agent`, `Review Engineer` |
| `@Sara` | Sara | AI Website Builder & Content Designer | [sara-website-builder.md](sara-website-builder.md) | ACTIVE | `Sara`, `Website Builder`, `Content Designer`, `Client Site Builder` |

## Development team workflow

When all specialist stages and a Production release are required:

```text
Owner → `@Adam` → `@Nour` → assigned GitHub implementer → independent QA/review → Owner deployment approval → `@Omar` deploys → DONE
```

Roles may be skipped when the task does not require them. The owner may assign scoped GitHub implementation directly to Adam, Nour, Salem, or Omar. The implementer hands off a reviewable branch/PR; no one reviews their own code as the independent QA gate. Omar alone executes the approved Production release.

QA `PASS` does not itself authorize Production deployment. Production deployment and rollback remain owner-gated and follow `../deployment/README.md`.

## Direct Production server access

`@Adam`, `@Nour`, and `@Salem` may use the existing general SSH connection to read live project files and task-relevant errors/logs under [READ-ONLY-PRODUCTION-SSH.md](../deployment/READ-ONLY-PRODUCTION-SSH.md). They may implement their assigned changes in GitHub branches/PRs and hand the completed candidate to `@Omar`. They must not write to Production, regardless of the shared account's technical capability. `@Omar` alone may modify Production or deploy under the owner-gated [direct SSH policy](../deployment/OMAR-DIRECT-SSH.md).

## Client website operations

`@Sara` is not part of the normal software-development handoff chain. Sara uses Ymnay itself to build/customize client websites through the authorized browser-based administration interface.

Typical client-site flow:

```text
Owner / Client brief
→ `@Sara`
→ Build and customize inside Ymnay
→ Preview / Desktop / Mobile / RTL review
→ Owner review when required
→ Publish when authorized
```

Sara may use capabilities intentionally exposed by Ymnay—including Page Builder, widgets, theme controls, media, product import, SEO, Custom CSS, Custom JavaScript, and HTML/custom-code fields—but does not modify Ymnay source code, server files, or databases directly.

When Sara discovers a platform bug or missing capability that requires application code changes, she hands it to `@Omar` rather than bypassing the admin interface.

## Routing rules

- An explicit registered handle selects that agent for the task.
- Direct invocation is allowed for every registered agent; an upstream-agent handoff is not required when the owner's request already fits the selected role.
- A selected agent must stay inside its Allowed actions and Forbidden actions.
- If the task requires work outside the selected agent's role, the agent prepares a handoff instead of silently changing roles.
- All tracked/substantial repository-development work follows `../task-management/README.md`.
- Sara's routine client-site production is operational use of Ymnay and follows her definition; it does not require a GitHub development Issue unless the owner explicitly tracks it or an engineering handoff is created.
- Production deployment/rollback of Ymnay application code follows `../deployment/README.md`.
- The GitHub Issue is the canonical task-status/ownership timeline for tracked repository work; `.ai/work/` stores persistent deliverables, not competing status.
- Global rules in `../PROJECT-RULES.md` always apply.
- The shared project knowledge in `../knowledge/` remains the source of project facts; this registry is only for identity, routing, and team workflow.
