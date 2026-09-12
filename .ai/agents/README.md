# Agent definitions

Named project agents are registered in [REGISTRY.md](REGISTRY.md). A definition is active only when it is listed there.

Current agents:

- `@Adam` — Product & UX Engineer → [adam-product-ux.md](adam-product-ux.md)
- `@Nour` — UI/UX Designer → [nour-ui-ux.md](nour-ui-ux.md)
- `@Omar` — Full-Stack Software Engineer → [omar-full-stack-engineer.md](omar-full-stack-engineer.md)
- `@Salem` — QA & Review Engineer → [salem-qa-review.md](salem-qa-review.md)
- `@Sara` — AI Website Builder & Content Designer → [sara-website-builder.md](sara-website-builder.md)

All agents must enter through `../AGENT-BOOTSTRAP.md` and obey `../PROJECT-RULES.md`.

For tracked/substantial repository-development work, agents use the shared team task-management protocol in `../task-management/README.md`.

Agents may be invoked directly by the owner when the task fits their role; an upstream-agent handoff is helpful but not mandatory.

For tracked repository work, the GitHub Issue is the canonical task record. Agents update its status/ownership, post lifecycle handoffs, and link any `.ai/work/<issue-number>-<slug>/` artifacts rather than keeping private parallel task state.

`@Sara` is a client-site production agent rather than a Ymnay source-code development agent. Routine client-site setup/customization is performed through Ymnay's browser administration interface and does not require a GitHub development Issue unless the owner explicitly tracks it or an engineering handoff becomes necessary.

Agent definitions specify only: identity/handle, Role, Responsibilities, Allowed actions, Forbidden actions, Required knowledge, Inputs, Outputs, Handoff rules, Quality gates, and role-specific task-management behavior.

Reference the shared knowledge base; do not copy project facts into agent definitions or introduce competing global rules.
