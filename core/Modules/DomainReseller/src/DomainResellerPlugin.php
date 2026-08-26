<?php

namespace Modules\DomainReseller\Src;

use App\PluginSystem\PluginBase;

class DomainResellerPlugin extends PluginBase
{
    public function id(): string
    {
        return 'domain-reseller';
    }

    public function boot(): void
    {
        // Module routes, views, and events are registered by DomainResellerServiceProvider.

        $this->add_menu([
            'id'         => 'domain-reseller-menu',
            'label'      => __('Domain Reseller'),
            'icon'       => 'mdi-web',
            'route'      => '#',
            'order'      => 60,
            'context'    => 'both',
            'permission' => 'domain-reseller',
        ]);

        $this->add_submenu('domain-reseller-menu', [
            'id'         => 'domain-reseller-dashboard',
            'label'      => __('Domain Dashboard'),
            'route'      => 'landlord.admin.domain-reseller.index',
            'permission' => 'domain-reseller',
        ]);

        $this->add_submenu('domain-reseller-menu', [
            'id'         => 'domain-reseller-settings',
            'label'      => __('Domain Settings'),
            'route'      => 'landlord.admin.domain-reseller.settings',
            'permission' => 'domain-reseller',
        ]);

        $this->add_filter('nazmart:price_plan_features', function (array $features): array {
            $features['domainreseller'] = __('DomainReseller');
            return $features;
        });
    }
}
