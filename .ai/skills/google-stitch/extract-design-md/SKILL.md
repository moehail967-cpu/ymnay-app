# YMNAY activation — `stitch::extract-design-md`

**Status:** ACTIVE for `@Nour`  
**Upstream repository:** `google-labs-code/stitch-skills`  
**Locked commit:** `0337446dadde6f8c94210444e2aa9d546126480f`  
**Upstream path:** `plugins/stitch-design/skills/extract-design-md/SKILL.md`  
**Upstream blob SHA:** `94228a67b62d35afe52b157a6deccfb8d4d9d610`

## Activation

Use this skill when Nour needs to reverse-engineer YMNAY's current visual language from frontend source into a Stitch-compatible `DESIGN.md`, audit design tokens/patterns, or document the styling system before redesign.

Before execution, fetch/read the exact upstream skill at the locked commit.

## YMNAY guardrails

- Source inspection is read-only for Nour.
- Prefer current theme/config/tokens and actually shipped component patterns over invented design values.
- Distinguish intended tokens from incidental one-off styles.
- A generated `.stitch/DESIGN.md` is a task artifact until reviewed; it does not automatically become YMNAY's canonical design system.
- Do not include secrets, runtime configuration, customer data, or credentials.
- Preserve RTL implications and Arabic typography/layout requirements when applicable.

Upstream URL: https://github.com/google-labs-code/stitch-skills/blob/0337446dadde6f8c94210444e2aa9d546126480f/plugins/stitch-design/skills/extract-design-md/SKILL.md
