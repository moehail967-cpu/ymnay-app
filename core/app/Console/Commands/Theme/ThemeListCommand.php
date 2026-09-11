<?php

namespace App\Console\Commands\Theme;

use Illuminate\Console\Command;

class ThemeListCommand extends Command
{
    protected $signature   = 'theme:list';
    protected $description = 'List all installed themes with their status';

    public function handle(): int
    {
        $base = config('theme.base_path');
        $rows = [];

        if (!is_dir($base)) {
            $this->warn('themes/ directory not found at: ' . $base);
            return self::SUCCESS;
        }

        foreach (glob($base . '/*/theme.json') as $jsonFile) {
            $slug = basename(dirname($jsonFile));
            if ($slug === '_stubs') {
                continue;
            }

            $meta      = json_decode(file_get_contents($jsonFile));
            $published = is_link(public_path("themes/{$slug}")) || is_dir(public_path("themes/{$slug}"));

            $rows[] = [
                $slug,
                $meta->name    ?? '—',
                $meta->version ?? '—',
                $meta->niche   ?? '—',
                ($meta->status ?? false) ? '<fg=green>active</>' : '<fg=red>inactive</>',
                $published ? '<fg=green>yes</>' : '<fg=yellow>no</>',
            ];
        }

        if (empty($rows)) {
            $this->warn('No themes found.');
            return self::SUCCESS;
        }

        $this->table(
            ['Slug', 'Name', 'Version', 'Niche', 'Status', 'Assets Published'],
            $rows
        );

        return self::SUCCESS;
    }
}
