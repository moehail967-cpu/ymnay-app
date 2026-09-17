# YMNAY activation — `stitch::extract-static-html`

**Status:** ACTIVE for `@Nour`  
**Upstream repository:** `google-labs-code/stitch-skills`  
**Locked commit:** `0337446dadde6f8c94210444e2aa9d546126480f`  
**Upstream path:** `plugins/stitch-design/skills/extract-static-html/SKILL.md`  
**Upstream blob SHA:** `914d1bb2776ace06a982ea1710d3d0751c5cd77f`

## Activation

Use this skill to capture a self-contained HTML representation of a specific YMNAY UI state for Stitch/design work when the required runtime/tooling is available.

Before execution, fetch/read the exact upstream skill at the locked commit. The upstream skill contains explicit strategy-selection and user-confirmation checkpoints; preserve those checkpoints.

## YMNAY guardrails

- Nour may capture/read UI, but may not mutate Production data to reach a state.
- For authenticated Production pages, never embed real credentials, session secrets, tokens, or private customer data in captured HTML.
- Prefer isolated/local/test rendering where practical; Production browser access remains read-only.
- Generated static HTML is a design artifact and must not replace authoritative YMNAY source.
- If upstream helper scripts/runtime dependencies are not installed in the active environment, record the limitation and use the approved browser/screenshot fallback rather than claiming extraction succeeded.

Upstream URL: https://github.com/google-labs-code/stitch-skills/blob/0337446dadde6f8c94210444e2aa9d546126480f/plugins/stitch-design/skills/extract-static-html/SKILL.md
