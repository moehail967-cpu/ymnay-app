@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Your Cart') }} @endsection
@section('page-title') {{ __('Your Cart') }} @endsection

@section('content')
<div class="ch-page-banner">
    <div class="container ch-page-banner-content">
        <h1>{{ __('Your Cart') }}</h1>
        <div class="ch-breadcrumb" style="margin-top:8px;">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <span class="current">{{ __('Cart') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:72px 0;">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div style="text-align:center;">
                <div style="font-size:72px;margin-bottom:16px;">🛒</div>
                <h2 style="font-size:24px;font-weight:900;color:var(--ch-dark);margin-bottom:12px;">
                    {{ __('Your cart is empty') }}
                </h2>
                <p style="font-size:14px;color:var(--ch-muted);margin-bottom:32px;line-height:1.6;">
                    {{ __("Looks like you haven't added anything to your cart yet. Start exploring our delicious offerings!") }}
                </p>
                <a href="{{ theme_shop_url() }}" class="ch-btn ch-btn-red" style="display:inline-flex;align-items:center;gap:8px;padding:14px 28px;font-size:15px;">
                    <i class="las la-shopping-bag"></i> {{ __('Continue Shopping') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
