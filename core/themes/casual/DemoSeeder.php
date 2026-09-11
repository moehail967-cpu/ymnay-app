<?php

namespace Themes\Casual;

use App\Contracts\ThemeDemoSeederContract;
use App\Services\ThemeDemoImporter;

class DemoSeeder implements ThemeDemoSeederContract
{
    public function run(): void
    {
        (new ThemeDemoImporter('casual'))->import();
    }
}
