@extends('tenant.frontend.frontend-page-master')
@section('title') {{__('Order Cancelled for:').' '.($order_details->name ?? '')}} @endsection
@section('page-title') {{__('Order Cancelled')}} @endsection
@section('content')
<div class="bp-page-banner">
    <div class="container">
        <h1>{{ __('Order Cancelled') }}</h1>
        <div class="bp-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span>/</span>
            <span class="current">{{ __('Order Cancelled') }}</span>
        </div>
    </div>
</div>
<div class="container" style="padding:80px 0;text-align:center;">
    <i class="las la-times-circle" style="font-size:80px;color:#e94560;display:block;margin-bottom:20px;"></i>
    <h2 style="font-weight:700;color:var(--heading-color,#1a1a1a);margin-bottom:12px;">
        {{ get_static_option('site_order_cancel_page_title') ?? __('Your order has been cancelled') }}
    </h2>
    <p style="color:#888;max-width:480px;margin:0 auto 32px;">
        @php
            $subtitle = get_static_option('site_order_cancel_page_subtitle') ?? '';
            $subtitle = str_replace('{pkname}', $order_details->package_name ?? '', $subtitle);
        @endphp
        {{ $subtitle }}
    </p>
    <p style="color:#888;max-width:480px;margin:0 auto 32px;">
        {{ get_static_option('site_order_cancel_page_description') }}
    </p>
    <div class="d-flex justify-content-center gap-3 flex-wrap">
        <a href="{{ theme_home_url() }}" class="bp-btn bp-btn-green"><i class="las la-home"></i> {{ __('Back to Home') }}</a>
        <a href="{{ theme_shop_url() }}" class="bp-btn bp-btn-outline"><i class="las la-store"></i> {{ __('Continue Shopping') }}</a>
    </div>
</div>
@endsection
