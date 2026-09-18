# Aromatic Arabic Stitch implementation — Issue #36

## Scope and design source

- Google Stitch MCP project `Arabic Rap Hub` (`projects/9322617709532472230`), especially its أثير desktop/mobile home, catalog, about and contact screens, supplied the visual reference.
- Tenant storefront theme source: `core/themes/aromatic`. No controller, route, checkout, authentication, persistence schema or shared Page Builder implementation was changed.
- Theme version `1.1.0`; source branch `codex/aromatic-arabic-stitch`.

## Implemented

- Added an Arabic Cairo/RTL visual layer with ivory, charcoal and muted gold styling across the theme's navigation, hero, product cards, catalog, internal page banners, contact and footer. Existing search, account, cart, product and checkout components remain in use.
- Replaced the Aromatic demo's English copy and imagery with Arabic sample catalog and page content. Demo media is imported per tenant; existing customer site title/description protections in the importer remain intact.
- Home, About and Contact demo pages now use `xg_page_builder_v2`. Their widget references and image tokens are handled by the existing `ThemeDemoImporter`, which stores `PageBuilderContent` and `PageBuilderWidget` per tenant and marks each page `use_page_builder=true`. Home retains `set_as_home=true`.
- Hero stats, photo gallery, craft story and FAQ are editable theme widgets. Other sections use the existing tenant Page Builder widgets. The new widget classes follow the theme's automatic discovery convention and are gated to Aromatic.
- Removed the contact demo's unconfigured map, which otherwise displayed the generic widget's default New York location. Contact form uses the existing FormBuilder widget and requires a form available to that tenant.
- Existing customer menus, logo, store settings and language selection remain controlled through their existing system settings. Demo import remains conditional on the existing “with demo data” selection.

## Verification performed

- Parsed the changed theme JSON, checked all 24 JPEG files and CSS entries exist, and validated all three Page Builder layouts have unique matching widget references, known widget types, and resolvable media aliases. Home: 8 widgets; About: 4; Contact: 2.
- Parsed all eight Aromatic PHP widget classes with `php-parser` (syntax only); `git diff --check` passed.
- Rendered a stand-alone HTML/CSS surrogate of the hero at desktop 1440px and narrow 500px using Edge headless. This checked typography, RTL flow, image placement and responsive sizing, but was **not** a Laravel tenant runtime test.
- Inspected the theme asset loading path and the importer's v2 writer in source. A PHP CLI, containerized Laravel runtime and isolated tenant database were unavailable in this Windows workspace; real tenant activation, editing round trip, cart/checkout and full page browser QA remain for `@Salem` in a safe QA tenant.

## QA focus

1. Activate Aromatic with demo data on a disposable tenant; confirm the Arabic Home, About and Contact pages render and can be saved again in the modern Page Builder, including image replacement.
2. Verify Arabic locale, responsive header/menu/search, product/category links and catalog, product detail, cart and checkout styling without a behavior regression.
3. Confirm contact form on a tenant with a configured FormBuilder form; verify a tenant without one receives the existing setup prompt.
4. Compare desktop/mobile storefront against the named Stitch project. Ensure demo media is tenant scoped, menus remain customer editable, and activation without demo data preserves existing content.

Production deployment requires a separate owner authorization after review and QA.
