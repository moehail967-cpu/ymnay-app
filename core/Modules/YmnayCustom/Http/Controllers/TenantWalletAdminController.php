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
            'wallets.*.account_number' => ['nullable', 'string', 'max:191'],
            'wallets.*.recipient_name' => ['nullable', 'string', 'max:191'],
            'wallets.*.instructions' => ['nullable', 'string', 'max:3000'],
        ]);

        $catalogIds = WalletRepository::activeCatalog()->pluck('id')->map(fn ($id) => (string) $id)->all();
        $settings = [];
        foreach ((array) $request->input('wallets', []) as $id => $row) {
            if (!in_array((string) $id, $catalogIds, true)) continue;
            $enabled = !empty($row['enabled']);
            $account = trim((string) ($row['account_number'] ?? ''));
            $recipient = trim((string) ($row['recipient_name'] ?? ''));
            if ($enabled && ($account === '' || $recipient === '')) {
                return back()->withErrors(["wallets.$id" => __('Wallet number and recipient name are required when enabling a wallet.')])->withInput();
            }
            $settings[$id] = [
                'enabled' => $enabled,
                'account_number' => $account,
                'recipient_name' => $recipient,
                'instructions' => trim((string) ($row['instructions'] ?? '')),
            ];
        }

        update_static_option('ymnay_manual_wallet_settings', json_encode($settings, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        update_static_option('ymnay_manual_wallet_status', collect($settings)->contains(fn ($row) => !empty($row['enabled'])) ? 'on' : '');
        return back()->with(['type' => 'success', 'msg' => __('Wallet settings saved.')]);
    }
}
