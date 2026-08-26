<?php

namespace App\Console\Commands\Theme;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ThemeCreateCommand extends Command
{
    protected $signature   = 'theme:create {name : Theme display name} {--niche= : Niche category (fashion, electronics, books, etc.)}';
    protected $description = 'Scaffold a new theme in themes/{slug}/';

    public function handle(): int
    {
        $name  = $this->argument('name');
        $slug  = Str::slug($name);
        $niche = $this->option('niche') ?? '';

        $dest = config('theme.base_path') . '/' . $slug;

        if (is_dir($dest)) {
            $this->error("Theme [{$slug}] already exists at themes/{$slug}/.");
            return self::FAILURE;
        }

        $this->info("Creating theme [{$name}] at themes/{$slug}/...");

        // Create directory structure
        $directories = [
            'views/frontend/shop',
            'views/frontend/pages',
            'views/frontend/partials',
            'views/frontend/payment',
            'views/frontend/invoice',
            'views/frontend/digital-shop',
            'views/header',
            'views/footer',
            'assets/css',
            'assets/js',
            'assets/img',
            'screenshot',
            'demo/media',
            'demo/layouts',
        ];

        foreach ($directories as $dir) {
            mkdir("{$dest}/{$dir}", 0755, true);
        }

        // Copy stubs if they exist, otherwise write inline defaults
        $stubPath = config('theme.stub_path');

        $this->writeThemeJson($dest, $slug, $name, $niche);
        $this->writeFromStub($stubPath, $dest, 'views/header/navbar.blade.php',     $this->defaultNavbarStub($name));
        $this->writeFromStub($stubPath, $dest, 'views/footer/widget-area.blade.php', $this->defaultFooterStub($name));
        $this->writeFromStub($stubPath, $dest, 'demo/data.json',                     $this->defaultDemoData($slug, $name));

        // Add .gitkeep to empty dirs
        foreach (['assets/css', 'assets/js', 'assets/img', 'screenshot'] as $emptyDir) {
            $keepFile = "{$dest}/{$emptyDir}/.gitkeep";
            if (!glob("{$dest}/{$emptyDir}/*")) {
                file_put_contents($keepFile, '');
            }
        }

        $this->info('');
        $this->info('<fg=green>Theme scaffolded successfully!</>');
        $this->table(['Step', 'Command'], [
            ['Add your CSS/JS',   "themes/{$slug}/assets/css/style.css"],
            ['Publish assets',    "php artisan theme:publish {$slug}"],
            ['Import demo data',  "php artisan theme:demo-import {$slug}"],
            ['Sync to DB',        "php artisan theme:sync-db"],
        ]);

        return self::SUCCESS;
    }

    protected function writeThemeJson(string $dest, string $slug, string $name, string $niche): void
    {
        $meta = [
            'name'        => $name,
            'slug'        => $slug,
            'description' => "{$name} eCommerce theme",
            'author'      => 'Nazmart Team',
            'version'     => '1.0.0',
            'niche'       => $niche,
            'status'      => true,
            'price'       => 0,
            'license'     => 'free',
            'screenshot'  => ['primary' => 'primary.jpg', 'previews' => []],
            'assets'      => [
                'header' => ['styles' => ['style'], 'rtl_styles' => [], 'scripts' => [], 'core' => ['exclude' => []]],
                'footer' => ['styles' => [], 'scripts' => ['main'], 'core' => ['exclude' => []]],
            ],
            'views'       => [
                'navbar'        => 'header/navbar',
                'breadcrumb'    => 'header/breadcrumb',
                'footer_widget' => 'footer/widget-area',
                'header_hook'   => 'header/hook',
                'footer_hook'   => 'footer/hook',
            ],
            // Legacy format kept so existing ThemeManager methods work immediately
            'headerHook'  => [[
                'loadCoreStyle' => (object)[],
                'style'         => ['style'],
                'rtl_style'     => [],
                'script'        => [],
                'blade'         => [],
                'navbarArea'    => 'navbar',
                'breadcrumbArea'=> 'breadcrumb',
            ]],
            'footerHook'  => [[
                'loadCoreScript' => (object)[],
                'style'          => [],
                'script'         => ['main'],
                'blade'          => [],
                'widgetArea'     => 'footer-widget',
            ]],
            'demo'        => ['data' => 'demo/data.json', 'preview_url' => ''],
            'changelog'   => [['version' => '1.0.0', 'date' => now()->toDateString(), 'notes' => 'Initial release']],
        ];

        file_put_contents("{$dest}/theme.json", json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->line("  Created theme.json");
    }

    protected function writeFromStub(string $stubPath, string $dest, string $relativePath, string $fallback): void
    {
        $stubFile = "{$stubPath}/{$relativePath}";
        $destFile = "{$dest}/{$relativePath}";
        $content  = file_exists($stubFile) ? file_get_contents($stubFile) : $fallback;
        file_put_contents($destFile, $content);
        $this->line("  Created {$relativePath}");
    }

    protected function defaultNavbarStub(string $name): string
    {
        return <<<BLADE
{{-- {$name} theme navbar --}}
@include('tenant.frontend.partials.navbar')
BLADE;
    }

    protected function defaultFooterStub(string $name): string
    {
        return <<<BLADE
{{-- {$name} theme footer widget area --}}
@include('tenant.frontend.partials.footer')
BLADE;
    }

    protected function defaultDemoData(string $slug, string $name): string
    {
        return json_encode([
            'version' => '1.0',
            'theme'   => $slug,
            'sections' => [
                'settings'   => [['key' => 'site_title', 'value' => $name . ' Store']],
                'media'      => [],
                'categories' => [],
                'products'   => [],
                'menus'      => [],
                'pages'      => [],
            ],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
