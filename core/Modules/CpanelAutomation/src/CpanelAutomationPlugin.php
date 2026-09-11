<?php

namespace Modules\CpanelAutomation\Src;

use App\PluginSystem\PluginBase;

class CpanelAutomationPlugin extends PluginBase
{
    public function id(): string
    {
        return 'cpanel-automation';
    }

    public function boot(): void
    {
        // Module routes, views, and events are registered by CpanelAutomationServiceProvider.

        $this->add_menu([
            'id'         => 'cpanel-automation-menu',
            'label'      => __('cPanel Automation'),
            'icon'       => 'mdi-server',
            'route'      => 'landlord.admin.cpanel.automation.settings',
            'order'      => 60,
            'context'    => 'landlord',
            'permission' => 'sms-gateway',
        ]);

        $this->add_filter('nazmart:price_plan_features', function (array $features): array {
            $features['cpanelautomation'] = __('CpanelAutomation');
            return $features;
        });
    }
}
