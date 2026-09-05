<?php

namespace Modules\YmnayCustom\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\YmnayCustom\Support\WalletRepository;

class TenantWalletAdminController extends Controller
{
    public function index()
    {
        $wallets = WalletRepository::activeCatalog();
        $settings = WalletRepository::tenantSettings();
        return view('ymnaycustom::admin.tenant-wallets', compact('wallets', 'settings'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'wallets' => ['nullable', 'array'],
            'wallets.*.enabled' => ['nullable', 'boolean'],
            'wallets.*.recipient_name' => ['nullable', 'string', 'max:191'],
            'wallets.*.instructions' => ['nullable', 'string', 'max:3000'],
            'wallets.*.accounts' => ['nullable', 'array'],
            'wallets.*.accounts.*.currency' => ['nullable', 'string', 'max:100'],
            'wallets.*.accounts.*.account_number' => ['nullable', 'string', 'max:191'],
            'wallets.*.accounts.*.description' => ['nullable', 'string', 'max:1000'],
        ]);

        $catalogIds = WalletRepository::activeCatalog()->pluck('id')->map(fn ($id) => (string) $id)->all();
        $settings = [];
        foreach ((array) $request->input('wallets', []) as $id => $row) {
            if (!in_array((string) $id, $catalogIds, true)) continue;
            $enabled = !empty($row['enabled']);
            $recipient = trim((string) ($row['recipient_name'] ?? ''));
            $accounts = [];
            $currencies = [];
            foreach ((array) ($row['accounts'] ?? []) as $account) {
                $currency = trim((string) ($account['currency'] ?? ''));
                $number = trim((string) ($account['account_number'] ?? ''));
                $description = trim((string) ($account['description'] ?? ''));
                if ($currency === '' && $number === '' && $description === '') {
                    continue;
                }
                if ($currency === '' || $number === '') {
                    return back()->withErrors(["wallets.$id" => __('يجب كتابة اسم العملة ورقم المحفظة لكل حساب.')])->withInput();
                }
                $currencyKey = mb_strtolower($currency);
                if (isset($currencies[$currencyKey])) {
                    return back()->withErrors(["wallets.$id" => __('لا يمكن تكرار اسم العملة في المحفظة نفسها.')])->withInput();
                }
                $currencies[$currencyKey] = true;
                $accounts[] = [
                    'currency' => $currency,
                    'account_number' => $number,
                    'description' => $description,
                ];
            }
            if ($enabled && ($recipient === '' || empty($accounts))) {
                return back()->withErrors(["wallets.$id" => __('عند تفعيل المحفظة، يجب إضافة اسم المستلم ورقم محفظة واحد على الأقل.')])->withInput();
            }
            $settings[$id] = [
                'enabled' => $enabled,
                'recipient_name' => $recipient,
                'instructions' => trim((string) ($row['instructions'] ?? '')),
                'accounts' => $accounts,
            ];
        }

        update_static_option('ymnay_manual_wallet_settings', json_encode($settings, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        update_static_option('ymnay_manual_wallet_status', collect($settings)->contains(fn ($row) => !empty($row['enabled'])) ? 'on' : '');
        return back()->with(['type' => 'success', 'msg' => __('Wallet settings saved.')]);
    }
}
