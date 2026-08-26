<?php

namespace Modules\LoyaltyPoints\Src;

use App\PluginSystem\PluginBase;
use Illuminate\Support\Facades\Log;
use Modules\LoyaltyPoints\Http\Controllers\Admin\LoyaltyController;
use Modules\LoyaltyPoints\Http\Controllers\Api\LoyaltyApiController;
use Modules\LoyaltyPoints\Http\Controllers\LoyaltyFrontendController;

class LoyaltyPointsPlugin extends PluginBase
{
    public function id(): string
    {
        return 'loyalty-points';
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
        view()->addNamespace('loyalty-points', $this->plugin_path('resources/views'));

        $this->registerSettings();

        if (!function_exists('tenant') || !tenant()) return;

        $this->registerMenus();

        if (!(bool) $this->get_option('enabled', false)) return;

        $this->registerHooks();
        $this->schedule('daily', [$this, 'runExpiry']);
    }

    // ── Lifecycle ─────────────────────────────────────────────────────────────

    public function on_activate(): void
    {
        $this->run_migrations();

        $defaults = [
            'enabled'            => '0',
            'earn_rate'          => '1',
            'redeem_rate'        => '100',
            'min_points_redeem'  => '100',
            'max_redeem_percent' => '50',
            'point_expiry_days'  => '0',
            'earn_on_signup'     => '0',
        ];

        foreach ($defaults as $key => $value) {
            if ($this->get_option($key) === null) {
                $this->update_option($key, $value);
            }
        }
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
            // User dashboard page — full tenant middleware stack (mirrors user-home group in tenant.php)
            \Route::middleware([
                    'web',
                    \App\Http\Middleware\Tenant\InitializeTenancyByDomainCustomisedMiddleware::class,
                    \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
                    'tenant_glvar',
                    'set_lang',
                    'tenantUserMailVerify',
                    'package_expire',
                    'auth',
                ])
                ->prefix('user-home/loyalty-points')
                ->name('tenant.user.loyalty-points.')
                ->group(function () {
                    \Route::get('/', [LoyaltyFrontendController::class, 'index'])->name('index');
                });

            // Tenant admin routes
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
                ->prefix('admin-home/loyalty-points')
                ->name('tenant.admin.loyalty-points.')
                ->group(function () {
                    \Route::get('/',              [LoyaltyController::class, 'index'])->name('index');
                    \Route::get('/customer/{id}', [LoyaltyController::class, 'show'])->name('show');
                    \Route::post('/adjust/{id}',  [LoyaltyController::class, 'adjust'])->name('adjust');
                    \Route::get('/settings',      [LoyaltyController::class, 'settings'])->name('settings');
                    \Route::post('/settings',     [LoyaltyController::class, 'saveSettings'])->name('settings.save');
                });
        });

        // API routes — PluginManager wraps these with prefix: api/v1/plugins/loyalty-points
        $this->register_api_routes(function () {
            \Route::name('tenant.api.loyalty-points.')
                ->group(function () {
                    \Route::get('/balance',  [LoyaltyApiController::class, 'balance'])->name('balance');
                    \Route::post('/redeem',  [LoyaltyApiController::class, 'redeem'])->name('redeem')->middleware('auth:web');
                    \Route::post('/cancel',  [LoyaltyApiController::class, 'cancel'])->name('cancel')->middleware('auth:web');
                });
        });
    }

    private function registerSettings(): void
    {
        $this->register_settings([
            ['key' => 'enabled',            'label' => 'Enable Loyalty Points',               'type' => 'toggle', 'group' => 'General'],
            ['key' => 'earn_rate',          'label' => 'Points earned per $1 spent',          'type' => 'number', 'group' => 'Earning'],
            ['key' => 'earn_on_signup',     'label' => 'Signup bonus points',                 'type' => 'number', 'group' => 'Earning'],
            ['key' => 'redeem_rate',        'label' => 'Points needed for $1 discount',       'type' => 'number', 'group' => 'Redemption'],
            ['key' => 'min_points_redeem',  'label' => 'Minimum points to redeem',            'type' => 'number', 'group' => 'Redemption'],
            ['key' => 'max_redeem_percent', 'label' => 'Max % of cart payable in points',     'type' => 'number', 'group' => 'Redemption'],
            ['key' => 'point_expiry_days',  'label' => 'Points expire after (days, 0=never)', 'type' => 'number', 'group' => 'Expiry'],
        ]);
    }

    private function registerMenus(): void
    {
        $this->add_menu([
            'id'      => 'loyalty-points-menu',
            'label'   => __('Loyalty Points'),
            'icon'    => 'mdi-star-circle-outline',
            'route'   => 'tenant.admin.loyalty-points.index',
            'order'   => 73,
            'context' => 'tenant',
        ]);

        $this->add_submenu('loyalty-points-menu', [
            'id'    => 'loyalty-points-index',
            'label' => __('Customers'),
            'route' => 'tenant.admin.loyalty-points.index',
        ]);

        $this->add_submenu('loyalty-points-menu', [
            'id'    => 'loyalty-points-settings',
            'label' => __('Settings'),
            'route' => 'tenant.admin.loyalty-points.settings',
        ]);
    }

    private function registerHooks(): void
    {
        // Order events
        $this->add_action('nazmart:order_completed', [$this, 'onOrderCompleted'], 10);
        $this->add_action('nazmart:order_cancelled', [$this, 'onOrderCancelled'], 10);
        $this->add_action('nazmart:user_registered', [$this, 'onUserRegistered'], 10);

        // Cart total discount (when points are being redeemed)
        $this->add_filter('nazmart:cart_total', [$this, 'applyRedemptionDiscount'], 30);

        // Cart summary widget — same mechanism as CartDiscount plugin
        $this->add_action('nazmart:frontend_head', [$this, 'injectWidgetCss']);
        $this->add_filter('nazmart:cart_summary',  [$this, 'renderCartSummaryWidget']);

        // User dashboard
        $this->add_action('nazmart:user_dashboard_sidebar', [$this, 'renderSidebarItem'], 25);
        $this->add_action('nazmart:user_dashboard_home',    [$this, 'renderDashboardHomeWidget'], 15);
    }

    // ── Hook callbacks ────────────────────────────────────────────────────────

    public function onOrderCompleted(mixed $order): void
    {
        try {
            $userId = $order->customer_id ?? $order->user_id ?? null;
            if (!$userId) return;

            $earnRate = (int) $this->get_option('earn_rate', 1);
            if ($earnRate <= 0) return;

            $total  = (float) ($order->order_amount ?? 0);
            $points = (int) floor($total * $earnRate);
            if ($points <= 0) return;

            $expiresAt  = null;
            $expiryDays = (int) $this->get_option('point_expiry_days', 0);
            if ($expiryDays > 0) {
                $expiresAt = now()->addDays($expiryDays);
            }

            (new LoyaltyService)->addPoints($userId, $points, 'earn', "Order #{$order->id}", $order->id, $expiresAt);
        } catch (\Throwable $e) {
            Log::warning("[loyalty-points] onOrderCompleted: {$e->getMessage()}");
        }
    }

    public function onOrderCancelled(mixed $order): void
    {
        try {
            $userId = $order->customer_id ?? $order->user_id ?? null;
            if (!$userId) return;

            $earnRate = (int) $this->get_option('earn_rate', 1);
            if ($earnRate <= 0) return;

            $total  = (float) ($order->order_amount ?? 0);
            $points = (int) floor($total * $earnRate);
            if ($points <= 0) return;

            (new LoyaltyService)->deductPoints($userId, $points, 'refund', "Order #{$order->id} cancelled", $order->id);
        } catch (\Throwable $e) {
            Log::warning("[loyalty-points] onOrderCancelled: {$e->getMessage()}");
        }
    }

    public function onUserRegistered(mixed $user): void
    {
        try {
            $bonus = (int) $this->get_option('earn_on_signup', 0);
            if ($bonus <= 0) return;

            $userId = is_object($user) ? $user->id : (int) $user;
            (new LoyaltyService)->addPoints($userId, $bonus, 'earn', 'Signup bonus');
        } catch (\Throwable $e) {
            Log::warning("[loyalty-points] onUserRegistered: {$e->getMessage()}");
        }
    }

    public function applyRedemptionDiscount(float $total): float
    {
        try {
            $points     = (int) session('loyalty_redeem_points', 0);
            $redeemRate = (int) $this->get_option('redeem_rate', 100);
            if ($points <= 0 || $redeemRate <= 0) return $total;

            // Raw discount in base currency (points ÷ rate = base-currency value)
            $rawDiscount = $points / $redeemRate;

            // loyalty_points:redemption_discount — lets currency plugins convert to display currency
            $discount    = (float) apply_filters('loyalty_points:redemption_discount', $rawDiscount);

            $maxPercent  = (int) $this->get_option('max_redeem_percent', 50);
            $maxDiscount = $total * ($maxPercent / 100);
            $discount    = min($discount, $maxDiscount);

            return max(0, $total - $discount);
        } catch (\Throwable) {
            return $total;
        }
    }

    /** Inject widget CSS on every storefront page head (CartDiscount pattern). */
    public function injectWidgetCss(): void
    {
        echo '<style>
.lp-cs-wrap{--lp-card:var(--vl-surface,var(--dk-surface,var(--fp-surface,var(--tz-surface,#f8f9fa))));--lp-border:var(--vl-border,var(--dk-border,var(--fp-border,var(--tz-border,#e5e7eb))));--lp-text:var(--vl-ivory,var(--dk-white,var(--fp-text,var(--tz-text,#111827))));--lp-muted:var(--vl-muted,var(--dk-muted,var(--fp-muted,var(--tz-muted,#6b7280))));--lp-gold:#f59e0b;--lp-gold-d:#d97706;--lp-green:#059669}
.lp-cs-wrap{background:var(--lp-card);border:1px solid var(--lp-border);border-radius:10px;padding:14px 16px;margin-bottom:14px}
.lp-cs-head{display:flex;align-items:center;gap:8px}
.lp-cs-head i{font-size:18px;color:var(--lp-gold)}
.lp-cs-head-text{flex:1;font-size:13px;font-weight:600;color:var(--lp-text)}
.lp-cs-earn-badge{font-size:12px;font-weight:700;color:var(--lp-gold);background:rgba(245,158,11,.12);border:1px solid rgba(245,158,11,.3);padding:2px 9px;border-radius:20px;white-space:nowrap}
.lp-cs-body{margin-top:10px;padding-top:10px;border-top:1px solid var(--lp-border)}
.lp-cs-hint{font-size:12px;color:var(--lp-muted);margin:0 0 8px}
.lp-cs-applied{font-size:12px;color:var(--lp-green);margin:0 0 6px;display:flex;align-items:center;gap:5px;font-weight:600}
.lp-cs-actions{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.lp-cs-apply-btn{background:var(--lp-gold);color:#fff;border:none;border-radius:7px;padding:6px 14px;font-size:12px;font-weight:600;cursor:pointer;transition:background .15s}
.lp-cs-apply-btn:hover{background:var(--lp-gold-d)}
.lp-cs-cancel-btn{background:transparent;color:var(--lp-muted);border:1px solid var(--lp-border);border-radius:7px;padding:5px 12px;font-size:11px;cursor:pointer;transition:all .15s}
.lp-cs-cancel-btn:hover{color:#dc2626;border-color:#dc2626}
.lp-cs-msg{font-size:12px;margin-top:6px;color:#dc2626}
.lp-cs-divider{border:none;border-top:1px solid var(--lp-border);margin:10px 0}
</style>' . "\n";
    }

    /** Render cart/checkout order-summary widget (CartDiscount pattern). */
    public function renderCartSummaryWidget(string $html): string
    {
        $widget = LoyaltyFrontendController::renderCartWidget($this);
        return $html . $widget;
    }

    /** User dashboard sidebar link. */
    public function renderSidebarItem(): void
    {
        $active = request()->routeIs('tenant.user.loyalty-points.*') ? 'active' : '';
        $url    = route('tenant.user.loyalty-points.index');
        $label  = __('Loyalty Points');
        echo "<li class=\"list {$active}\"><a href=\"{$url}\"><i class=\"mdi mdi-star-circle-outline\"></i> {$label}</a></li>";
    }

    /** User dashboard home widget (balance card). */
    public function renderDashboardHomeWidget(mixed $user): void
    {
        if (!$user) return;

        try {
            $service     = new LoyaltyService;
            $balance     = $service->getBalance($user->id);
            $redeemRate  = (int) $this->get_option('redeem_rate', 100);
            $redeemValue = $redeemRate > 0 ? amount_with_currency_symbol(round($balance / $redeemRate, 2)) : '—';
            $url         = route('tenant.user.loyalty-points.index');

            $title      = e(__('Loyalty Points'));
            $ptsLabel   = e(__('pts'));
            $worthLabel = e(__('Redeem value'));
            $histLabel  = e(__('View History'));

            echo <<<HTML
<style>
.lp-dhw{--_bg:var(--vl-card,var(--bk-surface,var(--ch-surface,var(--fp-surface,var(--fm-surface,var(--tz-surface,var(--pf-white,var(--ph-card,#f8f9fa))))))));--_br:var(--vl-border,var(--bk-border,var(--ch-border,var(--fp-border,var(--fm-border,var(--tz-border,var(--pf-border,var(--ph-border,#e5e7eb))))))));--_tx:var(--vl-ivory,var(--bk-text,var(--ch-text,var(--fp-text,var(--fm-dark,var(--tz-text,var(--pf-dark,var(--ph-dark,#111827))))))));--_mu:var(--vl-muted,var(--bk-muted,var(--ch-muted,var(--fp-muted,var(--fm-muted,var(--tz-muted,var(--pf-muted,var(--ph-muted,#6b7280))))))));--_ac:var(--vl-champagne,var(--bk-rose,var(--ch-rose,var(--fp-purple,var(--fm-green,#f59e0b)))));background:var(--_bg);border:1px solid var(--_br);border-radius:8px;padding:20px 24px;margin-bottom:20px;display:flex;align-items:center;gap:20px}
.lp-dhw-icon{width:52px;height:52px;background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.22);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.lp-dhw-icon i{font-size:24px;color:#f59e0b}
.lp-dhw-body{flex:1;min-width:0}
.lp-dhw-label{font-size:10px;color:var(--_mu);letter-spacing:2px;text-transform:uppercase;margin-bottom:6px}
.lp-dhw-bal{font-size:28px;font-weight:300;color:var(--_tx);line-height:1;margin-bottom:3px}
.lp-dhw-bal small{font-size:13px;color:var(--_mu);margin-left:4px;font-weight:400}
.lp-dhw-worth{font-size:12px;color:var(--_mu);margin-bottom:8px}
.lp-dhw-link{font-size:11px;color:#f59e0b;text-decoration:none;letter-spacing:1px;text-transform:uppercase;border-bottom:1px solid rgba(245,158,11,.3);padding-bottom:1px}
.lp-dhw-link:hover{border-bottom-color:#f59e0b}
</style>
<div class="lp-dhw">
    <div class="lp-dhw-icon"><i class="las la-star"></i></div>
    <div class="lp-dhw-body">
        <div class="lp-dhw-label">{$title}</div>
        <div class="lp-dhw-bal">{$balance} <small>{$ptsLabel}</small></div>
        <div class="lp-dhw-worth">{$worthLabel}: {$redeemValue}</div>
        <a href="{$url}" class="lp-dhw-link">{$histLabel}</a>
    </div>
</div>
HTML;
        } catch (\Throwable) {}
    }

    public function runExpiry(): void
    {
        $expiryDays = (int) $this->get_option('point_expiry_days', 0);
        if ($expiryDays <= 0) return;

        (new LoyaltyService)->expirePoints();
    }

}
