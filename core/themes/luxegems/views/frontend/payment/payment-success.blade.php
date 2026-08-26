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
        --main-color-one:     var(--lx-gold, #C9A84C);
        --main-color-one-rgb: 201, 168, 76;
        --heading-color:      var(--lg-dark, #E8E0D0);
        background:           var(--lg-bg, #080808);
    }
    /* Dark theme card overrides */
    .ps-page .ps-card { background: var(--lg-surface, #111111); border-color: var(--lg-border, #2A2410); border-radius: var(--lg-radius, 2px); box-shadow: var(--lg-shadow, 0 16px 48px -8px rgba(0,0,0,.6)); }
    .ps-page .ps-card-head { background: var(--lg-card, #1A1810); border-color: var(--lg-border, #2A2410); }
    .ps-page .ps-card-head-icon { background: rgba(201, 168, 76, .15); }
    .ps-page .ps-card-title { color: var(--lg-dark, #E8E0D0); font-family: var(--lg-font-body, 'Raleway', sans-serif); }
    .ps-page .ps-thank { color: var(--lg-dark, #E8E0D0); font-family: var(--lg-font-body, 'Raleway', sans-serif); }
    .ps-page .ps-sub { color: var(--lg-muted, #706A5A); }
    .ps-page .ps-info-label { color: var(--lg-muted, #706A5A); }
    .ps-page .ps-info-val { color: var(--lg-dark, #E8E0D0); }
    .ps-page .ps-info-row { border-color: var(--lg-border, #2A2410); }
    .ps-page .ps-table thead th { background: var(--lg-card, #1A1810); color: var(--lg-muted, #706A5A); }
    .ps-page .ps-table tbody td { color: var(--lg-dark, #E8E0D0); }
    .ps-page .ps-total-row { color: var(--lg-muted, #706A5A); }
    .ps-page .ps-total-row span:last-child { color: var(--lg-dark, #E8E0D0); }
    .ps-page .ps-qty { background: var(--lg-card, #1A1810); color: var(--lg-dark, #E8E0D0); }
    .ps-page .ps-status-pill { background: rgba(201, 168, 76,.2); color: var(--lx-gold, #C9A84C); }
    .billing-details li { text-transform: capitalize; }
    .vat-tax { font-size: 10px; }
</style>

<div class="ps-page">
    <div class="container-fluid">
        @include('themes.components.common-payment-success')
    </div>
</div>
@endsection
