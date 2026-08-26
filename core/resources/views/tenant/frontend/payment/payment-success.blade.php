@if (isset($payment_details))
    @if (empty($payment_details))
        @php
            header("Location: " . url('/'), true, 302);
            exit();
        @endphp
    @endif
@endif

@extends('tenant.frontend.frontend-page-master')
@section('title')
    {{__(sprintf('Payment Success From: %s', $payment_details->name))}}
@endsection

@section('page-title')
    {{__(sprintf('Payment Success From: %s', $payment_details->name))}}
@endsection
@section('content')
    @include('themes.components.common-payment-success')
@endsection
