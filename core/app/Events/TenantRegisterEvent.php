<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Session;

class TenantRegisterEvent
{
    use Dispatchable,Queueable;
    public $user_info;
    public $subdomain;
    public $theme;
    public ?string $onboarding_request_id;
    public function __construct(User $user, $subdomain, $theme = 'hexfashion', ?string $onboardingRequestId = null)
    {
        $this->user_info = $user;
        $this->subdomain = $subdomain;
        $this->theme = $theme;
        $this->onboarding_request_id = $onboardingRequestId;

        Session::put('theme', $theme);
    }
}
