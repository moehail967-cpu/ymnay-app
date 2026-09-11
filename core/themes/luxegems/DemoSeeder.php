<?php

namespace Themes\Luxegems;

use App\Contracts\ThemeDemoSeederContract;

class DemoSeeder implements ThemeDemoSeederContract
{
    public function run(): void
    {
        // ThemeDemoImporter (called by the controller before this) handles
        // all demo data: media, settings, categories, products, menus, and
        // the home page-builder layout via demo/data.json + demo/home-layout.json.
    }
}
