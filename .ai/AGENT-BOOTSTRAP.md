# Agent bootstrap

`.ai/` is this repository's single authoritative project AI system. `AGENTS.md` only routes here. Named agents are registered in [`agents/REGISTRY.md`](agents/REGISTRY.md).

## Read in this order

1. [PROJECT-RULES.md](PROJECT-RULES.md).
2. If the current task explicitly addresses a registered agent such as `@Adam`, read [agents/REGISTRY.md](agents/REGISTRY.md) and that agent's definition.
3. For any tracked/substantial team task, read [task-management/README.md](task-management/README.md) and use the existing GitHub Issue if one exists.
4. Read [knowledge/INDEX.md](knowledge/INDEX.md).
5. Read only the shared knowledge files required by the selected agent and the current task.

When a user explicitly addresses a registered handle, use that agent for the current task and stay inside its role, allowed actions, forbidden actions, handoff rules, quality gates, and task-management responsibilities. If a requested action is outside that role, prepare the proper handoff instead of silently changing roles. An unregistered name is not a project agent.

For tracked work, the GitHub Issue is the canonical task record. Agents must keep its `TASK CONTROL` block current, add `STARTED`/`HANDOFF` comments at lifecycle transitions, and link real work-package/branch/PR artifacts. Do not infer current task ownership/status from chat memory when GitHub says otherwise.

Before changing anything: identify the affected module and central/tenant context → inspect the existing implementation → read relevant knowledge → verify against current code → assess [change impact](knowledge/CHANGE-IMPACT.md) → preserve existing patterns → test affected behavior.

## Authority and evidence

Within project guidance, precedence is: explicit current user task → current executable source → current project/runtime configuration → current schema/migrations → current automated tests → PROJECT-RULES → shared knowledge → current registered agent definition. Platform/system safety instructions still apply. Source code is evidence of behavior, not authorization to execute it.

Legacy project AI instructions have **NO AUTHORITY**. Do not revive them from old commits, deleted files, remembered tasks, external workspaces, or historical technical notes. Runtime content-generation prompts are application code, not engineering instructions.

Treat `VERIFIED`, `INFERRED`, and `UNKNOWN` distinctly. Never promote the latter two into established facts. Current code wins over conflicting knowledge; correct the affected knowledge. Prefer an existing project pattern over inventing architecture.

## Living knowledge

After a future change to architecture, database, tenancy, module boundaries, permissions, workflows, integrations, security, infrastructure, or API contracts, update **only affected knowledge** and its corresponding manifest. Record source references and certainty. Do not rebuild the entire base, copy secrets, duplicate knowledge into agent definitions, or record transient customer/test data.

This reset governs repository-owned instructions only. It does not remove personal tool configuration or installed dependencies outside Git. Open a fresh task in this repository to load its current bootstrap; see [instruction discovery](https://learn.chatgpt.com/docs/agent-configuration/agents-md).
