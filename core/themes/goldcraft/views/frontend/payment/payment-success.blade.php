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
        --main-color-one:     var(--gc-gold, #C8A96E);
        --main-color-one-rgb: 200, 169, 110;
        --heading-color:      var(--gc-dark, #2A1F14);
        background:           var(--gc-bg, #FAF7F2);
    }
    .ps-page .ps-card {
        border-color: var(--gc-border, #DDD0BC);
        border-radius: var(--gc-radius, 6px);
        box-shadow: var(--gc-shadow, 0 8px 32px -8px rgba(200,169,110,.25));
    }
    .ps-page .ps-card-head {
        background: var(--gc-rose-light, #F5EAE0);
        border-color: var(--gc-border, #DDD0BC);
    }
    .ps-page .ps-card-head-icon { background: rgba(200, 169, 110, .15); }
    .ps-page .ps-card-title {
        font-family: Georgia, serif;
        color: var(--gc-dark, #2A1F14);
    }
    .ps-page .ps-thank {
        font-family: Georgia, 'Times New Roman', serif;
        color: var(--gc-dark, #2A1F14);
    }
    .ps-page .ps-table thead th {
        background: var(--gc-rose-light, #F5EAE0);
        color: var(--gc-muted, #8A7A6A);
    }
    .ps-page .ps-info-row { border-color: var(--gc-border, #DDD0BC); }
    .ps-page .ps-status-pill {
        background: var(--gc-rose-light, #F5EAE0);
        color: var(--gc-rose-deep, #A86552);
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
