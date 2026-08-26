<?php

namespace Modules\SocialAuth\Http\Controllers\Frontend;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Modules\SocialAuth\Services\GoogleAuthService;

class SocialAuthController extends Controller
{
    /**
     * Landlord frontend: redirect to Google.
     * Accepts optional ?from_tenant=shop.example.com to handle tenant logins.
     */
    public function redirectToGoogle(Request $request)
    {
        if (!$this->isEnabled()) {
            return redirect()->route('landlord.user.login')
                ->with(['msg' => __('Google login is not enabled.'), 'type' => 'danger']);
        }

        if ($request->filled('from_tenant')) {
            session(['google_from_tenant' => $request->from_tenant]);
        }

        $callbackUrl = request()->getSchemeAndHttpHost() . '/auth/google/callback';

        return Socialite::driver('google')->redirectUrl($callbackUrl)->redirect();
    }

    /**
     * Google OAuth callback — always lands on the central/landlord domain.
     */
    public function handleGoogleCallback(Request $request)
    {
        if (!$this->isEnabled()) {
            return redirect()->route('landlord.user.login')
                ->with(['msg' => __('Google login is not enabled.'), 'type' => 'danger']);
        }

        try {
            $callbackUrl = request()->getSchemeAndHttpHost() . '/auth/google/callback';
            $googleUser  = Socialite::driver('google')->redirectUrl($callbackUrl)->user();
        } catch (\Throwable $e) {
            return redirect()->route('landlord.user.login')
                ->with(['msg' => __('Google authentication failed. Please try again.'), 'type' => 'danger']);
        }

        $user       = GoogleAuthService::findOrCreate($googleUser);
        $fromTenant = session()->pull('google_from_tenant');

        // Tenant user: issue a signed short-lived token and redirect to the tenant subdomain
        if ($fromTenant) {
            $token       = GoogleAuthService::makeTenantToken($user->id);
            $scheme      = request()->isSecure() ? 'https' : 'http';
            $completeUrl = "{$scheme}://{$fromTenant}/auth/google/complete?token=" . urlencode($token);
            return redirect($completeUrl);
        }

        // Landlord user: log in directly
        Auth::guard('web')->login($user, true);

        return redirect()->intended(route('landlord.user.home'));
    }

    /**
     * Landlord frontend: handles the /auth/google/complete route on central domain.
     * Redirects to user dashboard after successful callback login.
     */
    public function completeLandlordLogin(Request $request)
    {
        return redirect()->route('landlord.user.home');
    }

    /**
     * Tenant frontend: validate the signed token issued by the central-domain callback and log in.
     */
    public function completeTenantLogin(Request $request)
    {
        $token  = $request->query('token');
        $userId = $token ? GoogleAuthService::validateTenantToken($token) : null;

        if (!$userId) {
            return redirect()->route('tenant.user.login')
                ->with(['msg' => __('Google login failed or session expired. Please try again.'), 'type' => 'danger']);
        }

        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('tenant.user.login')
                ->with(['msg' => __('User not found.'), 'type' => 'danger']);
        }

        Auth::guard('web')->login($user, true);

        return redirect()->intended(route('tenant.user.dashboard'));
    }

    private function isEnabled(): bool
    {
        return (bool) get_static_option('google_login_enable');
    }
}
