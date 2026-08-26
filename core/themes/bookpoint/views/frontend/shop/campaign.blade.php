@extends('tenant.frontend.frontend-page-master')
@section('title') {!! $campaign->title !!} @endsection
@section('page-title') {!! $campaign->title !!} @endsection
@section('content')
<div class="bp-page-banner">
    <div class="container">
        <h1>{!! $campaign->title !!}</h1>
        <div class="bp-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span>/</span>
            <span class="current">{!! $campaign->title !!}</span>
        </div>
    </div>
</div>

<section style="padding:60px 0;">
    <div class="container">
        @if($products->isNotEmpty())
        <div class="row g-4">
            @foreach($products as $product)
                @php
                    if (!$product) continue;
                    $data = get_product_dynamic_price($product);
                    $regular_price = $data['regular_price'];
                    $sale_price    = $data['sale_price'];
                    $discount      = $data['discount'];
                @endphp
                <div class="col-xxl-3 col-lg-4 col-sm-6">
                    <div class="bp-card">
                        <div class="bp-card-img">
                            <a href="{{ theme_product_url($product->slug) }}">
                                {!! theme_product_image($product->image_id, 'grid') !!}
                            </a>
                            @if($discount)
                            <span class="bp-badge-discount">{{ $discount }}% {{ __('off') }}</span>
                            @endif
                        </div>
                        <div class="bp-campaign-countdown flash-countdown-camp flash-countdown index-02" data-date="{{ $campaign->end_date }}">
                            <div class="single-box"><span class="counter-days item"></span><span class="label item">{{ __('Day') }}</span></div>
                            <div class="single-box"><span class="counter-hours item"></span><span class="label item">{{ __('Hr') }}</span></div>
                            <div class="single-box"><span class="counter-minutes item"></span><span class="label item">{{ __('Min') }}</span></div>
                            <div class="single-box"><span class="counter-seconds item"></span><span class="label item">{{ __('Sec') }}</span></div>
                        </div>
                        <div class="bp-card-body">
                            <h6 class="bp-card-title"><a href="{{ theme_product_url($product->slug) }}">{{ Str::words($product->name, 5) }}</a></h6>
                            <div class="bp-card-price">
                                <span class="bp-price-main">{{ amount_with_currency_symbol($sale_price) }}</span>
                                @if($regular_price)
                                <span class="bp-price-old">{{ amount_with_currency_symbol($regular_price) }}</span>
                                @endif
                            </div>
                            {!! theme_star_rating($product) !!}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-5">
            <i class="las la-box-open" style="font-size:64px;color:#ccc;display:block;margin-bottom:16px;"></i>
            <h4 style="color:#888;">{{ __('No products in this campaign') }}</h4>
        </div>
        @endif
    </div>
</section>
@endsection
@section('scripts')
<script src="{{ global_asset('assets/tenant/frontend/js/loopcounter.js') }}"></script>
<script>
$(function(){
    $(document).ready(function(){ loopcounter('flash-countdown'); });
});
</script>
@endsection
