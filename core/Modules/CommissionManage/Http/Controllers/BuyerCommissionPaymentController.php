<?php

namespace Modules\CommissionManage\Http\Controllers;

use App\Helpers\Payment\PaymentGatewayCredential;
use App\Mail\BasicMail;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\CommissionManage\Entities\CommissionPaymentLog;

class BuyerCommissionPaymentController extends Controller
{
    protected function cancel_page()
    {
        return redirect()->route('landlord.commission.payment.cancel.static');
    }

    // PayPal IPN
    public function paypal_ipn_for_commission()
    {
        $paypal = PaymentGatewayCredential::get_paypal_credential();
        $payment_data = $paypal->ipn_response();
        return $this->common_commission_ipn_data($payment_data);
    }

    // Paytm IPN
    public function paytm_ipn_for_commission()
    {
        $paytm = PaymentGatewayCredential::get_paytm_credential();
        $payment_data = $paytm->ipn_response();
        return $this->common_commission_ipn_data($payment_data);
    }

    // Flutterwave IPN
    public function flutterwave_ipn_for_commission()
    {
        $flutterwave = PaymentGatewayCredential::get_flutterwave_credential();
        $payment_data = $flutterwave->ipn_response();
        return $this->common_commission_ipn_data($payment_data);
    }

    // Stripe IPN
    public function stripe_ipn_for_commission()
    {
        $stripe = PaymentGatewayCredential::get_stripe_credential();
        $payment_data = $stripe->ipn_response();
        return $this->common_commission_ipn_data($payment_data);
    }

    // Razorpay IPN
    public function razorpay_ipn_for_commission()
    {
        $razorpay = PaymentGatewayCredential::get_razorpay_credential();
        $payment_data = $razorpay->ipn_response();
        return $this->common_commission_ipn_data($payment_data);
    }

    // Paystack IPN
    public function paystack_ipn_for_commission()
    {
        $paystack = PaymentGatewayCredential::get_paystack_credential();
        $payment_data = $paystack->ipn_response();
        return $this->common_commission_ipn_data($payment_data);
    }

    // Payfast IPN
    public function payfast_ipn_for_commission()
    {
        $payfast = PaymentGatewayCredential::get_payfast_credential();
        $payment_data = $payfast->ipn_response();
        return $this->common_commission_ipn_data($payment_data);
    }

    // Mollie IPN
    public function mollie_ipn_for_commission()
    {
        $mollie = PaymentGatewayCredential::get_mollie_credential();
        $payment_data = $mollie->ipn_response();
        return $this->common_commission_ipn_data($payment_data);
    }

    // Midtrans IPN
    public function midtrans_ipn_for_commission()
    {
        $midtrans = PaymentGatewayCredential::get_midtrans_credential();
        $payment_data = $midtrans->ipn_response();
        return $this->common_commission_ipn_data($payment_data);
    }

    // Cashfree IPN
    public function cashfree_ipn_for_commission()
    {
        $cashfree = PaymentGatewayCredential::get_cashfree_credential();
        $payment_data = $cashfree->ipn_response();
        return $this->common_commission_ipn_data($payment_data);
    }

    // Instamojo IPN
    public function instamojo_ipn_for_commission()
    {
        $instamojo = PaymentGatewayCredential::get_instamojo_credential();
        $payment_data = $instamojo->ipn_response();
        return $this->common_commission_ipn_data($payment_data);
    }

    // Mercadopago IPN
    public function marcadopago_ipn_for_commission()
    {
        $marcadopago = PaymentGatewayCredential::get_marcadopago_credential();
        $payment_data = $marcadopago->ipn_response();
        return $this->common_commission_ipn_data($payment_data);
    }

    // Squareup IPN
    public function squareup_ipn_for_commission()
    {
        $squareup = PaymentGatewayCredential::get_squareup_credential();
        $payment_data = $squareup->ipn_response();
        return $this->common_commission_ipn_data($payment_data);
    }

    // Cinetpay IPN
    public function cinetpay_ipn_for_commission()
    {
        $cinetpay = PaymentGatewayCredential::get_cinetpay_credential();
        $payment_data = $cinetpay->ipn_response();
        return $this->common_commission_ipn_data($payment_data);
    }

    // Paytabs IPN
    public function paytabs_ipn_for_commission()
    {
        $paytabs = PaymentGatewayCredential::get_paytabs_credential();
        $payment_data = $paytabs->ipn_response();
        return $this->common_commission_ipn_data($payment_data);
    }

    // Billplz IPN
    public function billplz_ipn_for_commission()
    {
        $billplz = PaymentGatewayCredential::get_billplz_credential();
        $payment_data = $billplz->ipn_response();
        return $this->common_commission_ipn_data($payment_data);
    }

    // Zitopay IPN
    public function zitopay_ipn_for_commission()
    {
        $zitopay = PaymentGatewayCredential::get_zitopay_credential();
        $payment_data = $zitopay->ipn_response();
        return $this->common_commission_ipn_data($payment_data);
    }

    // Toyyibpay IPN
    public function toyyibpay_ipn_for_commission()
    {
        $toyyibpay = PaymentGatewayCredential::get_toyyibpay_credential();
        $payment_data = $toyyibpay->ipn_response();
        return $this->common_commission_ipn_data($payment_data);
    }

    /**
     * Common IPN data processor for commission payments
     */
    private function common_commission_ipn_data($payment_data)
    {
        if (isset($payment_data['status']) && $payment_data['status'] === 'complete') {
            $order_id = $payment_data['order_id'];
            $transaction_id = $payment_data['transaction_id'];
            $userId = session('commission_user_id');

            if (empty($userId)) {
                return $this->cancel_page();
            }

            $this->update_commission_database($userId, $order_id, $transaction_id);
            $this->send_commission_payment_mail($userId);

            // Clear session
            session()->forget(['commission_payment', 'commission_user_id', 'payment_gateway', 'commission_order_id']);

            return redirect()
                ->route('landlord.user.wallet.history')
                ->with(['type' => 'success', 'msg' => 'Commission payment completed successfully']);
        }

        return $this->cancel_page();
    }

    /**
     * Update database after successful commission payment
     */
    private function update_commission_database($userId, $order_id, $transaction_id)
    {
        $unpaidCommissions = DB::table('commission_histories')
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->get();

        if ($unpaidCommissions->isEmpty()) {
            return;
        }

        $totalAmount = $unpaidCommissions->sum('commission_amount');

        DB::beginTransaction();

        try {
            // 1. Create Commission Payment Log
            $paymentLog = CommissionPaymentLog::create([
                'user_id' => $userId,
                'amount' => $totalAmount,
                'payment_gateway' => session('payment_gateway', 'unknown'),
                'transaction_id' => $transaction_id,
                'payment_status' => 'complete',
                'payment_date' => now(),
                'commission_ids' => $unpaidCommissions->pluck('id')->toJson(),
                'order_id'=> $order_id,
                'notes' => 'Order ID: ' . $order_id
            ]);

            // 2. Update Commission History status to complete
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

        } catch (\Exception $exception) {
            DB::rollBack();
        }
    }

    /**
     * Send email notification after commission payment
     */
    public function send_commission_payment_mail($userId)
    {
        try {
            $user = \App\Models\User::find($userId);

            if (!$user) {
                return;
            }

            $unpaidCommissions = DB::table('commission_histories')
                ->where('user_id', $userId)
                ->where('status', 'complete')
                ->orderByDesc('paid_at')
                ->limit(10)
                ->get();

            $commission_ids = $unpaidCommissions->pluck('id')->implode(', ');

            // Send email to admin
            $admin_message = __('A user just completed commission payment.') . '<br>' .
                '<span class="verify-code">' .
                __('User: ') . $user->name . '<br>' .
                __('Email: ') . $user->email . '<br>' .
                __('Commission IDs: ') . $commission_ids .
                '</span>';

            Mail::to(get_static_option('site_global_email'))
                ->send(new BasicMail($admin_message, __('Commission Payment Confirmation')));

            // Send email to user
            $user_message = __('Your commission payment has been successfully completed.') . '<br>' .
                '<span class="verify-code">' .
                __('Commission IDs: ') . $commission_ids .
                '</span>';

            Mail::to($user->email)
                ->send(new BasicMail($user_message, __('Commission Payment Confirmation')));

        } catch (\Exception $e) {
            Log::error('Commission Payment Email Error: ' . $e->getMessage());
        }
    }
}
