<?php

namespace Modules\SmsGateway\Src;

use App\PluginSystem\PluginBase;

class SmsGatewayPlugin extends PluginBase
{
    public function id(): string
    {
        return 'sms-gateway';
    }

    public function boot(): void
    {
        // Module routes, views, and events are registered by SmsGatewayServiceProvider.

        $this->add_menu([
            'id'         => 'sms-manage-index',
            'label'      => __('SMS Settings'),
            'icon'       => 'mdi-message-text',
            'route'      => route_prefix().'admin.sms.settings',
            'order'      => 60,
            'context'    => 'both',
            'permission' => 'sms-gateway',
        ]);

        $this->add_filter('nazmart:price_plan_features', function (array $features): array {
            $features['smsgateway'] = __('SmsGateway');
            return $features;
        });
    }
}
