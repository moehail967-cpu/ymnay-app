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
        --main-color-one:     var(--vl-plum, #5C1F6B);
        --main-color-one-rgb: 92, 31, 107;
        --heading-color:      var(--vl-dark, #1A0820);
        background:           var(--vl-bg, #F8F5F2);
    }
    .ps-page .ps-card {
        border-color: var(--vl-border, #E0D4E8);
        border-radius: var(--vl-radius, 2px);
        box-shadow: var(--vl-shadow, 0 16px 48px -8px rgba(92,31,107,.18));
    }
    .ps-page .ps-card-head {
        background: var(--vl-plum-light, #F0E8F4);
        border-color: var(--vl-border, #E0D4E8);
    }
    .ps-page .ps-card-head-icon { background: rgba(92, 31, 107, .15); }
    .ps-page .ps-card-title {
        font-family: var(--vl-font-body, 'Montserrat', sans-serif);
        color: var(--vl-dark, #1A0820);
    }
    .ps-page .ps-thank {
        font-family: var(--vl-font-head, 'Cormorant Garamond', Georgia, serif);
        color: var(--vl-dark, #1A0820);
    }
    .ps-page .ps-table thead th {
        background: var(--vl-plum-light, #F0E8F4);
        color: var(--vl-muted, #7A6A80);
    }
    .ps-page .ps-info-row { border-color: var(--vl-border, #E0D4E8); }
    .ps-page .ps-status-pill {
        background: var(--vl-plum-light, #F0E8F4);
        color: var(--vl-plum-deep, #3D1248);
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
