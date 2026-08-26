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
        --main-color-one:     var(--fp-green, #00FF41);
        --main-color-one-rgb: 0, 255, 65;
        --heading-color:      var(--fp-text, #E0E0E0);
        background:           var(--fp-bg, #0A0A0A);
    }
    /* Dark theme card overrides */
    .ps-page .ps-card { background: var(--fp-mid, #141414); border-color: var(--fp-border, #1A3A1A); border-radius: var(--fp-radius, 4px); box-shadow: var(--fp-shadow, 0 8px 32px -8px rgba(0,255,65,.15)); }
    .ps-page .ps-card-head { background: var(--fp-dark, #1A1A1A); border-color: var(--fp-border, #1A3A1A); }
    .ps-page .ps-card-head-icon { background: rgba(0, 255, 65, .15); }
    .ps-page .ps-card-title { color: var(--fp-text, #E0E0E0); font-family: var(--fp-font-body, 'Barlow', sans-serif); }
    .ps-page .ps-thank { color: var(--fp-text, #E0E0E0); font-family: var(--fp-font-body, 'Barlow', sans-serif); }
    .ps-page .ps-sub { color: var(--fp-muted, #666666); }
    .ps-page .ps-info-label { color: var(--fp-muted, #666666); }
    .ps-page .ps-info-val { color: var(--fp-text, #E0E0E0); }
    .ps-page .ps-info-row { border-color: var(--fp-border, #1A3A1A); }
    .ps-page .ps-table thead th { background: var(--fp-dark, #1A1A1A); color: var(--fp-muted, #666666); }
    .ps-page .ps-table tbody td { color: var(--fp-text, #E0E0E0); }
    .ps-page .ps-total-row { color: var(--fp-muted, #666666); }
    .ps-page .ps-total-row span:last-child { color: var(--fp-text, #E0E0E0); }
    .ps-page .ps-qty { background: var(--fp-dark, #1A1A1A); color: var(--fp-text, #E0E0E0); }
    .ps-page .ps-status-pill { background: rgba(0, 255, 65,.2); color: var(--fp-green, #00FF41); }
    .billing-details li { text-transform: capitalize; }
    .vat-tax { font-size: 10px; }
</style>

<div class="ps-page">
    <div class="container-fluid">
        @include('themes.components.common-payment-success')
    </div>
</div>
@endsection
