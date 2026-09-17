# Knowledge router

Baseline: `main` / `60ba2c68e09ed367026ac62c55a666a72fba900d`; discovery date: 2026-09-12. Paths are repository-relative; commands normally run in `core/`. Documentation is a map, not a runtime certification.

## Evidence convention

- **VERIFIED:** observed in the cited current source/lock/test. Statements describe what the source declares; they do not certify live configuration or deployed DB parity.
- **INFERRED:** engineering interpretation, purpose grouping or risk assessment supported by references but not proven end-to-end.
- **UNKNOWN:** unresolved; see numbered questions in `UNKNOWNS.md`.
- **OWNER-CONFIRMED:** infrastructure/task input supplied on 2026-09-12; explicitly distinguish this provenance from code verification.
- Table rows inherit their section's label unless marked otherwise. Manifest `evidence`/`sources` serve the same purpose. Empty arrays mean none found in that declared static inventory, not a guarantee of no dynamic behavior; `null` means unknown/not established.

External product/tool capability documents such as `STITCH-CODEX-DESIGN-WORKFLOW.md` may cite current vendor documentation. A verified vendor capability does not prove the capability is configured, connected, authorized, or working in the active YMNAY task environment; task access must be verified separately.

## Read only what the task needs

| Task | Start here | Then inspect |
|---|---|---|
| First orientation | [SYSTEM](SYSTEM.md), [ARCHITECTURE](ARCHITECTURE.md) | Entry points named there |
| Feature/bug | [CODE-MAP](CODE-MAP.md), [MODULES](MODULES.md), [CHANGE-IMPACT](CHANGE-IMPACT.md) | Smallest route → implementation path |
| Product/inventory | [CODE-MAP](CODE-MAP.md), [DATA-MODEL](DATA-MODEL.md) | Product/Inventory module and ProductGlobalTrait |
| Tenant/domain/provisioning | [TENANCY](TENANCY.md), [WORKFLOWS](WORKFLOWS.md) | Tenancy provider, jobs and boundary models |
| Database/migration | [DATA-MODEL](DATA-MODEL.md), [CONSTRAINTS](CONSTRAINTS.md) | Owning migration plus later alterations |
| Login/permissions | [AUTHORIZATION](AUTHORIZATION.md), [SECURITY](SECURITY.md) | Exact actor, guard, route middleware |
| Subscription/payment/checkout | [WORKFLOWS](WORKFLOWS.md), [INTEGRATIONS](INTEGRATIONS.md) | Central versus tenant payment chain |
| Theme/UI/Page Builder | [ARCHITECTURE](ARCHITECTURE.md), [CODE-MAP](CODE-MAP.md), [CONVENTIONS](CONVENTIONS.md) | Active theme or builder format |
| Stitch / AI-assisted UI redesign / design-to-code handoff | [STITCH-CODEX-DESIGN-WORKFLOW](STITCH-CODEX-DESIGN-WORKFLOW.md) | `../skills/google-stitch/REGISTRY.md`, current UI in browser, relevant frontend source, approved Product Brief/design artifacts |
| Plugin | [MODULES](MODULES.md), [ARCHITECTURE](ARCHITECTURE.md) | Manifest, main class, PluginManager |
| Queue/cron/event | [BACKGROUND-PROCESSING](BACKGROUND-PROCESSING.md) | Job plus dispatch site and driver |
| Infrastructure/performance | [INFRASTRUCTURE](INFRASTRUCTURE.md), [CHANGE-IMPACT](CHANGE-IMPACT.md) | Config versus actual runtime evidence |
| Security | [SECURITY](SECURITY.md), [CONSTRAINTS](CONSTRAINTS.md) | Relevant trust boundary only |
| Verification | [TESTING](TESTING.md) | Smallest applicable check |
| Unclear/conflicting fact | [UNKNOWNS](UNKNOWNS.md), [DECISIONS](DECISIONS.md) | Evidence cited by that question |

## Machine-readable views

`manifests/system.yaml`, `modules.yaml`, `entities.yaml`, `workflows.yaml`, `integrations.yaml` summarize this same base. They are not separate authorities. Update the affected narrative and manifest together when their shared structural facts change.

## Foundation audit — VERIFIED inventory, scope = tracked repository

| Previous source | Disposition |
|---|---|
| `AGENTS.md` | Replaced with short central-bootstrap pointer; historical knowledge not copied as authority |
| `core/.claude/settings.json` | Removed: enabled an old engineering assistant plugin |
| `core/.claude/skills/plugin-dev.md` | Removed: old triggered development skill and competing rules |
| `core/.claude/projects/-Users-m1-Desktop-localhost-nazmart/memory/feedback_sprint_tracking.md` | Removed: old autonomous sprint/ticket instructions |

Filename inventory and content searches also examined README/technical notes, prompt-bearing source, hidden configuration and rule-like filenames. No other tracked tool entry/rule source was established. `core/docs`, theme developer notes and PHP validator Rules are technical artifacts, not alternative AI authority; retain them, and verify their claims before use. For example, `core/docs/ymnay-core-patches.md` mentions a test absent from this baseline.

Application prompts in `core/Modules/AiIntegration/Services/AiService.php` and `core/Modules/MultiLingual/Services/TranslateAiService.php` remain intact: they generate customer content, not engineering instructions. Ignored installed dependencies and personal/global tool settings are outside this repository reset.