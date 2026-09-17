# Read-only Production SSH inspection

The owner authorizes `@Adam`, `@Nour`, and `@Salem` to inspect the live Ymnay project over direct SSH. For each assigned task whose answer depends on the current Production implementation, they should attempt a read-only connection and inspect the relevant live paths. This is an inspection permission, not a deployment or server-administration role. `@Omar` is the only registered project agent allowed to modify the server or deploy application code, and only under the applicable owner-approval protocol.

## Task-scoped workflow

1. Read the assigned task, identify the relevant central/tenant area and exact live paths, and consult the repository source and knowledge first.
2. Use an approved **separate, technically read-only** SSH identity. Do not use Omar's `root`/deployment identity or an account that can write to the application, databases, services, or deployment paths. Verify the server host key and run a non-mutating connection preflight before claiming access.
3. Inspect only task-relevant paths under `/home/ymnay/htdocs/ymnay.com` and, for Laravel, its `core/` directory. Use bounded listing, metadata, search, and file-read commands. Prefer repository evidence for broad code searches.
4. Compare live findings with the exact repository revision when relevant. Record the inspected path, time, and a redacted finding in the task record; distinguish live observation from inference. Do not copy private or customer data into chat, Issues, work packages, or the repository.
5. If the separate identity is unavailable, fails authentication, cannot reach the server, or lacks the required read permission, report that limit. Ask the owner or `@Omar` to arrange access or a sanitized evidence handoff; never fall back to a write-capable credential.

## Read-only boundary

- Do not create, edit, delete, rename, upload, sync, or change permissions/ownership of server files.
- Do not run `sudo`, deployment tools, migrations, seeders, queue/scheduler commands, application tests, interactive consoles, or application commands with possible side effects on Production.
- Do not restart services, clear caches, alter configuration, access databases directly, or trigger business actions/payments.
- Do not read Production `.env`, SSH/private keys, tokens, database credentials, payment secrets, customer uploads/proofs, session files, or log contents merely to investigate. If a task genuinely requires sensitive evidence, seek a separately scoped owner decision and use a redacted handoff.
- Do not use SSH forwarding, tunnels, or the inspection identity to reach other services.

The credential and operating-system permissions must enforce this boundary. A statement in an agent file or this policy does not itself provision SSH access. Store keys and connection secrets only in an approved secure credential store, never in Git or task records. Record the provisioning and a read-only access check before describing the three agents' direct access as operational.

Omar's separate direct-write/deployment path remains governed by [OMAR-DIRECT-SSH.md](OMAR-DIRECT-SSH.md) and [README.md](README.md). A QA `PASS` or successful inspection never authorizes deployment.
