@if (isset($payment_details))
    @if (empty($payment_details))
        @php
            header("Location: " . theme_home_url(), true, 302);
            exit();
        @endphp
    @endif
@endif

@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Payment Success From:') }} {{ $payment_details->name }} @endsection
@section('page-title') {{ __('Payment Success For:') }} {{ $payment_details->name }} @endsection

@section('content')
<div class="cs-payment-success-page">
    <div class="container-fluid">
        @include('themes.components.common-payment-success')
    </div>
</div>
@endsection
