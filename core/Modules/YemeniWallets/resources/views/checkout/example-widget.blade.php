{{--
    ⚠️ REFERENCE EXAMPLE ONLY -- not wired into the real checkout page.
    See the note in Http/Controllers/YemeniWalletsGatewayController.php:
    this platform's actual checkout payment-method integration point
    (hook/filter name) is unconfirmed. Once found, either:
      a) server-render this partial from that hook, or
      b) keep this fetch()-based widget and just place <div id="yemeni-wallets-widget">
         inside the real checkout payment-methods section.
--}}
<div id="yemeni-wallets-widget" dir="rtl"></div>

<script>
(function () {
    const container = document.getElementById('yemeni-wallets-widget');
    if (!container) return;

    fetch('{{ route('yemeniwallets.checkout-options') }}')
        .then(r => r.json())
        .then(data => renderWallets(data.wallets || []));

    function renderWallets(wallets) {
        if (!wallets.length) {
            container.innerHTML = '<p class="text-muted">{{ __('لا توجد محافظ إلكترونية مفعّلة حاليًا لدى هذا المتجر.') }}</p>';
            return;
        }

        let html = '<p class="mb-2">{{ __('اختر المحفظة التي ستحوّل منها المبلغ:') }}</p><div class="yemeni-wallets-options">';
        wallets.forEach(w => {
            html += `
                <label class="wallet-option-card">
                    <input type="radio" name="catalog_wallet_id" value="${w.catalog_wallet_id}" class="wallet-option-input" data-target="wallet-details-${w.catalog_wallet_id}" required>
                    ${w.logo ? `<img src="${w.logo}" class="wallet-logo">` : ''}
                    <span>${w.name}</span>
                </label>`;
        });
        html += '</div>';

        wallets.forEach(w => {
            let rows = Object.entries(w.values || {}).map(([k, v]) => `<li>${k}: <span>${v}</span></li>`).join('');
            html += `<div id="wallet-details-${w.catalog_wallet_id}" class="wallet-details" style="display:none;">
                        <div class="wallet-details-box"><strong>{{ __('حوّل المبلغ إلى:') }}</strong><ul>${rows}</ul></div>
                     </div>`;
        });

        html += `
            <div class="wallet-proof-upload mt-3">
                <label for="payment_proof">{{ __('أرفق لقطة شاشة لإثبات التحويل') }} <span class="required">*</span></label>
                <input type="file" name="payment_proof" id="payment_proof" accept="image/*" required>
            </div>`;

        container.innerHTML = html;

        container.querySelectorAll('.wallet-option-input').forEach(input => {
            input.addEventListener('change', function () {
                container.querySelectorAll('.wallet-details').forEach(el => el.style.display = 'none');
                const target = document.getElementById(this.dataset.target);
                if (target) target.style.display = 'block';
            });
        });
    }
})();
</script>
