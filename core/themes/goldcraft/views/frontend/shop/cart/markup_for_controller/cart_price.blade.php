@php
    $subtotal = \Gloudemans\Shoppingcart\Facades\Cart::subtotal(2, '.', ',');
    $total    = \Gloudemans\Shoppingcart\Facades\Cart::priceTotal(2, '.', ',');
@endphp
<div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px dashed var(--gc-border);font-size:13px;">
    <span style="color:var(--gc-muted);font-style:italic;">{{ __('Subtotal') }}</span>
    <span style="font-weight:700;">{{ site_currency_symbol() }}{{ $subtotal }}</span>
</div>
<div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px dashed var(--gc-border);font-size:13px;">
    <span style="color:var(--gc-muted);font-style:italic;">{{ __('Tax (Incl)') }}</span>
    <span style="font-weight:700;">--</span>
</div>
<div style="display:flex;justify-content:space-between;padding:16px 0;font-size:16px;font-weight:700;color:var(--gc-dark);">
    <span style="font-style:italic;">{{ __('Total') }}</span>
    <span class="gc-price">{{ site_currency_symbol() }}{{ $total }}</span>
</div>
