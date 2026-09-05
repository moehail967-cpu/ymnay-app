<?php

namespace Modules\YmnayCustom\Support;

use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TenantWalletOrder
{
    public static function capture(array $orderData): array
    {
        if (($orderData['payment_gateway'] ?? null) !== 'ymnay_manual_wallet') {
            return $orderData;
        }

        request()->validate([
            'ymnay_wallet_id' => ['required', 'integer'],
            'ymnay_wallet_account' => ['required', 'string', 'max:100'],
            'ymnay_wallet_proof' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
        ], [
            'ymnay_wallet_id.required' => __('يرجى اختيار المحفظة.'),
            'ymnay_wallet_account.required' => __('يرجى اختيار رقم المحفظة والعملة.'),
            'ymnay_wallet_proof.required' => __('صورة سند التحويل مطلوبة.'),
        ]);

        $snapshot = WalletRepository::checkoutSnapshot((int) request('ymnay_wallet_id'), (string) request('ymnay_wallet_account'));
        if (!$snapshot) {
            throw ValidationException::withMessages(['ymnay_wallet_id' => __('المحفظة أو رقم التحويل المحدد غير متاح.')]);
        }

        $image = request()->file('ymnay_wallet_proof');
        $filename = 'wallet-proof-' . Str::uuid() . '.' . $image->extension();
        $path = global_assets_path('assets/uploads/ymnay-wallet-proofs/');
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        $image->move($path, $filename);

        $meta = json_decode((string) ($orderData['payment_meta'] ?? '{}'), true);
        $meta = is_array($meta) ? $meta : [];
        $meta['ymnay_manual_wallet'] = [
            'wallet' => $snapshot,
            'proof' => $filename,
            'submitted_at' => now()->toIso8601String(),
            'review_status' => 'pending',
        ];

        $orderData['checkout_image_path'] = $filename;
        $orderData['payment_meta'] = json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $orderData['transaction_id'] = 'YMW-' . strtoupper(Str::random(12));
        $orderData['status'] = 'pending';
        $orderData['payment_status'] = 'pending';

        return $orderData;
    }
}
