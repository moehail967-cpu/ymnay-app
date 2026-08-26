<div class="lp-cs-wrap" id="lp-cart-summary">

    {{-- Header: icon + title + earn-preview badge --}}
    <div class="lp-cs-head">
        <i class="mdi mdi-star-circle-outline"></i>
        <span class="lp-cs-head-text">{{ __('Loyalty Points') }}</span>
        @if($earnPoints > 0)
        <span class="lp-cs-earn-badge">+{{ number_format($earnPoints) }} {{ __('pts') }}</span>
        @endif
    </div>

    {{-- Earn preview line --}}
    @if($earnPoints > 0)
    <div class="lp-cs-body" style="padding-top:0;margin-top:8px;border-top:none;">
        <p class="lp-cs-hint" style="margin-top:4px;">
            {{ __("You'll earn :n points on this order.", ['n' => number_format($earnPoints)]) }}
        </p>
    </div>
    @endif

    {{-- Redeem section (logged-in users with a balance only) --}}
    @auth('web')
    @if($balance > 0)
    <hr class="lp-cs-divider">
    <div class="lp-cs-body" style="padding-top:0;border-top:none;margin-top:0;">

        @if($redeeming > 0)
            <p class="lp-cs-applied">
                <i class="mdi mdi-check-circle-outline"></i>
                {{ __(':p pts applied — :v off', [
                    'p' => number_format($redeeming),
                    'v' => amount_with_currency_symbol(round($redeeming / ($redeemRate ?: 1), 2))
                ]) }}
            </p>
            <div class="lp-cs-actions">
                <button type="button" class="lp-cs-cancel-btn" id="lp-cs-cancel"
                        data-url="{{ url('/api/v1/plugins/loyalty-points/cancel') }}">
                    {{ __('Remove') }}
                </button>
            </div>

        @elseif($balance >= $minRedeem)
            <p class="lp-cs-hint">
                {{ __('You have :n pts — worth :v.', [
                    'n' => number_format($balance),
                    'v' => amount_with_currency_symbol($redeemValue)
                ]) }}
            </p>
            <div class="lp-cs-actions">
                <button type="button" class="lp-cs-apply-btn" id="lp-cs-apply"
                        data-url="{{ url('/api/v1/plugins/loyalty-points/redeem') }}"
                        data-points="{{ $balance }}">
                    {{ __('Use :p pts for :v off', [
                        'p' => number_format($balance),
                        'v' => amount_with_currency_symbol($redeemValue)
                    ]) }}
                </button>
            </div>

        @else
            <p class="lp-cs-hint">
                {{ __('You have :n pts. Earn :more more to start redeeming.', [
                    'n'    => number_format($balance),
                    'more' => number_format($minRedeem - $balance)
                ]) }}
            </p>
        @endif

        <div class="lp-cs-msg" id="lp-cs-msg" style="display:none;"></div>
    </div>
    @endif
    @endauth

</div>

<script>
(function () {
    var csrf      = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
    var applyBtn  = document.getElementById('lp-cs-apply');
    var cancelBtn = document.getElementById('lp-cs-cancel');
    var msgEl     = document.getElementById('lp-cs-msg');

    function showMsg(text, isError) {
        if (!msgEl) return;
        msgEl.textContent  = text;
        msgEl.style.color  = isError ? '#dc2626' : '#059669';
        msgEl.style.display = 'block';
    }

    function ajaxPost(url, body, btn) {
        if (btn) btn.disabled = true;
        fetch(url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: body ? JSON.stringify(body) : undefined,
        })
        .then(function (r) { return r.json(); })
        .then(function (res) {
            if (res.type === 'success') {
                location.reload();
            } else {
                showMsg(res.msg || '{{ __("Something went wrong") }}', true);
                if (btn) btn.disabled = false;
            }
        })
        .catch(function () {
            showMsg('{{ __("Request failed") }}', true);
            if (btn) btn.disabled = false;
        });
    }

    if (applyBtn) {
        applyBtn.addEventListener('click', function () {
            ajaxPost(applyBtn.dataset.url, { points: parseInt(applyBtn.dataset.points) }, applyBtn);
        });
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', function () {
            ajaxPost(cancelBtn.dataset.url, null, cancelBtn);
        });
    }
}());
</script>
