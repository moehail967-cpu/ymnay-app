@extends('tenant.frontend.frontend-page-master')

@php $is_wishlist = $wishlist ?? false; @endphp

@section('title') {{ $is_wishlist ? __('My Wishlist') : __('Your Cart is Empty') }} @endsection
@section('page-title') {{ $is_wishlist ? __('My Wishlist') : __('Shopping Cart') }} @endsection

@section('content')
<div style="background:var(--pf-teal-light);padding:36px 0 28px;">
    <div class="container">
        <h2 style="font-size:26px;font-weight:700;color:var(--pf-dark);margin-bottom:8px;">
            {{ $is_wishlist ? __('My Wishlist') : __('Shopping Cart') }}
        </h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--pf-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--pf-teal);font-weight:600;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right"></i>
            <span>{{ $is_wishlist ? __('Wishlist') : __('Cart') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:80px 0 100px;">
    <div style="text-align:center;max-width:420px;margin:0 auto;">
        <div style="width:100px;height:100px;border-radius:50%;background:var(--pf-teal-light);display:flex;align-items:center;justify-content:center;margin:0 auto 24px;border:2px solid var(--pf-border);">
            @if($is_wishlist)
                <i class="las la-heart" style="font-size:48px;color:var(--pf-teal);"></i>
            @else
                <i class="las la-shopping-cart" style="font-size:48px;color:var(--pf-teal);"></i>
            @endif
        </div>
        <h2 style="font-size:22px;font-weight:700;color:var(--pf-dark);margin-bottom:10px;">
            {{ $is_wishlist ? __('Your wishlist is empty') : __('Your cart is empty') }}
        </h2>
        <p style="font-size:14px;color:var(--pf-muted);margin-bottom:28px;line-height:1.7;">
            {{ $is_wishlist
                ? __('Save products you love to your wishlist and find them here.')
                : __('Browse our pharmacy and health products and add items to your cart.') }}
        </p>
        <a href="{{ theme_shop_url() }}" class="pf-btn pf-btn-teal" style="padding:12px 32px;font-size:15px;">
            <i class="las la-store"></i> {{ __('Browse Products') }}
        </a>
    </div>
</div>
@endsection
