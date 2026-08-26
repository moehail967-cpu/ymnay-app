@php
    $subtotal = \Gloudemans\Shoppingcart\Facades\Cart::subtotal(2, '.', ',');
    $total    = \Gloudemans\Shoppingcart\Facades\Cart::priceTotal(2, '.', ',');
@endphp
<div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px dashed var(--ms-border);font-size:13px;">
    <span style="color:var(--ms-muted);">{{ __('Subtotal') }}</span>
    <span style="font-weight:600;color:var(--ms-dark);">{{ site_currency_symbol() }}{{ $subtotal }}</span>
</div>
<div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px dashed var(--ms-border);font-size:13px;">
    <span style="color:var(--ms-muted);">{{ __('Shipping') }}</span>
    <span style="font-weight:500;color:var(--ms-muted);">{{ __('Calculated at checkout') }}</span>
</div>
<div style="display:flex;justify-content:space-between;padding:16px 0;font-size:16px;font-weight:700;color:var(--ms-dark);">
    <span>{{ __('Total') }}</span>
    <span style="color:var(--ms-linen-d);">{{ site_currency_symbol() }}{{ $total }}</span>
</div>
