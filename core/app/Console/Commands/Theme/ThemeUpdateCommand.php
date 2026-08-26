<?php

namespace App\Console\Commands\Theme;

use App\Models\ThemeMarketplace;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class ThemeUpdateCommand extends Command
{
    protected $signature   = 'theme:update {slug} {--zip= : Path to the new theme zip file}';
    protected $description = 'Apply a theme update from a zip file';

    public function handle(): int
    {
        $slug    = $this->argument('slug');
        $zipPath = $this->option('zip');

        $themeBase = config('theme.base_path') . '/' . $slug;

        if (!is_dir($themeBase)) {
            $this->error("Theme [{$slug}] not found at themes/{$slug}/.");
            return self::FAILURE;
        }

        if (!$zipPath || !file_exists($zipPath)) {
            $this->error('Provide the path to the new theme zip via --zip=<path>.');
            return self::FAILURE;
        }

        $marketplace = ThemeMarketplace::where('slug', $slug)->first();
        $oldVersion  = $marketplace?->installed_version ?? 'unknown';

        // 1. Backup current version
        $backupDir  = storage_path('theme-backups');
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        $backupFile = "{$backupDir}/{$slug}-{$oldVersion}.zip";
        $this->backup($themeBase, $backupFile);
        $this->info("  Backup saved to: {$backupFile}");

        // 2. Extract new zip over existing directory
        $tempDir = storage_path('app/theme-uploads/update-' . $slug);
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipPath) !== true) {
            $this->error('Could not open zip file.');
            return self::FAILURE;
        }
        $zip->extractTo($tempDir);
        $zip->close();

        // Find root of extracted theme
        $extractedRoot = $tempDir;
        if (!file_exists($extractedRoot . '/theme.json')) {
            $subdirs = glob($tempDir . '/*', GLOB_ONLYDIR);
            $extractedRoot = $subdirs[0] ?? $tempDir;
        }

        // Merge extracted files into themes/{slug}/ (preserves unrelated customizations)
        $this->mergeDirectories($extractedRoot, $themeBase);

        // Clean up temp
        $this->deleteDirectory($tempDir);

        // 3. Read new version from updated theme.json
        $newMeta    = json_decode(file_get_contents("{$themeBase}/theme.json"));
        $newVersion = $newMeta->version ?? $oldVersion;

        // 4. Run theme-specific migration script if present
        $migrateScript = "{$themeBase}/updates/{$newVersion}/migrate.php";
        if (file_exists($migrateScript)) {
            $this->info("  Running migration script for {$newVersion}...");
            require $migrateScript;
        }

        // 5. Republish assets
        Artisan::call('theme:publish', ['slug' => $slug]);

        // 6. Update DB record
        if ($marketplace) {
            $marketplace->update([
                'installed_version' => $newVersion,
                'latest_version'    => $newVersion,
                'update_available'  => false,
                'version'           => $newVersion,
            ]);
        }

        Cache::forget('theme_meta_' . $slug);

        $this->info("  [{$slug}] Updated: {$oldVersion} → {$newVersion}");

        return self::SUCCESS;
    }

    protected function backup(string $source, string $destination): void
    {
        if (!class_exists('ZipArchive')) {
            return;
        }
        $zip = new \ZipArchive();
        if ($zip->open($destination, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return;
        }

        $source   = realpath($source);
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            if (!$file->isDir()) {
                $filePath     = $file->getRealPath();
                $relativePath = substr($filePath, strlen($source) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();
    }

    protected function mergeDirectories(string $source, string $destination): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $relativePath = substr($item->getPathname(), strlen($source) + 1);
            $destPath     = $destination . '/' . $relativePath;

            if ($item->isDir()) {
                if (!is_dir($destPath)) {
                    mkdir($destPath, 0755, true);
                }
            } else {
                if (!is_dir(dirname($destPath))) {
                    mkdir(dirname($destPath), 0755, true);
                }
                copy($item->getPathname(), $destPath);
            }
        }
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
}
