@if($balance > 0)
<div class="lp-widget" id="lp-widget">
    <div class="lp-widget-header">
        <i class="mdi mdi-star-circle-outline lp-widget-icon"></i>
        <span class="lp-widget-title">{{ __('Your Points') }}</span>
        <span class="lp-widget-balance">{{ number_format($balance) }} {{ __('pts') }}</span>
    </div>

    @if($balance >= $minRedeem)
    <div class="lp-widget-body">
        @if($redeeming > 0)
            <p class="lp-widget-applied">
                <i class="mdi mdi-check-circle-outline"></i>
                {{ __(':p points applied (:v off)', ['p' => number_format($redeeming), 'v' => amount_with_currency_symbol($redeeming / ($redeemRate ?: 1))]) }}
            </p>
            <button type="button" class="lp-widget-cancel" id="lp-cancel-btn"
                    data-url="{{ $cancelUrl }}">
                {{ __('Remove') }}
            </button>
        @else
            <p class="lp-widget-hint">
                {{ __('Worth :v — apply to this order?', ['v' => amount_with_currency_symbol($redeemValue)]) }}
            </p>
            <button type="button" class="lp-widget-apply" id="lp-apply-btn"
                    data-url="{{ $redeemUrl }}"
                    data-points="{{ $balance }}">
                {{ __('Use :p points', ['p' => number_format($balance)]) }}
            </button>
        @endif
        <div id="lp-widget-msg" class="lp-widget-msg"></div>
    </div>
    @else
    <div class="lp-widget-body">
        <p class="lp-widget-hint lp-widget-hint-muted">
            {{ __(':n more points needed to redeem', ['n' => number_format($minRedeem - $balance)]) }}
        </p>
    </div>
    @endif
</div>

<style>
.lp-widget{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:14px 16px;margin-bottom:16px;font-family:inherit}
.lp-widget-header{display:flex;align-items:center;gap:8px}
.lp-widget-icon{font-size:20px;color:#f59e0b}
.lp-widget-title{font-size:14px;font-weight:600;color:#111827;flex:1}
.lp-widget-balance{font-size:13px;font-weight:700;color:#f59e0b;background:#fef3c7;padding:2px 8px;border-radius:20px}
.lp-widget-body{margin-top:10px;border-top:1px solid #f3f4f6;padding-top:10px}
.lp-widget-hint{font-size:13px;color:#6b7280;margin:0 0 8px}
.lp-widget-hint-muted{color:#9ca3af;margin:0}
.lp-widget-applied{font-size:13px;color:#059669;margin:0 0 6px;display:flex;align-items:center;gap:4px}
.lp-widget-apply{background:#f59e0b;color:#fff;border:none;border-radius:8px;padding:7px 14px;font-size:13px;font-weight:600;cursor:pointer;transition:background .15s}
.lp-widget-apply:hover{background:#d97706}
.lp-widget-cancel{background:#fff;color:#6b7280;border:1px solid #d1d5db;border-radius:8px;padding:5px 12px;font-size:12px;cursor:pointer;transition:all .15s}
.lp-widget-cancel:hover{background:#f9fafb;color:#374151}
.lp-widget-msg{font-size:12px;margin-top:6px;color:#dc2626}
</style>

<script>
(function () {
    var csrf = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

    var applyBtn  = document.getElementById('lp-apply-btn');
    var cancelBtn = document.getElementById('lp-cancel-btn');
    var msg       = document.getElementById('lp-widget-msg');

    if (applyBtn) {
        applyBtn.addEventListener('click', function () {
            applyBtn.disabled = true;
            fetch(applyBtn.dataset.url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ points: parseInt(applyBtn.dataset.points) })
            })
            .then(r => r.json())
            .then(res => {
                if (res.type === 'success') {
                    location.reload();
                } else {
                    if (msg) msg.textContent = res.msg;
                    applyBtn.disabled = false;
                }
            })
            .catch(() => { applyBtn.disabled = false; });
        });
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', function () {
            cancelBtn.disabled = true;
            fetch(cancelBtn.dataset.url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json', 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(() => location.reload())
            .catch(() => { cancelBtn.disabled = false; });
        });
    }
}());
</script>
@endif
