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
            $accounts = self::normalizeAccounts($configured);
            return [
                'id' => $wallet->id,
                'name' => $wallet->name,
                'logo_url' => $wallet->logo_url,
                'description' => trim((string) ($configured['instructions'] ?? '')),
                'recipient_name' => trim((string) ($configured['recipient_name'] ?? '')),
                'accounts' => $accounts,
            ];
        })->values()->all();
    }

    public static function checkoutSnapshot(int $walletId, ?string $accountKey = null): ?array
    {
        foreach (self::availableForCheckout() as $wallet) {
            if ((int) $wallet['id'] === $walletId) {
                if (!tenant()) {
                    return $wallet;
                }

                foreach ($wallet['accounts'] as $account) {
                    if ((string) $account['key'] === (string) $accountKey) {
                        $wallet['account_number'] = $account['account_number'];
                        $wallet['currency'] = $account['currency'];
                        $wallet['account_description'] = $account['description'];
                        $wallet['selected_account'] = $account;
                        return $wallet;
                    }
                }

                return null;
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
            'accounts' => [],
        ];
    }

    private static function normalizeAccounts(array $configured): array
    {
        $accounts = [];
        foreach ((array) ($configured['accounts'] ?? []) as $key => $account) {
            $currency = trim((string) ($account['currency'] ?? ''));
            $number = trim((string) ($account['account_number'] ?? ''));
            if ($currency === '' || $number === '') {
                continue;
            }
            $accounts[] = [
                'key' => (string) $key,
                'currency' => $currency,
                'account_number' => $number,
                'description' => trim((string) ($account['description'] ?? '')),
            ];
        }

        // Keep existing tenant wallet settings working after the upgrade.
        if (empty($accounts) && !empty($configured['account_number'])) {
            $accounts[] = [
                'key' => 'legacy',
                'currency' => __('العملة الأساسية'),
                'account_number' => trim((string) $configured['account_number']),
                'description' => '',
            ];
        }

        return $accounts;
    }
}
