<?php

namespace Plugins\WidgetBuilder\Src;

use App\PluginSystem\PluginBase;

class WidgetBuilderPlugin extends PluginBase
{
    public function id(): string
    {
        return 'widget-builder';
    }

    public function boot(): void
    {
        // Views and setup handled by WidgetBuilderServiceProvider.
    }
}
