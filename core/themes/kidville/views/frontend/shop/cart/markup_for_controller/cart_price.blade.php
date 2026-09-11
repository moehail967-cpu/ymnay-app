@php
    $subtotal = \Gloudemans\Shoppingcart\Facades\Cart::subtotal(2, '.', ',');
    $total    = \Gloudemans\Shoppingcart\Facades\Cart::priceTotal(2, '.', ',');
@endphp
<div class="kv-summary-row">
    <span>{{ __('Subtotal') }}</span>
    <span class="kv-summary-price">{{ site_currency_symbol() }}{{ $subtotal }}</span>
</div>
<div class="kv-summary-row">
    <span>{{ __('Tax (Incl)') }}</span>
    <span class="kv-summary-price">--</span>
</div>
<div class="kv-summary-row total">
    <span>{{ __('Total Amount') }}</span>
    <span class="kv-summary-price">{{ site_currency_symbol() }}{{ $total }}</span>
</div>
