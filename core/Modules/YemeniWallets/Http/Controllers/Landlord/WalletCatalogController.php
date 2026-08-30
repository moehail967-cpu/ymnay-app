<?php

namespace Modules\YemeniWallets\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Modules\YemeniWallets\Src\PluginOptions;

/**
 * Landlord-only. Manages the platform-wide catalog of Yemeni e-wallet
 * types (name, logo, dynamic field schema). Stored as a single JSON array
 * under the 'catalog' option key, globally (tenant_id = null) via
 * PluginOptions -- NOT a database table, per this platform's Settings API.
 */
class WalletCatalogController extends Controller
{
    public function index(): View
    {
        $wallets = PluginOptions::getGlobal('catalog', []);

        return view('yemeni_wallets::landlord.catalog-index', compact('wallets'));
    }

    public function create(): View
    {
        return view('yemeni_wallets::landlord.catalog-form', ['wallet' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateWallet($request);

        $wallets = PluginOptions::getGlobal('catalog', []);

        $logoPath = $request->hasFile('logo')
            ? $request->file('logo')->store('yemeni-wallets/logos', 'public')
            : null;

        $wallets[] = [
            'id' => (string) Str::uuid(),
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'logo' => $logoPath,
            'fields_schema' => $data['fields'],
            'status' => $data['status'] ?? true,
            'sort_order' => $data['sort_order'] ?? 0,
        ];

        PluginOptions::setGlobal('catalog', $wallets);

        return redirect()
            ->route('landlord.yemeniwallets.catalog.index')
            ->with('success', __('Wallet added to the catalog successfully.'));
    }

    public function edit(string $walletId): View
    {
        $wallets = PluginOptions::getGlobal('catalog', []);
        $wallet = collect($wallets)->firstWhere('id', $walletId);

        abort_if(! $wallet, 404);

        return view('yemeni_wallets::landlord.catalog-form', compact('wallet'));
    }

    public function update(Request $request, string $walletId): RedirectResponse
    {
        $data = $this->validateWallet($request);

        $wallets = collect(PluginOptions::getGlobal('catalog', []));

        $index = $wallets->search(fn ($w) => $w['id'] === $walletId);
        abort_if($index === false, 404);

        $existing = $wallets[$index];

        $logoPath = $request->hasFile('logo')
            ? $request->file('logo')->store('yemeni-wallets/logos', 'public')
            : ($existing['logo'] ?? null);

        $wallets[$index] = [
            'id' => $walletId,
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'logo' => $logoPath,
            'fields_schema' => $data['fields'],
            'status' => $data['status'] ?? $existing['status'],
            'sort_order' => $data['sort_order'] ?? $existing['sort_order'],
        ];

        PluginOptions::setGlobal('catalog', $wallets->values()->all());

        return redirect()
            ->route('landlord.yemeniwallets.catalog.index')
            ->with('success', __('Wallet updated successfully.'));
    }

    public function toggleStatus(string $walletId): RedirectResponse
    {
        $wallets = collect(PluginOptions::getGlobal('catalog', []));

        $index = $wallets->search(fn ($w) => $w['id'] === $walletId);
        abort_if($index === false, 404);

        $wallets[$index]['status'] = ! ($wallets[$index]['status'] ?? true);

        PluginOptions::setGlobal('catalog', $wallets->values()->all());

        return back()->with('success', __('Wallet status updated.'));
    }

    public function destroy(string $walletId): RedirectResponse
    {
        $wallets = collect(PluginOptions::getGlobal('catalog', []))
            ->reject(fn ($w) => $w['id'] === $walletId)
            ->values()
            ->all();

        PluginOptions::setGlobal('catalog', $wallets);

        // Note: tenants who activated this wallet keep their stored
        // activation/field values under their own tenant-scoped option key;
        // those become orphaned (harmless) once the catalog entry is gone.

        return back()->with('success', __('Wallet removed from catalog.'));
    }

    protected function validateWallet(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'status' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'fields' => ['required', 'array', 'min:1'],
            'fields.*.key' => ['required', 'string', 'alpha_dash'],
            'fields.*.label' => ['required', 'string', 'max:100'],
            'fields.*.type' => ['required', 'in:text,textarea,number'],
            'fields.*.required' => ['boolean'],
        ]);
    }
}
