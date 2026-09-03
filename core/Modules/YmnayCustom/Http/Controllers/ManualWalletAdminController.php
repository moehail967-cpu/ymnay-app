<?php

namespace Modules\YmnayCustom\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\YmnayCustom\Entities\ManualWallet;

class ManualWalletAdminController extends Controller
{
    public function index()
    {
        $wallets = ManualWallet::query()->orderBy('sort_order')->orderBy('id')->get();
        return view('ymnaycustom::admin.landlord-wallets', compact('wallets'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request, true);
        $data['logo'] = $this->storeLogo($request);
        $data['status'] = $request->boolean('status');
        ManualWallet::create($data);
        return back()->with(['type' => 'success', 'msg' => __('Wallet added successfully.')]);
    }

    public function update(Request $request, ManualWallet $wallet)
    {
        $data = $this->validated($request, false);
        if ($request->hasFile('logo')) $data['logo'] = $this->storeLogo($request);
        $data['status'] = $request->boolean('status');
        $wallet->update($data);
        return back()->with(['type' => 'success', 'msg' => __('Wallet updated successfully.')]);
    }

    public function toggle(ManualWallet $wallet)
    {
        $wallet->update(['status' => !$wallet->status]);
        return back()->with(['type' => 'success', 'msg' => __('Wallet status updated.')]);
    }

    public function destroy(ManualWallet $wallet)
    {
        $wallet->delete();
        return back()->with(['type' => 'success', 'msg' => __('Wallet deleted.')]);
    }

    private function validated(Request $request, bool $logoRequired): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'description' => ['required', 'string', 'max:5000'],
            'logo' => [$logoRequired ? 'required' : 'nullable', 'image', 'mimes:jpeg,jpg,png,webp,svg', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);
    }

    private function storeLogo(Request $request): string
    {
        $image = $request->file('logo');
        $filename = Str::slug(pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . Str::random(8) . '.' . $image->extension();
        $path = global_assets_path('assets/landlord/uploads/ymnay-wallets/');
        if (!is_dir($path)) mkdir($path, 0755, true);
        $image->move($path, $filename);
        return $filename;
    }
}
