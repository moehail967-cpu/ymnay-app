# Agent definitions

Named project agents are registered in [REGISTRY.md](REGISTRY.md). A definition is active only when it is listed there.

Current agents:

- `@Adam` — Product & UX Engineer → [adam-product-ux.md](adam-product-ux.md)
- `@Nour` — UI/UX Designer → [nour-ui-ux.md](nour-ui-ux.md)
- `@Omar` — Full-Stack Software Engineer → [omar-full-stack-engineer.md](omar-full-stack-engineer.md)
- `@Salem` — QA & Review Engineer → [salem-qa-review.md](salem-qa-review.md)

All agents must enter through `../AGENT-BOOTSTRAP.md`, obey `../PROJECT-RULES.md`, and use the shared team task-management protocol in `../task-management/README.md` for tracked/substantial work.

Agents may be invoked directly by the owner when the task fits their role; an upstream-agent handoff is helpful but not mandatory.

For tracked work, the GitHub Issue is the canonical task record. Agents update its status/ownership, post lifecycle handoffs, and link any `.ai/work/<issue-number>-<slug>/` artifacts rather than keeping private parallel task state.

Agent definitions specify only: identity/handle, Role, Responsibilities, Allowed actions, Forbidden actions, Required knowledge, Inputs, Outputs, Handoff rules, Quality gates, and role-specific task-management behavior.

Reference the shared knowledge base; do not copy project facts into agent definitions or introduce competing global rules.
