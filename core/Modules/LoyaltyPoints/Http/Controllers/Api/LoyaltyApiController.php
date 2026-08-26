<?php

namespace Modules\LoyaltyPoints\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\LoyaltyPoints\src\LoyaltyService;

class LoyaltyApiController extends Controller
{
    private function plugin(): mixed
    {
        return app(\App\PluginSystem\PluginManager::class)->get('loyalty-points');
    }

    public function balance()
    {
        if (!auth('web')->check()) {
            return response()->json(['balance' => 0, 'enabled' => false]);
        }

        $plugin = $this->plugin();
        $enabled = (bool) $plugin->get_option('enabled', false);

        if (!$enabled) {
            return response()->json(['balance' => 0, 'enabled' => false]);
        }

        $service = new LoyaltyService;
        $userId = auth('web')->id();
        $balance = $service->getBalance($userId);
        $redeemRate = (int) $plugin->get_option('redeem_rate', 100);
        $minRedeem = (int) $plugin->get_option('min_points_redeem', 100);
        $maxPercent = (int) $plugin->get_option('max_redeem_percent', 50);
        $redeeming = session('loyalty_redeem_points', 0);

        return response()->json([
            'enabled' => true,
            'balance' => $balance,
            'redeem_rate' => $redeemRate,
            'min_points_redeem' => $minRedeem,
            'max_redeem_percent' => $maxPercent,
            'redeeming' => $redeeming,
            'redeem_value' => $redeemRate > 0 ? round($balance / $redeemRate, 2) : 0,
        ]);
    }

    public function redeem(Request $request)
    {
        if (!auth('web')->check()) {
            return response()->json(['type' => 'danger', 'msg' => __('Not authenticated')], 401);
        }

        $plugin = $this->plugin();
        $enabled = (bool) $plugin->get_option('enabled', false);

        if (!$enabled) {
            return response()->json(['type' => 'danger', 'msg' => __('Loyalty points disabled')]);
        }

        $service = new LoyaltyService;
        $userId = auth('web')->id();
        $balance = $service->getBalance($userId);
        $minRedeem = (int) $plugin->get_option('min_points_redeem', 100);
        $redeemRate = (int) $plugin->get_option('redeem_rate', 100);
        $maxPercent = (int) $plugin->get_option('max_redeem_percent', 50);

        $pointsToUse = (int) $request->input('points', 0);

        if ($pointsToUse <= 0) {
            session()->forget('loyalty_redeem_points');
            return response()->json(['type' => 'success', 'msg' => __('Redemption cancelled'), 'redeeming' => 0]);
        }

        if ($balance < $minRedeem) {
            return response()->json(['type' => 'danger', 'msg' => __('Minimum :n points required to redeem', ['n' => $minRedeem])]);
        }

        if ($pointsToUse > $balance) {
            return response()->json(['type' => 'danger', 'msg' => __('Insufficient points balance')]);
        }

        // loyalty_points:cart_total — lets currency plugins supply the display-currency total for cap calculation
        $cartTotal = (float) apply_filters('loyalty_points:cart_total', theme_cart_total());
        $maxDiscount = $cartTotal * ($maxPercent / 100);

        // Raw discount in base currency, then converted via hook
        $rawDiscount = $redeemRate > 0 ? $pointsToUse / $redeemRate : 0;
        $discount = (float) apply_filters('loyalty_points:redemption_discount', $rawDiscount);

        if ($discount > $maxDiscount) {
            // Scale points proportionally so converted discount fits within the cap
            $scale = $discount > 0 ? $maxDiscount / $discount : 0;
            $pointsToUse = (int) floor($pointsToUse * $scale);
            $rawDiscount = $redeemRate > 0 ? $pointsToUse / $redeemRate : 0;
            $discount = (float) apply_filters('loyalty_points:redemption_discount', $rawDiscount);
        }

        session(['loyalty_redeem_points' => $pointsToUse]);

        return response()->json([
            'type' => 'success',
            'msg' => __(':p points applied — :v discount', ['p' => $pointsToUse, 'v' => amount_with_currency_symbol(round($discount, 2))]),
            'redeeming' => $pointsToUse,
            'discount' => round($discount, 2),
        ]);
    }

    public function cancel()
    {
        session()->forget('loyalty_redeem_points');
        return response()->json(['type' => 'success', 'msg' => __('Redemption cancelled'), 'redeeming' => 0]);
    }
}
