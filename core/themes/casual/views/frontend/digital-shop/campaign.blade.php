@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $campaign->title !!} @endsection
@section('page-title') {!! $campaign->title !!} @endsection

@section('content')
<div class="cs-page-banner">
    <div class="container">
        <h1 class="cs-page-banner-title">{!! $campaign->title !!}</h1>
        <div class="cs-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="cs-breadcrumb-sep"><i class="las la-angle-right"></i></span>
            <span class="cs-breadcrumb-current">{!! $campaign->title !!}</span>
        </div>
    </div>
</div>

<section class="cs-campaign-section">
    <div class="container">
        <div class="row g-4">
            @forelse($products as $product)
                @php
                    if (!$product) continue;
                    $data          = get_product_dynamic_price($product);
                    $regular_price = $data['regular_price'];
                    $sale_price    = $data['sale_price'];
                    $discount      = $data['discount'];
                    $hasDiscount   = $discount !== null;
                    $pImg = null;
                    if (!empty($product->image_id)) {
                        $pd   = get_attachment_image_by_id($product->image_id);
                        $pImg = $pd['img_url'] ?? null;
                    }
                    $pUrl = theme_product_url($product->slug);
                @endphp

                <div class="col-xl-3 col-lg-4 col-sm-6">
                    <div class="casual-new-product-card h-100">
                        <div class="casual-new-product-card-thumb">
                            <a href="{{ $pUrl }}">
                                @if($pImg)
                                    <img src="{{ $pImg }}" alt="{{ $product->name }}" loading="lazy">
                                @else
                                    <div class="casual-new-thumb-placeholder"><i class="las la-shopping-bag"></i></div>
                                @endif
                            </a>

                            <div class="casual-new-wishlist">
                                <a class="add-to-wishlist-btn cart-loading" href="javascript:void(0)"
                                   data-product_id="{{ $product->id }}">
                                    <i class="lar la-heart"></i>
                                </a>
                            </div>

                            @if($hasDiscount)
                            <div class="cs-product-badges">
                                <span class="cs-product-badge cs-product-badge-sale">{{ $discount }}% {{ __('Off') }}</span>
                            </div>
                            @endif

                            {{-- Countdown timer --}}
                            <div class="cs-campaign-timer flash-countdown" data-date="{{ $campaign->end_date }}">
                                <div class="cs-timer-box">
                                    <span class="counter-days item cs-timer-val"></span>
                                    <span class="label item cs-timer-label">{{ __('Day') }}</span>
                                </div>
                                <div class="cs-timer-box">
                                    <span class="counter-hours item cs-timer-val"></span>
                                    <span class="label item cs-timer-label">{{ __('Hr') }}</span>
                                </div>
                                <div class="cs-timer-box">
                                    <span class="counter-minutes item cs-timer-val"></span>
                                    <span class="label item cs-timer-label">{{ __('Min') }}</span>
                                </div>
                                <div class="cs-timer-box">
                                    <span class="counter-seconds item cs-timer-val"></span>
                                    <span class="label item cs-timer-label">{{ __('Sec') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="casual-new-product-card-contents">
                            <div class="casual-new-product-category">
                                {{ $product->category?->name ? strtoupper($product->category->name) : '' }}
                            </div>

                            <h5 class="casual-new-product-title">
                                <a href="{{ $pUrl }}">{!! Str::words($product->name, 10) !!}</a>
                            </h5>

                            <div class="casual-new-product-price">
                                <span class="casual-new-price-sale">{{ amount_with_currency_symbol($sale_price) }}</span>
                                @if($hasDiscount && $regular_price)
                                    <span class="casual-new-price-regular">{{ amount_with_currency_symbol($regular_price) }}</span>
                                @endif
                            </div>

                            <div class="casual-new-product-action">
                                <a href="javascript:void(0)" data-product_id="{{ $product->id }}"
                                   class="add-to-cart-btn casual-new-add-to-cart">
                                    <i class="las la-shopping-cart"></i> {{ __('Add to Cart') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center cs-no-data">
                    <i class="las la-box-open"></i> {{ __('No products in this campaign.') }}
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script src="{{ global_asset('assets/tenant/frontend/js/loopcounter.js') }}"></script>
<script>
$(function () {
    loopcounter('flash-countdown');
});
</script>
@endsection
