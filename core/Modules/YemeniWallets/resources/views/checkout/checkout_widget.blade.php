{{--
    YemeniWallets Checkout Widget
    ==============================
    Injected into the checkout page via the 'nazmart:render_checkout_form' filter
    (registered in YemeniWalletsPlugin::boot()).

    This div is hidden initially and shown by JS when the customer selects
    the "yemeni_wallets" payment gateway option.
--}}
<div id="yemeni-wallets-extra-wrapper"
     class="payment_extra_info_single_item"
     data-gateway="yemeni_wallets"
     style="display: none;">

    <div id="yemeni-wallets-widget" dir="rtl">
        <p class="yemeni-wallets-loading text-muted">{{ __('Loading wallets...') }}</p>
    </div>

    <script>
    (function () {
        'use strict';

        function initYemeniWalletsWidget() {
            var wrapper = document.getElementById('yemeni-wallets-extra-wrapper');
            var container = document.getElementById('yemeni-wallets-widget');
            if (!wrapper || !container) return;

            // Show/hide this section when the customer changes the payment gateway.
            document.addEventListener('change', function (e) {
                if (e.target && e.target.name === 'payment_gateway') {
                    wrapper.style.display = (e.target.value === 'yemeni_wallets') ? 'block' : 'none';
                    if (e.target.value === 'yemeni_wallets' && !container.dataset.loaded) {
                        loadWallets();
                    }
                }
            });

            // Also check on page load if yemeni_wallets is already selected.
            var selected = document.querySelector('[name="payment_gateway"]:checked');
            if (selected && selected.value === 'yemeni_wallets') {
                wrapper.style.display = 'block';
                loadWallets();
            }

            function loadWallets() {
                container.dataset.loaded = '1';
                fetch('{{ route("yemeniwallets.checkout-options") }}')
                    .then(function (r) { return r.json(); })
                    .then(function (data) { renderWallets(data.wallets || []); })
                    .catch(function () {
                        container.innerHTML = '<p class="text-danger">{{ __("Could not load wallets. Please refresh the page.") }}</p>';
                    });
            }

            function renderWallets(wallets) {
                if (!wallets.length) {
                    container.innerHTML = '<p class="text-muted small">{{ __("No e-wallets are activated for this store at the moment.") }}</p>';
                    return;
                }

                var html = '<p class="mb-2 fw-semibold">{{ __("Choose the wallet you will transfer from:") }}</p>'
                         + '<div class="yemeni-wallets-options d-flex flex-wrap gap-2 mb-3">';

                wallets.forEach(function (w) {
                    html += '<label class="wallet-option-card border rounded p-2 d-flex align-items-center gap-2 cursor-pointer" style="cursor:pointer;">'
                          + '<input type="radio" name="catalog_wallet_id" value="' + w.catalog_wallet_id + '" class="wallet-option-input" data-target="wallet-details-' + w.catalog_wallet_id + '" required>'
                          + (w.logo ? '<img src="' + w.logo + '" alt="' + w.name + '" style="height:32px;object-fit:contain;">' : '')
                          + '<span>' + w.name + '</span>'
                          + '</label>';
                });
                html += '</div>';

                // Account-detail panels (hidden until wallet selected).
                wallets.forEach(function (w) {
                    var rows = Object.entries(w.values || {}).map(function (entry) {
                        return '<li class="list-group-item d-flex justify-content-between"><span class="text-muted">' + entry[0] + '</span><strong>' + entry[1] + '</strong></li>';
                    }).join('');
                    html += '<div id="wallet-details-' + w.catalog_wallet_id + '" class="wallet-account-details mb-3" style="display:none;">'
                          + '<div class="alert alert-info p-2"><strong>{{ __("Transfer the amount to:") }}</strong></div>'
                          + '<ul class="list-group list-group-flush">' + rows + '</ul>'
                          + '</div>';
                });

                // Mandatory proof upload field.
                html += '<div class="wallet-proof-upload mt-3">'
                      + '<label for="payment_proof" class="form-label fw-semibold">'
                      + '{{ __("Attach transfer screenshot") }} <span class="text-danger">*</span>'
                      + '</label>'
                      + '<input type="file" name="payment_proof" id="payment_proof" accept="image/*" class="form-control" required>'
                      + '<div class="form-text text-muted">{{ __("JPG, PNG or WebP — max 5 MB") }}</div>'
                      + '</div>';

                container.innerHTML = html;

                // Wire up radio → show/hide account details.
                container.querySelectorAll('.wallet-option-input').forEach(function (input) {
                    input.addEventListener('change', function () {
                        container.querySelectorAll('.wallet-account-details').forEach(function (el) { el.style.display = 'none'; });
                        var target = document.getElementById(this.dataset.target);
                        if (target) target.style.display = 'block';
                    });
                });

                // Make the proof field required ONLY when yemeni_wallets is selected.
                document.addEventListener('submit', function (e) {
                    var selectedGw = document.querySelector('[name="payment_gateway"]:checked');
                    if (!selectedGw || selectedGw.value !== 'yemeni_wallets') return;

                    var proofInput = document.getElementById('payment_proof');
                    if (proofInput && !proofInput.files.length) {
                        e.preventDefault();
                        alert('{{ __("Please attach a screenshot of your transfer before completing the order.") }}');
                        proofInput.focus();
                    }
                }, true);
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initYemeniWalletsWidget);
        } else {
            initYemeniWalletsWidget();
        }
    })();
    </script>
</div>
