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
        --main-color-one:     var(--ch-red, #C0392B);
        --main-color-one-rgb: 192, 57, 43;
        --heading-color:      var(--ch-dark, #1A0A00);
        background:           var(--ch-cream, #FDF8F3);
    }
    .ps-page .ps-card {
        border-color: var(--ch-border, #EAD9CF);
        border-radius: var(--ch-radius, 14px);
        box-shadow: var(--ch-shadow, 0 8px 32px -8px rgba(192,57,43,.18));
    }
    .ps-page .ps-card-head {
        background: var(--ch-red-light, #FFF0EC);
        border-color: var(--ch-border, #EAD9CF);
    }
    .ps-page .ps-card-head-icon { background: rgba(192, 57, 43, .15); }
    .ps-page .ps-card-title {
        font-family: 'Inter', sans-serif;
        color: var(--ch-dark, #1A0A00);
    }
    .ps-page .ps-thank {
        font-family: 'Inter', sans-serif;
        color: var(--ch-dark, #1A0A00);
    }
    .ps-page .ps-table thead th {
        background: var(--ch-red-light, #FFF0EC);
        color: var(--ch-muted, #8B6050);
    }
    .ps-page .ps-info-row { border-color: var(--ch-border, #EAD9CF); }
    .ps-page .ps-status-pill {
        background: var(--ch-red-light, #FFF0EC);
        color: var(--ch-red-deep, #96281B);
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
