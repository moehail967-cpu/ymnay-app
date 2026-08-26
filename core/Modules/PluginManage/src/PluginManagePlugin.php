<?php

namespace Modules\PluginManage\Src;

use App\PluginSystem\PluginBase;

class PluginManagePlugin extends PluginBase
{
    public function id(): string
    {
        return 'plugin-manage';
    }

    public function boot(): void
    {
        // Module routes, views, and events are registered by PluginManageServiceProvider.

        $this->add_menu([
            'id'         => 'plugin-manage-settings-menu',
            'label'      => __('Plugins Manage'),
            'icon'       => 'mdi-puzzle-outline',
            'route'      => '#',
            'order'      => 58,
            'context'    => 'landlord',
            'permission' => 'plugin-manage',
        ]);

        $this->add_submenu('plugin-manage-settings-menu', [
            'id'         => 'plugin-manage-settings-all-menu',
            'label'      => __('All Plugins'),
            'route'      => 'landlord.plugin.manage.all',
            'permission' => null,
        ]);

        $this->add_submenu('plugin-manage-settings-menu', [
            'id'         => 'plugin-manage-settings-add-new-menu',
            'label'      => __('Add New Plugin'),
            'route'      => 'landlord.plugin.manage.new',
            'permission' => null,
        ]);

        $this->add_submenu('plugin-manage-settings-menu', [
            'id'         => 'plugin-manage-tenant-plugins-menu',
            'label'      => __('Tenant Plugins'),
            'route'      => 'landlord.admin.plugins.tenantPlugins',
            'permission' => null,
        ]);

        $this->add_submenu('plugin-manage-settings-menu', [
            'id'         => 'plugin-manage-core-features-menu',
            'label'      => __('Core Features'),
            'route'      => 'landlord.admin.plugins.coreFeatures',
            'permission' => null,
        ]);
    }
}
