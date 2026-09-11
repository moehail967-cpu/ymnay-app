<?php

namespace Modules\CommissionManage\Http\Controllers;

use App\Mail\BasicMail;
use App\Helpers\FlashMsg;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Wallet\Entities\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Gloudemans\Shoppingcart\Facades\Cart;
use Modules\Wallet\Entities\WalletHistory;
use App\Helpers\PaymentGatewayRenderHelper;
use App\Helpers\Payment\PaymentGatewayCredential;
use Modules\CommissionManage\Entities\CommissionHistory;
use Modules\CommissionManage\Entities\CommissionPaymentLog;
use Modules\CommissionManage\Entities\TenantWithdrawRequest;

class TenantCommissionController extends Controller
{
    private int $commission_id;
    public $custom_data = [];
    private const CANCEL_ROUTE = 'landlord.commission.payment.cancel.static';

    public function commission_payment_cancel_static()
    {
        return redirect()->route('landlord.user.wallet.history')
            ->with(['type' => 'warning', 'msg' => __('Payment cancelled')]);
    }

    /**
     * Handle successful commission payment
     * Called from IPN after payment verification
     */
    public function commission_payment_success(Request $request)
    {
        return redirect()
            ->route('landlord.user.wallet.history')
            ->with(['type' => 'success', 'msg' => __('Commission payment completed successfully')]);
    }


    /**
     * View tenant's withdraw requests
     */
    public function myWithdrawRequests()
    {
        $withdraws = TenantWithdrawRequest::where('tenant_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('commissionmanage::tenant.my-withdraws', compact('withdraws'));
    }

    /**
     * new pay and withdraw
     */

    public function index()
    {
        $userId = Auth::guard('web')->id();

        // Fetch unpaid commissions
        $unpaidCommissions = DB::table('commission_histories')
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->orderByDesc('id')
            ->get();

        // Totals
        $totalUnpaid = $unpaidCommissions->sum('commission_amount');
        $walletBalance = Wallet::where('user_id', $userId)->value('balance') ?? 0;

        return view('wallet::frontend.buyer.commission-payment-modal', compact(
            'unpaidCommissions',
            'totalUnpaid',
            'walletBalance'
        ));
    }

    /**
     * Load payment modal dynamically (AJAX)
     */
    public function showPaymentModal($id)
    {
        $userId = Auth::guard('web')->id();

        // We use 'all' to mean all unpaid commissions
        $unpaidCommissions = DB::table('commission_histories')
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->get();
        $totalUnpaid = $unpaidCommissions->sum('commission_amount');
        $walletBalance = Wallet::where('user_id', $userId)->value('balance') ?? 0;

        $showWallet = $walletBalance >= $totalUnpaid;
        $gatewayForm = PaymentGatewayRenderHelper::renderPaymentGatewayForForm();

        $html = view('wallet::frontend.buyer.commission-payment-modal', compact(
            'totalUnpaid',
            'walletBalance',
            'showWallet',
            'gatewayForm'
        ))->render();

        return response()->json(['html' => $html]);
    }


    /**
     * Handle commission payment
     */
    public function payCommission(Request $request, $id)
    {
        // dd($request->all());
        $userId = Auth::guard('web')->id();

        $unpaidCommissions = CommissionHistory::where('user_id', $userId)
            ->where('status', 'pending')
            ->get();

        $totalUnpaid = $unpaidCommissions->sum('commission_amount');

        if ($unpaidCommissions->isEmpty()) {
            return back()->with(['type' => 'info', 'msg' => __('No pending commissions found')]);
        }
        $wallet = Wallet::firstOrCreate(['user_id' => $userId], ['balance' => 0]);

        $order_id = 'COMM-' . random_int(111111,999999) . '-' . time();
        // Handle wallet payment
        if ($request->payment_gateway === 'wallet') {
            if ($wallet->balance < $totalUnpaid) {
               return back()->with('error',__('Insufficient wallet balance'));

            }
            DB::beginTransaction();

            try {
                // Deduct from wallet
               $wallet->balance -= $totalUnpaid;
                $wallet->save();


                // Create payment log
                $paymentLog = CommissionPaymentLog::create([
                    'user_id' => $userId,
                    'amount' => $totalUnpaid,
                    'payment_gateway' => 'wallet',
                    'payment_status' => 'complete',
                    'payment_date' => now(),
                    'order_id'=> $order_id,
                    'commission_ids' => $unpaidCommissions->pluck('id')->toJson(),
                ]);

                // Update commission status
                DB::table('commission_histories')
                    ->where('user_id', $userId)
                    ->where('status', 'pending')
                    ->update([
                        'status' => 'complete',
                        'paid_at' => now(),
                        'payment_log_id' => $paymentLog->id,
                        'updated_at' => now()
                    ]);

                DB::commit();

                return back()->with('success',__('Commissions paid from wallet successfully!'));
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error',__('Payment failed: ') . $e->getMessage());

            }
        }

        $payment_gateway = $request->selected_payment_gateway;

        // Store commission IDs in session for IPN
        session([
            'payment_gateway' => $payment_gateway,
            'commission_payment' => true,
            'commission_user_id' => $userId
        ]);

        // Handle manual payment
        if ($request->selected_payment_gateway === 'manual_payment') {
            if ($request->hasFile('manual_payment_image')) {
                $manual_payment_image = $request->manual_payment_image;
                $img_ext = $manual_payment_image->extension();

                $manual_payment_image_name = 'manual_attachment_' . time() . '.' . $img_ext;
                if (in_array($img_ext, ['jpg', 'jpeg', 'png', 'svg', 'pdf'])) {
                    $manual_image_path = 'assets/landlord/uploads/deposit_payment_attachments/';
                    $manual_payment_image->move($manual_image_path, $manual_payment_image_name);

                    DB::table('commission_histories')
                        ->where('user_id', $userId)
                        ->where('status', 'pending')
                        ->update(['manual_payment_image' => $manual_payment_image_name]);
                } else {
                    return back()->with(['msg' => __('image type not supported'), 'type' => 'danger']);
                }
            }

            try {
                $email = Auth::user()->email;
                $message_body = __('Hello a buyer just paid commission. Please check and confirm') . '</br>' . '<span class="verify-code">' . __('Commission IDs: ') . $unpaidCommissions->pluck('id')->implode(', ') . '</span>';
                Mail::to(get_static_option('site_global_email'))->send(new BasicMail($message_body, __('Commission Payment Confirmation')));
                Mail::to($email)->send(new BasicMail(__('Manual payment success. Your commission payment will be confirmed after admin approval'), __('Commission Payment Confirmation')));
            } catch (\Exception $e) {
                //
            }

            return back()->with(['type' => 'success', 'msg' => __('Manual payment success. Your commission payment will be confirmed after admin approval')]);

        } else {
            // Handle gateway payment
            $credential_function = 'get_' . $payment_gateway . '_credential';

            $custom_data['request'] = $request->all();
            $custom_data['payment_details'] = $unpaidCommissions->toArray();
            $custom_data['total'] = $totalUnpaid;
            $custom_data['payment_type'] = "pay_commission";
            $custom_data['payment_for'] = "landlord";
            $custom_data['cancel_url'] = route(self::CANCEL_ROUTE, random_int(111111,999999).random_int(111111,999999));
            $custom_data['success_url'] = route('landlord.commission.payment.success');

            if (!method_exists((new PaymentGatewayCredential()), $credential_function)) {
                $charge_customer_class_namespace = getChargeCustomerMethodNameByPaymentGatewayNameSpace($payment_gateway);
                $charge_customer_method_name = getChargeCustomerMethodNameByPaymentGatewayName($payment_gateway);

                $custom_charge_customer_class_object = new $charge_customer_class_namespace;
                if(class_exists($charge_customer_class_namespace) && method_exists($custom_charge_customer_class_object, $charge_customer_method_name)) {
                    Cart::instance("default")->destroy();
                    return $custom_charge_customer_class_object->$charge_customer_method_name($custom_data);
                } else {
                    return back()->with(FlashMsg::explain('danger', 'Incorrect Class or Method'));
                }
            } else {
                return $this->payment_with_gateway($request->selected_payment_gateway, $id, $custom_data);
            }
        }
    }

    public function payment_with_gateway($payment_gateway_name, $commission_id, $custom_data)
    {
        try {
            $gateway_function = 'get_' . $payment_gateway_name . '_credential';
            $gateway = PaymentGatewayCredential::$gateway_function();

            $redirect_url = $gateway->charge_customer(
                $this->common_charge_customer_data($payment_gateway_name, $custom_data)
            );

            session()->put('commission_order_id', $commission_id);
            return $redirect_url;
        } catch (\Exception $e) {
            return back()->with(['msg' => $e->getMessage(), 'type' => 'danger']);
        }
    }

    public function common_charge_customer_data($payment_gateway_name, $custom_data)
    {
        $user = Auth::guard('web')->user();
        $email = $user->email;
        $name = $user->name;

        // Generate unique order ID for commission payment
        $order_id = 'COMM-' . random_int(111111,999999) . '-' . time();

        if ($payment_gateway_name === 'paystack') {
            $ipn_route = route('landlord.user.paystack.ipn.commission');
        } else {
            $ipn_route = route('landlord.user.' . strtolower($payment_gateway_name) . '.ipn.commission');
        }

        return [
            'amount' => $custom_data['total'],
            'title' => 'Commission Payment',
            'description' => 'Commission Payment for User: ' . $name,
            'ipn_url' => $ipn_route,
            'order_id' => $order_id,
            'track' => Str::random(36),
            'cancel_url' => route(self::CANCEL_ROUTE),
            'success_url' => route('landlord.commission.payment.success'),
            'email' => $email,
            'name' => $name,
            'payment_type' => 'commission_payment',
        ];
    }



}
