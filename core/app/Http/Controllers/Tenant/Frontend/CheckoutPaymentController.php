<?php

namespace App\Http\Controllers\Tenant\Frontend;

use App\Actions\Payment\Tenant\PaymentGatewayIpn;
use App\Enums\ProductTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutFormRequest;
use App\Http\Services\CheckoutToPaymentService;
use App\Http\Services\ProductCheckoutService;
use App\Models\PricePlan;
use App\Models\ProductOrder;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\CountryManage\Entities\City;
use Modules\CountryManage\Entities\Country;
use Modules\CountryManage\Entities\State;
use Modules\TaxModule\Traits\TaxCalculatorTrait;

class CheckoutPaymentController extends Controller
{
    use TaxCalculatorTrait;
    public function checkout_page()
    {
        $account_info = Auth::guard('web')->user();
        $billing_info = $account_info?->delivery_address;
        $countries = Country::where('status', 'publish')->get();
        $states = State::where('status', 'publish')->get();

        $html = themeView('shop.checkout.checkout_page', compact('account_info', 'billing_info', 'countries', 'states'))->render();
        $html = apply_filters('nazmart:render_checkout_form', $html);

        return response($html);
    }

    public function searchState(Request $request)
    {
        $search = $request->input('q');

        $query = State::query()->where('status', 'publish');


        if ($search) {
            $query->where('name', 'LIKE', "%$search%");
        }

        $states = $query->limit(10)->get();

        return response()->json($states);
    }

    public function searchCity(Request $request)
    {
        $search = $request->input('q');

        $query = City::query()->where('status', 'publish');

        if ($search) {
            $query->where('name', 'LIKE', "%$search%");
        }

        $cities = $query->limit(10)->get();

        return response()->json($cities);
    }


    public function checkout(CheckoutFormRequest $request)
    {
        $validated_data = $request->validated();
        if($validated_data['payment_gateway'] === 'cash_on_delivery'){
            $validated_data['cash_on_delivery'] = 'on' ;
        }
        $validated_data['checkout_type'] = $validated_data['cash_on_delivery'] === 'on' ? 'cod' : 'digital';
        if (Cart::instance('default')->count() >! 0)
        {
            return back()->withErrors(__('Your cart contains no products. Please cart some products first.'));
        }

        if ($this->terminateIfUnauthenticated())
        {
            return back()->withErrors(__('Your cart contains digital products. Please login first to purchase.'));
        }

        $checkout_service = new ProductCheckoutService();
        $user = $checkout_service->getOrCreateUser($validated_data);
        if (empty($user))
        {
            return back()->withErrors(['error' => __('User exist with this username or email')]);
        }
        $order_log_id = $checkout_service->createOrder($validated_data, $user);
        // Checking shipping method is selected
        if(!$order_log_id) {
            return back()->withErrors(['error' => __('Please select a shipping method')]);
        }

        return CheckoutToPaymentService::checkoutToGateway(compact('order_log_id', 'validated_data')); // Sending multiple data compacting together in one array

    }

    private function terminateIfUnauthenticated()
    {
        if (!\auth('web')->user())
        {
            $cartData = Cart::content('default')->where('options.type', ProductTypeEnum::DIGITAL);
            return count($cartData) > 0;
        }

        return false;
    }

    public function paypal_ipn()
    {
        return (new PaymentGatewayIpn())->paypal_ipn();
    }

    public function paytm_ipn()
    {
        return (new PaymentGatewayIpn())->paytm_ipn();
    }

    public function mollie_ipn()
    {
        return (new PaymentGatewayIpn())->mollie_ipn();
    }

    public function stripe_ipn()
    {
        return (new PaymentGatewayIpn())->stripe_ipn();
    }

    public function razorpay_ipn()
    {
        return (new PaymentGatewayIpn())->razorpay_ipn();
    }

    public function payfast_ipn()
    {
        return (new PaymentGatewayIpn())->payfast_ipn();
    }

    public function flutterwave_ipn()
    {
        return (new PaymentGatewayIpn())->flutterwave_ipn();
    }

    public function paystack_ipn()
    {
        return (new PaymentGatewayIpn())->paystack_ipn();
    }

    public function midtrans_ipn()
    {
        return (new PaymentGatewayIpn())->midtrans_ipn();
    }

    public function cashfree_ipn()
    {
        return (new PaymentGatewayIpn())->cashfree_ipn();
    }

    public function instamojo_ipn()
    {
        return (new PaymentGatewayIpn())->instamojo_ipn();
    }

    public function marcadopago_ipn()
    {
        return (new PaymentGatewayIpn())->marcadopago_ipn();
    }

    public function zitopay_ipn()
    {
        return (new PaymentGatewayIpn())->zitopay_ipn();
    }

    public function billplz_ipn()
    {
        return (new PaymentGatewayIpn())->billplz_ipn();
    }

    public function paytabs_ipn()
    {
        return (new PaymentGatewayIpn())->paytabs_ipn();
    }

    public function cinetpay_ipn()
    {
        return (new PaymentGatewayIpn())->cinetpay_ipn();
    }

    public function squareup_ipn()
    {
        return (new PaymentGatewayIpn())->squareup_ipn();
    }

    public function iyzipay_ipn()
    {
        return (new PaymentGatewayIpn())->iyzipay_ipn();
    }

    public function order_payment_cancel($id)
    {
        $order_details = ProductOrder::find($id);
        return themeView('payment.payment-cancel')->with(['order_details' => $order_details]);
    }

    public function order_payment_cancel_static()
    {
        return themeView('payment.payment-cancel-static');
    }

    public function order_confirm($id)
    {
        $order_details = PricePlan::where('id', $id)->first();
        return themeView('tenant.frontend.pages.package.order-page')->with(['order_details' => $order_details]);
    }


    public function order_payment_success($id)
    {
        $extract_id = substr($id, 6);
        $extract_id = substr($extract_id, 0, -6);

        $payment_details = '';
        if (!empty($extract_id)) {
            $payment_details = ProductOrder::find($extract_id);
        }

        return themeView('payment.payment-success', compact('payment_details'));

    }
}
