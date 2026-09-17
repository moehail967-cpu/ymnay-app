# Ymnay Production Deployment

This is the official deployment protocol for Ymnay.

## Core rule

Production deployment is **owner-gated**.

No agent may deploy merely because implementation or QA is complete.

Normal flow after requirements and any owner-accepted interface design:

```text
@Omar application implementation
→ @Salem QA
→ READY_FOR_DEPLOYMENT
→ Owner explicitly approves deployment
→ @Omar runs the approved GitHub Actions manual workflow or authorized direct SSH deployment
→ Production health check
→ DEPLOYED / DEPLOY_FAILED
```

`@Adam`, `@Nour`, and `@Salem` may inspect task-relevant live files and errors through the existing general SSH connection under [read-only Production SSH policy](READ-ONLY-PRODUCTION-SSH.md). They do not modify application code or Production. Omar owns implementation and live changes. Design acceptance by the owner, when applicable, precedes Omar's implementation; release authorization remains a separate later decision.

## Production target

- Web root: `/home/ymnay/htdocs/ymnay.com`
- Laravel root: `/home/ymnay/htdocs/ymnay.com/core`
- Public health check: `https://ymnay.com/`

The canonical deployment source is the current `main` branch.

Team instructions remain in GitHub. The deployment filter excludes `.ai/` and `AGENTS.md`; do not upload them through direct SSH either. A change limited to agent instructions is effective after the approved merge to `main` and does not require a Production deployment. Production remains the target for application changes and task-relevant read-only inspection under the role policies.

## Deployment workflow

GitHub Actions workflow:

`.github/workflows/deploy-production.yml`

It is manual-only (`workflow_dispatch`). It refuses to deploy unless:

- it is run from `main`;
- the operator enters `DEPLOY` as explicit confirmation;
- required SSH secrets exist;
- the production preflight passes.

The workflow:

1. checks out the selected `main` revision;
2. runs the frontend build (`npm ci`, `npm run build`);
3. prepares SSH access;
4. verifies the Production target, `.env`, PHP, Composer, and rsync;
5. creates a source backup under `/home/ymnay/deploy-backups/`;
6. places Laravel in maintenance mode;
7. rsyncs the repository/build output while preserving runtime/customer paths;
8. runs production Composer install;
9. clears Laravel caches;
10. restores the application online;
11. performs Laravel boot and public HTTP health checks;
12. comments the deployment result on the tracked GitHub Issue.

## Required GitHub repository secrets

Configure these under GitHub repository Actions secrets before the first deployment:

- `PRODUCTION_HOST` — VPS hostname/IP.
- `PRODUCTION_USER` — SSH user that owns/can deploy the application.
- `PRODUCTION_SSH_KEY` — private SSH deployment key.
- `PRODUCTION_PORT` — optional; defaults to `22` when empty.

Never store secret values in this repository, `.ai/`, Issues, or work packages.

Use a dedicated deploy SSH key with the minimum practical server permissions.

## Runtime data protection

Deployment uses `.deployment/rsync-filter.rules`.

The sync must preserve rather than replace/delete:

- production `.env` values;
- `core/storage/` runtime data;
- Laravel runtime cache paths;
- customer uploads;
- payment proofs/business uploads;
- installed `vendor/` and `node_modules/` during file sync;
- generated publication links such as storage/theme links.

Tracked/default UI assets under approved icon/default-upload directories may still be deployed.

The workflow uses `rsync --delete` **without** `--delete-excluded`, so excluded Production paths are protected from deletion.

## Database migrations

The initial deployment workflow deliberately does **not** run database migrations automatically.

Ymnay has a central database plus database-per-tenant tenancy. A schema change must identify its target before Production execution.

If a task contains migrations, Omar must record:

- migration files;
- central vs tenant target;
- required order;
- backward-compatibility/rollback considerations;
- whether all tenants require migration.

Salem must verify the migration plan in a safe environment where possible.

The owner must explicitly authorize the Production migration operation separately from ordinary code deployment until a dedicated migration deployment protocol is approved.

## Queue/workers/scheduler

The workflow does not assume the effective queue/worker/scheduler configuration because runtime worker configuration remains environment-specific.

If a change requires a queue worker restart, scheduler change, or service restart, Omar must state that in the engineering handoff and the owner must explicitly authorize the operational step.

## Backup and rollback

Before code sync, the workflow creates a filtered source backup in:

`/home/ymnay/deploy-backups/`

Runtime/customer paths are not duplicated into this source backup because they are preserved in place by deployment filtering.

A failed deployment must be recorded as `DEPLOY_FAILED`.

If restoration of the previous source is required, use `ROLLBACK_REQUIRED` and perform rollback as a separately authorized operation using the recorded backup/revision. Do not improvise database rollback.

## Task-management integration

Deployment-related statuses are defined in `.ai/task-management/README.md`.

After Salem returns `PASS` for a change that must go live:

- set `Status: READY_FOR_DEPLOYMENT`;
- set `Current Agent: Owner`;
- record the exact main commit intended for deployment;
- do not close the Issue yet.

When the owner approves deployment, the workflow may run.

Success:

- `Status: DEPLOYED`;
- record workflow run, deployed commit, timestamp, and health-check result;
- then the task may be set `DONE` and closed if no post-deploy verification remains.

Failure:

- `Status: DEPLOY_FAILED`;
- keep the Issue open;
- attach workflow evidence;
- route the next action to the responsible role/Owner.

If rollback is required:

- `Status: ROLLBACK_REQUIRED`;
- do not perform it silently.

## Agent responsibilities

### @Omar

Omar prepares deployable code and must report deployment impact. He does not deploy without explicit owner authorization.

### @Salem

Salem verifies the exact candidate revision and states whether it is QA-ready. A PASS does not itself authorize Production deployment.

### Owner

The owner is the release gate and authorizes Production deployment/rollback.

## First-deployment caution

The canonical GitHub history was reset from the clean Production source while the live server checkout was intentionally left unchanged. Deployment uses rsync rather than `git reset --hard` specifically to avoid deleting runtime/customer files that were historically tracked on the old server checkout.

Do not replace this with a blind Production `git reset --hard origin/main` unless the live checkout has first been explicitly migrated to the new clean repository model and runtime data safety has been proven.
