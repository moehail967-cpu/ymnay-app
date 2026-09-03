<?php

namespace Modules\YmnayCustom\Http\Controllers;

use App\Http\Controllers\Landlord\Admin\OrderManageController;
use App\Models\PaymentLogs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\YmnayCustom\Support\WalletNotifier;

class LandlordWalletReviewController extends Controller
{
    public function approve(Request $request, PaymentLogs $order)
    {
        $this->guardWalletOrder($order);
        $this->updateReviewMeta($order, 'approved');
        $response = app(OrderManageController::class)->payment_logs_approve($request, $order->id);
        $order->refresh()->update(['status' => 'complete']);
        WalletNotifier::sms($order->user?->mobile, __('Your Ymnay package payment #:order has been approved.', ['order' => $order->id]), $order->id);
        return $response;
    }

    public function reject(Request $request, PaymentLogs $order)
    {
        $this->guardWalletOrder($order);
        $data = $request->validate(['rejection_reason' => ['required', 'string', 'max:1000']]);
        $this->updateReviewMeta($order, 'rejected', $data['rejection_reason']);
        $order->update(['status' => 'cancel', 'payment_status' => 'failed']);
        $subject = __('Your Ymnay package payment was rejected');
        $message = __('Payment for package order #:order was rejected.', ['order' => $order->id]) . '<br>' . __('Reason:') . ' ' . e($data['rejection_reason']);
        WalletNotifier::email($order->email, $subject, $message);
        WalletNotifier::sms($order->user?->mobile, strip_tags($message), $order->id);
        return back()->with(['type' => 'success', 'msg' => __('Payment rejected and the customer was notified.')]);
    }

    private function guardWalletOrder(PaymentLogs $order): void
    {
        abort_unless($order->package_gateway === 'ymnay_manual_wallet', 404);
        abort_if($order->payment_status === 'complete' || in_array($order->status, ['complete', 'cancel'], true), 422, __('This payment has already been reviewed.'));
    }

    private function updateReviewMeta(PaymentLogs $order, string $status, ?string $reason = null): void
    {
        $custom = json_decode((string) $order->custom_fields, true);
        $custom = is_array($custom) ? $custom : [];
        $custom['ymnay_manual_wallet']['review_status'] = $status;
        $custom['ymnay_manual_wallet']['reviewed_at'] = now()->toIso8601String();
        $custom['ymnay_manual_wallet']['rejection_reason'] = $reason;
        $order->update(['custom_fields' => json_encode($custom, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)]);
    }
}
