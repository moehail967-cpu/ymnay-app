<?php

namespace Modules\ShippingPlugin\Src;

use App\PluginSystem\PluginBase;

class ShippingPluginPlugin extends PluginBase
{
    public function id(): string
    {
        return 'shipping-plugin';
    }

    public function boot(): void
    {
        $this->add_menu([
            'id'         => 'shipping-index-menu',
            'label'      => __('Shipping Dashboard'),
            'icon'       => 'mdi-truck-outline',
            'route'      => 'tenant.admin.shipping.plugin.index',
            'order'      => 55,
            'context'    => 'tenant',
            'permission' => 'shipping-plugin',
        ]);

        $this->add_submenu('shipping-index-menu', [
            'id'         => 'shipping-settings-menu',
            'label'      => __('Shipping Settings'),
            'route'      => 'tenant.admin.shipping.plugin.settings',
            'permission' => 'shipping-plugin',
        ]);

        $this->add_filter('nazmart:price_plan_features', function (array $features): array {
            $features['shippingplugin'] = __('ShippingPlugin');
            return $features;
        });

        $this->add_filter('nazmart:pagebuilder_tenant_addons', function (array $addons): array {
            $class = 'Modules\ShippingPlugin\Http\PageBuilder\Addons\ShippingTracker';
            if (class_exists($class)) {
                $addons[] = $class;
            }
            return $addons;
        });
    }
}
