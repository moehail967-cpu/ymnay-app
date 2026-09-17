# Google Labs Stitch skills for YMNAY

This directory registers the Google Labs Stitch skills that `@Nour` is allowed to use for YMNAY UI/UX work.

## Upstream authority

- Repository: `google-labs-code/stitch-skills`
- Locked upstream commit: `0337446dadde6f8c94210444e2aa9d546126480f`
- License: Apache-2.0
- Agent Skills standard: https://agentskills.io
- Stitch: https://stitch.withgoogle.com

The upstream repository is published under the Google Labs GitHub organization and documents compatibility with Codex, Antigravity, Gemini CLI, Claude Code, Cursor, and OpenCode. Its README also states that the repository is **not an officially supported Google product**. YMNAY therefore describes these as **Google Labs-published Stitch skills**, not as a Google support/SLA guarantee.

## YMNAY installation model

YMNAY uses **pinned activation wrappers** rather than copying mutable upstream skill bodies into project instructions. Each local `SKILL.md` records:

- the exact upstream skill name;
- the exact upstream path;
- the locked upstream commit and blob SHA;
- Nour's role-specific activation rules and boundaries.

Before executing one of these skills, `@Nour` must read the local wrapper and then load the exact upstream `SKILL.md` at the locked commit. The upstream skill body remains authoritative for the skill procedure, while YMNAY project rules, security rules, owner gates, and agent role boundaries always take precedence.

This avoids silently drifting from Google Labs' actual skill instructions while keeping YMNAY's allowed skill set explicit and reviewable.

## Runtime prerequisite: Stitch MCP

The upstream repository states that the Stitch skills require the **Stitch MCP server** to be configured and running in the agent environment for full Stitch actions.

Skill registration in GitHub does **not** prove that MCP is connected. `@Nour` must verify current task access before using MCP-dependent actions. Never commit Stitch API keys, OAuth tokens, MCP credentials, or environment secrets.

Official Codex marketplace setup documented upstream:

```bash
codex plugin marketplace add google-labs-code/stitch-skills --ref main \
  --sparse .agents/plugins \
  --sparse plugins/stitch-design \
  --sparse plugins/stitch-utilities
```

After marketplace registration, the relevant plugins are:

- `stitch-design`
- `stitch-utilities`

`stitch-build` is intentionally **not assigned to Nour** because application/component implementation belongs to `@Omar` in YMNAY.

## Active Nour skill set

See [`REGISTRY.md`](REGISTRY.md).

## Update policy

Do not silently follow upstream `main`. To update the lock:

1. inspect the new upstream commit and skill diffs;
2. verify no new instruction conflicts with YMNAY role/security/deployment rules;
3. update `REGISTRY.md` and affected wrappers with the new commit/blob SHAs;
4. record the change in a GitHub Issue.
