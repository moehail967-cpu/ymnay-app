<?php

namespace Modules\LoyaltyPoints\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\LoyaltyPoints\src\LoyaltyService;

class LoyaltyFrontendController extends Controller
{
    private function plugin(): mixed
    {
        return app(\App\PluginSystem\PluginManager::class)->get('loyalty-points');
    }

    public function index()
    {
        $plugin = $this->plugin();
        if (!$plugin || !(bool) $plugin->get_option('enabled', false)) {
            abort(404);
        }

        $service = new LoyaltyService;
        $userId = auth('web')->id();
        $balance = $service->getBalance($userId);
        $transactions = $service->getTransactions($userId, 50);
        $earnRate = (int) $plugin->get_option('earn_rate', 1);
        $redeemRate = (int) $plugin->get_option('redeem_rate', 100);
        $minRedeem = (int) $plugin->get_option('min_points_redeem', 100);
        $redeemValue = $redeemRate > 0 ? round($balance / $redeemRate, 2) : 0;

        return view('loyalty-points::frontend.my-points', compact(
            'balance',
            'transactions',
            'earnRate',
            'redeemRate',
            'minRedeem',
            'redeemValue'
        ));
    }

    /**
     * Render the cart-summary widget HTML.
     * Called statically from LoyaltyPointsPlugin::renderCartSummaryWidget().
     */
    public static function renderCartWidget($plugin): string
    {
        try {
            $subtotalStr = \Gloudemans\Shoppingcart\Facades\Cart::instance('default')->subtotal();
            $cartTotal = (float) preg_replace('/[^0-9.]/', '', $subtotalStr);
        } catch (\Throwable) {
            return '';
        }

        $earnRate = (int) $plugin->get_option('earn_rate', 1);
        $redeemRate = (int) $plugin->get_option('redeem_rate', 100);
        $minRedeem = (int) $plugin->get_option('min_points_redeem', 100);
        $earnPoints = (int) floor($cartTotal * $earnRate);

        $balance = 0;
        $redeemValue = 0;
        $redeeming = 0;

        if (auth('web')->check()) {
            $service = new LoyaltyService;
            $balance = $service->getBalance(auth('web')->id());
            $redeemValue = $redeemRate > 0 ? round($balance / $redeemRate, 2) : 0;
            $redeeming = (int) session('loyalty_redeem_points', 0);
        }

        // Nothing to show
        if ($earnPoints <= 0 && $balance <= 0)
            return '';

        return view('loyalty-points::frontend.cart-summary', compact(
            'earnPoints',
            'balance',
            'redeemRate',
            'minRedeem',
            'redeemValue',
            'redeeming'
        ))->render();
    }
}
