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
        --main-color-one:     var(--dk-red, #E53030);
        --main-color-one-rgb: 229, 48, 48;
        --heading-color:      var(--dk-white, #F0F2F5);
        background:           var(--dk-dark, #111214);
    }
    /* Dark theme card overrides */
    .ps-page .ps-card { background: var(--dk-panel, #1A1D21); border-color: var(--dk-border, #2E3135); border-radius: var(--dk-radius, 6px); box-shadow: var(--dk-shadow, 0 16px 48px -12px rgba(229,48,48,.25)); }
    .ps-page .ps-card-head { background: var(--dk-surface, #22262C); border-color: var(--dk-border, #2E3135); }
    .ps-page .ps-card-head-icon { background: rgba(229, 48, 48, .15); }
    .ps-page .ps-card-title { color: var(--dk-white, #F0F2F5); font-family: var(--dk-font-body, 'Inter', sans-serif); }
    .ps-page .ps-thank { color: var(--dk-white, #F0F2F5); font-family: var(--dk-font-body, 'Inter', sans-serif); }
    .ps-page .ps-sub { color: var(--dk-silver, #8B9299); }
    .ps-page .ps-info-label { color: var(--dk-silver, #8B9299); }
    .ps-page .ps-info-val { color: var(--dk-white, #F0F2F5); }
    .ps-page .ps-info-row { border-color: var(--dk-border, #2E3135); }
    .ps-page .ps-table thead th { background: var(--dk-surface, #22262C); color: var(--dk-silver, #8B9299); }
    .ps-page .ps-table tbody td { color: var(--dk-white, #F0F2F5); }
    .ps-page .ps-total-row { color: var(--dk-silver, #8B9299); }
    .ps-page .ps-total-row span:last-child { color: var(--dk-white, #F0F2F5); }
    .ps-page .ps-qty { background: var(--dk-surface, #22262C); color: var(--dk-white, #F0F2F5); }
    .ps-page .ps-status-pill { background: rgba(229, 48, 48,.2); color: var(--dk-red, #E53030); }
    .billing-details li { text-transform: capitalize; }
    .vat-tax { font-size: 10px; }
</style>

<div class="ps-page">
    <div class="container-fluid">
        @include('themes.components.common-payment-success')
    </div>
</div>
@endsection
