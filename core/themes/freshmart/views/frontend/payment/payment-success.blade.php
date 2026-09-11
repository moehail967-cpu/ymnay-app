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
        --main-color-one:     var(--fm-green, #2E7D32);
        --main-color-one-rgb: 46, 125, 50;
        --heading-color:      var(--fm-dark, #1B2E1B);
        background:           var(--fm-bg, #F8FDF8);
    }
    .ps-page .ps-card {
        border-color: var(--fm-border, #C8E6C9);
        border-radius: var(--fm-radius, 10px);
        box-shadow: var(--fm-shadow, 0 8px 28px -8px rgba(46,125,50,.18));
    }
    .ps-page .ps-card-head {
        background: var(--fm-green-light, #E8F5E9);
        border-color: var(--fm-border, #C8E6C9);
    }
    .ps-page .ps-card-head-icon { background: rgba(46, 125, 50, .15); }
    .ps-page .ps-card-title {
        font-family: var(--fm-font, 'Poppins', sans-serif);
        color: var(--fm-dark, #1B2E1B);
    }
    .ps-page .ps-thank {
        font-family: var(--fm-font, 'Poppins', sans-serif);
        color: var(--fm-dark, #1B2E1B);
    }
    .ps-page .ps-table thead th {
        background: var(--fm-green-light, #E8F5E9);
        color: var(--fm-muted, #6A8A6A);
    }
    .ps-page .ps-info-row { border-color: var(--fm-border, #C8E6C9); }
    .ps-page .ps-status-pill {
        background: var(--fm-green-light, #E8F5E9);
        color: var(--fm-green-deep, #1B5E20);
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
