# Task-scoped read-only Production SSH inspection

The owner authorizes `@Adam`, `@Nour`, and `@Salem` to use the existing general Production SSH connection for assigned work. They may inspect the live project's task-relevant files, directories, and errors/logs. Their **authorized actions are read-only**, even when the shared SSH account itself has write capability. A separate read-only SSH identity is not required by the owner. `@Omar` alone may modify the live server or deploy, under the existing owner approval gates.

## Work sequence

1. Identify the assigned task, affected central/tenant context, and likely paths. Read the repository source and current project guidance.
2. Connect through the approved existing SSH connection. Verify the host and current session with non-mutating commands before claiming live access. Inspect only paths and errors relevant to the task under `/home/ymnay/htdocs/ymnay.com`, including `core/` when applicable.
3. Use bounded read commands such as `pwd`, `hostname`, `id`, `stat`, `ls`, `find`, `rg`/`grep`, `sed`, `cat`, `head`, and `tail`. Read only the smallest log segment needed. Redact secrets and customer data before recording evidence. Distinguish live findings from repository code and inference.
4. Complete the assigned role's artifacts in the GitHub Issue or task work package: Adam's system-grounded Product Brief and acceptance criteria for Nour; Nour's interface specification and real design references for owner acceptance, followed by a handoff of the accepted design and Adam's plan to Omar; Salem's independent QA evidence and verdict for Omar's implemented candidate. These roles do not edit application source code.
5. Record relevant live evidence, artifact links, checks, and remaining risks in the GitHub Issue. Follow the role handoff and owner design/release gates in task management. If live access fails, report it and continue with available repository evidence where possible.

## Server boundary

- Do not create, edit, delete, rename, upload, sync, or change permissions/ownership of any Production file. Do not run `git pull`, `git checkout`, Composer/NPM installs or builds, or deploy from SSH.
- Do not run migrations, seeders, queue/scheduler jobs, application tests, interactive consoles, payment commands, cache-clearing commands, or other commands that may change live state.
- Do not restart services, alter configuration, access customer databases for testing, or trigger business actions.
- Do not use SSH forwarding/tunnels or copy private keys, tokens, `.env` contents, payment credentials, customer uploads/proofs, sessions, or raw sensitive logs into chat, Issues, PRs, or the repository. Inspect sensitive configuration or logs only to the minimum extent needed for the assigned diagnosis, and report redacted findings.

The shared SSH account may technically allow writes; this owner-authorized role policy restricts what Adam, Nour, and Salem may **do**, not what the account could do. A successful connection is not permission to change Production. `@Omar` alone executes live changes and deployments under [OMAR-DIRECT-SSH.md](OMAR-DIRECT-SSH.md) and [README.md](README.md). QA `PASS` or a completed GitHub PR does not authorize deployment by itself.
