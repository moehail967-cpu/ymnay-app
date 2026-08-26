@extends('tenant.frontend.frontend-page-master')

@section('title')
    {{__('Order Cancelled for:'.' '.($order_details->name ?? ''))}}
@endsection

@section('page-title')
    {{__('Order Cancelled for:'.' '.($order_details->name ?? ''))}}
@endsection

@section('content')
<section class="hf-pay-section">
    <div class="container">
        <div class="hf-pay-cancel">
            <div class="hf-pay-canchf-icon"><i class="las la-times-circle"></i></div>
            <h1 class="hf-pay-canchf-title">{{get_static_option('site_order_canchf_page_title') ?? __('Your Order Has Been Canceled')}}</h1>
            @php
                $subtitle = get_static_option('site_order_canchf_page_subtitle');
                $subtitle = str_replace('{pkname}', $order_details->package_name, $subtitle);
            @endphp
            <p class="hf-pay-canchf-sub">{{$subtitle}}</p>
            <p class="hf-pay-canchf-desc">{{get_static_option('site_order_canchf_page_description')}}</p>
            <a href="{{url('/')}}" class="hf-btn hf-btn-primary mt-4">{{__('Back To Home')}}</a>
        </div>
    </div>
</section>
@endsection
