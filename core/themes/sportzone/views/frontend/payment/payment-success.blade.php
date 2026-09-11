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
        --main-color-one:     var(--sz-red, #C62828);
        --main-color-one-rgb: 198, 40, 40;
        --heading-color:      var(--sz-dark, #111111);
        background:           var(--sz-bg, #F5F5F5);
    }
    .ps-page .ps-card {
        border-color: var(--sz-border, #DDDDDD);
        border-radius: var(--sz-radius, 4px);
        box-shadow: var(--sz-shadow, 0 8px 28px -8px rgba(198,40,40,.2));
    }
    .ps-page .ps-card-head {
        background: var(--sz-red-light, #FFEBEE);
        border-color: var(--sz-border, #DDDDDD);
    }
    .ps-page .ps-card-head-icon { background: rgba(198, 40, 40, .15); }
    .ps-page .ps-card-title {
        font-family: var(--sz-font-body, 'Roboto', sans-serif);
        color: var(--sz-dark, #111111);
    }
    .ps-page .ps-thank {
        font-family: var(--sz-font-head, 'Oswald', sans-serif);
        color: var(--sz-dark, #111111);
    }
    .ps-page .ps-table thead th {
        background: var(--sz-red-light, #FFEBEE);
        color: var(--sz-muted, #888888);
    }
    .ps-page .ps-info-row { border-color: var(--sz-border, #DDDDDD); }
    .ps-page .ps-status-pill {
        background: var(--sz-red-light, #FFEBEE);
        color: var(--sz-red-deep, #8E0000);
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
