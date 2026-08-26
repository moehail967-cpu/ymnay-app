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
        --main-color-one:     var(--ms-olive, #7D8C5A);
        --main-color-one-rgb: 125, 140, 90;
        --heading-color:      var(--ms-dark, #1E1A14);
        background:           var(--ms-cream, #FAF7F2);
    }
    .ps-page .ps-card {
        border-color: var(--ms-border, #DDD5C4);
        border-radius: var(--ms-radius, 4px);
        box-shadow: var(--ms-shadow, 0 12px 40px -10px rgba(200,184,154,.25));
    }
    .ps-page .ps-card-head {
        background: var(--ms-olive-light, #EDF0E6);
        border-color: var(--ms-border, #DDD5C4);
    }
    .ps-page .ps-card-head-icon { background: rgba(125, 140, 90, .15); }
    .ps-page .ps-card-title {
        font-family: 'Inter', sans-serif;
        color: var(--ms-dark, #1E1A14);
    }
    .ps-page .ps-thank {
        font-family: 'Inter', sans-serif;
        color: var(--ms-dark, #1E1A14);
    }
    .ps-page .ps-table thead th {
        background: var(--ms-olive-light, #EDF0E6);
        color: var(--ms-muted, #8A8070);
    }
    .ps-page .ps-info-row { border-color: var(--ms-border, #DDD5C4); }
    .ps-page .ps-status-pill {
        background: var(--ms-olive-light, #EDF0E6);
        color: var(--ms-olive-d, #5A6A3A);
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
