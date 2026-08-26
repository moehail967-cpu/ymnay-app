@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Track Order') }} @endsection
@section('page-title') {{ __('Track Order') }} @endsection

@section('content')
<div class="cs-page-banner">
    <div class="container">
        <h1 class="cs-page-banner-title">{{ __('Track Order') }}</h1>
        <div class="cs-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="cs-breadcrumb-sep"><i class="las la-angle-right"></i></span>
            <span class="cs-breadcrumb-current">{{ __('Track Order') }}</span>
        </div>
    </div>
</div>

<div class="cs-track-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="cs-dash-box">
                    <div class="cs-dash-box-head">
                        <i class="las la-search-location"></i> {{ __('Order Tracking') }}
                    </div>
                    <div class="cs-dash-box-body">
                        <p class="cs-track-desc">{{ __('To track your order please enter your Order ID in the box below and press the "Track" button. This was given to you on your receipt and in the confirmation email you should have received.') }}</p>

                        <form action="{{ theme_order_track_url() }}" method="POST" class="cs-track-form">
                            @csrf
                            <div class="cs-track-input-wrap">
                                <label class="cs-dash-label">{{ __('Order ID') }}</label>
                                <input name="order_id" type="text" class="cs-dash-input"
                                       placeholder="{{ __('Example: 125') }}" value="{{ old('order_id') }}">
                            </div>
                            <button type="submit" class="cs-dash-submit-btn cs-track-btn">
                                <i class="las la-search"></i> {{ __('Track Now') }}
                            </button>
                        </form>

                        @if(session('track'))
                        <div class="cs-track-result mt-4">
                            <div class="alert {{ session('track')->status ? 'alert-success' : 'alert-danger' }} text-capitalize">
                                {!! session('track')->message !!}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
