<?php

namespace Modules\SocialAuth\Providers;

use Illuminate\Support\ServiceProvider;

class SocialAuthServiceProvider extends ServiceProvider
{
    protected $moduleName = 'SocialAuth';
    protected $moduleNameLower = 'socialauth';

    public function boot()
    {
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));

        // Dynamically configure the Google Socialite driver from DB settings
        // so the admin can update credentials without touching .env
        $this->app->booted(function () {
            $clientId     = get_static_option('google_client_id');
            $clientSecret = get_static_option('google_client_secret');

            if ($clientId && $clientSecret) {
                config([
                    'services.google.client_id'     => $clientId,
                    'services.google.client_secret' => $clientSecret,
                    'services.google.redirect'      => url('/auth/google/callback'),
                ]);
            }
        });
    }

    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    public function registerViews()
    {
        $sourcePath = module_path($this->moduleName, 'resources/views');
        $this->loadViewsFrom([$sourcePath], $this->moduleNameLower);
    }

    public function provides()
    {
        return [];
    }
}
