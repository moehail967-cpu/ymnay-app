<?php

namespace Modules\Affiliate\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class AffiliateAdminController extends Controller
{
    private function plugin()
    {
        return app(\App\PluginSystem\PluginManager::class)->get('affiliate');
    }

    public function index()
    {
        $affiliates = DB::table('affiliates')
            ->join('users', 'affiliates.user_id', '=', 'users.id')
            ->select('affiliates.*', 'users.name as user_name', 'users.email as user_email')
            ->orderByDesc('affiliates.created_at')
            ->paginate(20);

        return view('affiliate::admin.index', compact('affiliates'));
    }

    public function commissions(Request $request)
    {
        $query = DB::table('affiliate_commissions as ac')
            ->join('affiliates as a', 'ac.affiliate_id', '=', 'a.id')
            ->join('users as u', 'a.user_id', '=', 'u.id')
            ->select('ac.*', 'u.name as affiliate_name', 'u.email as affiliate_email', 'a.code')
            ->orderByDesc('ac.created_at');

        if ($request->filled('status')) {
            $query->where('ac.status', $request->status);
        }

        $commissions = $query->paginate(25);

        return view('affiliate::admin.commissions', compact('commissions'));
    }

    public function approveCommission(int $id)
    {
        $commission = DB::table('affiliate_commissions')->where('id', $id)->where('status', 'pending')->first();
        if (!$commission) {
            return back()->with('error', __('Commission not found or already processed.'));
        }

        DB::table('affiliate_commissions')->where('id', $id)->update(['status' => 'approved', 'updated_at' => now()]);
        DB::table('affiliates')->where('id', $commission->affiliate_id)->increment('balance', $commission->amount);
        DB::table('affiliates')->where('id', $commission->affiliate_id)->increment('total_earned', $commission->amount);

        return back()->with('success', __('Commission approved.'));
    }

    public function rejectCommission(int $id)
    {
        DB::table('affiliate_commissions')
            ->where('id', $id)
            ->where('status', 'pending')
            ->update(['status' => 'cancelled', 'updated_at' => now()]);

        return back()->with('success', __('Commission rejected.'));
    }

    public function payouts(Request $request)
    {
        $payouts = DB::table('affiliate_payouts as ap')
            ->join('affiliates as a', 'ap.affiliate_id', '=', 'a.id')
            ->join('users as u', 'a.user_id', '=', 'u.id')
            ->select('ap.*', 'u.name as affiliate_name', 'u.email as affiliate_email')
            ->orderByDesc('ap.created_at')
            ->paginate(25);

        $affiliates = DB::table('affiliates')
            ->join('users', 'affiliates.user_id', '=', 'users.id')
            ->select('affiliates.id', 'affiliates.balance', 'users.name', 'users.email')
            ->where('affiliates.status', 'active')
            ->where('affiliates.balance', '>', 0)
            ->get();

        return view('affiliate::admin.payouts', compact('payouts', 'affiliates'));
    }

    public function processPayout(Request $request)
    {
        $request->validate([
            'affiliate_id' => 'required|integer',
            'amount'       => 'required|numeric|min:0.01',
            'method'       => 'required|string|max:40',
            'reference'    => 'nullable|string|max:255',
            'notes'        => 'nullable|string|max:1000',
        ]);

        $affiliate = DB::table('affiliates')->where('id', $request->affiliate_id)->first();
        if (!$affiliate || $affiliate->balance < $request->amount) {
            return back()->with('error', __('Insufficient affiliate balance.'));
        }

        DB::table('affiliate_payouts')->insert([
            'tenant_id'    => $affiliate->tenant_id,
            'affiliate_id' => $affiliate->id,
            'amount'       => $request->amount,
            'method'       => $request->method,
            'reference'    => $request->reference,
            'notes'        => $request->notes,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        DB::table('affiliates')->where('id', $affiliate->id)->decrement('balance', $request->amount);

        return back()->with('success', __('Payout recorded successfully.'));
    }

    public function settings()
    {
        $plugin   = $this->plugin();
        $settings = [
            'commission_rate' => $plugin->get_option('commission_rate', '10'),
            'cookie_days'     => $plugin->get_option('cookie_days', '30'),
            'auto_approve'    => $plugin->get_option('auto_approve', '0'),
            'min_payout'      => $plugin->get_option('min_payout', '50'),
        ];

        return view('affiliate::admin.settings', compact('settings'));
    }

    public function saveSettings(Request $request)
    {
        $request->validate([
            'commission_rate' => 'required|numeric|min:0|max:100',
            'cookie_days'     => 'required|integer|min:1|max:365',
            'auto_approve'    => 'nullable|in:0,1',
            'min_payout'      => 'required|numeric|min:0',
        ]);

        $plugin = $this->plugin();
        $plugin->update_option('commission_rate', $request->commission_rate);
        $plugin->update_option('cookie_days', $request->cookie_days);
        $plugin->update_option('auto_approve', $request->input('auto_approve', '0'));
        $plugin->update_option('min_payout', $request->min_payout);

        return back()->with('success', __('Affiliate settings saved.'));
    }

    public function toggleAffiliate(int $id)
    {
        $affiliate = DB::table('affiliates')->where('id', $id)->first();
        if (!$affiliate) return back()->with('error', __('Affiliate not found.'));

        $newStatus = $affiliate->status === 'active' ? 'paused' : 'active';
        DB::table('affiliates')->where('id', $id)->update(['status' => $newStatus, 'updated_at' => now()]);

        return back()->with('success', __('Affiliate status updated.'));
    }
}
