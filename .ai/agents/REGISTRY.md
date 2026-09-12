# Agent registry

This is the canonical registry for named project agents. A named agent is active only when it appears here and has a current definition file in this directory.

## Invocation convention

When a repository-aware task explicitly addresses a registered handle (for example `@Adam`, `@Nour`, or `@Omar`) or clearly asks to work with that named agent, load that agent's definition and follow its role for the current task. The explicit current user instruction still has highest project-level authority.

Do not infer that an unregistered name is an agent. Do not copy project knowledge into agent definitions; agents read the shared knowledge base through `../AGENT-BOOTSTRAP.md`.

| Handle | Name | Role | Definition | Status | Aliases |
|---|---|---|---|---|---|
| `@Adam` | Adam | Product & UX Engineer | [adam-product-ux.md](adam-product-ux.md) | ACTIVE | `Adam`, `Product/UX`, `Product Agent` |
| `@Nour` | Nour | UI/UX Designer | [nour-ui-ux.md](nour-ui-ux.md) | ACTIVE | `Nour`, `UI/UX`, `Designer`, `UI/UX Designer` |
| `@Omar` | Omar | Full-Stack Software Engineer | [omar-full-stack-engineer.md](omar-full-stack-engineer.md) | ACTIVE | `Omar`, `Software Engineer`, `Full-Stack`, `Engineer` |

## Routing rules

- An explicit registered handle selects that agent for the task.
- Direct invocation is allowed for every registered agent; an upstream-agent handoff is not required when the owner's request already fits the selected role.
- A selected agent must stay inside its Allowed actions and Forbidden actions.
- If the task requires work outside the selected agent's role, the agent prepares a handoff instead of silently changing roles.
- Global rules in `../PROJECT-RULES.md` always apply.
- The shared project knowledge in `../knowledge/` remains the source of project facts; this registry is only for identity and routing.
