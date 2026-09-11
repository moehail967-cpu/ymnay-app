@if (isset($payment_details))
    @if (empty($payment_details))
        @php header("Location: " . url('/'), true, 302); exit(); @endphp
    @endif
@endif

@extends('tenant.frontend.frontend-page-master')
@section('title') {{__('Payment Success From:')}} {{$payment_details->name}} @endsection
@section('page-title') {{__('Payment Success For:')}} {{$payment_details->name}} @endsection
@section('content')
<section class="el-pay-section">
    <div class="container">
        @include('themes.components.common-payment-success')
    </div>
</section>
@endsection
