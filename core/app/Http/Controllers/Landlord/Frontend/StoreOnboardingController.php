<?php

namespace App\Http\Controllers\Landlord\Frontend;

use App\Actions\Tenant\TenantTrialPaymentLog;
use App\Events\TenantRegisterEvent;
use App\Http\Controllers\Controller;
use App\Models\PaymentLogs;
use App\Models\PricePlan;
use App\Models\StoreOnboardingRequest;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class StoreOnboardingController extends Controller
{
    private const SESSION_KEY = 'store_onboarding_request_id';
    private const COOKIE_MINUTES = 10080;

    public function index(Request $request): View
    {
        $onboarding = $this->currentRequest($request);
        $user = Auth::guard('web')->user();

        if ($onboarding && $user && empty($onboarding->user_id)) {
            $onboarding->update(['user_id' => $user->id]);
            $onboarding->refresh();
        }

        $plans = PricePlan::query()
            ->with('plan_themes')
            ->where('status', 1)
            ->orderBy('type')
            ->orderBy('id')
            ->get();

        $plan = $onboarding?->plan_id ? $plans->firstWhere('id', $onboarding->plan_id) : null;
        $themes = $plan ? $this->themeOptions($plan) : [];
        $maxStep = $this->maxStep($onboarding, $user);
        $step = max(1, min((int) $request->integer('step', $maxStep), $maxStep));
        $planChanged = $plan && $onboarding
            ? $this->planSnapshot($plan) !== ($onboarding->plan_snapshot ?? [])
            : false;
        $trialEligible = $user && $plan && $plan->has_trial && (int) $plan->trial_days > 0
            && !$this->hasOtherTrial($user->id, $onboarding?->subdomain);
        $readyDashboardUrl = $onboarding?->status === 'ready'
            ? ($this->statusPayload($onboarding)['dashboard_url'] ?? null)
            : null;

        return view('landlord.frontend.onboarding.store-setup', compact(
            'plans',
            'plan',
            'themes',
            'onboarding',
            'user',
            'step',
            'maxStep',
            'planChanged',
            'trialEligible',
            'readyDashboardUrl'
        ));
    }

    public function selectPlan(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'plan_id' => ['required', 'integer', Rule::exists('price_plans', 'id')->where('status', 1)],
        ]);

        $plan = PricePlan::findOrFail($validated['plan_id']);
        $onboarding = $this->currentRequest($request, true);
        if ($onboarding->status === 'ready') {
            $onboarding = $this->createRequest($request, Auth::guard('web')->user());
        }
        $theme = $onboarding->theme_slug;
        if ($theme && !$this->themeAllowed($plan, $theme)) {
            $theme = null;
        }

        $onboarding->update([
            'plan_id' => $plan->id,
            'theme_slug' => $theme,
            'plan_snapshot' => $this->planSnapshot($plan),
            'status' => 'draft',
            'last_error' => null,
        ]);

        return redirect()->route('landlord.store.onboarding', ['step' => 2]);
    }

    public function selectTheme(Request $request): RedirectResponse
    {
        $onboarding = $this->requireCurrentRequest($request);
        $plan = PricePlan::where('status', 1)->findOrFail($onboarding->plan_id);
        $validated = $request->validate(['theme_slug' => ['required', 'string', 'max:191']]);

        if (!$this->themeAllowed($plan, $validated['theme_slug'])) {
            return back()->withErrors(['theme_slug' => __('This theme is not available for the selected plan.')]);
        }

        $onboarding->update([
            'theme_slug' => $validated['theme_slug'],
            'status' => 'draft',
            'last_error' => null,
        ]);

        return redirect()->route('landlord.store.onboarding', ['step' => 3]);
    }

    public function storeDetails(Request $request): RedirectResponse
    {
        $onboarding = $this->requireCurrentRequest($request);
        abort_if(empty($onboarding->theme_slug), 422);

        $request->merge(['subdomain' => Str::lower(trim((string) $request->input('subdomain')))]);
        $restricted = array_filter(array_map('trim', explode(',', (string) get_static_option('forbidden_subdomains'))));
        $restricted = array_unique(array_merge($restricted, [
            'www', 'admin', 'user', 'tenant', 'tenants', 'landlord', 'landlords',
            'domain', 'subdomain', 'primary-domain', 'central-domain', 'http', 'https',
        ]));

        $validated = $request->validate([
            'store_name' => ['required', 'string', 'max:191'],
            'subdomain' => [
                'required',
                'string',
                'min:3',
                'max:63',
                'regex:/^(?!-)[a-z0-9-]+(?<!-)$/',
                Rule::notIn(array_map('strtolower', $restricted)),
                Rule::unique('tenants', 'id')->ignore($onboarding->tenant_id),
            ],
        ], [
            'subdomain.regex' => __('Use lowercase English letters, numbers, and hyphens only.'),
            'subdomain.not_in' => __('This subdomain is not available.'),
        ]);

        $onboarding->update([
            'store_name' => trim($validated['store_name']),
            'subdomain' => $validated['subdomain'],
            'status' => 'draft',
            'last_error' => null,
        ]);

        $nextStep = Auth::guard('web')->check() && Auth::guard('web')->user()->email_verified ? 5 : 4;
        return redirect()->route('landlord.store.onboarding', ['step' => $nextStep]);
    }

    public function acknowledgePlanChange(Request $request): RedirectResponse
    {
        $onboarding = $this->requireCurrentRequest($request);
        $plan = PricePlan::where('status', 1)->findOrFail($onboarding->plan_id);
        $onboarding->update(['plan_snapshot' => $this->planSnapshot($plan)]);

        return redirect()->route('landlord.store.onboarding', ['step' => 5]);
    }

    public function status(Request $request): JsonResponse
    {
        $onboarding = $this->requireCurrentRequest($request);
        abort_unless(Auth::guard('web')->check() && $onboarding->user_id === Auth::guard('web')->id(), 403);

        return response()->json($this->statusPayload($onboarding->fresh()));
    }

    public function complete(Request $request): JsonResponse
    {
        $request->validate(['terms_condition' => ['accepted']]);
        $user = Auth::guard('web')->user();
        abort_unless($user, 401);

        $onboarding = $this->requireCurrentRequest($request);
        abort_unless((int) $onboarding->user_id === (int) $user->id, 403);

        if (!$user->email_verified) {
            return response()->json(['message' => __('Verify your email before creating the store.')], 422);
        }

        if ($onboarding->status === 'ready') {
            return response()->json($this->statusPayload($onboarding));
        }

        $plan = PricePlan::with('plan_themes')->where('status', 1)->find($onboarding->plan_id);
        if (!$plan || !$plan->has_trial || (int) $plan->trial_days < 1) {
            return response()->json(['message' => __('The free trial is not available for this plan.')], 422);
        }

        if ($this->planSnapshot($plan) !== ($onboarding->plan_snapshot ?? [])) {
            return response()->json([
                'message' => __('The plan details changed. Review the update before continuing.'),
                'status' => 'plan_changed',
            ], 409);
        }

        if (!$this->themeAllowed($plan, (string) $onboarding->theme_slug)) {
            return response()->json(['message' => __('The selected theme is no longer available for this plan.')], 422);
        }

        if ($this->hasOtherTrial($user->id, $onboarding->subdomain)) {
            return response()->json(['message' => __('The free trial is not available for this account.')], 422);
        }

        $claimState = DB::transaction(function () use ($onboarding, $user) {
            User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $locked = StoreOnboardingRequest::whereKey($onboarding->id)->lockForUpdate()->firstOrFail();
            if ($locked->status === 'ready') return 'ready';
            if ($locked->status === 'provisioning') return 'processing';
            if ($this->hasOtherTrial($user->id, $locked->subdomain)
                || StoreOnboardingRequest::where('user_id', $user->id)
                    ->where('id', '!=', $locked->id)
                    ->where('status', 'provisioning')
                    ->exists()) {
                return 'ineligible';
            }
            $locked->update(['status' => 'provisioning', 'last_error' => null]);
            return 'claimed';
        });

        if ($claimState === 'ineligible') {
            return response()->json(['message' => __('The free trial is not available for this account.')], 422);
        }
        if ($claimState !== 'claimed') {
            return response()->json($this->statusPayload($onboarding->fresh()), 202);
        }

        try {
            $tenant = Tenant::find($onboarding->subdomain);
            if ($tenant && (int) $tenant->user_id !== (int) $user->id) {
                throw new \RuntimeException('Subdomain was claimed by another account.');
            }

            if (!$tenant) {
                event(new TenantRegisterEvent($user, $onboarding->subdomain, $onboarding->theme_slug));
                $tenant = Tenant::findOrFail($onboarding->subdomain);
            }
            $onboarding->update(['tenant_id' => $tenant->id]);

            if (!PaymentLogs::where('tenant_id', $tenant->id)->where('status', 'trial')->exists()) {
                TenantTrialPaymentLog::trial_payment_log($user, $plan, $tenant->id, $onboarding->theme_slug);
            }

            $tenant->refresh();
            if (!$tenant->domain || !$tenant->unique_key) {
                throw new \RuntimeException('Tenant provisioning did not produce a domain and login key.');
            }

            tenancy()->initialize($tenant);
            try {
                if (!update_static_option('site_title', $onboarding->store_name)) {
                    throw new \RuntimeException('Tenant store name could not be saved.');
                }
            } finally {
                tenancy()->end();
            }

            $user->update(['has_subdomain' => 1]);
            $onboarding->update([
                'tenant_id' => $tenant->id,
                'status' => 'ready',
                'completed_at' => now(),
                'last_error' => null,
            ]);

            return response()->json($this->statusPayload($onboarding->fresh()));
        } catch (Throwable $exception) {
            Log::error('Store onboarding provisioning failed', [
                'onboarding_id' => $onboarding->id,
                'user_id' => $user->id,
                'subdomain' => $onboarding->subdomain,
                'error' => $exception->getMessage(),
            ]);
            $onboarding->update([
                'status' => 'failed',
                'last_error' => Str::limit($exception->getMessage(), 1000, ''),
            ]);

            return response()->json([
                'message' => __('Your store setup did not finish. Use the same request to continue or contact support.'),
                'status' => 'failed',
                'reference' => $onboarding->id,
            ], 422);
        }
    }

    private function currentRequest(Request $request, bool $create = false): ?StoreOnboardingRequest
    {
        $sessionId = $request->session()->get(self::SESSION_KEY);
        $cookieId = $request->cookie(self::SESSION_KEY);
        $onboarding = $sessionId ? StoreOnboardingRequest::find($sessionId) : null;
        if (!$onboarding && $cookieId && $cookieId !== $sessionId) {
            $onboarding = StoreOnboardingRequest::find($cookieId);
        }
        $user = Auth::guard('web')->user();

        if ($onboarding && $onboarding->user_id && (!$user || (int) $onboarding->user_id !== (int) $user->id)) {
            $request->session()->forget(self::SESSION_KEY);
            Cookie::queue(Cookie::forget(self::SESSION_KEY));
            $onboarding = null;
        }

        if (!$onboarding && $user) {
            $onboarding = StoreOnboardingRequest::where('user_id', $user->id)
                ->whereIn('status', ['draft', 'account_verified', 'provisioning', 'failed', 'ready'])
                ->latest()
                ->first();
            if ($onboarding) {
                $this->rememberRequest($request, $onboarding);
            }
        }

        if (!$onboarding && $create) {
            $onboarding = $this->createRequest($request, $user);
        } elseif ($onboarding && !$request->session()->has(self::SESSION_KEY)) {
            $this->rememberRequest($request, $onboarding);
        }

        return $onboarding;
    }

    private function createRequest(Request $request, ?User $user): StoreOnboardingRequest
    {
        $onboarding = StoreOnboardingRequest::create([
            'id' => (string) Str::uuid(),
            'user_id' => $user?->id,
            'status' => 'draft',
        ]);
        $this->rememberRequest($request, $onboarding);
        return $onboarding;
    }

    private function rememberRequest(Request $request, StoreOnboardingRequest $onboarding): void
    {
        $request->session()->put(self::SESSION_KEY, $onboarding->id);
        Cookie::queue(
            self::SESSION_KEY,
            $onboarding->id,
            self::COOKIE_MINUTES,
            null,
            null,
            $request->isSecure(),
            true,
            false,
            'lax'
        );
    }

    private function requireCurrentRequest(Request $request): StoreOnboardingRequest
    {
        $onboarding = $this->currentRequest($request);
        abort_if(!$onboarding, 404);
        return $onboarding;
    }

    private function maxStep(?StoreOnboardingRequest $onboarding, $user): int
    {
        if (!$onboarding?->plan_id) return 1;
        if (!$onboarding->theme_slug) return 2;
        if (!$onboarding->store_name || !$onboarding->subdomain) return 3;
        if (!$user || !$user->email_verified || (int) $onboarding->user_id !== (int) $user->id) return 4;
        return 5;
    }

    private function planSnapshot(PricePlan $plan): array
    {
        return [
            'id' => (int) $plan->id,
            'title' => (string) $plan->title,
            'price' => (string) $plan->price,
            'type' => (int) $plan->type,
            'has_trial' => (bool) $plan->has_trial,
            'trial_days' => (int) $plan->trial_days,
            'updated_at' => optional($plan->updated_at)->toISOString(),
        ];
    }

    private function themeOptions(PricePlan $plan): array
    {
        $all = getAllThemeSlug();
        $assigned = $plan->plan_themes->where('status', 1)->pluck('theme_slug')->all();
        $slugs = count($assigned) ? array_values(array_intersect($all, $assigned)) : $all;
        return getPricePlanBasedAllThemeData($slugs);
    }

    private function themeAllowed(PricePlan $plan, string $slug): bool
    {
        return collect($this->themeOptions($plan))->contains(fn ($theme) => $theme->slug === $slug);
    }

    private function hasOtherTrial(int $userId, ?string $subdomain): bool
    {
        return PaymentLogs::where('user_id', $userId)
            ->where('status', 'trial')
            ->when($subdomain, function ($query, $currentSubdomain) {
                $query->where(function ($trialQuery) use ($currentSubdomain) {
                    $trialQuery->whereNull('tenant_id')
                        ->orWhere('tenant_id', '!=', $currentSubdomain);
                });
            })
            ->exists();
    }

    private function statusPayload(StoreOnboardingRequest $onboarding): array
    {
        $payload = ['status' => $onboarding->status, 'reference' => $onboarding->id];
        if ($onboarding->status !== 'ready') return $payload;

        $tenant = Tenant::find($onboarding->tenant_id);
        $user = Auth::guard('web')->user();
        if (!$tenant || !$user || !$tenant->domain || !$tenant->unique_key) return $payload;

        $token = hash_hmac('sha512', $user->username . '_' . $tenant->id, $tenant->unique_key);
        $payload['dashboard_url'] = rtrim(tenant_url_with_protocol($tenant->domain->domain), '/') . '/token-login/' . $token;
        return $payload;
    }
}
