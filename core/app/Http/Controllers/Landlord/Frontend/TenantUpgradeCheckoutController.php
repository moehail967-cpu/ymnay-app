<?php

namespace App\Http\Controllers\Landlord\Frontend;

use App\Helpers\Payment\PaymentGatewayCredential;
use App\Helpers\TenantHelper\TenantHelpers;
use App\Http\Controllers\Controller;
use App\Models\PaymentLogs;
use App\Models\PricePlan;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TenantUpgradeCheckoutController extends Controller
{
    /**
     * Validate a signed tenant-upgrade token, create a PaymentLog,
     * and redirect the browser to the payment gateway.
     *
     * Token payload (base64-encoded JSON):
     * {
     *   "tenant_id":   "shop1",
     *   "plan_id":     5,
     *   "gateway":     "paypal",
     *   "success_url": "https://shop1.example.com/admin-home/package/payment/success",
     *   "cancel_url":  "https://shop1.example.com/admin-home/package/buy-plan",
     *   "ts":          1717891200,
     *   "sig":         "hmac-hex"
     * }
     *
     * The sig = hash_hmac('sha256', "{tenant_id}:{plan_id}:{gateway}:{ts}", APP_KEY)
     * Token is valid for 15 minutes.
     */
    public function initiate(Request $request)
    {
        // ── 1. Decode & validate token ───────────────────────────────────────
        $rawToken = $request->query('token', '');
        $payload  = json_decode(base64_decode($rawToken), true);

        if (!$this->validateToken($payload)) {
            abort(403, 'Invalid or expired upgrade token.');
        }

        $tenantId   = $payload['tenant_id'];
        $planId     = (int) $payload['plan_id'];
        $gateway    = $payload['gateway'];
        $successUrl = $payload['success_url'];
        $cancelUrl  = $payload['cancel_url'];

        // ── 2. Load tenant & landlord user ───────────────────────────────────
        $tenant = Tenant::find($tenantId);
        if (!$tenant) {
            abort(404, 'Tenant not found.');
        }

        $user = User::find($tenant->user_id);
        if (!$user) {
            abort(404, 'Tenant user not found.');
        }

        // Authenticate for this request so gateway helpers can access the user
        Auth::guard('web')->setUser($user);

        // ── 3. Fetch price plan ───────────────────────────────────────────────
        $plan = PricePlan::find($planId);
        if (!$plan || !$plan->status) {
            abort(404, 'Plan not found or inactive.');
        }

        $price = (float) $plan->price;

        // ── 4. Build PaymentLog ───────────────────────────────────────────────
        $old_log = PaymentLogs::where([
            'user_id'   => $user->id,
            'tenant_id' => $tenantId,
        ])->latest()->first();

        $tenantHelper = TenantHelpers::init()
            ->setTenantId($tenantId)
            ->setPackage($plan)
            ->setPaymentLog($old_log)
            ->setTheme($tenant->theme_slug ?? 'default');

        $old_completed_log = PaymentLogs::where('user_id', $user->id)
            ->where('tenant_id', $tenantId)
            ->where('payment_status', 'complete')
            ->latest()
            ->first();

        $is_renewal       = !is_null($old_completed_log) || !is_null($tenant);
        $renew_status_val = ($is_renewal && $old_completed_log)
            ? (($old_completed_log->renew_status ?? 0) + 1)
            : null;

        $payment_log = PaymentLogs::create([
            'email'           => $user->email,
            'name'            => $user->name,
            'package_name'    => $plan->title,
            'package_price'   => $price,
            'package_gateway' => $gateway,
            'package_id'      => $plan->id,
            'user_id'         => $user->id,
            'tenant_id'       => $tenantId,
            'theme_slug'      => $old_completed_log->theme_slug ?? ($tenant->theme_slug ?? 'default'),
            'status'          => 'pending',
            'payment_status'  => 'pending',
            'is_renew'        => $is_renewal ? 1 : 0,
            'renew_status'    => $renew_status_val,
            'track'           => Str::random(10),
            'start_date'      => $tenantHelper->getStartDate(),
            'expire_date'     => $tenantHelper->getExpiredDate(),
        ]);

        // ── 5. Handle manual/zero-price ──────────────────────────────────────
        if ($price == 0 || in_array($gateway, ['manual_payment', 'cash_on_delivery'])) {
            return redirect($successUrl . (str_contains($successUrl, '?') ? '&' : '?') . 'log_id=' . $payment_log->id);
        }

        // ── 6. Charge via payment gateway ─────────────────────────────────────
        $charge_data = [
            'amount'       => $price,
            'title'        => $plan->title,
            'description'  => 'Plan upgrade — tenant: ' . $tenantId . ' | Order #' . $payment_log->id,
            'ipn_url'      => route('landlord.frontend.' . strtolower($gateway) . '.ipn', $payment_log->id),
            'order_id'     => $payment_log->id,
            'track'        => Str::random(36),
            'cancel_url'   => $cancelUrl,
            'success_url'  => $successUrl . (str_contains($successUrl, '?') ? '&' : '?') . 'log_id=' . $payment_log->id,
            'email'        => $user->email,
            'name'         => $user->name,
            'payment_type' => 'order',
        ];

        try {
            $gateway_function    = 'get_' . $gateway . '_credential';
            $gatewayInstance     = PaymentGatewayCredential::$gateway_function();
            return $gatewayInstance->charge_customer($charge_data);
        } catch (\Exception $e) {
            Log::error('[TenantUpgradeCheckout] Gateway error', [
                'tenant'  => $tenantId,
                'gateway' => $gateway,
                'error'   => $e->getMessage(),
            ]);
            return redirect($cancelUrl)->with('error', __('Payment initiation failed. Please try again.'));
        }
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function validateToken(?array $payload): bool
    {
        if (
            empty($payload['tenant_id']) ||
            empty($payload['plan_id'])   ||
            empty($payload['gateway'])   ||
            empty($payload['ts'])        ||
            empty($payload['sig'])
        ) {
            return false;
        }

        // Expire after 15 minutes
        if (Carbon::now()->timestamp - (int) $payload['ts'] > 900) {
            return false;
        }

        $expected = hash_hmac(
            'sha256',
            implode(':', [
                $payload['tenant_id'],
                $payload['plan_id'],
                $payload['gateway'],
                $payload['ts'],
            ]),
            config('app.key')
        );

        return hash_equals($expected, $payload['sig']);
    }
}
