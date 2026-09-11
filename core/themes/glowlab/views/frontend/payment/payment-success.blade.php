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
        --main-color-one:     var(--gl-gold, #B8965A);
        --main-color-one-rgb: 184, 150, 90;
        --heading-color:      var(--gl-dark, #1C1410);
        background:           var(--gl-bg, #FDFAF5);
    }
    .ps-page .ps-card {
        border-color: var(--gl-border, #EDE4D8);
        border-radius: var(--gl-radius, 12px);
        box-shadow: var(--gl-shadow, 0 8px 40px -8px rgba(184,150,90,.15));
    }
    .ps-page .ps-card-head {
        background: var(--gl-gold-pale, #FBF6EE);
        border-color: var(--gl-border, #EDE4D8);
    }
    .ps-page .ps-card-head-icon { background: rgba(184, 150, 90, .15); }
    .ps-page .ps-card-title {
        font-family: 'Inter', sans-serif;
        color: var(--gl-dark, #1C1410);
    }
    .ps-page .ps-thank {
        font-family: 'Inter', sans-serif;
        color: var(--gl-dark, #1C1410);
    }
    .ps-page .ps-table thead th {
        background: var(--gl-gold-pale, #FBF6EE);
        color: var(--gl-muted, #8A7A6A);
    }
    .ps-page .ps-info-row { border-color: var(--gl-border, #EDE4D8); }
    .ps-page .ps-status-pill {
        background: var(--gl-gold-pale, #FBF6EE);
        color: var(--gl-gold, #B8965A);
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
