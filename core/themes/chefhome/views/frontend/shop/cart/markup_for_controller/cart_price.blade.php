@php
    $subtotal = \Gloudemans\Shoppingcart\Facades\Cart::subtotal(2, '.', ',');
    $total    = \Gloudemans\Shoppingcart\Facades\Cart::priceTotal(2, '.', ',');
@endphp

<div class="ch-summary-row">
    <span>{{ __('Subtotal') }}</span>
    <span class="ch-summary-price">{{ site_currency_symbol() }}{{ $subtotal }}</span>
</div>
<div class="ch-summary-row">
    <span>{{ __('Tax (Incl)') }}</span>
    <span class="ch-summary-price">--</span>
</div>
<div class="ch-summary-row total">
    <span>{{ __('Total Amount') }}</span>
    <span class="ch-summary-price">{{ site_currency_symbol() }}{{ $total }}</span>
</div>
