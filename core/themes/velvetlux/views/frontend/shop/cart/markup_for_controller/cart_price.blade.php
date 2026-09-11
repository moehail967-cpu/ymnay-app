@php
    $subtotal = \Gloudemans\Shoppingcart\Facades\Cart::subtotal(2, '.', ',');
    $total    = \Gloudemans\Shoppingcart\Facades\Cart::priceTotal(2, '.', ',');
@endphp
<div class="vl-summary-row">
    <span>{{ __('Subtotal') }}</span>
    <span class="vl-cart-price">{{ site_currency_symbol() }}{{ $subtotal }}</span>
</div>
<div class="vl-summary-row">
    <span>{{ __('Tax (Incl)') }}</span>
    <span>--</span>
</div>
<div class="vl-summary-total">
    <span>{{ __('Total Amount') }}</span>
    <span>{{ site_currency_symbol() }}{{ $total }}</span>
</div>
{!! apply_filters('nazmart:cart_summary', '') !!}
