<?php

namespace Modules\Pos\Src;

use App\PluginSystem\PluginBase;

class PosPlugin extends PluginBase
{
    public function id(): string
    {
        return 'pos';
    }

    public function boot(): void
    {
        // Module routes, views, and events are registered by PosServiceProvider.

        $this->add_menu([
            'id'         => 'pos-manage',
            'label'      => __('POS'),
            'icon'       => 'mdi-point-of-sale',
            'route'      => '#',
            'order'      => 65,
            'context'    => 'tenant',
            'permission' => 'pos',
        ]);

        $this->add_submenu('pos-manage', [
            'id'         => 'pos-dashboard',
            'label'      => __('POS Manage'),
            'route'      => 'tenant.admin.pos.dashboard',
            'permission' => 'pos-dashboard',
        ]);

        $this->add_submenu('pos-manage', [
            'id'         => 'pos-settings',
            'label'      => __('POS Settings'),
            'route'      => 'tenant.admin.pos.settings',
            'permission' => 'pos-settings',
        ]);

        $this->add_filter('nazmart:price_plan_features', function (array $features): array {
            $features['pos'] = __('Pos');
            return $features;
        });
    }
}
