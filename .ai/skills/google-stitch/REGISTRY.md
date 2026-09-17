# Google Stitch skill registry for `@Nour`

Upstream: `google-labs-code/stitch-skills`  
Locked commit: `0337446dadde6f8c94210444e2aa9d546126480f`

Only the skills below are active for Nour. The local wrapper must be read first; then the exact upstream skill at the locked commit must be loaded before execution.

| Local skill | Upstream name | Purpose in Nour's role | Upstream blob SHA | Status |
|---|---|---|---|---|
| `code-to-design` | `stitch::code-to-design` | Move an existing frontend/UI into Stitch through static HTML + design-system extraction + upload | `c70c6f99ea2f77e93acb6e01193b5f0e810c9c76` | ACTIVE |
| `extract-design-md` | `stitch::extract-design-md` | Reverse-engineer a comprehensive `DESIGN.md` from frontend source | `94228a67b62d35afe52b157a6deccfb8d4d9d610` | ACTIVE |
| `extract-static-html` | `stitch::extract-static-html` | Capture self-contained rendered UI state for Stitch/design handoff | `914d1bb2776ace06a982ea1710d3d0751c5cd77f` | ACTIVE |
| `generate-design` | `stitch::generate-design` | Generate/edit Stitch screens and variants from prompts/images | `f434ad1bf052508d9668508e12250377166fe7d0` | ACTIVE |
| `manage-design-system` | `stitch::manage-design-system` | Create/update/apply Stitch design systems from `DESIGN.md` | `d45bef7b8c8bd0e1120244c39c0189db787c23b2` | ACTIVE |
| `upload-to-stitch` | `stitch::upload-to-stitch` | Upload approved local design inputs/assets/HTML to a Stitch project | `a2a9625331dbf90444dabca2341b348b60f15890` | ACTIVE |
| `design-md` | `design-md` | Analyze Stitch projects and produce semantic `DESIGN.md` documentation | `c29a0feb9c06c2fb48dc9114988382e06b1f9d9b` | ACTIVE |
| `enhance-prompt` | `enhance-prompt` | Turn rough UI requests into structured Stitch-oriented prompts | `04aec68becde63e5c1ea14a36c2a9ed51cedc056` | ACTIVE |
| `taste-design` | `taste-design` | Apply premium/anti-generic visual-design guidance and DESIGN.md discipline | `769b545ef84fed01577aa7511a707b7af1315a70` | ACTIVE |

## Explicitly not assigned to Nour

The following upstream categories/skills are not part of Nour's active skill set:

- all `stitch-build` skills (`react-components`, `react-native`, `react-vite-dashboard`, `remotion`, `shadcn-ui`);
- `stitch-loop` autonomous multi-page build workflow;
- implementation-oriented code generation or source edits.

Those actions cross YMNAY's role boundary into `@Omar` or a separate owner-authorized workflow.

## Conflict rule

If an upstream skill says to perform an action that conflicts with YMNAY project rules, Nour's role boundary, production read-only rules, task-management protocol, security restrictions, or an explicit owner instruction, **YMNAY rules win**. Use the skill's design method without taking the conflicting action.

## Access rule

A skill being ACTIVE here means Nour is trained/routed to use it. It does not mean Stitch MCP, an API key, a target Stitch project, or browser/terminal access is currently available. Verify access per task and record limitations honestly.
