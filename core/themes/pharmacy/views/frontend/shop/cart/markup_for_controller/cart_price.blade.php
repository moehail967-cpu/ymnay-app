@php
    $subtotal = \Gloudemans\Shoppingcart\Facades\Cart::subtotal(2, '.', ',');
    $total    = \Gloudemans\Shoppingcart\Facades\Cart::priceTotal(2, '.', ',');
@endphp

<div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px dashed var(--pf-border);font-size:14px;">
    <span style="color:var(--pf-muted);">{{ __('Subtotal') }}</span>
    <span style="font-weight:600;">{{ site_currency_symbol() }}{{ $subtotal }}</span>
</div>
<div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px dashed var(--pf-border);font-size:14px;">
    <span style="color:var(--pf-muted);">{{ __('Tax (Incl)') }}</span>
    <span style="font-weight:600;">--</span>
</div>
<div style="display:flex;justify-content:space-between;padding:14px 0;font-size:16px;font-weight:800;color:var(--pf-dark);">
    <span>{{ __('Total Amount') }}</span>
    <span style="color:var(--pf-teal);">{{ site_currency_symbol() }}{{ $total }}</span>
</div>
