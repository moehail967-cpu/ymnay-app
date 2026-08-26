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
                    <h2 class="cs-result-title">{{ __('Your Order Has Been Cancelled') }}</h2>
                    <a href="{{ theme_home_url() }}" class="cs-result-btn">
                        <i class="las la-home"></i> {{ __('Back To Home') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
