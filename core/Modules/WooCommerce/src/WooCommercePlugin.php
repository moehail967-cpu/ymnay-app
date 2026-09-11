<?php

namespace Modules\WooCommerce\Src;

use App\PluginSystem\PluginBase;

class WooCommercePlugin extends PluginBase
{
    public function id(): string
    {
        return 'woo-commerce';
    }

    public function boot(): void
    {
        // Module routes, views, and events are registered by WooCommerceServiceProvider.

        $this->add_menu([
            'id'         => 'woocommerce-manage-menu',
            'label'      => __('WooCommerce'),
            'icon'       => 'mdi-wordpress',
            'route'      => 'tenant.admin.woocommerce',
            'order'      => 70,
            'context'    => 'tenant',
            'permission' => 'woocommerce',
        ]);

        $this->add_submenu('woocommerce-manage-menu', [
            'id'         => 'woocommerce-manage-index',
            'label'      => __('WC Product List'),
            'route'      => 'tenant.admin.woocommerce',
            'permission' => 'woocommerce',
        ]);

        $this->add_submenu('woocommerce-manage-menu', [
            'id'         => 'woocommerce-manage-settings-import',
            'label'      => __('WC Import Settings'),
            'route'      => 'tenant.admin.woocommerce.settings.import',
            'permission' => 'woocommerce',
        ]);

        $this->add_submenu('woocommerce-manage-menu', [
            'id'         => 'woocommerce-manage-settings',
            'label'      => __('WC Settings'),
            'route'      => 'tenant.admin.woocommerce.settings',
            'permission' => 'woocommerce',
        ]);

        $this->add_filter('nazmart:price_plan_features', function (array $features): array {
            $features['woocommerce'] = __('WooCommerce');
            return $features;
        });
    }
}
