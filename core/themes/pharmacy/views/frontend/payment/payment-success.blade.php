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
    /* Map shared component variables to pharmacy tokens */
    .ps-page {
        --main-color-one:     var(--pf-mint, #00C896);
        --main-color-one-rgb: 0, 200, 150;
        --heading-color:      var(--pf-dark, #0A1628);
        background:           var(--pf-bg, #F4F7FF);
    }
    .ps-page .ps-card {
        border-color: var(--pf-border, #DDE4EF);
        border-radius: var(--pf-radius-lg, 16px);
        box-shadow: var(--pf-shadow-sm, 0 2px 16px rgba(12,27,58,.07));
    }
    .ps-page .ps-card-head {
        background: var(--pf-mint-light, #E0FBF4);
        border-color: var(--pf-border, #DDE4EF);
    }
    .ps-page .ps-card-head-icon {
        background: rgba(0,200,150,.15);
    }
    .ps-page .ps-card-title {
        color: var(--pf-dark, #0A1628);
        font-family: var(--pf-font, 'Plus Jakarta Sans', sans-serif);
        font-size: 15px;
    }
    .ps-page .ps-thank {
        font-family: var(--pf-display, 'DM Serif Display', Georgia, serif);
        color: var(--pf-dark, #0A1628);
        font-size: 32px;
    }
    .ps-page .ps-table thead th {
        background: var(--pf-mint-light, #E0FBF4);
        color: var(--pf-muted, #5A6A85);
    }
    .ps-page .ps-info-row { border-color: var(--pf-border, #DDE4EF); }
    .ps-page .ps-status-pill {
        background: var(--pf-mint-light, #E0FBF4);
        color: var(--pf-mint-deep, #009E78);
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
