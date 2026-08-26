<?php

namespace Modules\Affiliate\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AffiliateFrontendController extends Controller
{
    public function index()
    {
        $userId   = auth()->id();
        $tenantId = $this->tenantId();

        $affiliate = DB::table('affiliates')
            ->where('user_id', $userId)
            ->where('tenant_id', $tenantId)
            ->first();

        $commissions = $affiliate
            ? DB::table('affiliate_commissions')
                ->where('affiliate_id', $affiliate->id)
                ->orderByDesc('created_at')
                ->limit(20)
                ->get()
            : collect();

        $payouts = $affiliate
            ? DB::table('affiliate_payouts')
                ->where('affiliate_id', $affiliate->id)
                ->orderByDesc('created_at')
                ->limit(10)
                ->get()
            : collect();

        $referralLink = $affiliate ? url('/?ref=' . $affiliate->code) : null;

        return view('affiliate::frontend.referrals', compact('affiliate', 'commissions', 'payouts', 'referralLink'));
    }

    public function join()
    {
        $userId   = auth()->id();
        $tenantId = $this->tenantId();

        $exists = DB::table('affiliates')->where('user_id', $userId)->where('tenant_id', $tenantId)->exists();
        if ($exists) {
            return back()->with('error', __('You are already in the affiliate programme.'));
        }

        DB::table('affiliates')->insert([
            'tenant_id'   => $tenantId,
            'user_id'     => $userId,
            'code'        => strtoupper(Str::random(8)),
            'status'      => 'active',
            'balance'     => 0,
            'total_earned'=> 0,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return back()->with('success', __('Welcome to the affiliate programme! Your referral link is ready.'));
    }

    private function tenantId(): int|string|null
    {
        try {
            return function_exists('tenant') && tenant() ? tenant()->id : null;
        } catch (\Throwable) {
            return null;
        }
    }
}
