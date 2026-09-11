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
        --main-color-one:     var(--tn-purple, #7E57C2);
        --main-color-one-rgb: 126, 87, 194;
        --heading-color:      var(--tn-dark, #2E2438);
        background:           var(--tn-cream, #FDFAF8);
    }
    .ps-page .ps-card {
        border-color: var(--tn-border, #EDE8F5);
        border-radius: var(--tn-radius, 20px);
        box-shadow: var(--tn-shadow, 0 8px 32px -8px rgba(179,157,219,.25));
    }
    .ps-page .ps-card-head {
        background: rgba(126, 87, 194, 0.1);
        border-color: var(--tn-border, #EDE8F5);
    }
    .ps-page .ps-card-head-icon { background: rgba(126, 87, 194, .15); }
    .ps-page .ps-card-title {
        font-family: 'Inter', sans-serif;
        color: var(--tn-dark, #2E2438);
    }
    .ps-page .ps-thank {
        font-family: 'Inter', sans-serif;
        color: var(--tn-dark, #2E2438);
    }
    .ps-page .ps-table thead th {
        background: rgba(126, 87, 194, 0.1);
        color: var(--tn-muted, #8A7A9A);
    }
    .ps-page .ps-info-row { border-color: var(--tn-border, #EDE8F5); }
    .ps-page .ps-status-pill {
        background: rgba(126, 87, 194, 0.1);
        color: var(--tn-purple, #7E57C2);
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
