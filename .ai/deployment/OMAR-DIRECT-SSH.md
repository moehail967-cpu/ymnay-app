# Omar Direct Production SSH

This policy authorizes `@Omar` to use an owner-authorized direct SSH/terminal connection for Production deployments.

`@Adam`, `@Nour`, and `@Salem` may use the existing general connection only for task-scoped read actions under [READ-ONLY-PRODUCTION-SSH.md](READ-ONLY-PRODUCTION-SSH.md). Omar alone implements application code and performs owner-authorized live changes. Where Nour designs the interface, the owner accepts the specific design package before Nour hands it and Adam's product plan to Omar.

## Release gate

Direct SSH does not remove the owner gate.

Required flow:

```text
@Omar implementation
→ @Salem QA PASS
→ READY_FOR_DEPLOYMENT
→ Owner explicitly authorizes the specific deployment
→ @Omar direct SSH deployment
→ Production health check
→ DEPLOYED / DEPLOY_FAILED
```

## Production target

- Web root: `/home/ymnay/htdocs/ymnay.com`
- Laravel root: `/home/ymnay/htdocs/ymnay.com/core`
- Canonical source: approved revision from `main`
- Public health check: `https://ymnay.com/`

## Permission

After explicit owner authorization for the exact `READY_FOR_DEPLOYMENT` task/revision, Omar may use a connected secure SSH/terminal tool to deploy that revision directly to Production.

Omar must preserve the safety properties of `.github/workflows/deploy-production.yml` and `.deployment/rsync-filter.rules`:

- verify the exact approved revision;
- verify the Production target before mutation;
- create a filtered source backup first;
- keep repository-only team instructions (`.ai/` and `AGENTS.md`) out of the live server;
- preserve Production `.env`, runtime/storage data, customer uploads, payment proofs, generated links, and other excluded runtime paths;
- do not use blind `git reset --hard` against the live checkout;
- use maintenance mode only for the deployment window;
- sync only the approved source/build output;
- run required Production dependency/cache steps;
- restore the application online;
- perform public and task-specific health verification;
- update the tracked GitHub Issue with method `DIRECT_SSH`, deployed commit, timestamp, and result.

## Separate approvals remain required

Ordinary direct deployment permission does not authorize Omar to:

- run Production migrations or seeders;
- modify Production `.env`;
- run destructive database commands;
- change customer data;
- restart unrelated services/workers;
- perform rollback.

Those actions require their own explicit owner authorization under the main deployment protocol.

## Credentials

Private SSH keys, passwords, tokens, and connection secrets must never be stored in Git, `.ai/`, GitHub Issues, work packages, or project Context.

Direct access must use an owner-authorized secure connection/credential store. If the active Omar session does not have an authorized SSH/terminal connection, Omar must report the access requirement rather than ask for a private key to be pasted into normal chat or repository files.
