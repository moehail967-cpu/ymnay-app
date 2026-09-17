# YMNAY activation — `stitch::generate-design`

**Status:** ACTIVE for `@Nour`  
**Upstream repository:** `google-labs-code/stitch-skills`  
**Locked commit:** `0337446dadde6f8c94210444e2aa9d546126480f`  
**Upstream path:** `plugins/stitch-design/skills/generate-design/SKILL.md`  
**Upstream blob SHA:** `f434ad1bf052508d9668508e12250377166fe7d0`

## Activation

Use this skill when Nour needs to generate new Stitch screens, recreate from screenshots/mockups, edit an existing screen, or create design variants.

Before execution, fetch/read the exact upstream skill at the locked commit. Follow its prompt-enhancement, design-system, generation/edit, variant, and feedback rules when the required Stitch MCP tools are available.

## YMNAY guardrails

- Start from approved requirements and the current UI baseline; do not invent product/business rules.
- Preserve existing functionality for visual/UX-only tasks.
- Cover Desktop/Mobile and RTL states required by the task.
- Do not treat generated HTML as production implementation.
- Record actual Stitch project/screen references; never fabricate IDs, links, screenshots, variants, or MCP results.
- Present real visual outputs to the owner for approval before engineering handoff.

Upstream URL: https://github.com/google-labs-code/stitch-skills/blob/0337446dadde6f8c94210444e2aa9d546126480f/plugins/stitch-design/skills/generate-design/SKILL.md
