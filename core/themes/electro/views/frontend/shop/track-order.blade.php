@extends('tenant.frontend.frontend-page-master')

@section('title') {{__('Track Order')}} @endsection
@section('page-title') {{__('Track Order')}} @endsection

@section('content')
<section class="el-page-section">
    <div class="container">
        <div class="el-track-wrap">
            <h2 class="el-track-title">{{__('Order Tracking')}}</h2>
            <p class="el-track-desc">{{__('To track your order please enter your Order ID in the box below and press the "Track" button. This was given to you on your receipt and in the confirmation email you should have received.')}}</p>
            <form action="{{theme_order_track_url()}}" class="el-track-form" method="POST">
                @csrf
                <div class="el-form-group mt-4">
                    <label class="el-form-label">{{__('Order ID')}}</label>
                    <input name="order_id" type="text" class="el-form-input" placeholder="{{__('Example: 125')}}">
                </div>
                <button type="submit" class="el-btn el-btn-primary mt-4">{{__('Track Now')}}</button>
            </form>

            @if(session('track'))
                <div class="el-track-result mt-4 {{ session('track')->status ? 'el-track-success' : 'el-track-error' }}">
                    {!! session('track')->message !!}
                </div>
            @endif
        </div>
    </div>
</section>
@include(include_theme_path('shop.partials.shop-footer'))
@endsection
