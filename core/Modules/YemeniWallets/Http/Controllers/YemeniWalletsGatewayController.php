<?php

namespace Modules\YemeniWallets\Http\Controllers;

use App\Enums\PaymentRouteEnum;
use App\Http\Controllers\Controller;
use App\Models\ProductOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\YemeniWallets\Entities\WalletPaymentProof;
use Modules\YemeniWallets\Src\PluginOptions;

/**
 * Customer-facing. Lists the tenant active wallets, handles the mandatory
 * screenshot upload, and sets the order to "awaiting verification" status.
 *
 * Integrated via module.json nazmartMetaData.paymentGateway so that
 * CheckoutToPaymentService dispatches to chargeCustomer() instead of
 * calling a PaymentGatewayCredential IPN method.
 */
class YemeniWalletsGatewayController extends Controller
{
    /**
     * Called by CheckoutToPaymentService when payment_gateway === 'yemeni_wallets'.
     *
     * Receives $custom_data array (built in CheckoutToPaymentService::checkoutToGateway()):
     *   order_id, total, payment_type, success_url, cancel_url, request, payment_for.
     *
     * Redirects the customer to the proof-submission page.
     * The ProductOrder already exists with payment_status = 'pending'.
     */
    public function chargeCustomer(array $custom_data): RedirectResponse
    {
        $order_id = $custom_data['order_id'] ?? null;

        if (! $order_id) {
            return redirect()->route('homepage')->withErrors(__('Order not found.'));
        }

        // Explicitly stamp the gateway name on the order row so the tenant
        // admin order list shows "Yemeni E-Wallets" instead of blank.
        $order = ProductOrder::find($order_id);
        if ($order) {
            $order->payment_gateway = 'yemeni_wallets';
            $order->payment_status  = 'pending';
            $order->save();
        }

        // Redirect the customer to upload their payment screenshot.
        return redirect()->route('yemeniwallets.submit-proof-page', ['order_id' => $order_id]);
    }

    /**
     * JSON endpoint: returns the tenant's active wallets and their account
     * values for the checkout widget (fetched via AJAX).
     */
    public function paymentGateway(): JsonResponse
    {
        $catalog     = collect(PluginOptions::getGlobal('catalog', []))->keyBy('id');
        $activations = PluginOptions::get('activated_wallets', []);

        $wallets = collect($activations)
            ->filter(fn ($activation) => $activation['is_active'] ?? false)
            ->map(function ($activation, $catalogWalletId) use ($catalog) {
                $catalogWallet = $catalog->get($catalogWalletId);
                if (! $catalogWallet || ! ($catalogWallet['status'] ?? false)) {
                    return null;
                }

                return [
                    'catalog_wallet_id' => $catalogWalletId,
                    'name'              => $catalogWallet['name'],
                    'logo'              => $catalogWallet['logo'] ? asset('storage/' . $catalogWallet['logo']) : null,
                    'values'            => $activation['values'],
                ];
            })
            ->filter()
            ->values();

        return response()->json(['wallets' => $wallets]);
    }

    /**
     * Displays the proof-submission page the customer is redirected to
     * after choosing Yemeni E-Wallets at checkout.
     */
    public function submitProofPage(Request $request): \Illuminate\View\View
    {
        $order_id = $request->query('order_id');
        $order    = ProductOrder::find($order_id);
        abort_if(! $order, 404);

        // Reuse paymentGateway() but extract the array directly.
        $wallets = $this->paymentGateway()->getData(true)['wallets'] ?? [];

        return view('yemeni_wallets::checkout.submit-proof', compact('order', 'wallets'));
    }

    /**
     * POST: handles the screenshot upload from the submit-proof page.
     *
     * Rules enforced:
     *  - payment_proof is REQUIRED (validation with custom message).
     *  - catalog_wallet_id must exist in the active catalog.
     *
     * After saving the proof the order stays at payment_status = 'pending'
     * until the tenant admin approves it via TenantWalletController::approveProof().
     */
    public function submitProof(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'order_id'          => ['required', 'integer'],
            'catalog_wallet_id' => ['required', 'string'],
            'payment_proof'     => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ], [
            'payment_proof.required' => __('Please attach a screenshot of your transfer before completing the order.'),
            'payment_proof.image'    => __('The payment proof must be an image file (jpg, png, webp).'),
            'payment_proof.max'      => __('The screenshot file size must not exceed 5 MB.'),
        ]);

        $catalog       = collect(PluginOptions::getGlobal('catalog', []));
        $catalogWallet = $catalog->firstWhere('id', $data['catalog_wallet_id']);
        abort_if(! $catalogWallet, 404, __('Selected wallet is no longer available.'));

        $path = $request->file('payment_proof')->store(
            'yemeni-wallets/payment-proofs/' . $data['order_id'],
            'public'
        );

        $proof = WalletPaymentProof::create([
            'order_id'            => $data['order_id'],
            'catalog_wallet_id'   => $data['catalog_wallet_id'],
            'wallet_name'         => $catalogWallet['name'],
            'screenshot_path'     => $path,
            'verification_status' => 'pending',
        ]);

        // Ensure the order row is stamped correctly.
        // payment_status remains 'pending' — tenant admin will set it to 'success'.
        $order = ProductOrder::find($data['order_id']);
        if ($order) {
            $order->payment_gateway = 'yemeni_wallets';
            $order->payment_status  = 'pending';
            $order->save();
        }

        $message = __('Your order was submitted successfully. It is awaiting payment verification and you will be notified shortly.');

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $message, 'proof_id' => $proof->id]);
        }

        // Redirect to the platform's standard order-success page.
        $wrapped_id = wrap_random_number($data['order_id']);
        return redirect()
            ->route(PaymentRouteEnum::SUCCESS_ROUTE, $wrapped_id)
            ->with('wallet_proof_pending', $message);
    }
}
