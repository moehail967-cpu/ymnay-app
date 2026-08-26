@php
    $subtotal = \Gloudemans\Shoppingcart\Facades\Cart::subtotal(2, '.', ',');
    $total    = \Gloudemans\Shoppingcart\Facades\Cart::priceTotal(2, '.', ',');
@endphp
<div class="d-flex justify-content-between py-2 border-bottom tn-summary-sep">
    <span class="tn-summary-label">{{ __('Subtotal') }}</span>
    <strong id="tn_cart_subtotal">{{ site_currency_symbol() }}{{ $subtotal }}</strong>
</div>
@if(theme_cart_tax() > 0)
<div class="d-flex justify-content-between py-2 border-bottom tn-summary-sep">
    <span class="tn-summary-label">{{ __('Tax') }}</span>
    <strong>{{ amount_with_currency_symbol(theme_cart_tax()) }}</strong>
</div>
@endif
<div class="d-flex justify-content-between py-2 mb-3">
    <span class="tn-summary-total-label">{{ __('Total') }}</span>
    <strong class="tn-summary-total-val" id="tn_cart_total">{{ site_currency_symbol() }}{{ $total }}</strong>
</div>
{!! apply_filters('nazmart:cart_summary', '') !!}
