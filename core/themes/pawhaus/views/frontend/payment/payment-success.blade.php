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
        --main-color-one:     var(--ph-terra, #C87040);
        --main-color-one-rgb: 200, 112, 64;
        --heading-color:      var(--ph-dark, #2D2318);
        background:           var(--ph-bg, #FAFAF7);
    }
    .ps-page .ps-card {
        border-color: var(--ph-border, #E8DED2);
        border-radius: var(--ph-radius, 12px);
        box-shadow: var(--ph-shadow, 0 8px 28px -8px rgba(200,112,64,.2));
    }
    .ps-page .ps-card-head {
        background: var(--ph-terra-light, #FDF0E6);
        border-color: var(--ph-border, #E8DED2);
    }
    .ps-page .ps-card-head-icon { background: rgba(200, 112, 64, .15); }
    .ps-page .ps-card-title {
        font-family: var(--ph-font, 'Nunito', sans-serif);
        color: var(--ph-dark, #2D2318);
    }
    .ps-page .ps-thank {
        font-family: var(--ph-font, 'Nunito', sans-serif);
        color: var(--ph-dark, #2D2318);
    }
    .ps-page .ps-table thead th {
        background: var(--ph-terra-light, #FDF0E6);
        color: var(--ph-muted, #8A7A6A);
    }
    .ps-page .ps-info-row { border-color: var(--ph-border, #E8DED2); }
    .ps-page .ps-status-pill {
        background: var(--ph-terra-light, #FDF0E6);
        color: var(--ph-terra-deep, #A05A30);
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
