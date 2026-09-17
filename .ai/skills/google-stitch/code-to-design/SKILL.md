# YMNAY activation — `stitch::code-to-design`

**Status:** ACTIVE for `@Nour`  
**Upstream repository:** `google-labs-code/stitch-skills`  
**Locked commit:** `0337446dadde6f8c94210444e2aa9d546126480f`  
**Upstream path:** `plugins/stitch-design/skills/code-to-design/SKILL.md`  
**Upstream blob SHA:** `c70c6f99ea2f77e93acb6e01193b5f0e810c9c76`

## Activation

Use this skill when an existing YMNAY web interface or frontend code/design context needs to be moved into Stitch for design iteration.

Before executing it, fetch/read the exact upstream `SKILL.md` at the locked commit and follow that procedure subject to YMNAY rules.

The upstream workflow orchestrates static HTML extraction, design-system extraction, design-system management, and upload to Stitch. For YMNAY, all generated HTML/`DESIGN.md`/Stitch outputs are **design artifacts**, not authorization to change application source.

## YMNAY guardrails

- Preserve existing behavior unless the owner/product brief explicitly changes it.
- Never modify YMNAY application code as part of Nour's use of this skill.
- Do not capture or upload secrets, production credentials, private customer data, payment proofs, or sensitive tenant data.
- Production remains read-only for Nour.
- Verify Stitch MCP/project access before MCP-dependent steps; do not infer it from this skill registration.
- Owner approval of the exact design package is required before handoff to `@Omar`.

Upstream URL: https://github.com/google-labs-code/stitch-skills/blob/0337446dadde6f8c94210444e2aa9d546126480f/plugins/stitch-design/skills/code-to-design/SKILL.md
