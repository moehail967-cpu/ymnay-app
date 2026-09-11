<?php

namespace Modules\CartDiscount\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class CartDiscountApiController extends Controller
{
    private function plugin()
    {
        return app(\App\PluginSystem\PluginManager::class)->get('cart-discount');
    }

    public function state(): JsonResponse
    {
        $plugin = $this->plugin();

        if (!$plugin->get_option('enabled')) {
            return response()->json(['tiers' => []]);
        }

        $tiers       = json_decode($plugin->get_option('tiers', '[]'), true) ?? [];
        $cartTotal   = (float) apply_filters('cart_discount:cart_total', $this->resolveCartTotal());
        $showLocked  = (bool) $plugin->get_option('show_locked_tiers', '1');
        $widgetTitle = $plugin->get_option('widget_title', 'Unlock Rewards');

        $activeTier  = null;
        $savings     = 0.0;
        $tiersOutput = [];

        foreach ($tiers as $tier) {
            $threshold    = (float) apply_filters('cart_discount:threshold', (float) $tier['threshold']);
            $unlocked     = $cartTotal >= $threshold;
            $amountNeeded = max(0, $threshold - $cartTotal);

            if ($unlocked) {
                $activeTier = $tier;
            }

            if (!$showLocked && !$unlocked) continue;

            $tiersOutput[] = [
                'threshold'     => $threshold,
                'label'         => $tier['label'] ?? '',
                'type'          => $tier['type'] ?? 'percentage',
                'unlocked'      => $unlocked,
                'amount_needed' => $amountNeeded,
            ];
        }

        // Calculate savings from the best active tier
        if ($activeTier) {
            $savings = match ($activeTier['type']) {
                'percentage' => round($cartTotal * ($activeTier['value'] / 100), 2),
                'flat'       => min($cartTotal, (float) $activeTier['value']),
                default      => 0.0,
            };
        }

        return response()->json([
            'cart_total'        => $cartTotal,
            'active_tier'       => $activeTier,
            'tiers'             => $tiersOutput,
            'savings'           => $savings,
            'widget_title'      => $widgetTitle,
            'show_locked_tiers' => $showLocked,
        ]);
    }

    private function resolveCartTotal(): float
    {
        try {
            return theme_cart_subtotal();
        } catch (\Throwable) {
            return 0.0;
        }
    }
}
