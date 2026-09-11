<?php

namespace Modules\SiteWayPaymentGateway\Src;

use App\PluginSystem\PluginBase;

class SiteWayPaymentGatewayPlugin extends PluginBase
{
    public function id(): string
    {
        return 'siteway-payment-gateway';
    }

    public function boot(): void
    {
        // Module routes, views, and events are registered by SiteWayPaymentGatewayServiceProvider.
        // Register plugin-system extras here (menus, assets, settings).
    }
}
