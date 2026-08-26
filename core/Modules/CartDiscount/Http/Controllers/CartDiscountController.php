<?php

namespace Modules\CartDiscount\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartDiscountController extends Controller
{
    private function plugin()
    {
        return app(\App\PluginSystem\PluginManager::class)->get('cart-discount');
    }

    public function settings()
    {
        $plugin = $this->plugin();
        if (!$plugin) abort(500, 'Plugin not available');
        $options = [
            'enabled'           => $plugin->get_option('enabled', '0'),
            'tiers'             => json_decode($plugin->get_option('tiers', '[]'), true) ?? [],
            'widget_title'      => $plugin->get_option('widget_title', 'Unlock Rewards'),
            'widget_accent_color'=> $plugin->get_option('widget_accent_color', '#6366f1'),
            'show_locked_tiers' => $plugin->get_option('show_locked_tiers', '1'),
            'apply_to_shipping' => $plugin->get_option('apply_to_shipping', '0'),
            'stack_discounts'   => $plugin->get_option('stack_discounts', '0'),
        ];
        return view('cart-discount::admin.settings', compact('options'));
    }

    public function widget(): \Illuminate\Http\Response
    {
        $plugin = $this->plugin();
        if (!$plugin || !$plugin->get_option('enabled')) {
            return response('', 204);
        }
        return response($this->renderWidget($plugin));
    }

    public static function renderWidget($plugin): string
    {
        $tiers = json_decode($plugin->get_option('tiers', '[]'), true) ?? [];
        if (empty($tiers)) return '';

        try {
            // cart_discount:cart_total — lets currency plugins supply the display-currency total
            $cartTotal = (float) apply_filters('cart_discount:cart_total', theme_cart_subtotal());
        } catch (\Throwable) {
            return '';
        }

        $accentColor = $plugin->get_option('widget_accent_color', '#6366f1');
        $widgetTitle = $plugin->get_option('widget_title') ?: __('Unlock Rewards');
        $showLocked  = (bool) $plugin->get_option('show_locked_tiers', '1');

        usort($tiers, fn($a, $b) => $a['threshold'] <=> $b['threshold']);

        $rows = '';

        foreach ($tiers as $tier) {
            // cart_discount:threshold — lets currency plugins convert base threshold to display currency
            $threshold    = (float) apply_filters('cart_discount:threshold', (float) $tier['threshold']);
            $unlocked     = $cartTotal >= $threshold;
            $amountNeeded = max(0, $threshold - $cartTotal);
            $progress     = $threshold > 0 ? min(100, $cartTotal / $threshold * 100) : 100;

            if (!$unlocked && !$showLocked) continue;

            $label = e($tier['label'] ?? '');

            if ($unlocked) {
                $rows .= '<div class="cd-tier-row cd-unlocked-row">'
                    . '<i class="mdi mdi-check-circle cd-tier-icon cd-unlocked"></i>'
                    . '<div class="cd-tier-label">'
                    . '<strong>' . $label . '</strong> <span>' . __('unlocked!') . '</span>'
                    . '<div class="cd-progress-bar"><div class="cd-progress-fill" style="width:100%"></div></div>'
                    . '</div>'
                    . '</div>';
            } else {
                $needFmt    = amount_with_currency_symbol($amountNeeded);
                $totalFmt   = amount_with_currency_symbol($threshold);
                $currentFmt = amount_with_currency_symbol(min($cartTotal, $threshold));
                $rows .= '<div class="cd-tier-row">'
                    . '<i class="mdi mdi-lock-outline cd-tier-icon cd-locked"></i>'
                    . '<div class="cd-tier-label">'
                    . '<span>' . __('Spend') . ' <strong>' . $needFmt . '</strong> ' . __('more for') . ' ' . $label . '</span>'
                    . '<div class="cd-progress-bar"><div class="cd-progress-fill" style="width:' . round($progress) . '%"></div></div>'
                    . '<span class="cd-progress-label">' . $currentFmt . ' / ' . $totalFmt . '</span>'
                    . '</div>'
                    . '</div>';
            }
        }

        if (!$rows) return '';

        $accentSafe = e($accentColor);
        $titleSafe  = e($widgetTitle);

        return '<div class="cd-summary-block" id="cd-cart-summary-widget" style="--cd-accent:' . $accentSafe . '">'
            . '<div class="cd-summary-heading">' . $titleSafe . '</div>'
            . $rows
            . '</div>';
    }

    public function saveSettings(Request $request)
    {
        $plugin = $this->plugin();
        if (!$plugin) abort(500, 'Plugin not available');

        $plugin->update_option('enabled',            $request->has('enabled') ? '1' : '0');
        $plugin->update_option('show_locked_tiers',  $request->has('show_locked_tiers') ? '1' : '0');
        $plugin->update_option('apply_to_shipping',  $request->has('apply_to_shipping') ? '1' : '0');
        $plugin->update_option('stack_discounts',    $request->has('stack_discounts') ? '1' : '0');
        $plugin->update_option('widget_title',       $request->input('widget_title', 'Unlock Rewards'));
        $plugin->update_option('widget_accent_color',$request->input('widget_accent_color', '#6366f1'));

        // Build tiers from repeater inputs
        $thresholds = $request->input('tier_threshold', []);
        $types      = $request->input('tier_type', []);
        $values     = $request->input('tier_value', []);
        $labels     = $request->input('tier_label', []);

        $tiers = [];
        foreach ($thresholds as $i => $threshold) {
            if (!$threshold) continue;
            $tiers[] = [
                'threshold' => (float) $threshold,
                'type'      => $types[$i] ?? 'percentage',
                'value'     => (float) ($values[$i] ?? 0),
                'label'     => $labels[$i] ?? '',
            ];
        }

        usort($tiers, fn($a, $b) => $a['threshold'] <=> $b['threshold']);
        $plugin->update_option('tiers', json_encode($tiers));

        return back()->with('success', __('Cart discount settings saved.'));
    }
}
