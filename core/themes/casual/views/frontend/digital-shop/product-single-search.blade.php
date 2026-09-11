@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Search') }} @endsection
@section('page-title') {{ __('Search') }} @endsection

@section('content')
<div class="cs-page-banner">
    <div class="container">
        <h1 class="cs-page-banner-title">{{ __('Search Results') }}</h1>
        <div class="cs-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="cs-breadcrumb-sep"><i class="las la-angle-right"></i></span>
            <span class="cs-breadcrumb-current">{{ __('Search') }}</span>
        </div>
    </div>
</div>

<section class="cs-shop-section">
    <div class="container">
        <div class="cs-shop-results-heading">
            <h4>{{ __('Search Result For:') }} <strong>{{ $search }}</strong></h4>
        </div>

        <div class="row g-4 mt-2">
            @forelse($product_object as $product)
            @php
                $data          = get_product_dynamic_price($product);
                $regular_price = $data['regular_price'];
                $sale_price    = $data['sale_price'];
                $discount      = $data['discount'];
                $campaign_name = $data['campaign_name'];
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

                        @if($hasDiscount || !empty($product->badge) || !is_null($campaign_name))
                        <div class="cs-product-badges">
                            @if($hasDiscount)
                                <span class="cs-product-badge cs-product-badge-sale">{{ $discount }}% {{ __('Off') }}</span>
                            @endif
                            @if(!empty($product->badge))
                                <span class="cs-product-badge cs-product-badge-new">{{ $product->badge->name }}</span>
                            @endif
                            @if(!is_null($campaign_name))
                                <span class="cs-product-badge cs-product-badge-new">{{ $campaign_name }}</span>
                            @endif
                        </div>
                        @endif
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
            <div class="col-12 cs-no-data text-center">
                <i class="las la-search"></i>
                <p>{{ __('No products found for your search.') }}</p>
            </div>
            @endforelse
        </div>

        @if($product_object->hasPages())
        <div class="cs-pagination mt-4">
            {{ $product_object->links() }}
        </div>
        @endif
    </div>
</section>

<div id="product-modal"></div>
@endsection
