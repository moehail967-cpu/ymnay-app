<?php

namespace Modules\CommissionManage\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\CommissionManage\Events\OrderCompleted;
use Modules\CommissionManage\Listeners\CreateCommissionRecord;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */

    protected $listen = [
        OrderCompleted::class => [
            CreateCommissionRecord::class,
        ],

    ];
    public function register()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

}
