<?php

namespace Plugins\MenuBuilder\Src;

use App\PluginSystem\PluginBase;

class MenuBuilderPlugin extends PluginBase
{
    public function id(): string
    {
        return 'menu-builder';
    }

    public function boot(): void
    {
        // Views and setup handled by MenuBuilderServiceProvider.
    }
}
