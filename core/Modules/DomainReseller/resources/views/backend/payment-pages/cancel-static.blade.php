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
    <p class="text-sm text-muted">{{__('It may have happened due to an internal or network issue')}}</p>
</div>

@endsection
