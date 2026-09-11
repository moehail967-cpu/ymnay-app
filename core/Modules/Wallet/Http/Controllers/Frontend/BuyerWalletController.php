<?php

namespace Modules\Wallet\Http\Controllers\Frontend;


use App\Enums\PaymentRouteEnum;
use App\Helpers\FlashMsg;
use App\Helpers\ThemeMetaData;
use App\Helpers\Payment\PaymentGatewayCredential;
use App\Mail\BasicMail;
use App\Models\PaymentGateway;
use App\Models\ProductOrder;
use App\Models\User;
use FontLib\Table\Type\name;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Modules\CommissionManage\Entities\CommissionHistory;
use Modules\CommissionManage\Entities\CommissionPaymentLog;
use Modules\CommissionManage\Entities\CommissionSetting;
use Modules\CommissionManage\Entities\TenantWithdrawRequest;
use Modules\CommissionManage\Entities\WithdrawGateway;
use Modules\Wallet\Entities\Wallet;
use Modules\Wallet\Entities\WalletHistory;
use Modules\Wallet\Entities\WalletSettings;
use Modules\Wallet\Entities\WalletTenantList;
use Modules\Wallet\Http\Services\WalletService;
use Xgenious\Paymentgateway\Facades\XgPaymentGateway;

class BuyerWalletController extends Controller
{
    private const CANCEL_ROUTE = 'landlord.user.wallet.deposit.payment.cancel.static';
    private float $total;
    private string $title;
    private string $description;
    private int $last_deposit_id;

    function __construct()
    {
        abort_if(empty(get_static_option('user_wallet')), 404);
    }

    public function deposit_payment_cancel_static()
    {
        return view('wallet::frontend.buyer.payment-cancel-static');
    }

    public function deposit_payment_success()
    {
        return back()->with(['type' => 'success', 'Your Deposit is Successful']);
    }

    public function wallet_history()
{
    $userId = Auth::guard('web')->user()->id;

    // Wallet histories and balance
    $wallet_histories = WalletHistory::latest()
        ->where('user_id', $userId)
        ->whereIn('payment_status', ['complete', 'pending'])
        ->paginate(5);

    $balance = Wallet::select('balance')
        ->where('user_id', $userId)
        ->first();

    $commissionSetting = CommissionSetting::first();

//    dd($withdrawGateways);
    // All metrics from CommissionHistory in single query
    $commissionData = DB::table('commission_histories')->where('user_id', $userId)
        ->selectRaw('
            SUM(order_total) AS total_earning,
            SUM(commission_amount) AS total_commission,
            COUNT(DISTINCT order_id) AS total_orders,
            SUM(CASE WHEN status = "complete" THEN 1 ELSE 0 END) AS completed_orders,
            SUM(CASE WHEN MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW()) THEN order_total ELSE 0 END) AS monthly_earning,
            SUM(CASE WHEN status = "complete" THEN commission_amount ELSE 0 END) AS paid_commission,
            SUM(CASE WHEN status = "pending" THEN commission_amount ELSE 0 END) AS unpaid_commission,
            SUM(CASE WHEN payment_status = "pending" THEN commission_amount ELSE 0 END) AS pending_commission
        ')
        ->first();

    // Monthly earnings breakdown for chart (last 7 months)
    $monthlyEarningsRaw = DB::table('commission_histories')
        ->where('user_id', $userId)
        ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(order_total) as total")
        ->whereRaw('created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)')
        ->groupByRaw("DATE_FORMAT(created_at, '%Y-%m')")
        ->orderByRaw("DATE_FORMAT(created_at, '%Y-%m')")
        ->pluck('total', 'month');

    $monthlyEarningsLabels = [];
    $monthlyEarningsValues = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = now()->subMonths($i);
        $key = $date->format('Y-m');
        $monthlyEarningsLabels[] = $date->format('M');
        $monthlyEarningsValues[] = (float) ($monthlyEarningsRaw[$key] ?? 0);
    }

    $totalEarning = $commissionData->total_earning ?? 0;
    $totalCommission = $commissionData->total_commission ?? 0;
    $totalOrders = $commissionData->total_orders ?? 0;
    $monthlyEarning = $commissionData->monthly_earning ?? 0;
    $netEarning = $totalEarning - $totalCommission;
    $completedOrders = $commissionData->completed_orders ?? 0;
    $paidCommission = $commissionData->paid_commission ?? 0;
    $unpaidCommission = $commissionData->unpaid_commission ?? 0;
    $pendingCommission = $commissionData->pending_commission ?? 0;

    // Commission payment logs with pagination
    $commission_payment_logs = CommissionPaymentLog::where('user_id', $userId)
        ->latest()
        ->paginate(5);
    $withdraw_request_logs = TenantWithdrawRequest::where('tenant_id', $userId)
        ->latest()
        ->paginate(5);
        $withdraw_gateways = WithdrawGateway::where('commission_setting_id', $commissionSetting->id)
            ->where('status', true)
            ->orderBy('sort_order')
            ->get();
    // return view('wallet::frontend.buyer.wallet-history', compact(
    //     'wallet_histories',
    //     'balance',
    //     'totalEarning',
    //     'netEarning',
    //     'totalOrders',
    //     'completedOrders',
    //     'monthlyEarning',
    //     'commissionSetting',
    //     'totalCommission',
    //     'paidCommission',
    //     'unpaidCommission',
    //     'pendingCommission',
    //     'commission_payment_logs',
    //     'withdraw_request_logs',
    //     'withdraw_gateways',
    // ));
    return view('wallet::frontend.new-buyer.my_wallet', compact(
        'wallet_histories',
        'balance',
        'totalEarning',
        'netEarning',
        'totalOrders',
        'completedOrders',
        'monthlyEarning',
        'commissionSetting',
        'totalCommission',
        'paidCommission',
        'unpaidCommission',
        'pendingCommission',
        'commission_payment_logs',
        'withdraw_request_logs',
        'withdraw_gateways',
        'monthlyEarningsLabels',
        'monthlyEarningsValues',
    ));
}
    public function deposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:10|max:5000',
        ]);
        if ($request->selected_payment_gateway === 'manual_payment') {
            $request->validate([
                'manual_payment_image' => 'required|mimes:jpg,jpeg,png,svg,pdf'
            ],
                [
                    'The manual payment image must be a file of type - jpg, jpeg, png, svg and pdf.'
                ]);

            $file_extention = $request->manual_payment_image->getClientOriginalExtension();
            $imageTypes = ['jpeg', 'png', 'jpg', 'svg', 'pdf'];
            if (!in_array($file_extention, $imageTypes)) {
                return back()->withErrors('Please insert a valid image attachment');
            }
        }

        //deposit amount
        $this->total = $request->amount;

        $user = Auth::guard('web')->user();
        $user_id = $user->id;
        $name = $user->name;
        $email = $user->email;
        if ($request->selected_payment_gateway == 'manual_payment') {
            $payment_status = 'pending';
        } else {
            $payment_status = '';
        }

        $buyer = Wallet::where('user_id', $user_id)->first();
        if (empty($buyer)) {
            Wallet::create([
                'user_id' => $user_id,
                'balance' => 0,
                'status' => 0,
            ]);
        }

        $deposit = WalletHistory::create([
            'user_id' => $user_id,
            'amount' => $this->total,
            'payment_gateway' => $request->selected_payment_gateway,
            'payment_status' => $payment_status,
            'transaction_id' => $request->transaction_id,
            'status' => 1,
        ]);

        $this->last_deposit_id = $deposit->id;
        $this->title = __('Deposit To Wallet');
        $this->description = sprintf(__('Order id #%1$d Email: %2$s, Name: %3$s'), $this->last_deposit_id, $email, $name);

        $payment_gateway = $request->selected_payment_gateway;
        if ($request->selected_payment_gateway === 'manual_payment') {
            if ($request->hasFile('manual_payment_image')) {
                $manual_payment_image = $request->manual_payment_image;
                $img_ext = $manual_payment_image->extension();

                $manual_payment_image_name = 'manual_attachment_' . time() . '.' . $img_ext;
                if (in_array($img_ext, ['jpg', 'jpeg', 'png', 'svg', 'pdf'])) {
                    $manual_image_path = 'assets/landlord/uploads/deposit_payment_attachments/';
                    $manual_payment_image->move($manual_image_path, $manual_payment_image_name);
                    WalletHistory::where('id', $this->last_deposit_id)->update([
                        'manual_payment_image' => $manual_payment_image_name
                    ]);
                } else {
                    return back()->with(['msg' => __('image type not supported'), 'type' => 'danger']);
                }
            }

            try {
                $message_body = __('Hello a buyer just deposit to his wallet. Please check and confirm') . '</br>' . '<span class="verify-code">' . __('Deposit ID: ') . $this->last_deposit_id . '</span>';
                Mail::to(get_static_option('site_global_email'))->send(new BasicMail($message_body, __('Deposit Confirmation')));
                Mail::to($email)->send(new BasicMail(__('Manual deposit success. Your wallet will credited after admin approval #') . $this->last_deposit_id, __('Deposit Confirmation')));
            } catch (\Exception $e) {
                //
            }

            return back()->with(['type' => 'success', 'msg' => 'Manual deposit success. Your wallet will credited after admin approval']);

        } else {
            $credential_function = 'get_' . $payment_gateway . '_credential';

            if (!method_exists((new PaymentGatewayCredential()), $credential_function))
            {
                $custom_data['request'] = $request->all();
                $custom_data['payment_details'] = $deposit->toArray();
                $custom_data['total'] = $this->total;
                $custom_data['payment_type'] = "deposit";
                $custom_data['payment_for'] = "landlord";
                $custom_data['cancel_url'] = route(self::CANCEL_ROUTE, random_int(111111,999999).$custom_data['payment_details']['id'].random_int(111111,999999));
                $custom_data['success_url'] = route('landlord.user.wallet.history');

                $charge_customer_class_namespace = getChargeCustomerMethodNameByPaymentGatewayNameSpace($payment_gateway);
                $charge_customer_method_name = getChargeCustomerMethodNameByPaymentGatewayName($payment_gateway);
//dd($charge_customer_method_name);
                $custom_charge_customer_class_object = new $charge_customer_class_namespace;
                if(class_exists($charge_customer_class_namespace) && method_exists($custom_charge_customer_class_object, $charge_customer_method_name))
                {
                    Cart::instance("default")->destroy();
//                    dd($custom_charge_customer_class_object->$charge_customer_method_name($custom_data));
                    return $custom_charge_customer_class_object->$charge_customer_method_name($custom_data);
                } else {
                    return back()->with(FlashMsg::explain('danger', 'Incorrect Class or Method'));
                }
            } else {
//                dd($this->payment_with_gateway($request->selected_payment_gateway));
                return $this->payment_with_gateway($request->selected_payment_gateway);
            }
        }
    }

    public function payment_with_gateway($payment_gateway_name)
    {
        try {
            $gateway_function = 'get_' . $payment_gateway_name . '_credential';
            $gateway = PaymentGatewayCredential::$gateway_function();

            $redirect_url = $gateway->charge_customer(
                $this->common_charge_customer_data($payment_gateway_name, route('landlord.admin.wallet.buyer.'.strtolower($payment_gateway_name).'.ipn'))
            );
//            dd($redirect_url);

            session()->put('order_id', $this->last_deposit_id);
            return $redirect_url;
        } catch (\Exception $e) {
            return back()->with(['msg' => $e->getMessage(), 'type' => 'danger']);
        }
    }

    public function common_charge_customer_data($payment_gateway_name,$ipn_url): array
    {
        $user = Auth::guard('web')->user();
        $email = $user->email;
        $name = $user->name;
        $ipn_route = $ipn_url;
        return [
            'amount' => $this->total,
            'title' => $this->title,
            'description' => $this->description,
            'ipn_url' => $ipn_route,
            'order_id' => $this->last_deposit_id,
            'track' => Str::random(36),
            'cancel_url' => route(self::CANCEL_ROUTE, $this->last_deposit_id),
            'success_url' => route('landlord.user.wallet.deposit.payment.success'),
            'email' => $email,
            'name' => $name,
            'payment_type' => 'deposit',
        ];
    }

    public function wallet_settings()
    {
        $balance = Wallet::select('balance')->where('user_id', Auth::guard('web')->user()->id)->first();
        $settings = WalletSettings::where('user_id', Auth::guard('web')->user()->id)->first();
        // return view('wallet::frontend.buyer.wallet-settings', compact('settings', 'balance'));
        return view('wallet::frontend.new-buyer.setting', compact('settings', 'balance'));
    }

    public function wallet_settings_update(Request $request)
    {
        $request->validate([
            'renewal_using_wallet' => 'nullable',
            'tenants_list' => '' . $request->renewal_using_wallet != null ? 'required' : 'nullable' . '',
            'wallet_balance_alert' => 'nullable',
            'minimum_alert_amount' => '' . $request->wallet_balance_alert != null ? 'required' : 'nullable' . ''
        ], [
            'tenants_list.required' => 'The subdomains are required',
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::guard('web')->user();
            $settings = WalletSettings::updateOrCreate(
                [
                    'user_id' => $user->id
                ],
                [
                    'user_id' => $user->id,
                    'renew_package' => $request->renewal_using_wallet == 'on',
                    'wallet_alert' => $request->wallet_balance_alert == 'on',
                    'minimum_amount' => $request->minimum_alert_amount
                ]
            );

            $tenant_list = WalletTenantList::where('user_id', $user->id)->get();
            if (count($tenant_list) > 0)
            {
                WalletTenantList::where('user_id', $user->id)->delete();
            }

            foreach ($request->tenants_list ?? [] as $tenant) {
                WalletTenantList::insert([
                    'user_id' => $settings->user_id,
                    'tenant_id' => $tenant,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();
            return back()->with(FlashMsg::update_succeed('Wallet Settings'));
        } catch (\Exception $exception)
        {
            DB::rollBack();
            return back()->with($exception->getMessage());
        }
    }

    // wallet_withdraw
    public function deposit_withdraw(){
        $userId = Auth::guard('web')->user()->id;

        $withdraw_request_logs = TenantWithdrawRequest::where('tenant_id', $userId)
            ->latest()
            ->paginate(5);
        // return view('wallet::frontend.buyer.withdraw_request_logs', compact('withdraw_request_logs'));
        return view('wallet::frontend.new-buyer.withdrawal_money', compact('withdraw_request_logs'));
    }
    //pay_commission
    public function pay_commission(Request $request){
        $userId = Auth::guard('web')->user()->id;
        $commission_history = CommissionHistory::where('user_id', $userId)->get();

        $query = CommissionPaymentLog::where('user_id', $userId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', '%' . $search . '%')
                  ->orWhere('id', 'like', '%' . $search . '%')
                  ->orWhere('payment_gateway', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        $commission_payment_logs = $query->latest()->paginate(5)->appends($request->query());

        $unpaidCommission = $commission_history->where('status', 'pending')->sum('commission_amount');
        $walletBalance = Wallet::where('user_id', $userId)->value('balance') ?? 0;
        $showWallet = $walletBalance >= $unpaidCommission && $unpaidCommission > 0;

        return view('wallet::frontend.new-buyer.commission_payment', compact(
            'commission_payment_logs',
            'commission_history',
            'unpaidCommission',
            'walletBalance',
            'showWallet'
        ));
    }

    public function new_withdrawal()
    {
        $userId = Auth::guard('web')->user()->id;
        $balance = Wallet::select('balance')->where('user_id', $userId)->first();
        $commissionSetting = CommissionSetting::first();
        $withdraw_gateways = WithdrawGateway::where('commission_setting_id', $commissionSetting->id)
            ->where('status', true)
            ->orderBy('sort_order')
            ->get();
        return view('wallet::frontend.new-buyer.new_withdrawal', compact('balance', 'withdraw_gateways'));
    }
}
