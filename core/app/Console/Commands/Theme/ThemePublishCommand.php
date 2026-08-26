<?php

namespace App\Console\Commands\Theme;

use Illuminate\Console\Command;

class ThemePublishCommand extends Command
{
    protected $signature   = 'theme:publish {slug?} {--all : Publish all themes} {--copy : Copy instead of symlink (Windows)}';
    protected $description = 'Symlink (or copy) theme assets to public/themes/{slug}/';

    public function handle(): int
    {
        $slugs = $this->resolveSlugs();

        if (empty($slugs)) {
            $this->error('No themes found to publish.');
            return self::FAILURE;
        }

        foreach ($slugs as $slug) {
            $this->publishTheme($slug);
        }

        return self::SUCCESS;
    }

    protected function publishTheme(string $slug): void
    {
        $source      = config('theme.base_path') . '/' . $slug . '/assets';
        $destination = public_path("themes/{$slug}");

        if (!is_dir($source)) {
            $this->warn("  [{$slug}] No assets directory found at {$source} — skipped.");
            return;
        }

        // Remove existing symlink or directory
        if (is_link($destination)) {
            unlink($destination);
        } elseif (is_dir($destination)) {
            $this->warn("  [{$slug}] {$destination} is a real directory — use --copy to update it.");
            return;
        }

        // Ensure parent exists
        $old = umask(0);
        if (!is_dir(public_path('themes'))) {
            mkdir(public_path('themes'), 0775, true);
        }

        if ($this->option('copy')) {
            $this->copyDirectory($source, $destination);
            umask($old);
            $this->info("  [{$slug}] Copied assets → public/themes/{$slug}/");
        } else {
            $relativeSource = $this->relativeSymlinkPath(public_path("themes"), $source);
            symlink($relativeSource, $destination);
            umask($old);
            $this->info("  [{$slug}] Symlinked → public/themes/{$slug}/");
        }
    }

    protected function resolveSlugs(): array
    {
        if ($this->option('all')) {
            $base = config('theme.base_path');
            if (!is_dir($base)) {
                return [];
            }
            $dirs = array_map('basename', glob($base . '/*', GLOB_ONLYDIR) ?: []);
            return array_values(array_filter($dirs, fn($d) => $d !== '_stubs'));
        }

        $slug = $this->argument('slug');
        if (!$slug) {
            $this->error('Provide a slug or use --all.');
            return [];
        }

        return [$slug];
    }

    protected function relativeSymlinkPath(string $fromDir, string $toTarget): string
    {
        // Build a relative path from $fromDir to $toTarget
        $from   = explode(DIRECTORY_SEPARATOR, rtrim($fromDir, DIRECTORY_SEPARATOR));
        $to     = explode(DIRECTORY_SEPARATOR, rtrim($toTarget, DIRECTORY_SEPARATOR));
        $common = 0;

        foreach ($from as $i => $segment) {
            if (isset($to[$i]) && $segment === $to[$i]) {
                $common++;
            } else {
                break;
            }
        }

        $upCount = count($from) - $common;
        $downs   = array_slice($to, $common);

        return str_repeat('..' . DIRECTORY_SEPARATOR, $upCount) . implode(DIRECTORY_SEPARATOR, $downs);
    }

    protected function copyDirectory(string $source, string $destination): void
    {
        if (!is_dir($destination)) {
            mkdir($destination, 0775, true);
        }
        $dir = opendir($source);
        while (($file = readdir($dir)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $src  = $source . '/' . $file;
            $dest = $destination . '/' . $file;
            if (is_dir($src)) {
                $this->copyDirectory($src, $dest);
            } else {
                copy($src, $dest);
            }
        }
        closedir($dir);
    }
}
