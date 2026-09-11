<?php

namespace Modules\WebHook\Src;

use App\PluginSystem\PluginBase;

class WebHookPlugin extends PluginBase
{
    public function id(): string
    {
        return 'web-hook';
    }

    public function boot(): void
    {
        // Module routes, views, and events are registered by WebHookServiceProvider.

        $this->add_menu([
            'id'         => 'webhook-manage-settings-menu',
            'label'      => __('Webhook Manage'),
            'icon'       => 'mdi-webhook',
            'route'      => '#',
            'order'      => 65,
            'context'    => 'landlord',
            'permission' => 'webhook',
        ]);

        $this->add_submenu('webhook-manage-settings-menu', [
            'id'         => 'webhook-manage-settings-all-menu',
            'label'      => __('All WebHooks'),
            'route'      => 'webhook.index',
            'permission' => null,
        ]);
    }
}
