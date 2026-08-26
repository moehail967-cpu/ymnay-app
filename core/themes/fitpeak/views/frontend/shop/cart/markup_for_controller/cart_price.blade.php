@php
    $subtotal = \Gloudemans\Shoppingcart\Facades\Cart::subtotal(2, '.', ',');
    $total    = \Gloudemans\Shoppingcart\Facades\Cart::priceTotal(2, '.', ',');
@endphp

<div class="fp-summary-row">
    <span>{{ __('Subtotal') }}</span>
    <span class="fp-summary-price">{{ site_currency_symbol() }}{{ $subtotal }}</span>
</div>
<div class="fp-summary-row">
    <span>{{ __('Tax (Incl)') }}</span>
    <span class="fp-summary-price">--</span>
</div>
<div class="fp-summary-row total">
    <span>{{ __('Total Amount') }}</span>
    <span class="fp-summary-price" style="color:var(--fp-green);">{{ site_currency_symbol() }}{{ $total }}</span>
</div>
