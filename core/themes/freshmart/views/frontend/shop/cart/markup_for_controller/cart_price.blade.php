@php
    $subtotal = \Gloudemans\Shoppingcart\Facades\Cart::subtotal(2, '.', ',');
    $total    = \Gloudemans\Shoppingcart\Facades\Cart::priceTotal(2, '.', ',');
@endphp

<div class="fm-summary-row">
    <span class="label">{{ __('Subtotal') }}</span>
    <span class="fm-summary-price">{{ site_currency_symbol() }}{{ $subtotal }}</span>
</div>
<div class="fm-summary-row">
    <span class="label">{{ __('Tax (Incl)') }}</span>
    <span class="fm-summary-price">--</span>
</div>
<div class="fm-summary-total">
    <span class="label">{{ __('Total Amount') }}</span>
    <span class="value">{{ site_currency_symbol() }}{{ $total }}</span>
</div>
