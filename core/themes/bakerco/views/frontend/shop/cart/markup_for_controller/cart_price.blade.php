@php
    $subtotal = \Gloudemans\Shoppingcart\Facades\Cart::subtotal(2, '.', ',');
    $total    = \Gloudemans\Shoppingcart\Facades\Cart::priceTotal(2, '.', ',');
@endphp

<div class="bk-summary-row">
    <span>{{ __('Subtotal') }}</span>
    <span class="bk-summary-price">{{ site_currency_symbol() }}{{ $subtotal }}</span>
</div>
<div class="bk-summary-row">
    <span>{{ __('Tax (Incl)') }}</span>
    <span class="bk-summary-price">--</span>
</div>
<div class="bk-summary-row total">
    <span>{{ __('Total Amount') }}</span>
    <span class="bk-summary-price">{{ site_currency_symbol() }}{{ $total }}</span>
</div>
