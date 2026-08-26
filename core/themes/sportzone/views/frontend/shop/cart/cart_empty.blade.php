@extends('tenant.frontend.frontend-page-master')

@php $is_wishlist = $wishlist ?? false; @endphp
@php $page_title = $is_wishlist ? __('Wishlist') : __('Cart'); @endphp

@section('title') {{ $page_title }} @endsection

@section('content')
<div class="sz-page-banner">
    <div class="container">
        <h1>{{ $page_title }}</h1>
        <div class="sz-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep"><i class="mdi mdi-chevron-right"></i></span>
            <span class="current">{{ $page_title }}</span>
        </div>
    </div>
</div>

<div style="padding:80px 0;background:var(--sz-bg);">
    <div class="container">
        <div style="max-width:480px;margin:0 auto;text-align:center;">

            <div style="width:100px;height:100px;border-radius:50%;background:var(--sz-navy);display:flex;align-items:center;justify-content:center;margin:0 auto 28px;">
                @if($is_wishlist)
                    <i class="mdi mdi-dumbbell" style="font-size:44px;color:var(--sz-red);"></i>
                @else
                    <i class="mdi mdi-cart-outline" style="font-size:44px;color:var(--sz-red);"></i>
                @endif
            </div>

            <h2 style="font-family:var(--sz-font-head);font-size:26px;font-weight:700;color:var(--sz-dark);text-transform:uppercase;letter-spacing:1.5px;margin-bottom:12px;">
                @if($is_wishlist)
                    {{ __('Your Wishlist is Empty') }}
                @else
                    {{ __('Your Cart is Empty') }}
                @endif
            </h2>

            <p style="font-family:var(--sz-font-body);font-size:15px;color:var(--sz-muted);line-height:1.7;margin-bottom:32px;">
                @if($is_wishlist)
                    {{ __('You haven\'t saved any items yet. Browse our collection and hit the heart icon to save products for later.') }}
                @else
                    {{ __('Looks like you haven\'t added anything to your cart yet. Explore our sports gear and get ready to perform.') }}
                @endif
            </p>

            <a href="{{ theme_shop_url() }}" class="sz-btn sz-btn-red" style="display:inline-flex;align-items:center;gap:8px;font-size:14px;">
                <i class="mdi mdi-storefront-outline"></i>
                {{ __('Browse Products') }}
            </a>

        </div>
    </div>
</div>
@endsection
