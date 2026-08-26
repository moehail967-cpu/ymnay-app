@php
    $subtotal = \Gloudemans\Shoppingcart\Facades\Cart::subtotal(2, '.', ',');
    $total    = \Gloudemans\Shoppingcart\Facades\Cart::priceTotal(2, '.', ',');
@endphp
@php $rowSt = 'display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--tz-border);font-size:13px;'; @endphp

<div style="{{ $rowSt }}"><span style="color:var(--tz-muted);">{{ __('Subtotal') }}</span><span style="font-weight:600;color:var(--tz-text);">{{ site_currency_symbol() }}{{ $subtotal }}</span></div>
<div style="{{ $rowSt }}"><span style="color:var(--tz-muted);">{{ __('Tax (Incl)') }}</span><span style="font-weight:600;color:var(--tz-text);">--</span></div>
<div style="display:flex;justify-content:space-between;padding:14px 0;font-size:15px;font-weight:800;">
    <span style="color:var(--tz-text);">{{ __('Total Amount') }}</span>
    <span style="color:var(--tz-blue);">{{ site_currency_symbol() }}{{ $total }}</span>
</div>
