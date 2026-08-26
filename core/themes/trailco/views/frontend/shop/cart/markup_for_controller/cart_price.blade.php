@php
    $subtotal = \Gloudemans\Shoppingcart\Facades\Cart::subtotal(2, '.', ',');
    $total    = \Gloudemans\Shoppingcart\Facades\Cart::priceTotal(2, '.', ',');
@endphp
<div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--tr-border);font-size:14px;">
    <span style="color:var(--tr-stone);">{{ __('Subtotal') }}</span>
    <span style="font-weight:700;">{{ site_currency_symbol() }}{{ $subtotal }}</span>
</div>
<div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--tr-border);font-size:14px;">
    <span style="color:var(--tr-stone);">{{ __('Tax (Incl)') }}</span>
    <span style="font-weight:700;">--</span>
</div>
<div style="display:flex;justify-content:space-between;padding:14px 0;border-bottom:1px solid var(--tr-border);">
    <span style="font-size:16px;font-weight:900;color:var(--tr-bark);">{{ __('Total') }}</span>
    <span style="font-size:18px;font-weight:900;color:var(--tr-bark);">{{ site_currency_symbol() }}{{ $total }}</span>
</div>
{!! apply_filters('nazmart:cart_summary', '') !!}
