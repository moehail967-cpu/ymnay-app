<?php

namespace App\Http\Controllers\Landlord\Frontend;

use App\Models\User;
use App\Models\Coupon;
use App\Models\Tenant;
use App\Mail\BasicMail;
use App\Mail\PlaceOrder;
use App\Helpers\FlashMsg;
use App\Models\PricePlan;
use App\Models\FormBuilder;
use App\Models\PaymentLogs;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PaymentGateway;
use Illuminate\Support\Carbon;
use App\Models\TenantException;
use Illuminate\Validation\Rule;
use App\Enums\LandlordCouponType;
use App\Facades\ModuleDataFacade;
use App\Actions\Sms\SmsSendAction;
use App\Mail\TenantCredentialMail;
use App\Traits\PaymentUpdateTrait;
use Illuminate\Support\Facades\DB;
use App\Events\TenantRegisterEvent;
use Illuminate\Support\Facades\Log;
use Modules\Wallet\Entities\Wallet;
use App\Http\Controllers\Controller;
use App\Models\ZeroPricePlanHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Actions\Payment\PaymentGateways;
use App\Helpers\TenantHelper\TenantHelpers;
use Modules\Wallet\Entities\WalletSettings;
use Modules\Wallet\Entities\WalletTenantList;
use Modules\Wallet\Http\Services\WalletService;
use App\Helpers\Payment\PaymentGatewayCredential;
use Modules\SmsGateway\Http\Traits\OtpGlobalTrait;
use App\Services\Payment\RazorpaySubscriptionService;
use Xgenious\Paymentgateway\Facades\XgPaymentGateway;
use Modules\CommissionManage\Entities\CommissionSetting;
use Modules\CommissionManage\Http\Services\CommissionCalculatorService;
use Modules\Wallet\Http\Controllers\Frontend\BuyerWalletPaymentController;
use App\Helpers\Payment\DatabaseUpdateAndMailSend\LandlordPricePlanAndTenantCreate;



class PaymentLogController extends Controller
{
    use PaymentUpdateTrait;
    private const CANCEL_ROUTE = 'landlord.frontend.order.payment.cancel';
    private const SUCCESS_ROUTE = 'landlord.frontend.order.payment.success';

    private float $total;
    private object $payment_details;
    protected function cancel_page()
    {
        return redirect()->route('landlord.frontend.order.payment.cancel.static');
    }

    public function order_payment_form(Request $request)
    {
        // Safety: unauthenticated users cannot submit a purchase order
        if (!Auth::guard('web')->check()) {
            Log::warning('[order_payment_form] Unauthenticated request rejected', [
                'ip'      => $request->ip(),
                'referer' => $request->headers->get('referer'),
                'session' => $request->session()->getId(),
            ]);
            return redirect()->back()->with('error', __('You must be logged in to place an order.'));
        }

        //  Determine manual transaction rule based on gateway
        $manual_transection_condition = $request->selected_payment_gateway === 'manual_payment' ? 'required' : 'nullable';

        //  Fetch selected price plan
        $request_pack_id = $request->package_id;
        $plan = PricePlan::findOrFail($request_pack_id);

        /**
         * ======================================================
         *  Commission System Integration
         * ======================================================
         */
        $commissionSetting = CommissionSetting::first();
        $commissionService = app(CommissionCalculatorService::class);
        $effectivePrice = $commissionSetting
            ? $commissionService->handleSubscription($plan, $commissionSetting)
            : $plan->price;
        // Override price based on commission type
        $plan->price = $effectivePrice;

        // ======================================================
        // Handle zero/commission-only plan
        // ======================================================
        $zero_price_condition = 'required';
        if ($plan->price == 0) {
            $manual_transection_condition = 'nullable';
            $zero_price_condition = 'nullable';
            $request->selected_payment_gateway = 'manual_payment';

            $purchased_packaged = ZeroPricePlanHistory::where('user_id', Auth::guard('web')->user()->id)->count();
            $zero_plan_limit = get_static_option('zero_plan_limit');

            if ($purchased_packaged >= $zero_plan_limit) {
                Log::warning('[order_payment_form] Free plan limit exceeded', [
                    'user_id' => Auth::guard('web')->id(),
                    'count'   => $purchased_packaged,
                    'limit'   => $zero_plan_limit,
                ]);
                return back()->with(FlashMsg::explain('danger', __('Sorry! You can not purchase more free plan.')));
            }
        }

        // ======================================================
        // Validation logic
        // ======================================================
        $selected_payment_gateway = 'nullable';
        if ($request->selected_payment_gateway && !in_array($request->selected_payment_gateway, ['manual_payment', 'cash_on_delivery'])) {
            $zero_price_condition = 'nullable';
            $selected_payment_gateway = 'required';
        }

        $request->validate([
            'name' => 'nullable|string|max:191',
            'email' => 'nullable|email|max:191',
            'theme_slug' => ['required', Rule::in(getAllThemeSlug())],
            'package_id' => 'required|string',
            'payment_gateway' => $zero_price_condition . '|string',
            'selected_payment_gateway' => $selected_payment_gateway . '|string',
            'trasaction_id' => $manual_transection_condition,
            'trasaction_attachment' => $manual_transection_condition . '|mimes:jpeg,png,jpg,gif|max:2048',
            'ymnay_wallet_id' => 'required_if:selected_payment_gateway,ymnay_manual_wallet|nullable|integer',
            'ymnay_wallet_proof' => 'required_if:selected_payment_gateway,ymnay_manual_wallet|nullable|image|mimes:jpeg,jpg,png,webp|max:4096',
            'subdomain' => "required_if:custom_subdomain,!=,null",
            'custom_subdomain' => "required_if:subdomain,==,custom_domain__dd",
            'coupon' => "nullable"
        ], [
            "custom_subdomain.required_if" => __("Custom Sub Domain Required."),
            "trasaction_id" => __("Transaction ID Required."),
            "trasaction_attachment.required" => __("Transaction Attachment Required."),
            "ymnay_wallet_id.required_if" => __("Please select a wallet."),
            "ymnay_wallet_proof.required_if" => __("Transfer receipt image is required."),
            "theme_slug.in" => __("The selected theme is invalid.")
        ]);

        // ======================================================
        // Subdomain Validation
        // ======================================================
        if ($request->custom_subdomain == null) {
            $request->validate(['subdomain' => 'required']);
            $existing_lifetime_plan = PaymentLogs::where([
                'tenant_id' => $request->subdomain,
                'payment_status' => 'complete',
                'expire_date' => null
            ])->first();

            if ($existing_lifetime_plan) {
                Log::warning('[order_payment_form] Existing lifetime plan block', [
                    'user_id'   => Auth::guard('web')->id(),
                    'subdomain' => $request->subdomain,
                ]);
                return back()->with(['type' => 'danger', 'msg' => __('You are already using a lifetime plan')]);
            }
        }

        if ($request->custom_subdomain != null) {
            $has_subdomain = Tenant::find(trim($request->custom_subdomain));
            if (!empty($has_subdomain)) {
                Log::warning('[order_payment_form] Subdomain already in use', [
                    'user_id'   => Auth::guard('web')->id(),
                    'subdomain' => $request->custom_subdomain,
                ]);
                return back()->with(['type' => 'danger', 'msg' => __('This subdomain is already in use, Try something different')]);
            }

            $site_domain = str_replace(['http://', 'https://'], '', url('/'));
            $site_domain = substr($site_domain, 0, strpos($site_domain, '.'));
            $restricted_words = [
                'https',
                'http',
                'http://',
                'https://',
                'www',
                'subdomain',
                'domain',
                'primary-domain',
                'central-domain',
                'landlord',
                'landlords',
                'tenant',
                'tenants',
                'admin',
                'user',
                'users',
                $site_domain
            ];

            if (in_array(trim($request->custom_subdomain), $restricted_words)) {
                Log::warning('[order_payment_form] Restricted subdomain rejected', [
                    'user_id'   => Auth::guard('web')->id(),
                    'subdomain' => $request->custom_subdomain,
                ]);
                return back()->with(FlashMsg::explain('danger', __('Sorry, You can not use this subdomain')));
            }

            $auth_user = Auth::guard('web')->user();
            if (!empty(get_static_option('user_email_verify_status')) && !$auth_user->email_verified) {
                Log::warning('[order_payment_form] Email not verified — order blocked', [
                    'user_id'        => $auth_user->id,
                    'email_verified' => $auth_user->email_verified,
                    'subdomain'      => $request->custom_subdomain,
                ]);
                return back()->with(FlashMsg::explain('danger', __('Please verify your account first')));
            }

            // Alphanumeric check
            $sub = $request->custom_subdomain;
            if (!preg_match('/[A-Za-z0-9]/', $sub)) {
                Log::warning('[order_payment_form] Non-alphanumeric subdomain rejected', [
                    'user_id'   => Auth::guard('web')->id(),
                    'subdomain' => $sub,
                ]);
                return back()->with(FlashMsg::explain('danger', __('Sorry, You can not use this subdomain')));
            }
        }

        // ======================================================
        // Setup Payment Data
        // ======================================================
        $order_details = $plan;
        $subdomain = $request->subdomain != 'custom_domain__dd'
            ? Str::slug($request->subdomain)
            : Str::slug($request->custom_subdomain);

        $amount_to_charge = $this->applyCoupon($request->coupon, $order_details->price);
        $this->total = $amount_to_charge;

        $selected_payment_gateway = $request->selected_payment_gateway ?? $request->payment_gateway;
        if ($selected_payment_gateway == null || $amount_to_charge == 0) {
            $selected_payment_gateway = 'manual_payment';
        }

        $package_id = $request->package_id;
        $name = $request->name;
        $email = $request->email;
        $trasaction_id = $request->trasaction_id;

        // ======================================================
        // Upload Transaction Attachment
        // ======================================================
        $trasaction_attachment = null;
        if ($request->trasaction_attachment) {
            $image = $request->file('trasaction_attachment');
            $image_name = strtolower(Str::slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME)));
            $image_db = $image_name . time() . '.' . $image->extension();
            $path = global_assets_path('assets/landlord/uploads/payment_attachments/');
            $image->move($path, $image_db);
            $trasaction_attachment = $image_db;
        }

        // ======================================================
        // Payment Log Creation
        // ======================================================
        $auth = auth()->guard('web')->user();
        $auth_id = $auth->id;
        $old_tenant_log = PaymentLogs::where(['user_id' => $auth_id, 'tenant_id' => $subdomain])->latest()->first();

        $tenantHelper = TenantHelpers::init()
            ->setTenantId($subdomain)
            ->setPackage($plan)
            ->setPaymentLog($old_tenant_log)
            ->setTheme($request->theme_slug);

        $package_start_date = $tenantHelper->getStartDate();
        $package_expire_date = $tenantHelper->getExpiredDate();
        $is_tenant = Tenant::find($subdomain);

        DB::beginTransaction();
        try {
            $old_completed_log = PaymentLogs::where('user_id', $auth_id)
                ->where('tenant_id', $subdomain)
                ->where('payment_status', 'complete')
                ->latest()
                ->first();

            $is_renewal = !is_null($old_completed_log) || !is_null($is_tenant);
            $renew_status_value = $is_renewal && $old_completed_log ? (($old_completed_log->renew_status ?? 0) + 1) : null;

            $payment_log = PaymentLogs::create([
                'email' => $email,
                'name' => $name,
                'package_name' => $order_details->title,
                'package_price' => $amount_to_charge,
                'package_gateway' => $commissionSetting && $commissionSetting->commission_type === CommissionSetting::COMMISSION_TYPE_COMMISSION_ONLY
                    ? 'commission_based'
                    : $selected_payment_gateway,
                'package_id' => $package_id,
                'user_id' => $auth_id,
                'tenant_id' => $subdomain,
                'theme_slug' => $old_completed_log->theme_slug ?? $request->theme_slug,
                'status' => 'pending',
                'payment_status' => 'pending',
                'is_renew' => $is_renewal ? 1 : 0,
                'renew_status' => $renew_status_value,
                'track' => Str::random(10),
                'start_date' => $package_start_date,
                'expire_date' => $package_expire_date,
            ]);

            $this->payment_details = $payment_log;
            DB::commit();

        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Payment log creation failed', ['error' => $exception->getMessage()]);
            return back()->with('msg', __('Something went wrong'));
        }

        // ======================================================
        // Commission-only Auto-Processing (No Payment Needed)
        // ======================================================
        if ($commissionSetting && $commissionSetting->commission_type === CommissionSetting::COMMISSION_TYPE_COMMISSION_ONLY) {
            Log::info('Auto-processing commission-only subscription', ['tenant' => $subdomain]);

            // Mark log complete directly
            $payment_log->update([
                'payment_status' => 'complete',
                'status' => 'complete',
                'package_gateway' => 'commission_based',
                'transaction_id' => 'commission_auto_' . time(),
            ]);

            $this->update_tenant(['order_id' => $payment_log->id, 'transaction_id' => 'commission_only_auto'], 1);
            //            $this->send_order_mail($payment_log->id);
//            $this->tenant_create_event_with_credential_mail($payment_log->id);

            return redirect()->route(self::SUCCESS_ROUTE, wrap_random_number($payment_log->id));
        }

        // ======================================================
        // Manual Payment or Gateway Processing
        // ======================================================
        if (in_array($selected_payment_gateway, ['manual_payment', 'cash_on_delivery'])) {
            PaymentLogs::find($this->payment_details['id'])->update([
                'transaction_id' => $trasaction_id ?? '',
                'attachments' => $trasaction_attachment ?? '',
            ]);

            if ($this->payment_details['price'] == 0) {
                ZeroPricePlanHistory::create([
                    'user_id' => $this->payment_details['user_id'],
                    'plan_id' => $this->payment_details['package_id'],
                ]);
            }

            try {
                (new PaymentGateways())->send_order_mail($this->payment_details['id']);
            } catch (\Exception $e) {
            }

            return redirect()->route(self::SUCCESS_ROUTE, wrap_random_number($this->payment_details['id']));
        }

        //  Process via gateway
        return $this->payment_with_gateway($request->selected_payment_gateway, $request->all());
    }

    /**
     * Quick Pay: reuses an existing pending PaymentLog and skips order-page-new.
     */
    public function quick_pay(Request $request)
    {
        $request->validate([
            'payment_log_id' => 'required|integer',
            'payment_gateway' => 'required|string|max:100',
        ]);

        $auth_id = auth()->guard('web')->id();

        $log = PaymentLogs::where('id', $request->payment_log_id)
            ->where('user_id', $auth_id)
            ->whereNotIn('payment_status', ['complete'])
            ->whereNotIn('status', ['cancel'])
            ->first();

        if (!$log) {
            return back()->with(FlashMsg::explain('danger', __('Order not found or already completed.')));
        }

        // Update the gateway to whatever the user selected in the modal
        $log->update(['package_gateway' => $request->payment_gateway]);

        $this->payment_details = $log->fresh();
        $this->total = (float) $log->package_price;

        $gateway = $request->payment_gateway;

        if (in_array($gateway, ['manual_payment', 'cash_on_delivery'])) {
            try {
                (new PaymentGateways())->send_order_mail($log->id);
            } catch (\Exception $e) {
            }
            return redirect()->route(self::SUCCESS_ROUTE, wrap_random_number($log->id));
        }

        return $this->payment_with_gateway($gateway, $request->all());
    }

    public function payment_with_gateway($payment_gateway_name, $request = [])
    {
        try {
            $gateway_function = 'get_' . $payment_gateway_name . '_credential';

            if (!method_exists((new PaymentGatewayCredential()), $gateway_function)) {
                $custom_data['request'] = $request;
                $custom_data['payment_details'] = $this->payment_details->toArray();
                $custom_data['total'] = $this->total;
                $custom_data['payment_type'] = "price_plan";
                $custom_data['payment_for'] = "landlord";
                $custom_data['cancel_url'] = route(self::CANCEL_ROUTE, random_int(111111, 999999) . $this->payment_details['id'] . random_int(111111, 999999));
                $custom_data['success_url'] = route(self::SUCCESS_ROUTE, random_int(111111, 999999) . $this->payment_details['id'] . random_int(111111, 999999));

                $charge_customer_class_namespace = getChargeCustomerMethodNameByPaymentGatewayNameSpace($payment_gateway_name);
                $charge_customer_method_name = getChargeCustomerMethodNameByPaymentGatewayName($payment_gateway_name);

                abort_if(empty($charge_customer_method_name), 403); // If custom payment gateway not found

                $custom_charge_customer_class_object = new $charge_customer_class_namespace;
                if (class_exists($charge_customer_class_namespace) && method_exists($custom_charge_customer_class_object, $charge_customer_method_name)) {
                    return $custom_charge_customer_class_object->$charge_customer_method_name($custom_data);
                } else {
                    return back()->with(FlashMsg::explain('danger', 'Incorrect Class or Method'));
                }
            } else {
                $gateway = PaymentGatewayCredential::$gateway_function();
                //  FIXED: Check if this is a Razorpay recurring subscription
                if ($payment_gateway_name === 'razorpay') {

                    // Check if recurring is enabled AND this is not a lifetime plan
                    $plan = PricePlan::find($this->payment_details['package_id']);
                    $should_use_recurring = RazorpaySubscriptionService::shouldUseRecurring($this->payment_details['tenant_id'])
                        && $plan
                        && $plan->type != 2; // 2 = lifetime

                    if ($should_use_recurring) {
                        Log::info('Using Razorpay subscription', [
                            'tenant_id' => $this->payment_details['tenant_id'],
                            'plan_type' => $plan->type
                        ]);

                        // Update payment log with subscription flags
                        $this->payment_details->update([
                            'is_recurring' => 1,
                            'subscription_status' => 'pending'
                        ]);

                        $successUrl = route(self::SUCCESS_ROUTE, wrap_random_number($this->payment_details['id']));

                        // Prepare plan config for dynamic subscription
                        $plan_config = [
                            'id' => $plan->id,
                            'title' => $plan->title,
                            'package_description' => $plan->package_description,
                            'price' => $this->total,
                            'type' => $plan->type,
                            'razorpay_plan_id' => $plan->razorpay_plan_id ?? null
                        ];

                        // Prepare charge customer data with subscription info
                        $charge_data = $this->common_charge_customer_data($payment_gateway_name);

                        // Add subscription-specific data
                        $charge_data['is_subscription'] = true;
                        $charge_data['plan_config'] = $plan_config;

                        Log::info('Calling charge_customer with subscription data', [
                            'order_id' => $charge_data['order_id'],
                            'is_subscription' => true
                        ]);

                        $redirect_url = $gateway->charge_customer($charge_data);

                        return $redirect_url;
                    } else {
                        Log::info('Using standard Razorpay payment (recurring disabled or lifetime plan)', [
                            'tenant_id' => $this->payment_details['tenant_id'],
                            'plan_type' => $plan ? $plan->type : 'unknown',
                            'recurring_enabled' => RazorpaySubscriptionService::shouldUseRecurring($this->payment_details['tenant_id'])
                        ]);
                    }
                }

                // Regular payment process...
                $redirect_url = $gateway->charge_customer(
                    $this->common_charge_customer_data($payment_gateway_name)
                );

                return $redirect_url;

            }
        } catch (\Exception $e) {
            return back()->with(['msg' => $e->getMessage(), 'type' => 'danger']);
        }

    }

    public function common_charge_customer_data($payment_gateway_name)
    {
        $user = Auth::guard('web')->user();
        $email = $user->email;
        $name = $user->name;

        return [
            'amount' => $this->total,
            'title' => $this->payment_details['package_name'],
            'description' => 'Payment For Package Order Id: #' . $this->payment_details['id'] . ' Package Name: ' . $this->payment_details['package_name'] . ' Payer Name: ' . $this->payment_details['name'] . ' Payer Email:' . $this->payment_details['email'],
            'ipn_url' => route('landlord.frontend.' . strtolower($payment_gateway_name) . '.ipn', $this->payment_details['id']),
            'order_id' => $this->payment_details['id'],
            'track' => Str::random(36),
            'cancel_url' => route(self::CANCEL_ROUTE, random_int(111111, 999999) . $this->payment_details['id'] . random_int(111111, 999999)), // o
            'success_url' => route(self::SUCCESS_ROUTE, random_int(111111, 999999) . $this->payment_details['id'] . random_int(111111, 999999)),
            'email' => $email,
            'name' => $name,
            'payment_type' => 'order',
        ]; // eta emnei newa
    }


    // IPNs
    public function paypal_ipn()
    {
        $paypal = PaymentGatewayCredential::get_paypal_credential();
        $payment_data = $paypal->ipn_response();

        // todo: Implement it to every ipn method
        try {
            return $this->common_ipn_data($payment_data);
        } catch (\Exception $e) {

        }
    }

    public function paytm_ipn()
    {
        $paytm = PaymentGatewayCredential::get_paytm_credential();
        $payment_data = $paytm->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function flutterwave_ipn()
    {
        $flutterwave = PaymentGatewayCredential::get_flutterwave_credential();
        $payment_data = $flutterwave->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function stripe_ipn()
    {
        $stripe = PaymentGatewayCredential::get_stripe_credential();

        $payment_data = $stripe->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function razorpay_ipn()
    {
        $razorpay = PaymentGatewayCredential::get_razorpay_credential();
        $payment_data = $razorpay->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function paystack_ipn()
    {
        $paystack = PaymentGatewayCredential::get_paystack_credential();
        $payment_data = $paystack->ipn_response();

        return $this->paystack_action($payment_data);
    }

    public function payfast_ipn()
    {
        $payfast = PaymentGatewayCredential::get_payfast_credential();
        $payment_data = $payfast->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function mollie_ipn()
    {
        $mollie = PaymentGatewayCredential::get_mollie_credential();

        // todo: Implement it to every ipn method
//        try{
        $payment_data = $mollie->ipn_response();
        return $this->common_ipn_data($payment_data);
        //        }catch(\Exception $e){
//            $this->cancel_page();
//        }
    }

    public function midtrans_ipn()
    {
        $midtrans = PaymentGatewayCredential::get_midtrans_credential();
        $payment_data = $midtrans->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function cashfree_ipn(Request $request)
    {
        Log::info('Cashfree Webhook Received:', $request->all());

        $cashfree = PaymentGatewayCredential::get_cashfree_credential();
        $payment_data = $cashfree->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function instamojo_ipn()
    {
        $instamojo = PaymentGatewayCredential::get_instamojo_credential();
        $payment_data = $instamojo->ipn_response();
        return $this->common_ipn_data($payment_data);
    }
    public function marcadopago_ipn()
    {
        $marcadopago = PaymentGatewayCredential::get_marcadopago_credential();

        try {
            $payment_data = $marcadopago->ipn_response();
            return $this->common_ipn_data($payment_data);
        } catch (\Exception $exception) {
            $this->cancel_page();
        }
    }
    public function squareup_ipn()
    {
        $squareup = PaymentGatewayCredential::get_squareup_credential();
        $payment_data = $squareup->ipn_response();
        return $this->common_ipn_data($payment_data);
    }
    public function cinetpay_ipn()
    {
        $cinetpay = PaymentGatewayCredential::get_cinetpay_credential();
        $payment_data = $cinetpay->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function kineticpay_ipn()
    {
        $kineticpay = PaymentGatewayCredential::get_kineticpay_credential();
        $payment_data = $kineticpay->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function paytabs_ipn()
    {
        $paytabs = PaymentGatewayCredential::get_paytabs_credential();
        $payment_data = $paytabs->ipn_response();
        return $this->common_ipn_data($payment_data);
    }
    public function billplz_ipn()
    {
        $billplz = PaymentGatewayCredential::get_billplz_credential();
        $payment_data = $billplz->ipn_response();
        return $this->common_ipn_data($payment_data);
    }
    public function zitopay_ipn()
    {
        $zitopay = PaymentGatewayCredential::get_zitopay_credential();
        $payment_data = $zitopay->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function iyzipay_ipn()
    {
        $iyzipay = PaymentGatewayCredential::get_iyzipay_credential();
        $payment_data = $iyzipay->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function awdpay_ipn()
    {
        $awdpay = PaymentGatewayCredential::get_awdpay_credential();
        $payment_data = $awdpay->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function sslcommerz_ipn()
    {
        $sslcommerz = PaymentGatewayCredential::get_sslcommerz_credential();
        $payment_data = $sslcommerz->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    public function toyyibpay_ipn()
    {
        $toyyibpay = PaymentGatewayCredential::get_toyyibpay_credential();
        $payment_data = $toyyibpay->ipn_response();
        return $this->common_ipn_data($payment_data);
    }

    private function paystack_action($data)
    {
        if ($data['type'] === 'deposit') {
            return (new BuyerWalletPaymentController())->paystack_common_ipn_data($data);
        } else {
            return $this->common_ipn_data($data);
        }
    }

    private function common_ipn_data($payment_data)
    {
        //        dd($payment_data);
        if (isset($payment_data['status']) && $payment_data['status'] === 'complete') {
            try {
                $this->update_database($payment_data['order_id'], $payment_data['transaction_id'], $payment_data['razorpay_subscription_id'] ?? null);
                $this->send_order_mail($payment_data['order_id']);
                $this->tenant_create_event_with_credential_mail($payment_data['order_id']);

                // Get the current payment log (force fresh query to ensure latest subscription ID)
                $current_payment_log = PaymentLogs::find($payment_data['order_id']);

                Log::info('Payment log loaded for tenant update', [
                    'order_id' => $payment_data['order_id'],
                    'razorpay_subscription_id' => $current_payment_log->razorpay_subscription_id ?? 'none',
                    'is_recurring' => $current_payment_log->is_recurring ?? 'none'
                ]);

                // Calculate renewal_after_expire_date based on OLD payment log
                $renewal_after_expire_date = null;

                if ($current_payment_log && $current_payment_log->tenant_id) {
                    // Find the OLD payment log (previous one before this renewal)
                    $old_payment_log = PaymentLogs::where('tenant_id', $current_payment_log->tenant_id)
                        ->where('user_id', $current_payment_log->user_id)
                        ->where('payment_status', 'complete')
                        ->latest()
                        ->first();
                    // Check if old payment log exists and has expire_date
                    if ($old_payment_log && $old_payment_log->expire_date) {
                        if (Carbon::parse($old_payment_log->expire_date)->isFuture()) {
                            $renewal_after_expire_date = 0;
                        } else {
                            $renewal_after_expire_date = 1;
                        }
                    }
                }
                // Add it to payment_data array
                $renewal_after_expire_date_payment_data = $renewal_after_expire_date;


                $this->update_tenant($payment_data, $renewal_after_expire_date_payment_data);

            } catch (\Exception $exception) {
                $message = $exception->getMessage();
                if (str_contains($message, 'Access denied')) {
                    if (request()->ajax()) {
                        abort(462, __('Database created failed, Make sure your database user has permission to create database'));
                    }
                }

                $payment_details = PaymentLogs::where('id', $payment_data['order_id'])->first();
                if (empty($payment_details)) {
                    abort(500, __('Does not exist, Tenant does not exists'));
                }
                LandlordPricePlanAndTenantCreate::store_exception($payment_details->tenant_id, 'Domain create', $exception->getMessage(), 0);

                //todo: send an email to admin that this user database could not able to create automatically

                try {
                    $message = sprintf(
                        __('Database Creating failed for user id %1$s , please checkout admin panel and generate database for this user from admin panel manually'),
                        $payment_details->user_id
                    );
                    $subject = sprintf(__('Database Crating failed for user id %1$s'), $payment_details->user_id);
                    Mail::to(get_static_option_central('site_global_email'))->send(new BasicMail($message, $subject));

                } catch (\Exception $e) {
                    LandlordPricePlanAndTenantCreate::store_exception($payment_details->tenant_id, 'domain failed email', $e->getMessage(), 0);
                }
            }

            $order_id = wrap_random_number($payment_data['order_id']);
            return redirect()->route(self::SUCCESS_ROUTE, $order_id);
        }

        return $this->cancel_page();
    }

    public function send_order_mail($order_id)
    {
        $package_details = PaymentLogs::where('id', $order_id)->first();
        $all_fields = [];
        unset($all_fields['package']);
        $all_attachment = [];
        $order_mail = get_static_option('order_page_form_mail') ? get_static_option('order_page_form_mail') : get_static_option('site_global_email');

        try {
            Mail::to($order_mail)->send(new PlaceOrder($all_fields, $all_attachment, $package_details, "admin", 'regular'));
            Mail::to($package_details->email)->send(new PlaceOrder($all_fields, $all_attachment, $package_details, 'user', 'regular'));

        } catch (\Exception $e) {
            return redirect()->back()->with(['type' => 'danger', 'msg' => $e->getMessage()]);
        }
    }

    //    public function tenant_create_event_with_credential_mail($order_id)
//    {
//        $log = PaymentLogs::findOrFail($order_id);
//        if (empty($log))
//        {
//            abort(462,__('Does not exist, Tenant does not exists'));
//        }
//
//        $user = User::where('id', $log->user_id)->first();
//        $tenant = Tenant::find($log->tenant_id);
//
//        if (!empty($log) && $log->payment_status == 'complete' && is_null($tenant)) {
////            event(new TenantRegisterEvent($user, $log->tenant_id, $log->theme_slug));
//            Tenant::create(['id' => $log->tenant_id]);
//            try {
//                $raw_pass = get_static_option_central('tenant_admin_default_password') ??'12345678';
//                $credential_password = $raw_pass;
//                $credential_email = $user->email;
//                $credential_username = get_static_option_central('tenant_admin_default_username') ?? 'super_admin';
//
//                Mail::to($credential_email)->send(new TenantCredentialMail($credential_username, $credential_password));
//            } catch (\Exception $e) {}
//
//        } else if (!empty($log) && $log->payment_status == 'complete' && !is_null($tenant) && $log->is_renew == 0) {
//            try {
//                $raw_pass = get_static_option_central('tenant_admin_default_password') ?? '12345678';
//                $credential_password = $raw_pass;
//                $credential_email = $user->email;
//                $credential_username = get_static_option_central('tenant_admin_default_username') ?? 'super_admin';
//
//                Mail::to($credential_email)->send(new TenantCredentialMail($credential_username, $credential_password));
//            } catch (\Exception $exception) {
//                $message = $exception->getMessage();
//                if(str_contains($message,'Access denied')){
//                    abort(463,__('Database created failed, Make sure your database user has permission to create database'));
//                }
//            }
//        }
//
//        return true;
//    }

    public function tenant_create_event_with_credential_mail($order_id)
    {
        //        dd($order_id);
        $log = PaymentLogs::findOrFail($order_id);
        $user = User::where('id', $log->user_id)->first();
        $tenant = Tenant::find($log->tenant_id);

        if (!empty($log) && $log->payment_status == 'complete' && is_null($tenant)) {
            event(new TenantRegisterEvent($user, $log->tenant_id, $log->theme_slug));
            try {
                $raw_pass = get_static_option('tenant_admin_default_password') ?? '12345678';
                $credential_password = $raw_pass;
                $credential_email = $user->email;
                $credential_username = get_static_option('tenant_admin_default_username') ?? 'super_admin';

                Mail::to($credential_email)->send(new TenantCredentialMail($credential_username, $credential_password));

            } catch (\Exception $e) {

            }

        } else if (!empty($log) && $log->payment_status == 'complete' && !is_null($tenant) && $log->is_renew == 0) {
            try {
                $raw_pass = get_static_option('tenant_admin_default_password') ?? '12345678';
                $credential_password = $raw_pass;
                $credential_email = $user->email;
                $credential_username = get_static_option('tenant_admin_default_username') ?? 'super_admin';

                Mail::to($credential_email)->send(new TenantCredentialMail($credential_username, $credential_password));

            } catch (\Exception $e) {

            }
        }

        return true;
    }

    public function applyCoupon($code, $package_price)
    {
        if (empty($code)) {
            return $package_price;
        }

        $coupon_info = Coupon::published()->active()->where('code', $code)
            ->select('code', 'discount_type', 'discount_amount')
            ->first();

        if ($coupon_info) {
            $coupon_info['discount_type'] = LandlordCouponType::getText($coupon_info->discount_type);

            $new_price = 0;
            if ($coupon_info->discount_type === 'percentage') {
                $percent_amount = ($coupon_info->discount_amount / 100) * $package_price;
                $new_price = $package_price - $percent_amount;
            } else {
                $new_price = $package_price - $coupon_info->discount_amount;
            }

            return $new_price;
        }

        return $package_price;
    }

    /**
     * Handle Razorpay subscription initial payment authorization
     * This is called when the subscription's first payment is authorized
     */
    public function razorpay_subscription_ipn(Request $request)
    {
        try {
            $razorpay = PaymentGatewayCredential::get_razorpay_credential();

            // Get the subscription ID from the request
            $subscription_id = $request->input('payload.subscription.entity.id');
            $order_id = $request->input('payload.payment.entity.notes.payment_log_id');

            // Verify webhook signature
            $signature = $request->header('X-Razorpay-Signature');
            if (!$razorpay->verify_webhook_signature($request->all(), $signature)) {
                Log::warning('Invalid Razorpay subscription webhook signature');
                return response()->json(['status' => 'invalid'], 401);
            }

            // Handle subscription.charged event (renewal)
            if ($request->input('event') === 'subscription.charged') {

                RazorpaySubscriptionService::handleSubscriptionCharged($request->all());
                return response()->json(['status' => 'success']);
            }

            // If order_id provided, update the initial payment log
            if ($order_id) {
                $payment_data = [
                    'status' => 'complete',
                    'order_id' => $order_id,
                    'transaction_id' => $request->input('payload.payment.entity.id'),
                    'payment_amount' => $request->input('payload.payment.entity.amount')
                ];

                return $this->common_ipn_data($payment_data);
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Razorpay subscription IPN processing failed', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);
            return response()->json(['status' => 'error'], 500);
        }
    }
}
