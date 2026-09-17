# Agent registry

This is the canonical registry for named project agents. A named agent is active only when it appears here and has a current definition file in this directory.

## Invocation convention

When a repository-aware task explicitly addresses a registered handle (for example `@Adam`, `@Nour`, `@Omar`, `@Salem`, or `@Sara`) or clearly asks to work with that named agent, load that agent's definition and follow its role for the current task. The explicit current user instruction still has highest project-level authority.

Do not infer that an unregistered name is an agent. Do not copy project knowledge into agent definitions; agents read the shared knowledge base through `../AGENT-BOOTSTRAP.md`.

For `@Nour`, Google Stitch work has one additional mandatory role resource: when a task explicitly uses Stitch or materially depends on the Stitch-to-Code workflow, read [`../skills/google-stitch/REGISTRY.md`](../skills/google-stitch/REGISTRY.md) after Nour's definition and before invoking any Stitch skill. Only skills marked ACTIVE there are assigned to Nour. Each local skill wrapper pins the exact Google Labs upstream skill revision and YMNAY guardrails. A registered skill does not prove Stitch MCP/runtime access; verify task access separately.

| Handle | Name | Role | Definition | Status | Aliases |
|---|---|---|---|---|---|
| `@Adam` | Adam | Product & UX Engineer | [adam-product-ux.md](adam-product-ux.md) | ACTIVE | `Adam`, `Product/UX`, `Product Agent` |
| `@Nour` | Nour | UI/UX Designer | [nour-ui-ux.md](nour-ui-ux.md) | ACTIVE | `Nour`, `UI/UX`, `Designer`, `UI/UX Designer` |
| `@Omar` | Omar | Full-Stack Software Engineer | [omar-full-stack-engineer.md](omar-full-stack-engineer.md) | ACTIVE | `Omar`, `Software Engineer`, `Full-Stack`, `Engineer` |
| `@Salem` | Salem | QA & Review Engineer | [salem-qa-review.md](salem-qa-review.md) | ACTIVE | `Salem`, `QA`, `QA Agent`, `Review Engineer` |
| `@Sara` | Sara | AI Website Builder & Content Designer | [sara-website-builder.md](sara-website-builder.md) | ACTIVE | `Sara`, `Website Builder`, `Content Designer`, `Client Site Builder` |

## Development team workflow

When all software-development stages and a Production release are required:

```text
Owner → `@Adam` product brief/acceptance criteria → `@Nour` interface design → Owner design acceptance → `@Omar` application implementation → `@Salem` QA → Owner deployment approval → `@Omar` deployment → DONE
```

Roles may be skipped when the task does not require them. Direct owner invocation remains allowed.

Adam, Nour, and Salem may inspect task-relevant live files and errors over the existing general SSH connection under [read-only Production SSH policy](../deployment/READ-ONLY-PRODUCTION-SSH.md). Their deliverables are product, design, and QA artifacts respectively; Omar owns application code. Owner acceptance of Nour's specific design package is required before the Nour-to-Omar handoff when design is part of the task.

QA `PASS` does not itself authorize Production deployment. Production deployment and rollback remain owner-gated and follow `../deployment/README.md`.

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
