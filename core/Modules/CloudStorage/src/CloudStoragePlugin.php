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

        $this->add_menu([
            'id'         => 'cloud-storage-menu',
            'label'      => __('Cloud Storage'),
            'icon'       => 'mdi-cloud-cog-outline',
            'route'      => 'landlord.admin.cloud.storage.settings',
            'parent'     => 'general-settings-menu-items',
            'permission' => 'cloud-storage',
            'context'    => 'landlord',
        ]);

        $this->add_filter('nazmart:price_plan_features', function (array $features): array {
            $features['cloudstorage'] = __('CloudStorage');
            return $features;
        });
    }
}
