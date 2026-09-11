@extends('tenant.frontend.frontend-page-master')

@section('title') {{__('Track Order')}} @endsection
@section('page-title') {{__('Track Order')}} @endsection

@section('content')
<section class="mc-page-section">
    <div class="container">
        <div class="mc-track-wrap">
            <h2 class="mc-track-title">{{__('Order Tracking')}}</h2>
            <p class="mc-track-desc">{{__('To track your order please enter your Order ID in the box below and press the "Track" button. This was given to you on your receipt and in the confirmation email you should have received.')}}</p>
            <form action="{{theme_order_track_url()}}" class="mc-track-form" method="POST">
                @csrf
                <div class="mc-form-group mt-4">
                    <label class="mc-form-label">{{__('Order ID')}}</label>
                    <input name="order_id" type="text" class="mc-form-input" placeholder="{{__('Example: 125')}}">
                </div>
                <button type="submit" class="mc-btn mc-btn-primary mt-4">{{__('Track Now')}}</button>
            </form>

            @if(session('track'))
                <div class="mc-track-result mt-4 {{ session('track')->status ? 'mc-track-success' : 'mc-track-error' }}">
                    {!! session('track')->message !!}
                </div>
            @endif
        </div>
    </div>
</section>
@include(include_theme_path('shop.partials.shop-footer'))
@endsection
