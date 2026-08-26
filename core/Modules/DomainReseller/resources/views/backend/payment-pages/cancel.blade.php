@extends('tenant.admin.admin-master')

@section('title')
    {{ __('Domain Reseller Plugin - Order Canceled') }}
@endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

<div class="dr-status-card shadow-main">
    <div class="dr-status-icon danger">
        <i class="las la-times-circle"></i>
    </div>

    <h2 class="text-xl font-bold text-danger mb-1">{{__('The order is unsuccessful')}}</h2>
    <p class="text-sm text-muted mb-5">{{$order_details->domain .' - '. $order_details->period . ' Year'}}</p>

    @php
        $statusText = '\Modules\DomainReseller\Http\Enums\StatusEnum::getText';
        $colors = [0 => 'tw-pill-danger', 1 => 'tw-pill-success'];
    @endphp

    <div class="inline-flex items-center gap-4 bg-secondary border border-main rounded-xl px-6 py-4 mb-5">
        <div class="text-center">
            <p class="text-xs text-muted uppercase tracking-wider font-semibold mb-1">{{__('Payment Status')}}</p>
            <span class="tw-pill {{$colors[$order_details->payment_status]}}">
                {{$statusText($order_details->payment_status, true)}}
            </span>
        </div>
        <div class="w-px h-8 bg-main"></div>
        <div class="text-center">
            <p class="text-xs text-muted uppercase tracking-wider font-semibold mb-1">{{__('Domain Status')}}</p>
            <span class="tw-pill {{$colors[$order_details->status]}}">
                {{$statusText($order_details->status)}}
            </span>
        </div>
    </div>

    <p class="text-sm text-muted">{{__('The reason could be the service may not be available at your geo location. Contact admin for further actions.')}}</p>
</div>

@endsection
