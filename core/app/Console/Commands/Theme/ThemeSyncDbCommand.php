<?php

namespace App\Console\Commands\Theme;

use App\Models\Themes;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ThemeSyncDbCommand extends Command
{
    protected $signature   = 'theme:sync-db';
    protected $description = 'Sync themes table from all theme.json files in themes/';

    public function handle(): int
    {
        $base  = config('theme.base_path');
        $count = 0;

        if (!is_dir($base)) {
            $this->error('themes/ directory not found at: ' . $base);
            return self::FAILURE;
        }

        foreach (glob($base . '/*/theme.json') as $jsonFile) {
            $slug = basename(dirname($jsonFile));
            if ($slug === '_stubs') {
                continue;
            }

            $meta = json_decode(file_get_contents($jsonFile), true);
            if (!$meta || empty($meta['slug'])) {
                continue;
            }

            Themes::updateOrCreate(
                ['slug' => $slug],
                [
                    'title'             => $meta['name']        ?? $slug,
                    'description'       => $meta['description'] ?? '',
                    'status'            => $meta['status']      ?? true,
                    'unique_key'        => Themes::where('slug', $slug)->value('unique_key') ?? Str::uuid()->toString(),
                    'version'           => $meta['version']     ?? null,
                    'niche'             => $meta['niche']       ?? null,
                    'installed_version' => $meta['version']     ?? null,
                ]
            );

            $themeName = $meta['name'] ?? $slug;
            $this->line("  Synced: {$slug} ({$themeName})");
            $count++;
        }

        $this->info("Synced {$count} theme(s) to the database.");

        return self::SUCCESS;
    }
}
