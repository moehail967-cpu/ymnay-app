<?php

namespace Plugins\PageBuilder\Src;

use App\PluginSystem\PluginBase;

class PageBuilderPlugin extends PluginBase
{
    public function id(): string
    {
        return 'page-builder';
    }

    public function boot(): void
    {
        // Views and setup are handled by PageBuilderServiceProvider (loaded via config/app.php).
        // This class provides plugin system integration only.

        $this->enqueue_admin_style('pagebuilder', asset('backend/css/xgpagebuilder.min.css'), ['tailwind']);
        $this->enqueue_admin_script('pagebuilder', asset('backend/js/xgpagebuilder.min.js'), ['jquery'], true);
    }
}
