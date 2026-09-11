/**
 * Cart Discount Widget — client-side refresh
 * Re-renders #cd-cart-summary-widget after cart quantity changes.
 * The initial render is server-side (always correct on page load).
 * This script handles live updates via window.cdConfig (embedded by plugin).
 */
(function () {
    'use strict';

    var cfg = window.cdConfig;
    if (!cfg || !cfg.tiers || !cfg.tiers.length) return;

    function parseAmount(str) {
        if (typeof str === 'number') return str;
        return parseFloat(String(str).replace(/[^0-9.]/g, '')) || 0;
    }

    function fmt(n) {
        var sym = cfg.symbol || '$';
        return sym + parseAmount(n).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    function renderWidget(cartTotal) {
        var tiers   = cfg.tiers;
        var accent  = cfg.accent || '#c9a96e';
        var title   = cfg.title  || 'Unlock Rewards';
        var showAll = cfg.showLocked !== false;

        var rows = '';

        for (var i = 0; i < tiers.length; i++) {
            var tier      = tiers[i];
            var threshold = parseAmount(tier.threshold);
            var unlocked  = cartTotal >= threshold;
            var needed    = Math.max(0, threshold - cartTotal);
            var progress  = threshold > 0 ? Math.min(100, cartTotal / threshold * 100) : 100;

            if (!unlocked && !showAll) continue;

            var label = (tier.label || '').replace(/</g, '&lt;');

            if (unlocked) {
                rows += '<div class="cd-tier-row cd-unlocked-row">'
                    + '<i class="mdi mdi-check-circle cd-tier-icon cd-unlocked"></i>'
                    + '<div class="cd-tier-label">'
                    + '<strong>' + label + '</strong> <span>unlocked!</span>'
                    + '<div class="cd-progress-bar"><div class="cd-progress-fill" style="width:100%"></div></div>'
                    + '</div>'
                    + '</div>';
            } else {
                var current = Math.min(cartTotal, threshold);
                rows += '<div class="cd-tier-row">'
                    + '<i class="mdi mdi-lock-outline cd-tier-icon cd-locked"></i>'
                    + '<div class="cd-tier-label">'
                    + '<span>Spend <strong>' + fmt(needed) + '</strong> more for ' + label + '</span>'
                    + '<div class="cd-progress-bar"><div class="cd-progress-fill" style="width:' + Math.round(progress) + '%"></div></div>'
                    + '<span class="cd-progress-label">' + fmt(current) + ' / ' + fmt(threshold) + '</span>'
                    + '</div>'
                    + '</div>';
            }
        }

        if (!rows) return '';

        return '<div class="cd-summary-block" style="--cd-accent:' + accent + '">'
            + '<div class="cd-summary-heading">' + title + '</div>'
            + rows
            + '</div>';
    }

    // Observe .coupon-contents for DOM mutations (AJAX cart price update)
    document.addEventListener('DOMContentLoaded', function () {
        var target = document.querySelector('.coupon-contents');
        var widget = document.getElementById('cd-cart-summary-widget');
        if (!target || !widget) return;

        new MutationObserver(function () {
            // Look for any price element updated in the coupon-contents
            var priceEl = target.querySelector(
                '.coupon-contents-details-list-item-price, [class*="subtotal"], [class*="price"]'
            );
            var newTotal = priceEl ? parseAmount(priceEl.textContent || priceEl.innerText) : 0;
            if (newTotal > 0) {
                var html = renderWidget(newTotal);
                if (html) widget.innerHTML = html;
            }
        }).observe(target, { childList: true, subtree: true, characterData: true });
    });
})();
