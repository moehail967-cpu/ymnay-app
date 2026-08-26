@extends('tenant.frontend.frontend-page-master')

@section('title') {{__('Track Order')}} @endsection
@section('page-title') {{__('Track Order')}} @endsection

@section('content')
<section class="hf-page-section">
    <div class="container">
        <div class="hf-track-wrap">
            <h2 class="hf-track-title">{{__('Order Tracking')}}</h2>
            <p class="hf-track-desc">{{__('To track your order please enter your Order ID in the box below and press the "Track" button. This was given to you on your receipt and in the confirmation email you should have received.')}}</p>
            <form action="{{theme_order_track_url()}}" class="hf-track-form" method="POST">
                @csrf
                <div class="hf-form-group mt-4">
                    <label class="hf-form-label">{{__('Order ID')}}</label>
                    <input name="order_id" type="text" class="hf-form-input" placeholder="{{__('Example: 125')}}">
                </div>
                <button type="submit" class="hf-btn hf-btn-primary mt-4">{{__('Track Now')}}</button>
            </form>

            @if(session('track'))
                <div class="hf-track-result mt-4 {{ session('track')->status ? 'hf-track-success' : 'hf-track-error' }}">
                    {!! session('track')->message !!}
                </div>
            @endif
        </div>
    </div>
</section>
@include(include_theme_path('shop.partials.shop-footer'))
@endsection
