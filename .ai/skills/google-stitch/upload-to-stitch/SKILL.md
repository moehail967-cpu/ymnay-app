# YMNAY activation — `stitch::upload-to-stitch`

**Status:** ACTIVE for `@Nour`  
**Upstream repository:** `google-labs-code/stitch-skills`  
**Locked commit:** `0337446dadde6f8c94210444e2aa9d546126480f`  
**Upstream path:** `plugins/stitch-design/skills/upload-to-stitch/SKILL.md`  
**Upstream blob SHA:** `a2a9625331dbf90444dabca2341b348b60f15890`

## Activation

Use this skill when approved local design inputs such as screenshots, mockups, static HTML, or related assets need to be uploaded to a verified Stitch project.

Before execution, fetch/read the exact upstream skill at the locked commit and verify the required Stitch access/credentials in the active environment.

## YMNAY guardrails

- Never upload secrets, environment files, private keys, API tokens, credentials, payment proofs, or sensitive customer/tenant data.
- Use only task-relevant design artifacts.
- Record the exact target project/screen and what was uploaded.
- Do not claim success unless the upload result is actually verified.
- Uploading an artifact to Stitch does not approve the design or authorize implementation.

Upstream URL: https://github.com/google-labs-code/stitch-skills/blob/0337446dadde6f8c94210444e2aa9d546126480f/plugins/stitch-design/skills/upload-to-stitch/SKILL.md
