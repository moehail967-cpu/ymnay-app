@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Order Cancelled') }} @endsection
@section('page-title') {{ __('Order Cancelled') }} @endsection

@section('content')
<div class="cs-result-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="cs-result-card cs-result-card-cancel">
                    <div class="cs-result-icon">
                        <i class="las la-times-circle"></i>
                    </div>
                    <h2 class="cs-result-title">
                        {{ get_static_option('site_order_cancel_page_title') ?? __('Your order has been cancelled') }}
                    </h2>
                    @php
                        $subtitle = get_static_option('site_order_cancel_page_subtitle');
                        $subtitle = str_replace('{pkname}', $order_details->package_name ?? '', $subtitle);
                    @endphp
                    @if($subtitle)
                    <p class="cs-result-sub">{{ $subtitle }}</p>
                    @endif
                    @if(get_static_option('site_order_cancel_page_description'))
                    <p class="cs-result-desc">{{ get_static_option('site_order_cancel_page_description') }}</p>
                    @endif
                    <a href="{{ theme_home_url() }}" class="cs-result-btn">
                        <i class="las la-home"></i> {{ __('Back To Home') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
