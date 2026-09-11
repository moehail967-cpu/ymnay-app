@extends('tenant.frontend.frontend-page-master')

@php $is_wishlist = $wishlist ?? false; @endphp

@section('title') {{ $is_wishlist ? __('Wishlist') : __('Cart') }} @endsection
@section('page-title') {{ $is_wishlist ? __('Wishlist') : __('Cart') }} @endsection

@section('content')
<div class="tz-page-banner">
    <div class="container tz-page-banner-content">
        <h1>{{ $is_wishlist ? __('My Wishlist') : __('Shopping Cart') }}</h1>
        <div class="tz-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right sep"></i>
            <span class="current">{{ $is_wishlist ? __('Wishlist') : __('Cart') }}</span>
        </div>
    </div>
</div>

<div class="tz-empty-state-wrap">
    <div class="tz-empty-state">
        <div class="tz-empty-icon {{ $is_wishlist ? 'tz-empty-icon-wish' : '' }}">
            <i class="mdi {{ $is_wishlist ? 'mdi-heart-outline' : 'mdi-cart-outline' }}"></i>
        </div>
        <h2 class="tz-empty-title">
            {{ $is_wishlist ? __('Your Wishlist is Empty') : __('Your Cart is Empty') }}
        </h2>
        <p class="tz-empty-text">
            {{ $is_wishlist ? __('Save items you love and shop them anytime.') : __("Looks like you haven't added anything yet.") }}
        </p>
        <a href="{{ theme_shop_url() }}" class="tz-btn tz-btn-blue">
            <i class="mdi mdi-arrow-left"></i> {{ __('Continue Shopping') }}
        </a>
    </div>
</div>
@endsection
