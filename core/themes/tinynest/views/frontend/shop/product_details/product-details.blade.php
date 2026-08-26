@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $product->name !!} @endsection
@section('page-title') {!! $product->name !!} @endsection

@php
    $data               = theme_product_price($product);
    $sale_price         = $data['sale_price'];
    $deleted_price      = $data['regular_price'];
    $regular_price      = $data['regular_price'];
    $discount           = $data['discount'];
    $campaign_percentage = $discount;
    $campaign_name      = $data['campaign_name'];
    $campaign_product   = $product?->campaign_product;

    $stock_count        = $campaign_product
        ? ($campaign_product->units_for_sale !== null
            ? max(0, $campaign_product->units_for_sale - (int) $campaign_product->sold_count)
            : null)
        : optional($product->inventory)->stock_count;

    if ($campaign_product) {
        $campaign_active = !empty($campaign_product) && empty($data['campaign_name']) ? 1 : 0;
    }

    $image_array    = [(int) $product->image_id];
    foreach ($product->gallery_images ?? [] as $gi) { $image_array[] = $gi->id; }
    $main_img       = get_attachment_image_by_id($product->image_id ?? null, 'large');
    $main_img_url   = $main_img['img_url'] ?? null;

    $quickView      = false;
@endphp

@section('content')

<div class="tn-page-banner">
    <div class="container tn-page-banner-content">
        <h1>{{ \Illuminate\Support\Str::words($product->name, 6) }}</h1>
        <div class="tn-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <a href="{{ theme_shop_url() }}">{{ __('Shop') }}</a>
            <span class="sep">/</span>
            <span class="current">{{ \Illuminate\Support\Str::words($product->name, 5) }}</span>
        </div>
    </div>
</div>

<div class="container tn-page-wrap">
    <div class="row g-5">

        {{-- Gallery --}}
        <div class="col-lg-6">
            <div class="tn-gallery">

                {{-- Main image --}}
                <div class="tn-gallery-main">
                    <div class="shop-details-thums long-img" data-src="{{ $main_img_url }}">
                        @if($main_img_url)
                            <img id="tn_main_img" src="{{ $main_img_url }}" alt="{{ $product->name }}" class="tn-gallery-main-img">
                        @else
                            <div class="tn-product-placeholder tn-gallery-main-img">
                                <i class="las la-baby"></i>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Thumbnails --}}
                @if(count($image_array) > 1)
                <div class="tn-thumb-grid">
                    @foreach($image_array as $imgId)
                        @php $td = get_attachment_image_by_id($imgId, 'grid'); $turl = $td['img_url'] ?? null; @endphp
                        <div class="tn-thumb {{ $loop->first ? 'active' : '' }}"
                             onclick="tnSwitchImg('{{ $turl }}', this)">
                            @if($turl)
                                <img src="{{ $turl }}" alt="{{ $product->name }}">
                            @else
                                <div class="tn-product-placeholder"><i class="las la-baby"></i></div>
                            @endif
                        </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- Info --}}
        <div class="col-lg-6">
            <div class="tn-product-info">

                @if($product->badge)
                    <span class="tn-card-badge tn-info-badge">{{ $product->badge->name }}</span>
                @endif

                @if($product->short_description)
                    <p class="tn-info-desc-pre">{{ $product->short_description }}</p>
                @endif

                {{-- Core product options: name, rating, status, price, colors, sizes, qty, ATC, wishlist, categories --}}
                <div class="tn-product-form">
                    @include(include_theme_path('shop.product_details.partials.product-options'))
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="tn-tabs mt-5">
        <div class="tn-tab-nav">
            <button class="tn-tab-btn active" data-tab="description">{{ __('Description') }}</button>
            <button class="tn-tab-btn" data-tab="reviews">{{ __('Reviews') }}</button>
            <button class="tn-tab-btn" data-tab="shipping">{{ __('Shipping & Returns') }}</button>
        </div>
        <div class="tn-tab-content">
            <div class="tn-tab-pane active" id="tab-description">
                @include('tenant.frontend.shop.product_details.partials.product-description')
            </div>
            <div class="tn-tab-pane" id="tab-reviews">
                @include('tenant.frontend.shop.product_details.partials.product-reviews')
            </div>
            <div class="tn-tab-pane" id="tab-shipping">
                @include('tenant.frontend.shop.product_details.partials.product-ship_return')
            </div>
        </div>
    </div>

    {{-- Related Products --}}
    @if(!empty($related_products) && $related_products->isNotEmpty())
    <div class="mt-5">
        <h3 class="tn-section-title mb-4">{{ __('You Might Also Like') }}</h3>
        <div class="row g-4">
            @foreach($related_products as $rp)
            @php
                $rpdata   = theme_product_price($rp);
                $rp_img   = get_attachment_image_by_id($rp->image_id ?? null, 'grid');
                $rp_url   = theme_product_url($rp->slug);
            @endphp
            <div class="col-6 col-sm-4 col-md-3">
                <div class="tn-card">
                    <div class="tn-card-img">
                        <a href="{{ $rp_url }}">
                            @if($rp_img['img_url'] ?? null)
                                <img src="{{ $rp_img['img_url'] }}" alt="{{ $rp->name }}" loading="lazy" class="tn-product-img">
                            @else
                                <div class="tn-product-placeholder"><i class="las la-baby"></i></div>
                            @endif
                        </a>
                        <div class="tn-card-actions">
                            <button class="tn-action-btn add-to-cart-btn" data-product_id="{{ $rp->id }}" title="{{ __('Add to Cart') }}">
                                <i class="las la-shopping-cart"></i>
                            </button>
                            <button class="tn-action-btn add-to-wishlist-btn" data-product_id="{{ $rp->id }}" title="{{ __('Wishlist') }}">
                                <i class="las la-heart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="tn-card-body">
                        <div class="tn-card-name">
                            <a href="{{ $rp_url }}" class="tn-card-name-link">
                                {{ \Illuminate\Support\Str::words($rp->name, 6) }}
                            </a>
                        </div>
                        <div class="tn-card-price">
                            <span class="tn-price-sale">{{ amount_with_currency_symbol($rpdata['sale_price']) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection

@section('scripts')
{!! theme_product_js(
    $product_inventory_set ?? [],
    $additional_info_store ?? [],
    $campaign_product ?? null,
    $campaign_active ?? 0,
    $product
) !!}
<script>
(function ($) {
    'use strict';

    window.tnSwitchImg = function (url, el) {
        if (url) {
            $('#tn_main_img').attr('src', url);
            $('.long-img').attr('data-src', url);
        }
        if (el) {
            $('.tn-thumb').removeClass('active');
            $(el).addClass('active');
        }
    };

    $(document).on('click', '.tn-tab-btn', function () {
        var tab = $(this).data('tab');
        $('.tn-tab-btn').removeClass('active');
        $(this).addClass('active');
        $('.tn-tab-pane').removeClass('active');
        $('#tab-' + tab).addClass('active');
    });
})(jQuery);
</script>
@endsection
