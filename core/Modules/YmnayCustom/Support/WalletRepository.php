<?php

namespace Modules\YmnayCustom\Support;

use Modules\YmnayCustom\Entities\ManualWallet;

class WalletRepository
{
    public static function activeCatalog()
    {
        try {
            return ManualWallet::query()->where('status', true)->orderBy('sort_order')->orderBy('id')->get();
        } catch (\Throwable) {
            return collect();
        }
    }

    public static function tenantSettings(): array
    {
        $settings = json_decode((string) get_static_option('ymnay_manual_wallet_settings', '[]'), true);
        return is_array($settings) ? $settings : [];
    }

    public static function availableForCheckout(): array
    {
        $catalog = self::activeCatalog();
        if (!tenant()) {
            return $catalog->map(fn (ManualWallet $wallet) => self::catalogSnapshot($wallet))->values()->all();
        }

        $settings = self::tenantSettings();
        return $catalog->filter(function (ManualWallet $wallet) use ($settings) {
            return !empty($settings[$wallet->id]['enabled']);
        })->map(function (ManualWallet $wallet) use ($settings) {
            $configured = $settings[$wallet->id];
            return [
                'id' => $wallet->id,
                'name' => $wallet->name,
                'logo_url' => $wallet->logo_url,
                'description' => trim((string) ($configured['instructions'] ?? '')),
                'account_number' => trim((string) ($configured['account_number'] ?? '')),
                'recipient_name' => trim((string) ($configured['recipient_name'] ?? '')),
            ];
        })->values()->all();
    }

    public static function checkoutSnapshot(int $walletId): ?array
    {
        foreach (self::availableForCheckout() as $wallet) {
            if ((int) $wallet['id'] === $walletId) {
                return $wallet;
            }
        }

        return null;
    }

    private static function catalogSnapshot(ManualWallet $wallet): array
    {
        return [
            'id' => $wallet->id,
            'name' => $wallet->name,
            'logo_url' => $wallet->logo_url,
            'description' => $wallet->description,
            'account_number' => '',
            'recipient_name' => '',
        ];
    }
}
