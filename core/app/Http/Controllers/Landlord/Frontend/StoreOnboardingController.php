<?php

namespace App\Http\Controllers\Landlord\Frontend;

use App\Actions\Tenant\TenantTrialPaymentLog;
use App\Events\TenantRegisterEvent;
use App\Services\Onboarding\StoreOnboardingProvisioner;
use App\Services\Onboarding\RecoveryRequired;
use App\Models\StaticOptionCentral;
use Closure;
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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
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
            StoreOnboardingRequest::whereKey($onboarding->id)->whereNull('user_id')
                ->whereIn('status', ['draft', 'account_verified'])->update(['user_id' => $user->id]);
            $onboarding->refresh();
            $this->assertRequestOwner($onboarding);
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
            && !$this->hasOtherTrial($user->id, $onboarding?->tenant_id);
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
        $validated = $request->validate(['plan_id' => ['required', 'integer']]);
        $onboarding = $this->currentRequest($request, true);
        $this->mutateDraft($onboarding, function (StoreOnboardingRequest $locked) use ($validated) {
            $plan = PricePlan::where('status', 1)->findOrFail($validated['plan_id']);
            $theme = $locked->theme_slug;
            if ($theme && !$this->themeAllowed($plan, $theme)) {
                $theme = null;
            }
            $locked->update([
                'plan_id' => $plan->id,
                'theme_slug' => $theme,
                'plan_snapshot' => $this->planSnapshot($plan),
                'status' => 'draft',
                'last_error' => null,
            ]);
        });

        return redirect()->route('landlord.store.onboarding', ['step' => 2]);
    }

    public function selectTheme(Request $request): RedirectResponse
    {
        $onboarding = $this->requireCurrentRequest($request);
        $validated = $request->validate(['theme_slug' => ['required', 'string', 'max:191']]);
        $this->mutateDraft($onboarding, function (StoreOnboardingRequest $locked) use ($validated) {
            $plan = PricePlan::where('status', 1)->findOrFail($locked->plan_id);
            if (!$this->themeAllowed($plan, $validated['theme_slug'])) {
                throw ValidationException::withMessages([
                    'theme_slug' => __('This theme is not available for the selected plan.'),
                ]);
            }
            $locked->update([
                'theme_slug' => $validated['theme_slug'],
                'status' => 'draft',
                'last_error' => null,
            ]);
        });

        return redirect()->route('landlord.store.onboarding', ['step' => 3]);
    }

    public function storeDetails(Request $request): RedirectResponse
    {
        $onboarding = $this->requireCurrentRequest($request);
        $this->mutateDraft($onboarding, function (StoreOnboardingRequest $locked) use ($request) {
            abort_if(empty($locked->theme_slug), 422);
            $validated = $this->validateStoreData([
                'store_name' => $request->input('store_name'),
                'subdomain' => $request->input('subdomain'),
            ]);
            $this->assertAvailableAddress($locked, $validated['subdomain']);
            $locked->update($validated + ['status' => 'draft', 'last_error' => null]);
        });

        $nextStep = Auth::guard('web')->check() && Auth::guard('web')->user()->email_verified ? 5 : 4;
        return redirect()->route('landlord.store.onboarding', ['step' => $nextStep]);
    }

    public function acknowledgePlanChange(Request $request): RedirectResponse
    {
        $onboarding = $this->requireCurrentRequest($request);
        // A failed attempt may acknowledge current pricing, never change its store identity.
        $this->mutateDraft($onboarding, function (StoreOnboardingRequest $locked) {
            $plan = PricePlan::where('status', 1)->findOrFail($locked->plan_id);
            $locked->update(['plan_snapshot' => $this->planSnapshot($plan)]);
        }, true);

        return redirect()->route('landlord.store.onboarding', ['step' => 5]);
    }

    private function mutateDraft(StoreOnboardingRequest $onboarding, Closure $change, bool $acknowledge = false): void
    {
        // All writers use the same CENTRAL row lock as completion. Never trust a stale model.
        $onboarding->getConnection()->transaction(function () use ($onboarding, $change, $acknowledge) {
            $locked = StoreOnboardingRequest::whereKey($onboarding->id)->lockForUpdate()->firstOrFail();
            $this->assertRequestOwner($locked);
            $allowed = $acknowledge ? ['draft', 'account_verified', 'failed'] : ['draft', 'account_verified'];
            if (!$acknowledge && $locked->status === 'failed' && !$locked->tenant_id) {
                $partial = $locked->subdomain ? Tenant::find($locked->subdomain) : null;
                if (!$partial || ((int) $partial->user_id !== (int) $locked->user_id
                    && $partial->getInternal('onboarding_request_id') !== $locked->id)) $allowed[] = 'failed';
            }
            if (!in_array($locked->status, $allowed, true) || (!$acknowledge && $locked->tenant_id)) {
                throw ValidationException::withMessages([
                    'onboarding' => __('Store creation has already started. Return to the review step to continue this request.'),
                ]);
            }
            $change($locked);
        });
    }

    private function assertRequestOwner(StoreOnboardingRequest $onboarding): void
    {
        abort_if($onboarding->user_id && (int) $onboarding->user_id !== (int) Auth::guard('web')->id(), 403);
    }

    private function validateStoreData(array $data): array
    {
        // Read the central source directly: a previously cached availability result is not authority.
        $restricted = explode(',', (string) StaticOptionCentral::where('option_name', 'forbidden_subdomains')->value('option_value'));
        $restricted = array_unique(array_merge(array_map(fn ($value) => Str::lower(trim($value)), $restricted), [
            'www', 'admin', 'user', 'tenant', 'tenants', 'landlord', 'landlords',
            'domain', 'subdomain', 'primary-domain', 'central-domain', 'http', 'https',
        ]));
        // Do not coerce arrays/objects into strings; validation must reject malformed input.
        foreach (['store_name', 'subdomain'] as $field) {
            if (is_string($data[$field] ?? null)) $data[$field] = trim($data[$field]);
        }
        if (is_string($data['subdomain'] ?? null)) $data['subdomain'] = Str::lower($data['subdomain']);

        return Validator::make($data, [
            'store_name' => ['required', 'string', 'max:191'],
            'subdomain' => [
                'required', 'string', 'min:3', 'max:63',
                'regex:/^(?!-)[a-z0-9-]+(?<!-)$/', Rule::notIn($restricted),
            ],
        ], [
            'subdomain.regex' => __('Use lowercase English letters, numbers, and hyphens only.'),
            'subdomain.not_in' => __('This subdomain is not available.'),
        ])->validate();
    }

    private function assertAvailableAddress(StoreOnboardingRequest $onboarding, string $subdomain): ?string
    {
        $tenant = Tenant::find($subdomain);
        // An owned store is not automatically this request's partially created store.
        $resuming = $tenant && $onboarding->status === 'failed'
            && (int) $tenant->user_id === (int) $onboarding->user_id
            && ($tenant->getInternal('onboarding_request_id') === $onboarding->id
                || app(StoreOnboardingProvisioner::class)->canAdoptLegacyPartial($tenant, $onboarding))
            && (!$onboarding->tenant_id || $onboarding->tenant_id === $tenant->id);
        if (($tenant && !$resuming) || ($onboarding->tenant_id && $onboarding->tenant_id !== $subdomain)) {
            throw ValidationException::withMessages(['subdomain' => __('This subdomain is not available.')]);
        }
        // The unique tenant primary key remains the final guard against other creation paths.
        return $resuming ? $tenant->id : null;
    }

    public function status(Request $request): JsonResponse
    {
        $onboarding = $this->requireCurrentRequest($request);
        abort_unless(Auth::guard('web')->check() && (int) $onboarding->user_id === (int) Auth::guard('web')->id(), 403);

        return response()->json($this->statusPayload($onboarding->fresh()));
    }

    public function complete(Request $request): JsonResponse
    {
        $request->validate(['terms_condition' => ['accepted']]);
        $user = Auth::guard('web')->user();
        abort_unless($user, 401);

        $onboarding = $this->requireCurrentRequest($request);
        abort_unless((int) $onboarding->user_id === (int) $user->id, 403);

        $plan = null;
        $claim = $onboarding->getConnection()->transaction(function () use ($onboarding, &$user, &$plan) {
            // Account first, then request: serialize eligibility across the user's requests.
            $user = User::on($onboarding->getConnectionName())->whereKey($user->id)->lockForUpdate()->firstOrFail();
            $locked = StoreOnboardingRequest::whereKey($onboarding->id)->lockForUpdate()->firstOrFail();
            abort_unless((int) $locked->user_id === (int) $user->id, 403);
            if (!$user->email_verified) {
                return response()->json(['message' => __('Verify your email before creating the store.')], 422);
            }
            if ($locked->status === 'ready' || $locked->status === 'provisioning') {
                return response()->json($this->statusPayload($locked), $locked->status === 'ready' ? 200 : 202);
            }
            abort_unless(in_array($locked->status, ['draft', 'account_verified', 'failed'], true), 409);

            // Gate ALL persisted inputs under the lock, before any tenant/domain/trial side effect.
            $data = $this->validateStoreData([
                'store_name' => $locked->store_name,
                'subdomain' => $locked->subdomain,
            ]);
            $resumingTenantId = $this->assertAvailableAddress($locked, $data['subdomain']);
            $plan = PricePlan::with('plan_themes')->where('status', 1)->find($locked->plan_id);
            if (!$plan || !$plan->has_trial || (int) $plan->trial_days < 1) {
                return response()->json(['message' => __('The free trial is not available for this plan.')], 422);
            }
            if ($this->planSnapshot($plan) !== ($locked->plan_snapshot ?? [])) {
                return response()->json([
                    'message' => __('The plan details changed. Review the update before continuing.'),
                    'status' => 'plan_changed',
                ], 409);
            }
            if (!$this->themeAllowed($plan, (string) $locked->theme_slug)) {
                return response()->json(['message' => __('The selected theme is no longer available for this plan.')], 422);
            }
            if ($this->hasOtherTrial($user->id, $resumingTenantId)
                || StoreOnboardingRequest::where('user_id', $user->id)
                    ->where('id', '!=', $locked->id)->where('status', 'provisioning')->exists()) {
                return response()->json(['message' => __('The free trial is not available for this account.')], 422);
            }
            $locked->update($data + ['status' => 'provisioning', 'last_error' => null]);
            return $locked;
        });

        if ($claim instanceof JsonResponse) return $claim;
        $onboarding = $claim; // The validated, locked snapshot, not the pre-lock read.

        try {
            $tenant = Tenant::find($onboarding->subdomain);
            if ($tenant && (int) $tenant->user_id !== (int) $user->id) {
                throw new \RuntimeException('Subdomain was claimed by another account.');
            }

            if (!$tenant) {
                event(new TenantRegisterEvent($user, $onboarding->subdomain, $onboarding->theme_slug, $onboarding->id));
                $tenant = Tenant::findOrFail($onboarding->subdomain);
            } else {
                app(StoreOnboardingProvisioner::class)->resume($tenant, $onboarding);
            }
            // The resumable pipeline verifies database, seed/theme/admin, domain and login key.
            app(StoreOnboardingProvisioner::class)->assertReady($tenant, $onboarding);

            // Keep the existing trial action and its tenant update in ONE central transaction.
            $onboarding->getConnection()->transaction(function () use ($onboarding, $user, $plan, $tenant) {
                User::on($onboarding->getConnectionName())->whereKey($user->id)->lockForUpdate()->firstOrFail();
                $trial = PaymentLogs::where('tenant_id', $tenant->id)->where('status', 'trial')->first();
                if (!$trial) {
                    abort_if($this->hasOtherTrial($user->id, $tenant->id), 422);
                    TenantTrialPaymentLog::trial_payment_log($user, $plan, $tenant->id, $onboarding->theme_slug);
                    $trial = PaymentLogs::where('tenant_id', $tenant->id)->where('status', 'trial')->firstOrFail();
                } else {
                    // Never reuse another request's/plan's ledger or restart an existing trial's clock.
                    abort_unless((int) $trial->user_id === (int) $user->id
                        && (int) $trial->package_id === (int) $plan->id
                        && $trial->theme_slug === $onboarding->theme_slug, 409);
                }
                app(StoreOnboardingProvisioner::class)->synchronizeTrial($tenant, $trial);
            });

            $user->update(['has_subdomain' => 1]);
            $onboarding->update([
                'tenant_id' => $tenant->id,
                'status' => 'ready',
                'completed_at' => now(),
                'last_error' => null,
            ]);

            app(StoreOnboardingProvisioner::class)->notifyReady($tenant);
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
                // Internal exceptions can include infrastructure details; do not persist them in the request.
                'last_error' => 'Provisioning failed. See the restricted application log using the request reference.',
            ]);

            return response()->json([
                'message' => $exception instanceof RecoveryRequired
                    ? __('Store setup needs a support review before it can safely continue. Please provide the request reference.')
                    : __('Your store setup did not finish. Use the same request to continue or contact support.'),
                'status' => 'failed',
                'recovery_required' => $exception instanceof RecoveryRequired,
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
        if (!$tenant || !$user || (int) $onboarding->user_id !== (int) $user->id
            || (int) $tenant->user_id !== (int) $user->id || !$tenant->domain || !$tenant->unique_key) return $payload;

        $token = hash_hmac('sha512', $user->username . '_' . $tenant->id, $tenant->unique_key);
        $payload['dashboard_url'] = rtrim(tenant_url_with_protocol($tenant->domain->domain), '/') . '/token-login/' . $token;
        return $payload;
    }
}
