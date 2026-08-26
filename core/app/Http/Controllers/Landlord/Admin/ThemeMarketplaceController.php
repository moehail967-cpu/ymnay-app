<?php

namespace App\Http\Controllers\Landlord\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThemeMarketplace;
use App\Models\Themes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use ZipArchive;

class ThemeMarketplaceController extends Controller
{
    /**
     * List all marketplace entries (installed themes + available uploads).
     */
    public function index()
    {
        $installed = ThemeMarketplace::installed()->orderBy('name')->get();
        $available = ThemeMarketplace::where('is_installed', false)->orderBy('name')->get();

        return view('landlord.admin.themes.marketplace.index', compact('installed', 'available'));
    }

    /**
     * Accept a zip upload, extract, validate, publish assets, and register the theme.
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'theme_zip'    => 'required|file|mimes:zip|max:51200',
            'price'        => 'nullable|numeric|min:0',
            'license_type' => 'nullable|in:free,single,unlimited',
        ]);

        $zipFile = $request->file('theme_zip');
        $tempDir = storage_path('app/theme-uploads/' . Str::uuid());

        mkdir($tempDir, 0755, true);

        try {
            $zip = new ZipArchive();
            if ($zip->open($zipFile->getRealPath()) !== true) {
                return response()->json(['status' => false, 'msg' => 'Could not open zip file.']);
            }

            // Zip-slip prevention: reject any entry whose resolved path escapes $tempDir
            $realTemp = realpath($tempDir);
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entry    = $zip->getNameIndex($i);
                $resolved = realpath($tempDir . '/' . $entry);
                // realpath returns false for non-existent paths during extraction check,
                // so we normalise the path manually instead
                $normalised = $tempDir . '/' . ltrim($entry, '/');
                $normalised = str_replace(['/./', '/../'], ['/', '/'], $normalised);
                if (!str_starts_with($normalised, $tempDir)) {
                    $zip->close();
                    return response()->json(['status' => false, 'msg' => 'Malicious zip entry detected.']);
                }
            }

            $zip->extractTo($tempDir);
            $zip->close();

            // The zip must contain a theme.json at its root or inside a single subdirectory
            $themeJsonPath = $this->findThemeJson($tempDir);
            if (!$themeJsonPath) {
                return response()->json(['status' => false, 'msg' => 'theme.json not found in zip.']);
            }

            $meta = json_decode(file_get_contents($themeJsonPath));
            if (!$meta || empty($meta->slug)) {
                return response()->json(['status' => false, 'msg' => 'Invalid theme.json — slug is required.']);
            }

            // Slug must be a safe directory name: lowercase letters, digits, hyphens only
            $slug = $meta->slug;
            if (!preg_match('/^[a-z0-9][a-z0-9\-]*[a-z0-9]$/', $slug)) {
                return response()->json(['status' => false, 'msg' => 'Invalid theme slug format — use lowercase letters, digits and hyphens only.']);
            }

            $themeRoot = dirname($themeJsonPath);
            $dest      = config('theme.base_path') . '/' . $slug;

            // Copy extracted theme to themes/{slug}/
            if (is_dir($dest)) {
                $this->deleteDirectory($dest);
            }
            rename($themeRoot, $dest);

            // Publish assets (creates public/themes/{slug}/ symlink)
            Artisan::call('theme:publish', ['slug' => $slug]);

            // Persist in DB
            $price       = (float) ($request->price ?? $meta->price ?? 0);
            $licenseType = $request->license_type ?? ($meta->license ?? 'free');

            $marketplace = ThemeMarketplace::updateOrCreate(
                ['slug' => $slug],
                [
                    'name'              => $meta->name        ?? $slug,
                    'description'       => $meta->description ?? '',
                    'niche'             => $meta->niche       ?? '',
                    'version'           => $meta->version     ?? '1.0.0',
                    'price'             => $price,
                    'is_free'           => $price === 0.0,
                    'is_installed'      => true,
                    'installed_version' => $meta->version ?? '1.0.0',
                    'author'            => $meta->author  ?? '',
                    'license_type'      => $licenseType,
                    'update_available'  => false,
                ]
            );

            // Sync to the themes table so tenants can browse/select it
            Themes::updateOrCreate(
                ['slug' => $slug],
                [
                    'title'       => $meta->name        ?? $slug,
                    'description' => $meta->description ?? '',
                    'status'      => $meta->status      ?? true,
                    'unique_key'  => Themes::where('slug', $slug)->value('unique_key') ?? Str::uuid()->toString(),
                ]
            );

            Cache::forget('theme_meta_' . $slug);

            return response()->json([
                'status' => true,
                'msg'    => __('Theme installed successfully.'),
                'slug'   => $slug,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Theme upload failed: ' . $e->getMessage());
            return response()->json(['status' => false, 'msg' => 'Installation failed: ' . $e->getMessage()]);
        } finally {
            // Clean up temp directory
            if (is_dir($tempDir)) {
                $this->deleteDirectory($tempDir);
            }
        }
    }

    /**
     * Upload a new zip to update an existing installed theme.
     */
    public function update(Request $request, string $slug): JsonResponse
    {
        $request->validate(['theme_zip' => 'required|file|mimes:zip|max:51200']);

        $marketplace = ThemeMarketplace::where('slug', $slug)->firstOrFail();
        $currentDest = config('theme.base_path') . '/' . $slug;

        // Backup current version
        $backupDir = storage_path('theme-backups');
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        $backupFile = "{$backupDir}/{$slug}-{$marketplace->installed_version}.zip";
        $this->zipDirectory($currentDest, $backupFile);

        // Re-use upload logic
        return $this->upload($request);
    }

    /**
     * Update price / plan assignment for an installed theme.
     */
    public function setPricing(Request $request, string $slug): JsonResponse
    {
        $request->validate([
            'price'        => 'required|numeric|min:0',
            'license_type' => 'nullable|in:free,single,unlimited',
        ]);

        $marketplace = ThemeMarketplace::where('slug', $slug)->firstOrFail();

        $price = (float) $request->price;
        $marketplace->update([
            'price'        => $price,
            'is_free'      => $price === 0.0,
            'license_type' => $request->license_type ?? 'free',
        ]);

        return response()->json(['status' => true, 'msg' => __('Pricing updated.')]);
    }

    /**
     * Mark theme as uninstalled (preserve files so existing tenants don't break).
     */
    public function destroy(string $slug): JsonResponse
    {
        $marketplace = ThemeMarketplace::where('slug', $slug)->firstOrFail();
        $marketplace->update(['is_installed' => false]);

        Themes::where('slug', $slug)->update(['status' => false]);

        return response()->json(['status' => true, 'msg' => __('Theme deactivated from marketplace.')]);
    }

    // ── Internal helpers ──────────────────────────────────────────────────────

    protected function findThemeJson(string $dir): ?string
    {
        // Check root-level
        if (file_exists($dir . '/theme.json')) {
            return $dir . '/theme.json';
        }

        // Check one level deep (zip wraps everything in a subdirectory)
        foreach (glob($dir . '/*/theme.json') as $path) {
            return $path;
        }

        return null;
    }

    protected function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $items = array_diff(scandir($dir), ['.', '..']);
        foreach ($items as $item) {
            $path = $dir . '/' . $item;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }

    protected function zipDirectory(string $source, string $destination): void
    {
        if (!class_exists('ZipArchive')) {
            return;
        }
        $zip = new ZipArchive();
        if ($zip->open($destination, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return;
        }

        $source   = realpath($source);
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            if (!$file->isDir()) {
                $filePath   = $file->getRealPath();
                $relativePath = substr($filePath, strlen($source) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();
    }
}
