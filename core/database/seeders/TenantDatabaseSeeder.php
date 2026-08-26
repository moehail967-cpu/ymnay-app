<?php

namespace Database\Seeders;

use Database\Seeders\Tenant\AdminSeed;
use Database\Seeders\Tenant\BlogSeed;
use Database\Seeders\Tenant\BrandSeed;
use Database\Seeders\Tenant\DefaultDataSeeder;
use Database\Seeders\Tenant\DigitalProductSeed;
use Database\Seeders\Tenant\Footer\WidgetSeed;
use Database\Seeders\Tenant\FormBuilderSeed;
use Database\Seeders\Tenant\GeneralData;
use Database\Seeders\Tenant\LanguageSeed;
use Database\Seeders\Tenant\MediaSeed;
use Database\Seeders\Tenant\MenuSeed;
use Database\Seeders\Tenant\PageSeed;
use Database\Seeders\Tenant\PaymentGatewayFieldsSeed;
use Database\Seeders\Tenant\PermissionSyncSeeder;
use Database\Seeders\Tenant\RolePermissionSeed;
use Database\Seeders\Tenant\StatusSeed;
use Database\Seeders\Tenant\TestimonialSeed;
use Database\Seeders\Tenant\ProductSeed;
use App\Contracts\ThemeDemoSeederContract;
use App\Services\ThemeDemoImporter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TenantDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeed::class,
            AdminSeed::class,
            LanguageSeed::class,
            MenuSeed::class,
            GeneralData::class,
            PageSeed::class,
            MediaSeed::class,
            FormBuilderSeed::class,
            PaymentGatewayFieldsSeed::class,
            StatusSeed::class,
            WidgetSeed::class,
            BlogSeed::class,
            TestimonialSeed::class,
            BrandSeed::class,
            ProductSeed::class,
            DigitalProductSeed::class,
            DefaultDataSeeder::class,
            PermissionSyncSeeder::class,
        ]);

        $this->callThemeDemoSeeder();

        session()->forget('theme');
    }

    private function callThemeDemoSeeder(): void
    {
        $themeSlug = tenant()->theme_slug ?? null;
        if (! $themeSlug) {
            return;
        }

        // Step 1 — generic importer: media, settings, categories, products, menus, pages, widgets
        try {
            (new ThemeDemoImporter($themeSlug))->import();
        } catch (\Throwable $e) {
            \Log::error("ThemeDemoImporter failed during tenant seed [{$themeSlug}]: " . $e->getMessage());
        }

        // Step 2 — theme-specific extras (DemoSeeder should be empty or add only
        // data that ThemeDemoImporter does not cover; it must NOT call import() again)
        $class = 'Themes\\' . Str::studly($themeSlug) . '\\DemoSeeder';
        if (class_exists($class) && is_a($class, ThemeDemoSeederContract::class, true)) {
            try {
                (new $class())->run();
            } catch (\Throwable $e) {
                \Log::error("ThemeDemoSeeder failed during tenant seed [{$themeSlug}]: " . $e->getMessage());
            }
        }
    }
}

