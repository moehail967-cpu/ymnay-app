<?php

namespace Modules\SocialAuth\Src;

use App\PluginSystem\PluginBase;

class SocialAuthPlugin extends PluginBase
{
    public function id(): string
    {
        return 'social-auth';
    }

    public function boot(): void
    {
        $this->add_menu([
            'id'      => 'social-auth-settings',
            'label'   => __('Social Auth'),
            'icon'    => 'mdi-google',
            'route'   => route_prefix() . 'admin.social.auth.settings',
            'order'   => 65,
            'context' => 'both',
        ]);
    }
}
