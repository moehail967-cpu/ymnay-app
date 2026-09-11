<?php

namespace Modules\CommissionManage\Src;

use App\PluginSystem\PluginBase;

class CommissionManagePlugin extends PluginBase
{
    public function id(): string
    {
        return 'commission-manage';
    }

    public function boot(): void
    {
        // Module routes, views, and events are registered by CommissionManageServiceProvider.

        $this->add_menu([
            'id'         => 'commission-manage-settings-menu-items',
            'label'      => __('Commission Manage'),
            'icon'       => 'mdi-percent',
            'route'      => '#',
            'order'      => 55,
            'context'    => 'landlord',
            'permission' => null,
        ]);

        $this->add_submenu('commission-manage-settings-menu-items', [
            'id'         => 'commission-manage-settings-page',
            'label'      => __('Commission Settings'),
            'route'      => 'landlord.admin.commission.settings',
            'permission' => null,
        ]);

        $this->add_filter('nazmart:price_plan_features', function (array $features): array {
            $features['commissionmanage'] = __('CommissionManage');
            return $features;
        });
    }
}
