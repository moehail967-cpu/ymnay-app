<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Mail\BasicMail;
use App\Models\PaymentLogs;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;


class MyPackageOrderController extends Controller
{
    /**
     * Fetch plans from the landlord (central) DB.
     *
     * Stancl Tenancy reassigns the `mysql` connection at runtime to point at the
     * tenant's database, so CentralConnection / PricePlan::all() would silently
     * query the tenant DB.  env() values are never mutated by Stancl, so we use
     * them to build an explicit landlord connection and query price_plans directly.
     */
    public function update_other_settings()
    {
        // Register a temporary connection using the original landlord env values.
        config([
            'database.connections.landlord_central' => [
                'driver' => 'mysql',
                'host' => env('DB_HOST', '127.0.0.1'),
                'port' => env('DB_PORT', '3306'),
                'database' => env('DB_DATABASE', ''),
                'username' => env('DB_USERNAME', ''),
                'password' => env('DB_PASSWORD', ''),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => false,
            ]
        ]);

        $db = DB::connection('landlord_central');

        // Fetch active plans
        $plansRaw = $db->table('price_plans')
            ->where('status', 1)
            ->orderBy('price')
            ->get();

        // Attach features for each plan
        $planIds = $plansRaw->pluck('id')->toArray();
        $features = [];
        if (!empty($planIds)) {
            $db->table('plan_features')
                ->whereIn('plan_id', $planIds)
                ->get()
                ->each(function ($f) use (&$features) {
                    $features[$f->plan_id][] = $f;
                });
        }

        $plans = $plansRaw->map(function ($plan) use ($features) {
            $plan->plan_features = collect($features[$plan->id] ?? []);
            return $plan;
        });

        $current_log = PaymentLogs::where('id', tenant()->renewal_payment_log_id)->first();
        $current_package_id = optional($current_log)->package_id;

        // Disconnect the temporary connection so it doesn't persist
        DB::disconnect('landlord_central');

        return view('tenant.admin.my-orders.buy-plan', compact('plans', 'current_package_id', 'current_log'));
    }

    /**
     * Show in-dashboard checkout page for a specific plan.
     */
    public function plan_checkout(Request $request, $plan_id)
    {
        config([
            'database.connections.landlord_central' => [
                'driver' => 'mysql',
                'host' => env('DB_HOST', '127.0.0.1'),
                'port' => env('DB_PORT', '3306'),
                'database' => env('DB_DATABASE', ''),
                'username' => env('DB_USERNAME', ''),
                'password' => env('DB_PASSWORD', ''),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => false,
            ]
        ]);

        $db = DB::connection('landlord_central');

        $plan = $db->table('price_plans')
            ->where('id', $plan_id)
            ->where('status', 1)
            ->first();

        if (!$plan) {
            DB::disconnect('landlord_central');
            abort(404, 'Plan not found.');
        }

        $plan->plan_features = $db->table('plan_features')
            ->where('plan_id', $plan_id)
            ->get();

        // Fetch active payment gateways from landlord DB
        $gateways = $db->table('payment_gateways')
            ->where('status', 1)
            ->whereNotIn('name', ['manual_payment', 'cash_on_delivery'])
            ->select(['name', 'image'])
            ->get();

        DB::disconnect('landlord_central');

        return view('tenant.admin.my-orders.plan-checkout', compact('plan', 'gateways'));
    }

    /**
     * Generate a signed HMAC token and redirect to the landlord upgrade endpoint.
     */
    public function initiate_checkout(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|integer',
            'gateway' => 'required|string|max:100',
        ]);

        $tenantId = tenant()->id;
        $planId = $request->plan_id;
        $gateway = $request->gateway;
        $timestamp = now()->timestamp;

        $sig = hash_hmac(
            'sha256',
            implode(':', [$tenantId, $planId, $gateway, $timestamp]),
            config('app.key')
        );

        // Build success/cancel URLs on THIS tenant domain
        $successUrl = route('tenant.my.package.payment.success');
        $cancelUrl = route('tenant.my.package.order.buy.plan');

        $token = base64_encode(json_encode([
            'tenant_id' => $tenantId,
            'plan_id' => $planId,
            'gateway' => $gateway,
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'ts' => $timestamp,
            'sig' => $sig,
        ]));

        // Landlord upgrade endpoint — use env('APP_URL') since Stancl never mutates env
        $landlordUrl = rtrim(env('APP_URL', url('/')), '/') . '/landlord/tenant-upgrade?token=' . urlencode($token);

        return redirect($landlordUrl);
    }

    /**
     * Show in-dashboard payment success page after returning from the gateway.
     */
    public function payment_success(Request $request)
    {
        $log = null;
        if ($request->filled('log_id')) {
            $log = PaymentLogs::find($request->log_id);
        }
        return view('tenant.admin.my-orders.payment-success', compact('log'));
    }

    public function my_payment_logs()
    {
        $package_orders = tenant()->payment_log()->get();
        $current_package = PaymentLogs::where('id', tenant()->renewal_payment_log_id)->first();

        return view('tenant.admin.my-orders.package-order')->with(['package_orders' => $package_orders, 'current_package' => $current_package]);
    }

    public function generate_package_invoice(Request $request)
    {
        $payment_details = PaymentLogs::with('user')->find($request->id);

        if (!$payment_details) {
            return abort(404);
        }

        $user = $payment_details->user;

        $pdf = Pdf::loadView('tenant.frontend.invoice.order', [
            'payment_details' => $payment_details,
            'user' => $user,
        ])->setOptions(['defaultFont' => 'sans-serif']);

        return $pdf->stream('invoice.pdf');
    }

    public function package_order_cancel(Request $request)
    {
        $this->validate($request, [
            'package_id' => 'required'
        ]);

        $user = tenant()->user()->first()->payment_log();
        $order_details = $user->where('id', $request->package_id)->first();

        if (empty($order_details)) {
            return abort(404);
        }

        //send mail to admin
        $order_page_form_mail = get_static_option('order_page_form_mail');
        $order_mail = $order_page_form_mail ? $order_page_form_mail : get_static_option('site_global_email');
        $order_details->status = 'cancel';
        $order_details->save();
        //send mail to customer
        $data['subject'] = __('one of your package order has been cancelled');
        $data['message'] = __('hello') . '<br>';
        $data['message'] .= __('your package order ') . ' #' . $order_details->id . ' ';
        $data['message'] .= __('has been cancelled by the user');

        //send mail while order status change
        try {
            Mail::to($order_mail)->send(new BasicMail($data['message'], $data['subject']));
        } catch (\Exception $e) {
            //handle error
            return redirect()->back()->with(['msg' => __('Order Cancel, mail send failed'), 'type' => 'warning']);
        }
        if (!empty($order_details)) {
            //send mail to customer
            $data['subject'] = __('your order status has been cancel');
            $data['message'] = __('hello') . '<br>';
            $data['message'] .= __('your order') . ' #' . $order_details->id . ' ';
            $data['message'] .= __('status has been changed to cancel');
            try {
                //send mail while order status change
                Mail::to($order_details->email)->send(new BasicMail($data['message'], $data['subject']));
            } catch (\Exception $e) {
                //handle error
                return redirect()->back()->with(['msg' => __('Order Cancel, mail send failed'), 'type' => 'warning']);
            }

        }
        return redirect()->back()->with(['msg' => __('Order Cancel'), 'type' => 'warning']);
    }
}
