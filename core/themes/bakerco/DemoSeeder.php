<?php

namespace Themes\Bakerco;

use App\Contracts\ThemeDemoSeederContract;

class DemoSeeder implements ThemeDemoSeederContract
{
    public function run(): void
    {
        // All demo data (media, settings, categories, products, menus, and the
        // home page-builder layout) is declared in demo/data.json and imported
        // by ThemeDemoImporter — nothing extra is needed here.
    }
}
