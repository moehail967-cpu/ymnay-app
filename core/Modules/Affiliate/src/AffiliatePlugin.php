<?php

namespace Modules\Affiliate\Src;

use App\PluginSystem\PluginBase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Affiliate\Http\Controllers\AffiliateAdminController;
use Modules\Affiliate\Http\Controllers\AffiliateFrontendController;

class AffiliatePlugin extends PluginBase
{
    public function id(): string
    {
        return 'affiliate';
    }

    // ── Route registration (always called, regardless of active state) ─────────

    public function routes(): void
    {
        $this->registerSettings();
        $this->registerRoutes();
    }

    // ── Boot (called only when plugin is active) ──────────────────────────────

    public function boot(): void
    {
        view()->addNamespace('affiliate', $this->plugin_path('resources/views'));

        $this->registerSettings();

        if (!function_exists('tenant') || !tenant()) return;

        $this->registerMenus();
        $this->registerHooks();
    }

    // ── Lifecycle ─────────────────────────────────────────────────────────────

    public function on_activate(): void
    {
        $this->run_migrations();
        $this->update_option('commission_rate', '10');
        $this->update_option('cookie_days', '30');
        $this->update_option('auto_approve', '0');
        $this->update_option('min_payout', '50');
    }

    public function on_deactivate(): void {}

    public function on_update(string $from_version): void
    {
        $this->run_migrations();
    }

    // ── Private: route registration ───────────────────────────────────────────

    private function registerRoutes(): void
    {
        $this->register_web_routes(function () {
            // User dashboard — must include tenancy middleware so bootForTenant() fires
            \Route::middleware([
                    'web',
                    \App\Http\Middleware\Tenant\InitializeTenancyByDomainCustomisedMiddleware::class,
                    \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
                    'tenant_glvar',
                    'set_lang',
                    'auth',
                    'tenantUserMailVerify',
                    'package_expire',
                ])
                ->prefix('user-home/referrals')
                ->name('tenant.user.affiliate.')
                ->group(function () {
                    \Route::get('/', [AffiliateFrontendController::class, 'index'])->name('index');
                    \Route::post('/join', [AffiliateFrontendController::class, 'join'])->name('join');
                });

            // Tenant admin — must mirror the tenant_admin.php middleware + prefix exactly
            \Route::middleware([
                    'web',
                    \App\Http\Middleware\Tenant\InitializeTenancyByDomainCustomisedMiddleware::class,
                    \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
                    'auth:admin',
                    'tenant_admin_glvar',
                    'package_expire',
                    'tenantAdminPanelMailVerify',
                    'tenant_status',
                    'set_lang',
                ])
                ->prefix('admin-home/affiliate')
                ->name('tenant.admin.affiliate.')
                ->group(function () {
                    \Route::get('/', [AffiliateAdminController::class, 'index'])->name('index');
                    \Route::get('/commissions', [AffiliateAdminController::class, 'commissions'])->name('commissions');
                    \Route::post('/commissions/{id}/approve', [AffiliateAdminController::class, 'approveCommission'])->name('commissions.approve');
                    \Route::post('/commissions/{id}/reject', [AffiliateAdminController::class, 'rejectCommission'])->name('commissions.reject');
                    \Route::get('/payouts', [AffiliateAdminController::class, 'payouts'])->name('payouts');
                    \Route::post('/payouts', [AffiliateAdminController::class, 'processPayout'])->name('payouts.process');
                    \Route::get('/settings', [AffiliateAdminController::class, 'settings'])->name('settings');
                    \Route::post('/settings', [AffiliateAdminController::class, 'saveSettings'])->name('settings.save');
                    \Route::post('/affiliates/{id}/toggle', [AffiliateAdminController::class, 'toggleAffiliate'])->name('affiliates.toggle');
                });
        });
    }

    private function registerSettings(): void
    {
        $this->register_settings([
            ['key' => 'commission_rate', 'label' => 'Commission rate (%)',              'type' => 'number', 'default' => '10'],
            ['key' => 'cookie_days',     'label' => 'Referral cookie duration (days)', 'type' => 'number', 'default' => '30'],
            ['key' => 'auto_approve',    'label' => 'Auto-approve commissions',         'type' => 'toggle', 'default' => '0'],
            ['key' => 'min_payout',      'label' => 'Minimum payout amount',            'type' => 'number', 'default' => '50'],
        ]);
    }

    private function registerMenus(): void
    {
        $this->add_menu([
            'id'      => 'affiliate-menu',
            'label'   => __('Affiliate Program'),
            'icon'    => 'mdi-account-network-outline',
            'route'   => 'tenant.admin.affiliate.index',
            'order'   => 75,
            'context' => 'tenant',
        ]);

        $this->add_submenu('affiliate-menu', [
            'id'    => 'affiliate-commissions',
            'label' => __('Commissions'),
            'route' => 'tenant.admin.affiliate.commissions',
        ]);

        $this->add_submenu('affiliate-menu', [
            'id'    => 'affiliate-payouts',
            'label' => __('Payouts'),
            'route' => 'tenant.admin.affiliate.payouts',
        ]);

        $this->add_submenu('affiliate-menu', [
            'id'    => 'affiliate-settings',
            'label' => __('Settings'),
            'route' => 'tenant.admin.affiliate.settings',
        ]);
    }

    private function registerHooks(): void
    {
        $this->add_action('nazmart:user_dashboard_sidebar', [$this, 'renderSidebarItem'], 20);
        $this->add_action('nazmart:user_dashboard_home',    [$this, 'renderDashboardWidget'], 10);
        $this->add_action('nazmart:frontend_head',          [$this, 'captureReferral'], 5);
        $this->add_action('nazmart:user_registered',        [$this, 'linkReferrer'], 10);
        $this->add_action('nazmart:order_completed',        [$this, 'recordCommission'], 10);
        $this->add_action('nazmart:payment_success',        [$this, 'recordCommissionOnPayment'], 10);
        $this->add_action('nazmart:order_cancelled',        [$this, 'cancelCommission'], 10);
        $this->add_action('nazmart:frontend_footer',        [$this, 'injectReferralWidget'], 30);
    }

    // ── Hook callbacks ────────────────────────────────────────────────────────

    public function renderSidebarItem(): void
    {
        $active = request()->routeIs('tenant.user.affiliate.*') ? 'active' : '';
        $url    = route('tenant.user.affiliate.index');
        $label  = __('Referral Programme');
        echo "<li class=\"list {$active}\"><a href=\"{$url}\"><i class=\"mdi mdi-account-multiple-outline\"></i> {$label}</a></li>";
    }

    public function renderDashboardWidget(mixed $user): void
    {
        if (!$user) return;

        $affiliate = DB::table('affiliates')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$affiliate) return;

        $url     = route('tenant.user.affiliate.index');
        $balance = amount_with_currency_symbol($affiliate->balance);
        $label   = __('Affiliate Balance');
        $link    = __('View Programme');

        echo <<<HTML
        <div class="col-xl-6 col-md-6 orders-child">
            <div class="single-orders">
                <div class="orders-flex-content">
                    <div class="icon"><i class="las la-user-friends"></i></div>
                    <div class="contents">
                        <h2 class="order-titles">{$balance}</h2>
                        <span class="order-para">{$label}</span>
                        <a href="{$url}" style="font-size:12px;display:block;margin-top:4px;">{$link}</a>
                    </div>
                </div>
            </div>
        </div>
        HTML;
    }

    public function captureReferral(): void
    {
        $code = request()->query('ref');
        if (!$code) return;

        $affiliate = DB::table('affiliates')
            ->where('code', $code)
            ->where('status', 'active')
            ->first();

        if (!$affiliate) return;

        session(['affiliate_ref' => $affiliate->id]);
        cookie()->queue('affiliate_ref', $affiliate->id, (int) $this->get_option('cookie_days', 30) * 1440);

        // If user is already logged in and not yet linked, link them immediately
        if (auth()->check()) {
            $user = auth()->user();
            $alreadyLinked = DB::table('users')->where('id', $user->id)->value('referred_by_affiliate');
            if (!$alreadyLinked && $affiliate->user_id != $user->id) {
                DB::table('users')->where('id', $user->id)
                    ->update(['referred_by_affiliate' => $affiliate->id]);
            }
        }
    }

    public function linkReferrer(mixed $user): void
    {
        $affiliateId = session('affiliate_ref') ?? request()->cookie('affiliate_ref');
        if (!$affiliateId || !$user) return;

        $affiliate = DB::table('affiliates')->where('id', $affiliateId)->first();
        if ($affiliate && $affiliate->user_id == $user->id) return;

        DB::table('users')->where('id', $user->id)
            ->update(['referred_by_affiliate' => $affiliateId]);
    }

    public function recordCommission(mixed $order): void
    {
        try {
            // Prevent double commission for the same order
            $orderId = data_get($order, 'id');
            if ($orderId && DB::table('affiliate_commissions')->where('order_id', $orderId)->exists()) {
                return;
            }

            $userId = data_get($order, 'user_id') ?? auth()->id();
            if (!$userId) return;

            $referredBy = DB::table('users')->where('id', $userId)->value('referred_by_affiliate');
            if (!$referredBy) return;

            $affiliate = DB::table('affiliates')
                ->where('id', $referredBy)
                ->where('status', 'active')
                ->first();
            if (!$affiliate) return;

            // Self-purchase exclusion — affiliate cannot earn commission on their own orders
            if ((string) $affiliate->user_id === (string) $userId) return;

            $rate   = (float) $this->get_option('commission_rate', 10);
            $total  = (float) data_get($order, 'sub_total', 0);
            $amount = round($total * $rate / 100, 2);
            $status = $this->get_option('auto_approve', '0') === '1' ? 'approved' : 'pending';

            DB::table('affiliate_commissions')->insert([
                'tenant_id'        => $this->currentTenantId(),
                'affiliate_id'     => $affiliate->id,
                'order_id'         => data_get($order, 'id'),
                'referred_user_id' => $userId,
                'order_total'      => $total,
                'rate'             => $rate,
                'amount'           => $amount,
                'status'           => $status,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            if ($status === 'approved') {
                DB::table('affiliates')->where('id', $affiliate->id)->increment('balance', $amount);
                DB::table('affiliates')->where('id', $affiliate->id)->increment('total_earned', $amount);
            }
        } catch (\Throwable $e) {
            Log::channel('plugin')->error("[affiliate] recordCommission: {$e->getMessage()}");
        }
    }

    public function recordCommissionOnPayment(mixed $payment_data, mixed $order): void
    {
        if (!$order) return;

        // Prevent duplicate commission if order_completed fires later (admin marks complete)
        $orderId = data_get($order, 'id');
        if ($orderId && DB::table('affiliate_commissions')->where('order_id', $orderId)->exists()) {
            return;
        }

        $this->recordCommission($order);
    }

    public function cancelCommission(mixed $order): void
    {
        try {
            $orderId = data_get($order, 'id');
            if (!$orderId) return;

            $commission = DB::table('affiliate_commissions')
                ->where('order_id', $orderId)
                ->where('status', 'pending')
                ->first();

            if ($commission) {
                DB::table('affiliate_commissions')
                    ->where('id', $commission->id)
                    ->update(['status' => 'cancelled']);
            }
        } catch (\Throwable $e) {
            Log::channel('plugin')->warning("[affiliate] cancelCommission: {$e->getMessage()}");
        }
    }

    public function injectReferralWidget(): void
    {
        if (!auth()->check()) return;

        $user      = auth()->user();
        $affiliate = DB::table('affiliates')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$affiliate) return;

        $link = url('/?ref=' . $affiliate->code);
        echo "<script>window.__affiliateRefLink = \"{$link}\";</script>";
    }

    private function currentTenantId(): int|string|null
    {
        try {
            return function_exists('tenant') && tenant() ? tenant()->id : null;
        } catch (\Throwable) {
            return null;
        }
    }
}
