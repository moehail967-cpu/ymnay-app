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
        --main-color-one:     var(--tc-olive, #4A5E3A);
        --main-color-one-rgb: 74, 94, 58;
        --heading-color:      var(--tc-dark, #1E2019);
        background:           var(--tc-bg, #F4F1EC);
    }
    .ps-page .ps-card {
        border-color: var(--tc-border, #D6CFC4);
        border-radius: var(--tc-radius, 6px);
        box-shadow: var(--tc-shadow, 0 10px 32px -8px rgba(74,94,58,.2));
    }
    .ps-page .ps-card-head {
        background: var(--tc-olive-light, #EAF0E3);
        border-color: var(--tc-border, #D6CFC4);
    }
    .ps-page .ps-card-head-icon { background: rgba(74, 94, 58, .15); }
    .ps-page .ps-card-title {
        font-family: var(--tc-font, 'Cabin', sans-serif);
        color: var(--tc-dark, #1E2019);
    }
    .ps-page .ps-thank {
        font-family: var(--tc-font, 'Cabin', sans-serif);
        color: var(--tc-dark, #1E2019);
    }
    .ps-page .ps-table thead th {
        background: var(--tc-olive-light, #EAF0E3);
        color: var(--tc-muted, #7A7A6A);
    }
    .ps-page .ps-info-row { border-color: var(--tc-border, #D6CFC4); }
    .ps-page .ps-status-pill {
        background: var(--tc-olive-light, #EAF0E3);
        color: var(--tc-olive-deep, #2E3D22);
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
