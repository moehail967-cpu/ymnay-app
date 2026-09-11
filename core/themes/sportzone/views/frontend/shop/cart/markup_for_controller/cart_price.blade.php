@php
    $subtotal = \Gloudemans\Shoppingcart\Facades\Cart::subtotal(2, '.', ',');
    $total    = \Gloudemans\Shoppingcart\Facades\Cart::priceTotal(2, '.', ',');
@endphp

<div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--sz-border);font-size:14px;">
    <span style="color:var(--sz-muted);font-family:var(--sz-font-head);text-transform:uppercase;letter-spacing:1px;font-size:12px;">{{ __('Subtotal') }}</span>
    <span style="font-weight:600;">{{ site_currency_symbol() }}{{ $subtotal }}</span>
</div>
<div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--sz-border);font-size:14px;">
    <span style="color:var(--sz-muted);font-family:var(--sz-font-head);text-transform:uppercase;letter-spacing:1px;font-size:12px;">{{ __('Tax (Incl)') }}</span>
    <span style="font-weight:600;">--</span>
</div>
<div style="display:flex;justify-content:space-between;padding:14px 0;border-bottom:2px solid var(--sz-border);">
    <span style="font-family:var(--sz-font-head);font-size:16px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--sz-dark);">{{ __('Total') }}</span>
    <span class="sz-price-sale" style="font-size:20px;">{{ site_currency_symbol() }}{{ $total }}</span>
</div>
