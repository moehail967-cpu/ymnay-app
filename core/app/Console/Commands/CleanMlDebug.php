<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Remove temporary console.log debug lines from lang-tabs.js.
 * Run: php artisan ml:clean-debug
 * Delete this file after running.
 */
class CleanMlDebug extends Command
{
    protected $signature   = 'ml:clean-debug';
    protected $description = 'Remove [ML] debug console.log lines from lang-tabs.js';

    public function handle(): int
    {
        $file = base_path('Modules/MultiLingual/Resources/js/lang-tabs.js');

        if (!file_exists($file)) {
            $this->error("File not found: $file");
            return 1;
        }

        $original = file_get_contents($file);

        // Lines to strip
        $patterns = [
            "/^\s*\/\/ DEBUG[^\n]*\n/m",
            "/^\s*console\.log\('\[ML\][^\n]*\n/m",
            "/^\s*console\.warn\('\[ML\][^\n]*\n/m",
        ];

        // Also strip the block: "if (LOCALES.length < 2) {" ... "}" back to single-line form
        $clean = preg_replace($patterns, '', $original);

        // Restore clean early-exit (single line, no warn)
        $clean = preg_replace(
            '/if \(LOCALES\.length < 2\) \{[^}]+\}/s',
            'if (LOCALES.length < 2) return;',
            $clean
        );

        // Remove consecutive blank lines
        $clean = preg_replace("/\n{3,}/", "\n\n", $clean);

        file_put_contents($file, $clean);
        $this->info('Debug lines removed from lang-tabs.js');
        return 0;
    }
}
