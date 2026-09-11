<?php

namespace Themes\Hexfashion;

use App\Contracts\ThemeDemoSeederContract;

class DemoSeeder implements ThemeDemoSeederContract
{
    public function run(): void
    {
        // All demo data is declared in demo/data.json and imported
        // by ThemeDemoImporter — nothing extra is needed here.
    }
}
