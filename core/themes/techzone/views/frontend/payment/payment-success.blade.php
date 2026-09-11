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
        --main-color-one:     var(--tz-blue, #0066FF);
        --main-color-one-rgb: 0, 102, 255;
        --heading-color:      var(--tz-text, #CDD9E5);
        background:           var(--tz-bg, #0D1117);
    }
    /* Dark theme card overrides */
    .ps-page .ps-card { background: var(--tz-surface, #161B22); border-color: var(--tz-border, #30363D); border-radius: var(--tz-radius, 6px); box-shadow: var(--tz-shadow, 0 8px 32px -8px rgba(0,102,255,.2)); }
    .ps-page .ps-card-head { background: var(--tz-panel, #1C2128); border-color: var(--tz-border, #30363D); }
    .ps-page .ps-card-head-icon { background: rgba(0, 102, 255, .15); }
    .ps-page .ps-card-title { color: var(--tz-text, #CDD9E5); font-family: var(--tz-font, 'Inter', sans-serif); }
    .ps-page .ps-thank { color: var(--tz-text, #CDD9E5); font-family: var(--tz-font, 'Inter', sans-serif); }
    .ps-page .ps-sub { color: var(--tz-muted, #8B949E); }
    .ps-page .ps-info-label { color: var(--tz-muted, #8B949E); }
    .ps-page .ps-info-val { color: var(--tz-text, #CDD9E5); }
    .ps-page .ps-info-row { border-color: var(--tz-border, #30363D); }
    .ps-page .ps-table thead th { background: var(--tz-panel, #1C2128); color: var(--tz-muted, #8B949E); }
    .ps-page .ps-table tbody td { color: var(--tz-text, #CDD9E5); }
    .ps-page .ps-total-row { color: var(--tz-muted, #8B949E); }
    .ps-page .ps-total-row span:last-child { color: var(--tz-text, #CDD9E5); }
    .ps-page .ps-qty { background: var(--tz-panel, #1C2128); color: var(--tz-text, #CDD9E5); }
    .ps-page .ps-status-pill { background: rgba(0, 102, 255,.2); color: var(--tz-blue, #0066FF); }
    .billing-details li { text-transform: capitalize; }
    .vat-tax { font-size: 10px; }
</style>

<div class="ps-page">
    <div class="container-fluid">
        @include('themes.components.common-payment-success')
    </div>
</div>
@endsection
