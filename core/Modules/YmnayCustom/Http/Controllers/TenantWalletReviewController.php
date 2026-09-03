<?php

namespace Modules\YmnayCustom\Http\Controllers;

use App\Http\Controllers\Tenant\Admin\OrderManageController;
use App\Models\ProductOrder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\YmnayCustom\Support\WalletNotifier;

class TenantWalletReviewController extends Controller
{
    public function approve(Request $request, ProductOrder $order)
    {
        $this->guardWalletOrder($order);
        $this->updateReviewMeta($order, 'approved');
        $request->merge(['order_id' => (string) $order->id, 'order_status' => 'complete', 'payment_status' => 'success']);
        $response = app(OrderManageController::class)->change_status($request);
        WalletNotifier::sms($order->phone, __('Your payment for order #:order has been approved.', ['order' => $order->id]), $order->id);
        return $response;
    }

    public function reject(Request $request, ProductOrder $order)
    {
        $this->guardWalletOrder($order);
        $data = $request->validate(['rejection_reason' => ['required', 'string', 'max:1000']]);
        $this->updateReviewMeta($order, 'rejected', $data['rejection_reason']);
        $request->merge(['order_id' => (string) $order->id, 'order_status' => 'cancel', 'payment_status' => 'failed']);
        $response = app(OrderManageController::class)->change_status($request);
        $subject = __('Your payment was rejected');
        $message = __('Payment for order #:order was rejected.', ['order' => $order->id]) . '<br>' . __('Reason:') . ' ' . e($data['rejection_reason']);
        WalletNotifier::email($order->email, $subject, $message);
        WalletNotifier::sms($order->phone, strip_tags($message), $order->id);
        return $response;
    }

    private function guardWalletOrder(ProductOrder $order): void
    {
        abort_unless($order->payment_gateway === 'ymnay_manual_wallet', 404);
        abort_if($order->payment_status === 'success' || in_array($order->status, ['complete', 'cancel'], true), 422, __('This payment has already been reviewed.'));
    }

    private function updateReviewMeta(ProductOrder $order, string $status, ?string $reason = null): void
    {
        $meta = json_decode((string) $order->payment_meta, true);
        $meta = is_array($meta) ? $meta : [];
        $meta['ymnay_manual_wallet']['review_status'] = $status;
        $meta['ymnay_manual_wallet']['reviewed_at'] = now()->toIso8601String();
        $meta['ymnay_manual_wallet']['rejection_reason'] = $reason;
        $order->update(['payment_meta' => json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)]);
    }
}
