<?php

namespace App\Listeners;

use App\Events\TenantRegisterEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Helpers\Payment\DatabaseUpdateAndMailSend\LandlordPricePlanAndTenantCreate;

class TenantDataSeedListener
{
    public function __construct()
    {
        //
    }

    public function handle(TenantRegisterEvent $event)
    {
        //database migrate
        Log::driver('seed-log')->info('tenants migrate started');
        $command = 'tenants:migrate --force --tenants='.$event->subdomain;
        Artisan::call($command);
        Log::driver('seed-log')->info('tenants migrate ended');

        Log::driver('seed-log')->info('tenants seed started');
        $command = 'tenants:seed --force --tenants='.$event->subdomain;
        Artisan::call($command);
        Log::driver('seed-log')->info('tenants seed ended');
    }
}
