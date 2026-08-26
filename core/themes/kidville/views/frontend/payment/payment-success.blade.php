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
    {{__('Payment Success From:')}} {{$payment_details->name}}
@endsection
@section('page-title')
    {{__('Payment Success For:')}} {{$payment_details->name}}
@endsection
@section('content')
<style>
    .ps-page {
        --main-color-one:     var(--kv-blue, #1E88E5);
        --main-color-one-rgb: 30, 136, 229;
        --heading-color:      var(--kv-dark, #1A1A2E);
        background:           var(--kv-light, #FAFAFA);
    }
    .ps-page .ps-card {
        border-color: var(--kv-border, #E8ECEF);
        border-radius: var(--kv-radius, 16px);
        box-shadow: var(--kv-shadow, 0 8px 32px -8px rgba(30,136,229,.2));
    }
    .ps-page .ps-card-head {
        background: rgba(30, 136, 229, 0.1);
        border-color: var(--kv-border, #E8ECEF);
    }
    .ps-page .ps-card-head-icon { background: rgba(30, 136, 229, .15); }
    .ps-page .ps-card-title {
        font-family: 'Inter', sans-serif;
        color: var(--kv-dark, #1A1A2E);
    }
    .ps-page .ps-thank {
        font-family: 'Inter', sans-serif;
        color: var(--kv-dark, #1A1A2E);
    }
    .ps-page .ps-table thead th {
        background: rgba(30, 136, 229, 0.1);
        color: #6b7280;
    }
    .ps-page .ps-info-row { border-color: var(--kv-border, #E8ECEF); }
    .ps-page .ps-status-pill {
        background: rgba(30, 136, 229, 0.1);
        color: var(--kv-blue, #1E88E5);
    }
    .billing-details li { text-transform: capitalize; }
    .vat-tax { font-size: 10px; }
</style>

<div class="ps-page">
    <div class="container-fluid">
        @include('themes.components.common-payment-success')
    </div>
</div>
@endsection
