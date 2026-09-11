<?php

namespace App\Console\Commands\Theme;

use App\Models\ThemeMarketplace;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ThemeCheckUpdatesCommand extends Command
{
    protected $signature   = 'theme:check-updates';
    protected $description = 'Check for theme updates by comparing installed vs. theme.json versions';

    public function handle(): int
    {
        $installed = ThemeMarketplace::installed()->get();

        if ($installed->isEmpty()) {
            $this->info('No installed themes found.');
            return self::SUCCESS;
        }

        $updatesFound = 0;

        foreach ($installed as $theme) {
            $jsonPath = config('theme.base_path') . '/' . $theme->slug . '/theme.json';

            if (!file_exists($jsonPath)) {
                $this->warn("  [{$theme->slug}] theme.json not found — skipped.");
                continue;
            }

            $meta    = json_decode(file_get_contents($jsonPath));
            $onDisk  = $meta->version ?? '1.0.0';
            $inDb    = $theme->installed_version ?? '1.0.0';

            // An update is available when the on-disk version exceeds what was last recorded
            $hasUpdate = version_compare($onDisk, $inDb, '>');

            $theme->update([
                'installed_version' => $inDb,   // what tenant currently has
                'latest_version'    => $onDisk, // what's on disk now
                'update_available'  => $hasUpdate,
            ]);

            if ($hasUpdate) {
                $this->line("  [{$theme->slug}] Update available: {$inDb} → {$onDisk}");
                $updatesFound++;
                Cache::forget('theme_meta_' . $theme->slug);
            } else {
                $this->line("  [{$theme->slug}] Up to date ({$onDisk})");
            }
        }

        $this->info($updatesFound > 0
            ? "{$updatesFound} theme(s) have updates available."
            : 'All themes are up to date.'
        );

        return self::SUCCESS;
    }
}
