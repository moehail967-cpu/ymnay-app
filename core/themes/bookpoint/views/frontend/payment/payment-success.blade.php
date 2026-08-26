@if (isset($payment_details))
    @if (empty($payment_details))
        @php header("Location: " . url('/'), true, 302); exit(); @endphp
    @endif
@endif

@extends('tenant.frontend.frontend-page-master')
@section('title') {{__('Payment Success From:')}} {{$payment_details->name}} @endsection
@section('page-title') {{__('Payment Success For:')}} {{$payment_details->name}} @endsection
@section('content')
<style>
    .ps-page {
        --main-color-one: var(--bp-accent, #118668);
        --main-color-one-rgb: 17, 134, 104;
        --heading-color: var(--heading-color, #1a1a1a);
        background: #f5f9f7;
    }
    .ps-page .ps-card { border-color: #d0e8e0; border-radius: 10px; box-shadow: 0 8px 32px -8px rgba(17,134,104,.15); }
    .ps-page .ps-card-head { background: #f0faf6; border-color: #d0e8e0; }
    .ps-page .ps-card-head-icon { background: rgba(17,134,104,.12); }
    .ps-page .ps-card-title { color: #1a1a1a; }
    .ps-page .ps-thank { color: #1a1a1a; }
    .ps-page .ps-table thead th { background: #f0faf6; color: #555; }
    .ps-page .ps-info-row { border-color: #d0e8e0; }
    .ps-page .ps-status-pill { background: #f0faf6; color: #118668; }
    .billing-details li { text-transform: capitalize; }
    .vat-tax { font-size: 10px; }
</style>
<div class="ps-page">
    <div class="container-fluid">
        @include('themes.components.common-payment-success')
    </div>
</div>
@endsection
