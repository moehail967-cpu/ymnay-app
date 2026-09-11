@php
    $subtotal = \Gloudemans\Shoppingcart\Facades\Cart::subtotal(2, '.', ',');
    $total    = \Gloudemans\Shoppingcart\Facades\Cart::priceTotal(2, '.', ',');
@endphp

<div class="ph-summary-row">
    <span>{{ __('Subtotal') }}</span>
    <span class="ph-summary-price">{{ site_currency_symbol() }}{{ $subtotal }}</span>
</div>
<div class="ph-summary-row">
    <span>{{ __('Tax (Incl)') }}</span>
    <span class="ph-summary-price">--</span>
</div>
<div class="ph-summary-row total">
    <span>{{ __('Total Amount') }}</span>
    <span class="ph-summary-price">{{ site_currency_symbol() }}{{ $total }}</span>
</div>
