<?php
namespace Modules\CommissionManage\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\CommissionManage\Entities\CommissionSetting;
use Modules\CommissionManage\Entities\WithdrawGateway;


class WithdrawGatewayController extends Controller
{
    public function index(){
        $setting = CommissionSetting::where('user_id', Auth::guard('admin')->id())->first();
        // Format withdraw gateways for JavaScript
        $withdraw_gateways_json = $setting && $setting->withdrawGateways->count() > 0
            ? $setting->withdrawGateways->map(function ($gateway) {
                return [
                    'id' => $gateway->gateway_id,
                    'name' => $gateway->name,
                    'fields' => $gateway->fields ?? []
                ];
            })->toJson()
            : '[]';
        return view('commissionmanage::withdraw-gateway',compact('withdraw_gateways_json'));
    }

    public function store(Request $request)
    {
        $commissionSetting = CommissionSetting::where('user_id', auth('admin')->id())->first();

        // Delete old gateways
        $commissionSetting->withdrawGateways()->delete();

        if (!$request->filled('withdraw_gateways')) {
            return back()->with('success', 'Saved (no gateways submitted)');
        }

        $withdrawGateways = json_decode($request->withdraw_gateways, true);

        if (!is_array($withdrawGateways)) {
            return back()->with('error', 'Invalid gateway JSON');
        }

        foreach ($withdrawGateways as $index => $gateway) {

            if (empty($gateway['name'])) continue;

            WithdrawGateway::create([
                'commission_setting_id' => $commissionSetting->id,
                'gateway_id' => $gateway['id'],
                'name' => $gateway['name'],
                'fields' => $gateway['fields'] ?? [],
                'status' => true,
                'sort_order' => $index,
            ]);
        }

        return back()->with('success', 'Withdraw gateways saved!');
    }

}
