<?php

namespace App\Listeners;

use App\Events\TenantRegisterEvent;
use App\Helpers\GenerateTenantToken;
use App\Models\Tenant;
use App\Models\TenantUniqueKey;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class TenantDomainCreate
{

    public function __construct()
    {

    }

    public function handle(TenantRegisterEvent $event)
    {
        if ($event->onboarding_request_id !== null) {
            $tenant = new Tenant([
                'id' => $event->subdomain,
                'user_id' => $event->user_info->id,
                'theme_slug' => $event->theme,
            ]);
            // Save the origin with the tenant row BEFORE the synchronous created-event pipeline.
            $tenant->setInternal('onboarding_request_id', $event->onboarding_request_id);
            if (!$tenant->save()) throw new \RuntimeException('Tenant could not be saved.');
            return; // The checkpointed pipeline owns the login key for this flow.
        }

        $tenant = Tenant::create([
            'id' => $event->subdomain, 
            'user_id' => $event->user_info->id,
            'theme_slug' => $event->theme,
        ]);
        $hash_key = GenerateTenantToken::token();

        DB::table('tenants')->where('id', $tenant->id)->update([
            'user_id' => $event->user_info->id,
            'theme_slug' => $event->theme,
            'unique_key' => $hash_key
        ]);

        TenantUniqueKey::create([
            'tenant_id' =>  $tenant->id,
            'unique_key' => $hash_key
        ]);

        // Domain creation is handled by TenantDomainCreateJob in the TenantCreated pipeline (TenancyServiceProvider).
    }
}