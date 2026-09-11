# Ymnay core patches

## 2026-09-02 — Scheduled package tasks

- Expiry notices are sent only on configured notification dates. The previous
  comparison sent the same notice every day after a threshold had passed.
- Wallet auto-renewal now starts from each opted-in tenant and uses that
  tenant's latest completed payment and actual plan.
- Wallet balance and tenant expiry are locked and rechecked in one database
  transaction before renewal, preventing concurrent double deductions.
- Empty users, tenants, wallets, plans, and settings are handled without
  undefined variables or a fatal error.
- Renewal results are grouped and emailed to the correct account instead of
  being accumulated and sent to the last account processed.
- Exceptions propagate to the Artisan command so failed runs return a failure
  exit code and are logged accurately.

Regression coverage: `tests/Unit/PackageExpireCommandTest.php`.
