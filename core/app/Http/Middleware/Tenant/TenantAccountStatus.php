<?php

namespace App\Http\Middleware\Tenant;

use App\Models\PaymentLogs;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TenantAccountStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $tenant = tenant();

        // Get the tenant's renewal payment log, if it exists
        $tenant_log = PaymentLogs::find($tenant->renewal_payment_log_id);

        // Fallback: find any trial log linked to this tenant (handles legacy records
        // where renewal_payment_log_id was not set during trial creation)
        if (!$tenant_log) {
            $tenant_log = PaymentLogs::where('tenant_id', $tenant->id)
                ->where('status', 'trial')
                ->latest()
                ->first();
        }

        // Check if tenant's subscription has not expired
        $isActive = $tenant->expire_date && Carbon::parse($tenant->expire_date)->isFuture();

        // Check if payment is complete
        $isPaid = $tenant_log && $tenant_log->payment_status === 'complete';

        // Check if tenant is in trial
        $isTrial = $tenant_log && $tenant_log->status === 'trial';

        // Allow access if tenant is active, paid, or in trial
        if ($isActive || $isPaid || $isTrial) {
            return $next($request);
        }

        // Otherwise, redirect to restricted page
        return redirect()->route('tenant.admin.restricted');
    }
}
