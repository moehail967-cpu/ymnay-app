@extends('tenant.frontend.frontend-page-master')

@section('title')
    {{__('Order Cancelled for:'.' '.($order_details->name ?? ''))}}
@endsection

@section('page-title')
    {{__('Order Cancelled for:'.' '.($order_details->name ?? ''))}}
@endsection

@section('content')
<section class="el-pay-section">
    <div class="container">
        <div class="el-pay-cancel">
            <div class="el-pay-cancel-icon"><i class="las la-times-circle"></i></div>
            <h1 class="el-pay-cancel-title">{{get_static_option('site_order_cancel_page_title') ?? __('Your Order Has Been Canceled')}}</h1>
            @php
                $subtitle = get_static_option('site_order_cancel_page_subtitle');
                $subtitle = str_replace('{pkname}', $order_details->package_name, $subtitle);
            @endphp
            <p class="el-pay-cancel-sub">{{$subtitle}}</p>
            <p class="el-pay-cancel-desc">{{get_static_option('site_order_cancel_page_description')}}</p>
            <a href="{{url('/')}}" class="el-btn el-btn-primary mt-4">{{__('Back To Home')}}</a>
        </div>
    </div>
</section>
@endsection
