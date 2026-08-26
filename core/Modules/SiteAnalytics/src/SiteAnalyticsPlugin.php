<?php

namespace Modules\SiteAnalytics\Src;

use App\PluginSystem\PluginBase;

class SiteAnalyticsPlugin extends PluginBase
{
    public function id(): string
    {
        return 'site-analytics';
    }

    public function boot(): void
    {
        // Module routes, views, and events are registered by SiteAnalyticsServiceProvider.

        $this->add_menu([
            'id'         => 'site-analytics-menu',
            'label'      => __('Site Analytics'),
            'icon'       => 'mdi-chart-line',
            'route'      => '#',
            'order'      => 60,
            'context'    => 'both',
            'permission' => 'site-analytics',
        ]);

        $this->add_submenu('site-analytics-menu', [
            'id'         => 'site-analytics-index',
            'label'      => __('SA Dashboard'),
            'route'      => 'landlord.admin.dashboard.analytics',
            'permission' => null,
        ]);

        $this->add_submenu('site-analytics-menu', [
            'id'         => 'site-analytics-packages',
            'label'      => __('SA Analytics'),
            'route'      => 'landlord.admin.analytics',
            'permission' => null,
        ]);

        $this->add_submenu('site-analytics-menu', [
            'id'         => 'site-analytics-settings',
            'label'      => __('SA Settings'),
            'route'      => 'landlord.admin.analytics.settings',
            'permission' => null,
        ]);

        $this->add_filter('nazmart:price_plan_features', function (array $features): array {
            $features['siteanalytics'] = __('SiteAnalytics');
            return $features;
        });
    }
}
