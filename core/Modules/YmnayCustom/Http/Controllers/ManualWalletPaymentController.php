<?php

namespace Modules\YmnayCustom\Http\Controllers;

use App\Actions\Payment\PaymentGateways;
use App\Actions\Payment\Tenant\PaymentGatewayIpn;
use App\Enums\PaymentRouteEnum;
use App\Models\PaymentLogs;
use App\Models\ProductOrder;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\YmnayCustom\Support\WalletRepository;

class ManualWalletPaymentController extends Controller
{
    public function chargeCustomer(array $args)
    {
        return ($args['payment_for'] ?? '') === 'tenant'
            ? $this->tenantCheckout($args)
            : $this->landlordCheckout($args);
    }

    private function landlordCheckout(array $args)
    {
        request()->validate([
            'ymnay_wallet_id' => ['required', 'integer'],
            'ymnay_wallet_proof' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
        ]);

        $snapshot = WalletRepository::checkoutSnapshot((int) request('ymnay_wallet_id'));
        if (!$snapshot) throw ValidationException::withMessages(['ymnay_wallet_id' => __('The selected wallet is unavailable.')]);

        $image = request()->file('ymnay_wallet_proof');
        $filename = 'wallet-proof-' . Str::uuid() . '.' . $image->extension();
        $path = global_assets_path('assets/landlord/uploads/payment_attachments/');
        if (!is_dir($path)) mkdir($path, 0755, true);
        $image->move($path, $filename);

        $log = PaymentLogs::findOrFail($args['payment_details']['id']);
        $custom = json_decode((string) $log->custom_fields, true);
        $custom = is_array($custom) ? $custom : [];
        $custom['ymnay_manual_wallet'] = [
            'wallet' => $snapshot,
            'proof' => $filename,
            'submitted_at' => now()->toIso8601String(),
            'review_status' => 'pending',
        ];
        $log->update([
            'attachments' => $filename,
            'transaction_id' => 'YMW-' . strtoupper(Str::random(12)),
            'custom_fields' => json_encode($custom, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        try { (new PaymentGateways())->send_order_mail($log->id); } catch (\Throwable) {}
        return redirect($args['success_url']);
    }

    private function tenantCheckout(array $args)
    {
        $order = ProductOrder::findOrFail($args['payment_details']['id']);
        $meta = json_decode((string) $order->payment_meta, true);
        if (empty($meta['ymnay_manual_wallet'])) {
            throw ValidationException::withMessages(['ymnay_wallet_id' => __('Wallet payment details were not captured.')]);
        }
        $order->update(['status' => 'pending', 'payment_status' => 'pending']);
        try { (new PaymentGatewayIpn())->send_order_mail($order->id); } catch (\Throwable) {}
        return redirect()->route(PaymentRouteEnum::SUCCESS_ROUTE, Str::random(6) . $order->id . Str::random(6));
    }
}
