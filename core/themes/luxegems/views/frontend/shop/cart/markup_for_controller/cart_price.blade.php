@php
    $subtotal = \Gloudemans\Shoppingcart\Facades\Cart::subtotal(2, '.', ',');
    $total    = \Gloudemans\Shoppingcart\Facades\Cart::priceTotal(2, '.', ',');
@endphp
<div class="lg-summary-row">
    <span>{{ __('Subtotal') }}</span>
    <span class="lg-cart-price">{{ site_currency_symbol() }}{{ $subtotal }}</span>
</div>
<div class="lg-summary-row">
    <span>{{ __('Tax (Incl)') }}</span>
    <span>--</span>
</div>
<div class="lg-summary-row total">
    <span>{{ __('Total Amount') }}</span>
    <span>{{ site_currency_symbol() }}{{ $total }}</span>
</div>
