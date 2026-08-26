<div class="modal-header">
    <h5 class="modal-title">{{ __('Pay Commission') }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
    <p class="fw-semibold mb-3">
        {{ __('Total Amount to Pay:') }}
        <strong>{{ amount_with_currency_symbol($totalUnpaid) }}</strong>
    </p>

    <form id="commissionPaymentForm" method="POST" action="{{ route('landlord.tenant.commission.pay', 'all') }}">
        @csrf
        <input type="hidden" name="amount" value="{{ $totalUnpaid }}">

        @if($showWallet)
            <div class="form-check mb-3">
                <input class="form-check-input" type="radio" name="payment_gateway" value="wallet" id="walletOption">
                <label class="form-check-label" for="walletOption">
                    {{ __('Pay using Wallet Balance') }} –
                    <strong>{{ amount_with_currency_symbol($walletBalance) }}</strong>
                </label>
            </div>
            <hr>
            <p class="text-muted small mb-2">{{ __('Or choose another payment method:') }}</p>
        @endif

        <div class="gateway-wrapper">
            {!! $gatewayForm !!}
        </div>

        <div class="mt-4 text-end">
            <button type="submit" class="btn btn-primary w-100">{{ __('Confirm Payment') }}</button>
        </div>
    </form>
</div>
