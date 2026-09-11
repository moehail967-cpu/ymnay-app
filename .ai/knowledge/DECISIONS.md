# Architecture decision log

Only decisions explicitly established by the current task are recorded. Historical implementation choices are observations, not invented ADRs.

| Date | Decision | Reason | Alternatives | Affected components | Status |
|---|---|---|---|---|---|
| 2026-09-12 | One project AI bootstrap/rules/knowledge system under `.ai/`; root AGENTS is a pointer | Owner requested central, shared, source-backed knowledge without conflicting legacy instructions | Retain multiple legacy rule sources (rejected by owner) | AGENTS.md, .ai, removed tracked .claude instructions | Implemented on foundation branch; not merged |
| 2026-09-12 | Specialized agent definitions deferred | Owner will commission agents later; avoid embedding duplicate knowledge | Create agents now (out of scope) | .ai/agents/README.md | Accepted owner constraint |

Future entries: Date · Decision · Reason · Alternatives · Affected components · Status. Record stable architectural decisions, not temporary failures, customer data or task diaries. Branch merge status is not a source-code behavior claim.
