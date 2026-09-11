@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Order Details For:') }} {{ $order_details->package_name }} @endsection
@section('page-title') {{ __('Order Details For:') }} {{ $order_details->package_name }} @endsection

@section('content')
<div class="cs-result-page">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <div class="col-lg-6">
                <div class="cs-dash-box mb-3">
                    <div class="cs-dash-box-head">
                        <i class="las la-list-ul"></i> {{ __('Order Details') }}
                    </div>
                    <div class="cs-dash-box-body">
                        <div class="cs-dash-summary-rows">
                            <div class="cs-dash-summary-row"><span>{{ __('Order Status') }}</span><span class="cs-dash-badge cs-dash-badge-warning" style="text-transform:capitalize;">{{ $order_details->status }}</span></div>
                            <div class="cs-dash-summary-row"><span>{{ __('Payment Method') }}</span><span>{{ str_replace('_', ' ', $payment_details->package_gateway) }}</span></div>
                            @if(!empty($payment_details->coupon))
                            <div class="cs-dash-summary-row"><span>{{ __('Paid After Discount') }}</span><span class="cs-dash-td-price">{{ amount_with_currency_symbol($payment_details->package_price) }}</span></div>
                            @endif
                            <div class="cs-dash-summary-row"><span>{{ __('Payment Status') }}</span><span style="text-transform:capitalize;">{{ $payment_details->status }}</span></div>
                            @if($payment_details->transaction_id)
                            <div class="cs-dash-summary-row"><span>{{ __('Transaction ID') }}</span><span class="cs-dash-td-bold">{{ $payment_details->transaction_id }}</span></div>
                            @endif
                            <div class="cs-dash-summary-row"><span>{{ __('Date') }}</span><span class="cs-dash-td-muted">{{ date_format($payment_details->created_at, 'd M, Y') }}</span></div>
                        </div>
                    </div>
                </div>

                <div class="cs-dash-box mb-3">
                    <div class="cs-dash-box-head">
                        <i class="las la-user"></i> {{ __('Billing Details') }}
                    </div>
                    <div class="cs-dash-box-body">
                        <div class="cs-dash-summary-rows">
                            <div class="cs-dash-summary-row"><span>{{ __('Name') }}</span><span class="cs-dash-td-bold">{{ $payment_details->name }}</span></div>
                            <div class="cs-dash-summary-row"><span>{{ __('Email') }}</span><span>{{ $payment_details->email }}</span></div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-3">
                    @if(auth()->guard('web')->check())
                        <a href="{{ theme_user_dashboard_url() }}" class="cs-dash-submit-btn">
                            <i class="las la-tachometer-alt"></i> {{ __('Go To Dashboard') }}
                        </a>
                    @else
                        <a href="{{ theme_home_url() }}" class="cs-dash-submit-btn">
                            <i class="las la-home"></i> {{ __('Back To Home') }}
                        </a>
                    @endif
                </div>
            </div>

            <div class="col-lg-4">
                <div class="cs-dash-box">
                    <div class="cs-dash-box-head">
                        <i class="las la-cube"></i> {{ $package_details->title }}
                    </div>
                    <div class="cs-dash-box-body">
                        @if($package_details->image)
                        <div class="text-center mb-3">
                            {!! render_image_markup_by_attachment_id($package_details->image) !!}
                        </div>
                        @endif
                        <div class="cs-dash-total-value mb-3">{{ amount_with_currency_symbol($package_details->price) }} <small class="cs-price-period">/ {{ $package_details->type }}</small></div>
                        <ul class="cs-pkg-features">
                            @foreach(explode(',', $package_details->features) as $item)
                            <li><i class="las la-check-circle"></i> {{ trim($item) }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
