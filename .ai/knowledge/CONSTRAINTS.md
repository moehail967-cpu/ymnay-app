# Constraints

## VERIFIED contracts to preserve

| Constraint | Why breaking it has wider impact | Source |
|---|---|---|
| Central/tenant DB ownership | Same model classes and numeric IDs can refer to unrelated actors in different DBs | `core/config/tenancy.php`, boundary models in DATA-MODEL |
| Tenant key is a string | Domains, plugin overrides, credentials and DB naming depend on it | `core/app/Models/Tenant.php`, domain and plugin migrations |
| Payment and fulfillment states are distinct | Approval, provisioning, stock, commissions, mail and UI consume separate values | PaymentLogController, ProductCheckoutService, tenant OrderManageController |
| Provider/job order | Tenant DB/schema/data/domain/theme must exist at the right lifecycle point | `core/app/Providers/TenancyServiceProvider.php` |
| Separate schema paths | Central migrate is not tenants:migrate; module table alters may depend on core creation | `core/config/tenancy.php` |
| Theme fallback and widget keys | Active overrides and persisted builder JSON depend on stable names/formats | ThemeManager, ThemeServiceProvider, CustomPageBuilderController |
| Plugin lifecycle/context | Route discovery, active boot, settings and tenant overrides are separate phases | `core/app/PluginSystem/PluginManager.php` |
| HTTP contracts | External callbacks, AJAX selectors, mobile routes and forms consume existing paths/keys | `core/routes`, `core/resources/views`, `core/Modules/MobileApp` |
| Reproducible source boundary | Runtime dependencies/media are deliberately outside Git; never reintroduce them as source | `.gitignore`, `core/.gitignore`, `core/.env.example` |

## Owner constraints and recommendations

- Preserve branding, fonts and authored presentation when a backend task does not request UI changes. Removed custom features are not implicitly approved for reintroduction.
- Do not change Production or merge a branch merely because local checks pass; current task authority determines deployment scope.
- Migrations affecting multiple tenants, callback semantics, financial ledger operations and tenant deletion require explicit targets and proportionate isolated tests.
- External API backward compatibility and database rollback safety need case-specific verification; neither is guaranteed by this source inventory.

See [CHANGE-IMPACT](CHANGE-IMPACT.md) before selecting verification scope, and [UNKNOWNS](UNKNOWNS.md) when a constraint depends on live configuration.
