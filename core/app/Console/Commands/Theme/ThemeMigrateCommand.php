<?php

namespace App\Console\Commands\Theme;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ThemeMigrateCommand extends Command
{
    protected $signature   = 'theme:migrate {slug?} {--all : Migrate all legacy themes} {--dry-run : Preview without making changes}';
    protected $description = 'Migrate themes from resources/views/themes/{slug}/ to themes/{slug}/';

    protected string $legacyBase;
    protected string $newBase;
    protected bool   $dryRun;

    public function handle(): int
    {
        $this->legacyBase = resource_path('views/themes');
        $this->newBase    = config('theme.base_path');
        $this->dryRun     = (bool) $this->option('dry-run');

        if ($this->dryRun) {
            $this->warn('[DRY RUN] No files will be moved.');
        }

        $slugs = $this->resolveSlugs();

        if (empty($slugs)) {
            $this->error('No legacy themes found to migrate.');
            return self::FAILURE;
        }

        foreach ($slugs as $slug) {
            $this->migrate($slug);
        }

        $this->info($this->dryRun
            ? 'Dry run complete. Run without --dry-run to apply changes.'
            : 'Migration complete. Legacy directories left untouched.'
        );

        return self::SUCCESS;
    }

    protected function resolveSlugs(): array
    {
        if ($this->option('all')) {
            if (!is_dir($this->legacyBase)) {
                return [];
            }
            $dirs = array_map('basename', glob($this->legacyBase . '/*', GLOB_ONLYDIR) ?: []);
            return array_values(array_filter($dirs, fn($d) => $d !== '_stubs'));
        }

        $slug = $this->argument('slug');
        if (!$slug) {
            $this->error('Provide a slug or use --all.');
            return [];
        }

        return [$slug];
    }

    protected function migrate(string $slug): void
    {
        $src  = $this->legacyBase . '/' . $slug;
        $dest = $this->newBase    . '/' . $slug;

        if (!is_dir($src)) {
            $this->warn("  [{$slug}] Legacy source not found at {$src} — skipped.");
            return;
        }

        $this->line('');
        $this->line("  Migrating: <fg=cyan>{$slug}</>");

        // Determine what goes where
        $map = $this->buildFileMap($slug, $src, $dest);

        if (empty($map)) {
            $this->line("  [{$slug}] Nothing to migrate.");
            return;
        }

        foreach ($map as [$from, $to, $label]) {
            $this->line("    {$label}");
            if (!$this->dryRun) {
                $dir = dirname($to);
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }
                if (!file_exists($to)) {
                    copy($from, $to);
                }
            }
        }

        if (!$this->dryRun) {
            // If no theme.json exists in new location, scaffold a minimal one
            $themeJson = $dest . '/theme.json';
            if (!file_exists($themeJson)) {
                $this->scaffoldThemeJson($slug, $dest);
                $this->line("    + Scaffolded theme.json");
            }
        }

        $this->info("  [{$slug}] Done.");
    }

    protected function buildFileMap(string $slug, string $src, string $dest): array
    {
        $map   = [];
        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($src, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($items as $file) {
            $abs      = $file->getRealPath();
            $relative = ltrim(str_replace($src, '', $abs), '/\\');

            // Determine destination path — view files go into views/
            $destPath = $this->resolveDestPath($dest, $relative);

            if (file_exists($destPath)) {
                $map[] = [$abs, $destPath, "<fg=yellow>skip (exists):</> {$relative}"];
                continue;
            }

            $map[] = [$abs, $destPath, "<fg=green>copy:</> {$relative} → " . ltrim(str_replace($this->newBase . '/' . $slug, '', $destPath), '/\\')];
        }

        return $map;
    }

    protected function resolveDestPath(string $dest, string $relative): string
    {
        // Already-structured items (assets/, screenshot/, demo/, theme.json) stay at root
        $rootItems = ['assets', 'screenshot', 'demo', 'theme.json'];
        $first     = explode('/', str_replace('\\', '/', $relative))[0];

        if (in_array($first, $rootItems, true)) {
            return $dest . '/' . $relative;
        }

        // Everything else is a view file — nest under views/
        return $dest . '/views/' . $relative;
    }

    protected function scaffoldThemeJson(string $slug, string $dest): void
    {
        $name = Str::title(str_replace(['-', '_'], ' ', $slug));
        $json = json_encode([
            'name'        => $name,
            'slug'        => $slug,
            'version'     => '1.0.0',
            'niche'       => '',
            'price'       => 0,
            'license'     => 'free',
            'status'      => true,
            'description' => "Migrated from resources/views/themes/{$slug}/",
            'screenshot'  => [['primary' => 'primary.jpg', 'sort' => 'desc']],
            'headerHook'  => [['style' => ['style'], 'rtl_style' => ['rtl'], 'script' => [], 'blade' => [], 'navbarArea' => 'header/navbar']],
            'footerHook'  => [['style' => [], 'script' => ['main'], 'blade' => [], 'widgetArea' => 'footer/widget-area']],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        file_put_contents($dest . '/theme.json', $json);
    }
}
