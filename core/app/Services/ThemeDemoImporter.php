<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\MediaUploader;
use App\Models\Page;
use App\Models\PageBuilder;
use App\Models\Slug;
use App\Models\Widgets;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Attributes\Entities\Brand;
use Modules\Attributes\Entities\Category;
use Modules\Product\Entities\Product;
use Modules\Product\Entities\ProductCategory;
use Modules\Product\Entities\ProductInventory;
use Modules\Product\Entities\ProductShippingReturnPolicy;
use Modules\Product\Entities\ProductTag;
use Modules\Product\Entities\ProductUom;
use Xgenious\PageBuilder\Models\PageBuilderContent;
use Xgenious\PageBuilder\Models\PageBuilderWidget;

class ThemeDemoImporter
{
    protected string $themeBase;

    public function __construct(protected string $slug)
    {
        $this->themeBase = config('theme.base_path') . '/' . $slug;
    }

    public function import(): array
    {
        $dataFile = $this->themeBase . '/demo/data.json';

        if (!file_exists($dataFile)) {
            return ['status' => false, 'msg' => __('No demo data found for this theme.')];
        }

        $data = json_decode(file_get_contents($dataFile), true);

        if (empty($data['sections'])) {
            return ['status' => false, 'msg' => __('Demo data file is empty or invalid.')];
        }

        // Media + settings have their own error handling — run outside a transaction.
        $mediaMap = $this->importMedia($data['sections']['media'] ?? []);
        $this->importSettings($data['sections']['settings'] ?? [], $mediaMap);

        // Categories + products + brands: atomic together (products need categoryMap).
        try {
            DB::transaction(function () use ($data, $mediaMap) {
                $this->importCategories($data['sections']['categories'] ?? [], $mediaMap);
                $this->importProducts($data['sections']['products'] ?? [], $mediaMap);
                $this->importBrands($data['sections']['brands'] ?? [], $mediaMap);
                $this->importMenus($data['sections']['menus'] ?? []);
            });
        } catch (\Throwable $e) {
            Log::error('ThemeDemoImporter (catalog) failed: ' . $e->getMessage());
            return ['status' => false, 'msg' => __('Demo data import failed: ') . $e->getMessage()];
        }

        // Pages in a separate transaction so a layout failure never rolls back catalog data.
        try {
            DB::transaction(function () use ($data, $mediaMap) {
                $this->importPages($data['sections']['pages'] ?? [], $mediaMap);
            });
        } catch (\Throwable $e) {
            Log::error('ThemeDemoImporter (pages) failed: ' . $e->getMessage());
            // Non-fatal: catalog data is already committed.
        }

        // Widgets — non-fatal, run after pages so primary_menu token can resolve.
        try {
            $this->importWidgets($data['sections']['widgets'] ?? []);
        } catch (\Throwable $e) {
            Log::error('ThemeDemoImporter (widgets) failed: ' . $e->getMessage());
        }

        return ['status' => true, 'msg' => __('Theme demo data imported successfully.')];
    }

    protected function importMedia(array $mediaList): array
    {
        $map = [];

        if (empty($mediaList)) {
            return $map;
        }

        $tenantId  = function_exists('tenant') ? (tenant()?->getTenantKey() ?? '') : '';
        $uploadDir = global_assets_path("assets/tenant/uploads/media-uploader/{$tenantId}");

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $admin = Admin::first();

        foreach ($mediaList as $item) {
            if (empty($item['path']) || empty($item['import_as'])) {
                continue;
            }

            $src = $this->themeBase . '/' . $item['path'];
            if (!file_exists($src)) {
                continue;
            }

            $filename  = pathinfo($src, PATHINFO_FILENAME);
            $extension = strtolower(pathinfo($src, PATHINFO_EXTENSION));
            $destName  = Str::slug($filename) . '-' . Str::random(8) . '.' . $extension;
            $dest      = $uploadDir . '/' . $destName;

            if (!copy($src, $dest)) {
                continue;
            }

            $dimensions = @getimagesize($dest);
            $dimString  = $dimensions ? ($dimensions[0] . ' x ' . $dimensions[1] . ' pixels') : null;

            $media = MediaUploader::create([
                'title'      => basename($src),
                'path'       => $destName,
                'size'       => null,
                'dimensions' => $dimString,
                'user_type'  => 0,
                'user_id'    => $admin?->id,
                'load_from'  => 0,
            ]);

            $map[$item['import_as']] = $media->id;
        }

        return $map;
    }

    // Branding keys that belong to the tenant, not the theme demo — never overwrite.
    protected array $protectedSettingKeys = [
        'site_logo',
        'site_white_logo',
        'site_favicon',
        'site_title',
        'site_description',
    ];

    protected function importSettings(array $settings, array $mediaMap = []): void
    {
        foreach ($settings as $setting) {
            if (!empty($setting['key']) && !in_array($setting['key'], $this->protectedSettingKeys)) {
                $value = $setting['value'] ?? '';
                if (is_string($value) && str_starts_with($value, '@media:')) {
                    $value = $mediaMap[substr($value, 7)] ?? '';
                }
                update_static_option($setting['key'], $value);
            }
        }
    }

    /** @var array<string, int> slug → category id, built during importCategories */
    protected array $categoryMap = [];

    protected function importCategories(array $categories, array $mediaMap = []): void
    {
        foreach ($categories as $item) {
            if (empty($item['name'])) {
                continue;
            }

            $slug    = Str::slug($item['slug'] ?? $item['name']);
            $imageId = isset($item['image']) ? ($mediaMap[$item['image']] ?? null) : null;

            // withTrashed so soft-deleted rows don't break the unique constraint on re-import
            $existing = Category::withTrashed()->where('slug', $slug)->first();
            if ($existing) {
                if ($existing->trashed()) {
                    $existing->restore();
                }
                $existing->update(['image_id' => $imageId ?: $existing->image_id, 'theme_slug' => $this->slug]);
                // Ensure a Slug record exists — may be missing if category was created
                // before the slug system was added, or via a partial/manual import.
                if (!Slug::where('slug', $slug)->exists()) {
                    $existing->slug()->create(['slug' => $slug]);
                }
                $this->categoryMap[$slug] = $existing->id;
                continue;
            }

            $category = Category::create([
                'name'       => $item['name'],
                'slug'       => $slug,
                'image_id'   => $imageId,
                'status_id'  => 1,
                'theme_slug' => $this->slug,
            ]);

            // Guard against leftover slug records from a previous partial import
            if (!Slug::where('slug', $slug)->exists()) {
                $category->slug()->create(['slug' => $slug]);
            }

            $this->categoryMap[$slug] = $category->id;
        }
    }

    protected function importBrands(array $brands, array $mediaMap = []): void
    {
        if (empty($brands) || !class_exists(Brand::class)) {
            return;
        }

        foreach ($brands as $item) {
            if (empty($item['name'])) {
                continue;
            }

            $slug    = Str::slug($item['slug'] ?? $item['name']);
            $imageId = isset($item['image']) ? ($mediaMap[$item['image']] ?? null) : null;

            $existing = Brand::withTrashed()->where('slug', $slug)->first();
            if ($existing) {
                if ($existing->trashed()) {
                    $existing->restore();
                }
                $existing->update([
                    'image_id' => $imageId ?: $existing->image_id,
                    'url'      => $item['url'] ?? $existing->url,
                ]);
                continue;
            }

            Brand::create([
                'name'      => $item['name'],
                'slug'      => $slug,
                'url'       => $item['url'] ?? null,
                'image_id'  => $imageId,
                'banner_id' => $imageId,
            ]);
        }
    }

    protected function importProducts(array $products, array $mediaMap): void
    {
        foreach ($products as $item) {
            if (empty($item['name'])) {
                continue;
            }

            $existing = Product::where('name', $item['name'])->first();
            if ($existing) {
                $newImageId = isset($item['image']) ? ($mediaMap[$item['image']] ?? null) : null;
                if ($newImageId) {
                    $existing->image_id = $newImageId;
                }
                // Always refresh sold_count and pricing so Best Sellers / sorting
                // reflects the current theme's demo data, even on re-import.
                $existing->sold_count = $item['sold_count'] ?? $existing->sold_count;
                if (isset($item['price']))         $existing->price      = $item['price'];
                if (isset($item['compare_price'])) $existing->sale_price = $item['compare_price'];
                $existing->created_at = now();
                $existing->save();

                // Also sync inventory stock if provided
                if (isset($item['stock']) && $existing->inventory) {
                    $existing->inventory->update(['stock_count' => $item['stock']]);
                }
                continue;
            }

            $slug = create_slug(Str::slug($item['name']), 'Slug');

            $product = Product::create([
                'name'        => $item['name'],
                'slug'        => $slug,
                'summary'     => $item['summary'] ?? '',
                'description' => $item['description'] ?? '',
                'price'       => $item['price'] ?? 0,
                'sale_price'  => $item['compare_price'] ?? $item['sale_price'] ?? null,
                'cost'        => $item['cost'] ?? 0,
                'image_id'    => isset($item['image']) ? ($mediaMap[$item['image']] ?? null) : null,
                'status_id'   => 1,
                'is_taxable'  => false,
                'min_purchase' => 1,
                'max_purchase' => 100,
                'is_inventory_warn_able' => 2,
                'is_refundable' => false,
                'sold_count'  => $item['sold_count'] ?? 0,
            ]);

            $product->slug()->create(['slug' => $slug]);

            // Inventory — simple fixed stock
            ProductInventory::create([
                'product_id'  => $product->id,
                'sku'         => strtoupper(Str::random(8)),
                'stock_count' => $item['stock'] ?? 100,
                'sold_count'  => 0,
            ]);

            // Link to category
            $categorySlug = $item['category'] ?? null;
            if ($categorySlug && isset($this->categoryMap[$categorySlug])) {
                ProductCategory::create([
                    'category_id' => $this->categoryMap[$categorySlug],
                    'product_id'  => $product->id,
                ]);
            } elseif (!empty($this->categoryMap)) {
                // Fallback to first imported category
                ProductCategory::create([
                    'category_id' => array_values($this->categoryMap)[0],
                    'product_id'  => $product->id,
                ]);
            }

            // Tags
            if (!empty($item['tags']) && is_array($item['tags'])) {
                $tagRows = array_map(fn($t) => ['product_id' => $product->id, 'tag_name' => $t], $item['tags']);
                ProductTag::insert($tagRows);
            }

            // Required related records
            ProductShippingReturnPolicy::create([
                'product_id'                  => $product->id,
                'shipping_return_description' => '',
            ]);

            // ProductUom requires a valid unit_id — skip if none is available
            $defaultUnit = \Modules\Attributes\Entities\Unit::first();
            if ($defaultUnit) {
                ProductUom::create([
                    'product_id' => $product->id,
                    'unit_id'    => $defaultUnit->id,
                    'quantity'   => 1,
                ]);
            }
        }
    }

    protected function importMenus(array $menus): void
    {
        if (empty($menus)) {
            return;
        }

        $baseUrl = url('/');

        foreach ($menus as $menuData) {
            $title  = $menuData['title'] ?? 'Menu';
            $status = $menuData['status'] ?? 'active';

            // Build content items, replacing @url token with base URL
            $items = [];
            foreach ($menuData['items'] ?? [] as $item) {
                $purl = str_replace('@url', $baseUrl, $item['url'] ?? '#');
                $items[] = [
                    'ptype'     => 'custom',
                    'purl'      => $purl,
                    'pname'     => $item['label'] ?? '',
                    'menulabel' => $item['label'] ?? '',
                ];
            }

            \App\Models\Menu::updateOrCreate(
                ['title' => $title],
                [
                    'status'  => $status,
                    'content' => json_encode($items),
                ]
            );
        }
    }

    /**
     * Import footer/sidebar widget rows from data.json sections.widgets.
     *
     * Each item format:
     * {
     *   "widget_location": "footer",
     *   "widget_name":     "NavigationMenuWidget",
     *   "widget_order":    1,
     *   "widget_namespace":"Plugins\\WidgetBuilder\\Widgets\\Tenants\\Electro\\NavigationMenuWidget",
     *   "widget_settings": { ... }   ← plain JSON object; stored as PHP serialize()
     * }
     *
     * Token in widget_settings values:
     *   "@primary_menu" → resolved to the tenant's first menu id
     */
    protected function importWidgets(array $widgets): void
    {
        if (empty($widgets)) {
            return;
        }

        // Resolve @primary_menu token → first menu id on this tenant
        $primaryMenuId = \App\Models\Menu::orderBy('id')->value('id');

        // Collect unique locations so we can wipe them cleanly before insert
        $locations = array_unique(array_column($widgets, 'widget_location'));
        foreach ($locations as $loc) {
            Widgets::where('widget_location', $loc)->delete();
        }

        foreach ($widgets as $item) {
            if (empty($item['widget_location']) || empty($item['widget_name'])) {
                continue;
            }

            // Resolve tokens inside widget_settings (recursive)
            $settings = $this->resolveWidgetTokens(
                $item['widget_settings'] ?? [],
                $primaryMenuId
            );

            // Build the content array the way the WidgetBuilder expects it
            $content = array_merge([
                'widget_name'     => $item['widget_name'],
                'widget_type'     => 'new',
                'widget_location' => $item['widget_location'],
                'widget_order'    => (string) ($item['widget_order'] ?? 1),
                'namespace'       => $item['widget_namespace'] ?? '',
            ], $settings);

            Widgets::create([
                'widget_location'  => $item['widget_location'],
                'widget_name'      => $item['widget_name'],
                'widget_order'     => $item['widget_order'] ?? 1,
                'widget_namespace' => $item['widget_namespace'] ?? '',
                'widget_content'   => serialize($content),
                'widget_area'      => $item['widget_location'],
            ]);
        }
    }

    /**
     * Recursively resolve "@media:import_as" tokens inside page-builder widget
     * general_settings. A token value like "@media:garage-hero" is replaced with
     * the MediaUploader record that was just imported (looked up by import_as key
     * in $mediaMap), formatted as the array the image picker widget expects:
     * { id, url, img_url, title, mime_type }
     *
     * If the value is a string token: it is replaced with the resolved media array.
     * If the value is already an array containing an "id" key (exported from editor):
     * the stored id/url are replaced with the freshly-imported media data so URLs
     * are always relative to the current tenant.
     */
    private function resolveMediaTokensInSettings(mixed $settings, array $mediaMap): mixed
    {
        if ($settings === null) {
            return $settings;
        }

        if (is_string($settings)) {
            // "@media:import_as_key" → resolved full media object for the image picker
            if (!empty($mediaMap) && str_starts_with($settings, '@media:')) {
                $key     = substr($settings, 7);
                $mediaId = $mediaMap[$key] ?? null;
                return $mediaId ? $this->buildMediaObject($mediaId) : null;
            }

            // "@url/path" → resolved absolute URL for the current tenant
            if (str_starts_with($settings, '@url')) {
                return rtrim(url('/'), '/') . substr($settings, 4);
            }

            return $settings;
        }

        if (is_array($settings)) {
            // Detect an "exported-from-editor" media picker object:
            // { id: <int>, url: "https://...", title: "filename.jpg", ... }
            // The editor saves real IDs which are tenant-specific and invalid on import.
            // Re-resolve by matching the title slug against the import_as keys in mediaMap.
            if (isset($settings['id'], $settings['url'], $settings['title'])
                && is_numeric($settings['id'])
                && is_string($settings['url'])
                && is_string($settings['title'])
            ) {
                $titleSlug = Str::slug(pathinfo($settings['title'], PATHINFO_FILENAME));
                if ($titleSlug && isset($mediaMap[$titleSlug])) {
                    return $this->buildMediaObject($mediaMap[$titleSlug]);
                }
                // No matching import_as key — return null so the widget uses its placeholder
                return null;
            }

            foreach ($settings as $key => $value) {
                $settings[$key] = $this->resolveMediaTokensInSettings($value, $mediaMap);
            }
        }

        return $settings;
    }

    /**
     * Build the full media object that the page builder image picker widget expects.
     * The picker needs {id, url, img_url, title} to display the thumbnail in the
     * settings panel. Storing only an integer ID causes "broken image" in the editor.
     */
    private function buildMediaObject(int $mediaId): ?array
    {
        $media = \App\Models\MediaUploader::find($mediaId);
        if (!$media) {
            return null;
        }

        // Store null for URL fields intentionally.
        // During CLI execution (artisan), url('/') returns the landlord URL, not the
        // tenant URL, so any URL generated here would be wrong. By storing null, the
        // widget resolveImage() helper falls through to get_attachment_image_by_id($id)
        // at HTTP render time, which correctly generates the tenant-scoped URL.
        return [
            'id'        => $media->id,
            'url'       => null,
            'img_url'   => null,
            'title'     => $media->title ?? $media->path,
            'mime_type' => 'image/jpeg',
        ];
    }

    /**
     * Recursively walk widget_settings and replace token strings.
     *
     * Supported tokens:
     *   "@primary_menu"      → first menu id
     *   "@menu:status_name"  → id of menu whose `status` column matches status_name
     *   "@url/path"          → resolved base URL + /path  (e.g. "@url/shop" → "https://tenant.test/shop")
     */
    private function resolveWidgetTokens(array $settings, ?int $primaryMenuId): array
    {
        $baseUrl = rtrim(url('/'), '/');

        foreach ($settings as $key => $value) {
            if (is_array($value)) {
                $settings[$key] = $this->resolveWidgetTokens($value, $primaryMenuId);
            } elseif ($value === '@primary_menu') {
                $settings[$key] = $primaryMenuId ? (string) $primaryMenuId : '1';
            } elseif (is_string($value) && str_starts_with($value, '@menu:')) {
                $status  = substr($value, 6);
                $menuId  = \App\Models\Menu::where('status', $status)->value('id');
                $settings[$key] = $menuId ? (string) $menuId : ($primaryMenuId ? (string) $primaryMenuId : '');
            } elseif (is_string($value) && str_starts_with($value, '@url')) {
                $settings[$key] = $baseUrl . substr($value, 4);
            }
        }
        return $settings;
    }

    protected function importPages(array $pages, array $mediaMap): void
    {
        foreach ($pages as $pageData) {
            $slug = $pageData['slug'] ?? null;
            if (!$slug) {
                continue;
            }

            $existing = Page::where('slug', $slug)->first();

            // If a page with this slug exists, assign a new unique slug
            if ($existing) {
                $latest = Page::latest('id')->value('id') ?? 0;
                $slug   = $pageData['slug'] . '-' . ($latest + 1);
            }

            $page = Page::create([
                'title'        => $pageData['title']        ?? 'Page',
                'slug'         => $slug,
                'page_builder' => ($pageData['page_builder'] ?? false) ? 1 : 0,
                'breadcrumb'   => ($pageData['breadcrumb']   ?? true)  ? 1 : 0,
                'status'       => 1,
            ]);
            $page->slug()->create(['slug' => $slug]);

            // Import page builder layout if specified
            if (!empty($pageData['layout_file'])) {
                $layoutFile = $this->themeBase . '/' . $pageData['layout_file'];
                $this->importPageLayout($layoutFile, $page->id, $mediaMap);
            }

            if (!empty($pageData['set_as_home'])) {
                update_static_option('home_page', $page->id);
            }
        }
    }

    protected function importPageLayout(string $layoutFile, int $pageId, array $mediaMap = []): void
    {
        if (!file_exists($layoutFile)) {
            return;
        }

        $raw  = file_get_contents($layoutFile);
        $data = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE || empty($data)) {
            return;
        }

        // ── New React page builder format (xg_page_builder_v2) ──────────
        if (isset($data['format']) && $data['format'] === 'xg_page_builder_v2') {
            $content     = $data['content'] ?? ['containers' => []];
            $widgetsData = $data['widgets'] ?? [];
            $version     = $data['version'] ?? '1.0';

            // Remap all UUIDs so this import never collides with existing pages
            $widgetIdMap    = [];
            $containerIdMap = [];
            $columnIdMap    = [];

            foreach (array_keys($widgetsData) as $oldId) {
                $widgetIdMap[$oldId] = (string) Str::uuid();
            }

            foreach ($content['containers'] as &$container) {
                if (!empty($container['id'])) {
                    $newCId = (string) Str::uuid();
                    $containerIdMap[$container['id']] = $newCId;
                    $container['id'] = $newCId;
                }
                foreach ($container['columns'] as &$column) {
                    if (!empty($column['id'])) {
                        $newColId = (string) Str::uuid();
                        $columnIdMap[$column['id']] = $newColId;
                        $column['id'] = $newColId;
                    }
                    foreach ($column['widgets'] as &$wRef) {
                        if (isset($wRef['id'], $widgetIdMap[$wRef['id']])) {
                            $wRef['id'] = $widgetIdMap[$wRef['id']];
                        }
                    }
                    unset($wRef);
                }
                unset($column);
            }
            unset($container);

            PageBuilderContent::updateOrCreate(
                ['page_id' => $pageId],
                [
                    'content'      => $content,
                    'version'      => $version,
                    'is_published' => true,
                    'published_at' => now(),
                    'updated_by'   => \App\Models\Admin::find(auth('admin')->id()) ? auth('admin')->id() : \App\Models\Admin::first()?->id,
                ]
            );

            PageBuilderWidget::where('page_id', $pageId)->delete();

            $adminId = \App\Models\Admin::find(auth('admin')->id()) ? auth('admin')->id() : \App\Models\Admin::first()?->id;
            foreach ($widgetsData as $oldWidgetId => $w) {
                if (empty($w['type'])) {
                    continue;
                }
                $generalSettings = $this->resolveMediaTokensInSettings(
                    $w['settings']['general'] ?? null,
                    $mediaMap
                );
                PageBuilderWidget::create([
                    'page_id'             => $pageId,
                    'widget_id'           => $widgetIdMap[$oldWidgetId],
                    'widget_type'         => $w['type'],
                    'container_id'        => $containerIdMap[$w['container_id'] ?? ''] ?? $w['container_id'] ?? null,
                    'column_id'           => $columnIdMap[$w['column_id'] ?? '']    ?? $w['column_id']    ?? null,
                    'sort_order'          => $w['sort_order']  ?? 0,
                    'is_visible'          => $w['is_visible']  ?? true,
                    'is_enabled'          => $w['is_enabled']  ?? true,
                    'version'             => $w['version']     ?? '1.0.0',
                    'general_settings'    => $generalSettings,
                    'style_settings'      => $w['settings']['style']      ?? null,
                    'advanced_settings'   => $w['settings']['advanced']   ?? null,
                    'responsive_settings' => $w['settings']['responsive'] ?? null,
                    'created_by'          => $adminId,
                    'updated_by'          => $adminId,
                ]);
            }

            Page::findOrFail($pageId)->update(['use_page_builder' => true, 'page_builder' => 0]);
            return;
        }

        // ── Legacy old page builder format ───────────────────────────────
        $fileContents = json_decode($raw);
        $fileContents = $fileContents->data ?? $fileContents;

        if (empty($fileContents) || !is_array($fileContents)) {
            return;
        }

        $first = current($fileContents);

        if (isset($first->addon_page_type) && $first->addon_page_type === 'dynamic_page') {
            $contentArr = [];
            foreach ($fileContents as $key => $content) {
                unset($content->id);
                $content->addon_page_id = $pageId;
                $content->created_at    = now();
                $content->updated_at    = now();
                foreach ($content as $k => $v) {
                    $contentArr[$key][$k] = $v;
                }
            }

            Page::findOrFail($pageId)->update(['page_builder' => 1]);
            PageBuilder::where('addon_page_id', $pageId)->delete();
            PageBuilder::insert($contentArr);
        } else {
            Page::findOrFail($pageId)->update([
                'page_builder' => 0,
                'page_content' => $first->text ?? '',
            ]);
        }
    }
}
