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
        --main-color-one:     var(--bk-rose, #D4856A);
        --main-color-one-rgb: 212, 133, 106;
        --heading-color:      var(--bk-dark, #3D1C12);
        background:           var(--bk-cream, #FDF5E6);
    }
    .ps-page .ps-card {
        border-color: var(--bk-border, #E8D5C4);
        border-radius: var(--bk-radius, 12px);
        box-shadow: var(--bk-shadow, 0 8px 32px -8px rgba(212,133,106,.2));
    }
    .ps-page .ps-card-head {
        background: var(--bk-rose-light, #F9EDE8);
        border-color: var(--bk-border, #E8D5C4);
    }
    .ps-page .ps-card-head-icon { background: rgba(212, 133, 106, .15); }
    .ps-page .ps-card-title {
        font-family: var(--bk-font-body, 'Lato', sans-serif);
        color: var(--bk-dark, #3D1C12);
    }
    .ps-page .ps-thank {
        font-family: var(--bk-font-head, 'Playfair Display', Georgia, serif);
        color: var(--bk-dark, #3D1C12);
    }
    .ps-page .ps-table thead th {
        background: var(--bk-rose-light, #F9EDE8);
        color: var(--bk-muted, #8B6B5A);
    }
    .ps-page .ps-info-row { border-color: var(--bk-border, #E8D5C4); }
    .ps-page .ps-status-pill {
        background: var(--bk-rose-light, #F9EDE8);
        color: var(--bk-rose-deep, #B5694E);
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
