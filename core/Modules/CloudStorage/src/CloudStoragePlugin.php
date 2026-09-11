<?php

namespace Modules\CloudStorage\Src;

use App\PluginSystem\PluginBase;

class CloudStoragePlugin extends PluginBase
{
    public function id(): string
    {
        return 'cloud-storage';
    }

    public function boot(): void
    {
        // Module routes, views, and events are registered by CloudStorageServiceProvider.

        $this->add_submenu('general-settings-menu-items', [
            'id'         => 'cloud-storage-menu',
            'label'      => __('Cloud Storage'),
            'route'      => 'landlord.admin.cloud.storage.settings',
            'permission' => 'cloud-storage',
        ]);

        $this->add_filter('nazmart:price_plan_features', function (array $features): array {
            $features['cloudstorage'] = __('CloudStorage');
            return $features;
        });
    }
}
