<?php

namespace Modules\GdprExport\Src;

use App\PluginSystem\PluginBase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\GdprExport\Http\Controllers\GdprAdminController;
use Modules\GdprExport\Http\Controllers\GdprFrontendController;

class GdprExportPlugin extends PluginBase
{
    public function id(): string
    {
        return 'gdpr-export';
    }

    // ── Called during route loading phase ─────────────────────────────────────

    public function routes(): void
    {
        $this->registerSettings();
        $this->registerRoutes();
    }

    // ── Called during application boot ────────────────────────────────────────

    public function boot(): void
    {
        view()->addNamespace('gdpr-export', $this->plugin_path('resources/views'));

        $this->registerSettings();

        if (!function_exists('tenant') || !tenant()) return;

        $this->schedule('daily', [$this, 'cleanupExpiredExports']);
        $this->registerMenus();
        $this->registerHooks();
    }

    // ── Lifecycle ─────────────────────────────────────────────────────────────

    public function on_activate(): void
    {
        $this->run_migrations();
        $this->update_option('expiry_hours', '48');
        $this->update_option('notify_admin', '1');
    }

    public function on_deactivate(): void
    {
        // Leave files and DB rows intact — customer data must not vanish on deactivation
    }

    public function on_update(string $from_version): void
    {
        $this->run_migrations();
    }

    // ── Private: route registration ───────────────────────────────────────────

    private function registerRoutes(): void
    {
        $this->register_web_routes(function () {
            // Frontend user routes — full tenant middleware stack (mirrors the outer group in tenant.php)
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
                ->prefix('user-home/my-data')
                ->name('tenant.user.gdpr-export.')
                ->group(function () {
                    \Route::get('/', [GdprFrontendController::class, 'index'])->name('index');
                    \Route::post('/request', [GdprFrontendController::class, 'request'])->name('request');
                    \Route::get('/download/{token}', [GdprFrontendController::class, 'download'])->name('download');
                });

            // Admin routes — full tenant admin middleware stack (mirrors tenant_admin.php outer group)
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
                ->prefix('admin-home/gdpr-export')
                ->name('tenant.admin.gdpr-export.')
                ->group(function () {
                    \Route::get('/', [GdprAdminController::class, 'index'])->name('index');
                    \Route::post('/{id}/status', [GdprAdminController::class, 'updateStatus'])->name('updateStatus');
                });
        });
    }

    private function registerSettings(): void
    {
        $this->register_settings([
            ['key' => 'expiry_hours', 'label' => 'Download link expires after (hours)', 'type' => 'number', 'default' => '48'],
            ['key' => 'notify_admin', 'label' => 'Notify admin on new request',          'type' => 'toggle', 'default' => '1'],
        ]);
    }

    private function registerMenus(): void
    {
        $this->add_menu([
            'id'      => 'gdpr-export-menu',
            'label'   => __('GDPR Requests'),
            'icon'    => 'mdi-shield-account-outline',
            'route'   => 'tenant.admin.gdpr-export.index',
            'order'   => 90,
            'context' => 'tenant',
        ]);
    }

    private function registerHooks(): void
    {
        $this->add_action('nazmart:user_dashboard_sidebar', [$this, 'renderSidebarItem'], 10);
    }

    // ── Scheduled ─────────────────────────────────────────────────────────────

    public function cleanupExpiredExports(): void
    {
        $tenantId = $this->currentTenantId();

        $query = DB::table('gdpr_export_requests')
            ->where('status', 'ready')
            ->where('expires_at', '<', now());

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $expired = $query->get();

        foreach ($expired as $req) {
            try {
                if ($req->file_path && Storage::exists($req->file_path)) {
                    Storage::delete($req->file_path);
                }
                DB::table('gdpr_export_requests')
                    ->where('id', $req->id)
                    ->update(['status' => 'expired', 'file_path' => null]);
            } catch (\Throwable $e) {
                Log::channel('plugin')->warning("[gdpr-export] cleanup [{$req->id}]: {$e->getMessage()}");
            }
        }
    }

    // ── Hook callbacks ─────────────────────────────────────────────────────────

    public function renderSidebarItem(): void
    {
        $active = request()->routeIs('tenant.user.gdpr-export.*') ? 'active' : '';
        $url    = route('tenant.user.gdpr-export.index');
        $label  = __('My Data (GDPR)');
        echo "<li class=\"list {$active}\"><a href=\"{$url}\"><i class=\"mdi mdi-shield-account-outline\"></i> {$label}</a></li>";
    }

    private function currentTenantId(): ?int
    {
        try {
            return function_exists('tenant') && tenant() ? (int) tenant()->id : null;
        } catch (\Throwable) {
            return null;
        }
    }
}
