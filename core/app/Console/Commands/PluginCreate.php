<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PluginCreate extends Command
{
    protected $signature   = 'plugin:create {name : Vendor-PluginName in kebab-case (e.g. acme-seo-tool)}';
    protected $description = 'Scaffold a new plugin skeleton inside Modules/';

    public function handle(): int
    {
        $raw    = $this->argument('name');
        $kebab  = strtolower(preg_replace('/[^a-zA-Z0-9-]/', '-', $raw));
        $kebab  = trim(preg_replace('/-+/', '-', $kebab), '-');
        $pascal = str_replace(' ', '', ucwords(str_replace('-', ' ', $kebab)));

        $modDir = base_path("Modules/{$pascal}");

        if (is_dir($modDir)) {
            $this->error("Directory already exists: Modules/{$pascal}");
            return self::FAILURE;
        }

        $author = $this->ask('Author name', 'Your Name');
        $type   = $this->choice('Plugin type', ['both', 'tenant', 'landlord'], 0);
        $pricing = $this->choice('Pricing', ['free', 'paid'], 0);

        // Collect info
        $data = [
            'id'      => $kebab,
            'pascal'  => $pascal,
            'author'  => $author,
            'type'    => $type,
            'pricing' => $pricing,
        ];

        // Create directories
        $dirs = [
            $modDir,
            "{$modDir}/src",
            "{$modDir}/database/migrations",
            "{$modDir}/resources/views",
            "{$modDir}/resources/css",
            "{$modDir}/resources/js",
            "{$modDir}/routes",
        ];

        foreach ($dirs as $dir) {
            mkdir($dir, 0755, true);
        }

        // Write files
        $this->writePluginJson($modDir, $data);
        $this->writeMainClass($modDir, $data);
        $this->writeRoutes($modDir, $data);
        $this->writeGitkeeps($modDir);

        $this->info("Plugin scaffold created at Modules/{$pascal}/");
        $this->line("  <fg=green>plugin.json</>          — manifest");
        $this->line("  <fg=green>src/{$pascal}Plugin.php</> — main class (extends PluginBase)");
        $this->line("  <fg=green>routes/web.php</>        — optional web routes");
        $this->newLine();
        $this->comment("Next: add plugin.json to modules_statuses.json and activate it.");

        return self::SUCCESS;
    }

    private function writePluginJson(string $dir, array $d): void
    {
        $json = json_encode([
            'id'                   => $d['id'],
            'name'                 => str_replace('-', ' ', ucwords($d['id'], '-')),
            'version'              => '1.0.0',
            'description'         => '',
            'author'              => $d['author'],
            'author_url'          => '',
            'type'                => $d['type'],
            'pricing'             => $d['pricing'],
            'min_platform_version' => '1.0.0',
            'main'                => "src/{$d['pascal']}Plugin.php",
            'requires'            => [],
            'update_server'       => null,
            'purchase_url'        => null,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        file_put_contents("{$dir}/plugin.json", $json . PHP_EOL);
    }

    private function writeMainClass(string $dir, array $d): void
    {
        $pascal = $d['pascal'];
        $id     = $d['id'];

        $content = <<<PHP
        <?php

        namespace Modules\\{$pascal};

        use App\\PluginSystem\\PluginBase;

        class {$pascal}Plugin extends PluginBase
        {
            public function id(): string
            {
                return '{$id}';
            }

            public function boot(): void
            {
                // Register menu items, hooks, assets, settings here.
                // Example:
                // \$this->add_menu([
                //     'id'      => '{$id}-menu',
                //     'label'   => '{$pascal}',
                //     'icon'    => 'mdi-puzzle-outline',
                //     'route'   => 'tenant.admin.{$id}.index',
                //     'order'   => 60,
                //     'context' => '{$d['type']}',
                // ]);
                //
                // \$this->register_settings([
                //     ['key' => 'api_key', 'label' => 'API Key', 'type' => 'text', 'required' => true],
                // ]);
            }

            public function on_activate(): void
            {
                \$this->run_migrations();
            }

            public function on_deactivate(): void
            {
                // Clean up if needed
            }
        }
        PHP;

        // Normalize indentation
        $content = preg_replace('/^        /m', '', $content);
        file_put_contents("{$dir}/src/{$pascal}Plugin.php", $content);
    }

    private function writeRoutes(string $dir, array $d): void
    {
        $content = <<<PHP
        <?php

        // Plugin: {$d['id']}
        // Routes registered here are loaded automatically by PluginBase::register_api_routes().
        // Or use standard Laravel module routing if preferred.

        use Illuminate\\Support\\Facades\\Route;
        PHP;

        $content = preg_replace('/^        /m', '', $content);
        file_put_contents("{$dir}/routes/web.php", $content . PHP_EOL);
    }

    private function writeGitkeeps(string $dir): void
    {
        $keepDirs = [
            "{$dir}/database/migrations",
            "{$dir}/resources/views",
            "{$dir}/resources/css",
            "{$dir}/resources/js",
        ];

        foreach ($keepDirs as $d) {
            if (is_dir($d)) {
                touch("{$d}/.gitkeep");
            }
        }
    }
}
