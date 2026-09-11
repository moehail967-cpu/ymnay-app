@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $product->name !!} @endsection
@section('page-title') {!! $product->name !!} @endsection
@section('meta-data') {!! render_page_meta_data($product) !!} @endsection

@section('style')
<link rel="stylesheet" href="{{ global_asset('assets/common/css/star-rating.min.css') }}">
@endsection

@section('content')
@php
    $data               = get_product_dynamic_price($product);
    $campaign_name      = $data['campaign_name'];
    $data_regular_price = $data['regular_price'];
    $data_sale_price    = $data['sale_price'];
    $discount           = $data['discount'];
    $campaign_product   = $product?->campaign_product;
    $sale_price         = $data_sale_price;
    $deleted_price      = $data_regular_price;
    $campaign_percentage = $discount;
    $stock_count        = $campaign_product
        ? ($campaign_product->units_for_sale !== null
            ? max(0, $campaign_product->units_for_sale - (int)$campaign_product->sold_count)
            : null)
        : optional($product->inventory)->stock_count;
    $stock_count        = $stock_count > 0 ? $stock_count : 0;
    if ($campaign_product) {
        $campaign_title  = \Modules\Campaign\Entities\Campaign::select('id','title')->where('id', $campaign_product?->id)->first();
        $campaign_active = !empty($campaign_product) && empty($data['campaign_name']) ? 1 : 0;
    }
    $quickView = false;

    // Image gallery
    $image_array = [(int)$product->image_id];
    foreach ($product->gallery_images ?? [] as $gi) { $image_array[] = $gi->id; }
    $main_img     = get_attachment_image_by_id($product->image_id ?? null, 'large');
    $main_img_url = $main_img['img_url'] ?? null;

    $final_price = calculatePrice($sale_price, $product);
@endphp

{{-- Page Banner / Breadcrumb --}}
<div class="cs-page-banner">
    <div class="container">
        <h1 class="cs-page-banner-title">{{ Str::limit($product->name, 60) }}</h1>
        <div class="cs-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="cs-breadcrumb-sep"><i class="las la-angle-right"></i></span>
            <a href="{{ theme_shop_url() }}">{{ __('Shop') }}</a>
            <span class="cs-breadcrumb-sep"><i class="las la-angle-right"></i></span>
            <span class="cs-breadcrumb-current">{{ Str::limit($product->name, 40) }}</span>
        </div>
    </div>
</div>

<div class="container cs-pd-wrap">

    {{-- ===== Product Top: Images + Info ===== --}}
    <div class="row g-5">

        {{-- Images --}}
        <div class="col-lg-5">
            @include(include_theme_path('shop.product_details.partials.product-images-slider'))
        </div>

        {{-- Info --}}
        <div class="col-lg-7">

            @if($product->badge)
                <span class="casual-new-badge cs-pd-badge-inline">{{ $product->badge->name }}</span>
            @endif

            <h1 class="cs-pd-title">{!! $product->name !!}</h1>

            {{-- Stars + stock --}}
            <div class="cs-pd-meta-row">
                {!! render_product_star_rating_markup_with_count($product) !!}
                <span id="stock"
                      data-stock-text='{!! $stock_count > 0 ? "<span class=\"text-success\">".__("In Stock")."</span>" : "<span class=\"text-danger\">".__("Out of Stock")."</span>" !!}'>
                    {!! $stock_count > 0
                        ? '<span class="cs-pd-instock"><i class="las la-check-circle"></i> '.__('In Stock').'</span>'
                        : '<span class="cs-pd-outstock"><i class="las la-times-circle"></i> '.__('Out of Stock').'</span>' !!}
                </span>
            </div>

            {{-- Price --}}
            <div class="cs-pd-price-row">
                <span class="cs-pd-price"
                      id="price"
                      data-main-price="{{ $final_price }}"
                      data-currency-symbol="{{ site_currency_symbol() }}">
                    {{ amount_with_currency_symbol($final_price) }}
                </span>
                @if($deleted_price)
                    <span class="cs-pd-was">{{ amount_with_currency_symbol($deleted_price) }}</span>
                @endif
                @if($discount)
                    <span class="casual-new-badge">{{ $discount }}% {{ __('OFF') }}</span>
                @endif
            </div>

            @if($product->summary)
                <p class="cs-pd-summary">{{ $product->summary }}</p>
            @endif

            {{-- Product options form (sizes, colors, qty, ATC) --}}
            <div class="cs-pd-options">
                @include(include_theme_path('shop.product_details.partials.product-options'))
            </div>

            {{-- Trust pills --}}
            <div class="cs-pd-trust">
                <span><i class="las la-shield-alt"></i> {{ __('Secure Checkout') }}</span>
                <span><i class="las la-undo-alt"></i> {{ __('Easy Returns') }}</span>
                <span><i class="las la-truck"></i> {{ __('Fast Delivery') }}</span>
            </div>

            {{-- Category / SKU / Tags --}}
            <div class="cs-pd-stock-meta">
                <ul class="cs-pd-stock-list">
                    @if($product?->category?->name)
                        <li><span>{{ __('Category:') }}</span>
                            <a href="{{ theme_product_url($product?->category?->slug) }}">{{ $product->category->name }}</a>
                            @if($product?->subCategory?->slug)
                                | <a href="{{ theme_product_url($product?->subCategory?->slug) }}">{{ $product->subCategory->name }}</a>
                            @endif
                        </li>
                    @endif
                    @if($product?->inventory?->sku)
                        <li><span>{{ __('SKU:') }}</span> {{ $product->inventory->sku }}</li>
                    @endif
                    @if($product->tags?->isNotEmpty())
                        <li><span>{{ __('Tags:') }}</span>
                            @foreach($product->tags as $tag){{ $tag->tag_name }}{{ !$loop->last ? ', ' : '' }}@endforeach
                        </li>
                    @endif
                </ul>
            </div>

            {{-- Delivery options --}}
            @if($product->product_delivery_option?->count())
            <div class="cs-pd-delivery">
                @foreach($product->product_delivery_option as $opt)
                <div class="cs-pd-delivery-item">
                    <i class="{{ $opt->icon }}"></i>
                    <div>
                        <strong>{{ $opt->title }}</strong>
                        <span>{{ $opt->sub_title }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Social share --}}
            <div class="cs-pd-share">
                @php
                    $product_primary_image = get_attachment_image_by_id($product->image_id);
                    $product_primary_image = $product_primary_image ? $product_primary_image['img_url'] : '';
                @endphp
                {!! single_post_share($product->slug, $product->name, $product_primary_image) !!}
            </div>

            {{-- Payment gateway logos --}}
            @php $pgImages = \App\Models\PaymentGateway::where('status',1)->permittedPaymentGateway()->get('image')->pluck('image'); @endphp
            @if($pgImages->isNotEmpty())
            <div class="cs-pd-payment">
                <span class="cs-pd-payment-label">{{ __('Guaranteed Safe Checkout') }}</span>
                <ul class="cs-pd-payment-list">
                    @foreach($pgImages as $pgImg)
                    <li>{!! render_image_markup_by_attachment_id($pgImg) !!}</li>
                    @endforeach
                </ul>
            </div>
            @endif

        </div>
    </div>

    {{-- ===== Tabs: Description / Reviews / Ship & Return ===== --}}
    <div class="cs-pd-tabs-wrap">
        <div class="cs-tab-nav">
            <button class="cs-tab-btn active" data-target="cs-tab-desc">{{ __('Description') }}</button>
            <button class="cs-tab-btn" data-target="cs-tab-reviews">{{ __('Reviews') }}</button>
            <button class="cs-tab-btn" data-target="cs-tab-ship">{{ __('Ship & Return') }}</button>
        </div>
        <div id="cs-tab-desc" class="cs-tab-panel active">
            @include(include_theme_path('shop.product_details.partials.product-description'))
        </div>
        <div id="cs-tab-reviews" class="cs-tab-panel">
            @include(include_theme_path('shop.product_details.partials.product-reviews'))
        </div>
        <div id="cs-tab-ship" class="cs-tab-panel">
            @include(include_theme_path('shop.product_details.partials.product-ship_return'))
        </div>
    </div>

    {{-- ===== Related Products ===== --}}
    @if($related_products->isNotEmpty())
    <div class="cs-pd-related">
        <h3 class="cs-pd-related-title">{{ __('You Might Also Like') }}</h3>
        <div class="row g-3">
            @foreach($related_products as $rp)
            @php
                $rpdata    = theme_product_price($rp);
                $rp_img    = get_attachment_image_by_id($rp->image_id ?? null, 'grid');
                $rp_imgurl = $rp_img['img_url'] ?? null;
                $rp_url    = theme_product_url($rp->slug);
            @endphp
            <div class="col-6 col-md-3">
                <div class="casual-new-product-card">
                    <div class="casual-new-product-card-thumb">
                        @if($rp_imgurl)
                            <a href="{{ $rp_url }}"><img src="{{ $rp_imgurl }}" alt="{{ $rp->name }}" loading="lazy"></a>
                        @else
                            <a href="{{ $rp_url }}" class="casual-new-thumb-placeholder">
                                <i class="las la-tshirt"></i>
                            </a>
                        @endif
                        @if($rpdata['discount'])
                            <span class="casual-new-badge">{{ $rpdata['discount'] }}% {{ __('off') }}</span>
                        @endif
                        <button class="casual-new-wishlist add-to-wishlist-btn" data-product_id="{{ $rp->id }}">
                            <i class="lar la-heart"></i>
                        </button>
                    </div>
                    <div class="casual-new-product-card-contents">
                        <h5 class="casual-new-product-title">
                            <a href="{{ $rp_url }}">{{ Str::words($rp->name, 8) }}</a>
                        </h5>
                        <div class="casual-new-product-price">
                            <span class="casual-new-price-sale">{{ amount_with_currency_symbol($rpdata['sale_price']) }}</span>
                        </div>
                        <a href="{{ $rp_url }}" class="add-to-cart-btn casual-new-add-to-cart">
                            <i class="las la-shopping-cart"></i> {{ __('Add to Cart') }}
                        </a>
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
<script>
$(function () {

    // Tab switching
    $(document).on('click', '.cs-tab-btn', function () {
        var target = $(this).data('target');
        $('.cs-tab-btn').removeClass('active');
        $(this).addClass('active');
        $('.cs-tab-panel').removeClass('active');
        $('#' + target).addClass('active');
    });

    // Image gallery thumbnail switch
    window.csSwapImg = function (el, url) {
        document.querySelectorAll('.cs-pd-thumb').forEach(function (t) { t.classList.remove('active'); });
        el.classList.add('active');
        var mainImg = document.getElementById('cs-main-product-img');
        if (mainImg && url) { mainImg.src = url; }
        var longImg = document.querySelector('.long-img');
        if (longImg && url) { longImg.setAttribute('data-src', url); }
    };

    // Also keep old .small-img click for JS compat
    $(document).on('click', '.small-img', function () {
        var image = $(this).data('image-path');
        csSwapImg(this, image);
    });

    // Star rating
    if (typeof StarRating !== 'undefined') {
        new StarRating('.star-rating', {
            maxStars: 5,
            clearable: false,
            stars: function (el) {
                el.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><rect class="gl-star-full" width="19" height="19" x="2.5" y="2.5"/><polygon fill="#FFF" points="12 5.375 13.646 10.417 19 10.417 14.665 13.556 16.313 18.625 11.995 15.476 7.688 18.583 9.333 13.542 5 10.417 10.354 10.417"/></svg>';
            },
            classNames: { active: 'gl-active', base: 'gl-star-rating', selected: 'rating-selected' },
        });
    }

    // Campaign countdown
    @php if (!empty($campaign_product) && isset($campaign_active) && $campaign_active != 0) { $end_date = $campaign_product->campaign?->end_date; } @endphp
    if (typeof $.fn.syotimer !== 'undefined') {
        $('.global-timer').syotimer({
            year:  '{{ $end_date->year  ?? 0 }}',
            month: '{{ $end_date->month ?? 0 }}',
            day:   '{{ $end_date->day   ?? 0 }}',
        });
    }

    // Quantity stepper
    $(document).on('click', '.plus', function () {
        var i = $(this).prev('.quantity-input');
        if (i.val()) i[0].stepUp(1);
    });
    $(document).on('click', '.substract', function () {
        var i = $(this).next('.quantity-input');
        if (parseInt(i.val()) > 1) i[0].stepDown(1);
    });

    // Review submit
    $(document).on('click', '#review-submit-btn', function (e) {
        e.preventDefault();
        var btn = $(this);
        $.ajax({
            url: '{{ theme_product_review_url() }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                product_id: '{{ $product->id }}',
                review_text: $('#review-text').val(),
                rating: $('.rating-selected').data('value')
            },
            beforeSend: function () {
                toastr.warning('{{ __("Submitting please wait.") }}');
                btn.text('{{ __("Submitting..") }}');
            },
            success: function (data) {
                if (data.type === 'success') {
                    toastr.success(data.msg);
                    setTimeout(function () { location.reload(); }, 300);
                } else {
                    toastr.error(data.msg);
                }
                btn.text('{{ __("Submit Review") }}');
            },
            error: function (data) {
                $.each(data.responseJSON?.errors ?? {}, function (v, i) { toastr.error(i); });
                btn.text('{{ __("Submit Review") }}');
            }
        });
    });

    // See more reviews
    $(document).on('click', '.see-more-review', function () {
        var el = $(this), items = el.attr('data-items');
        $.ajax({
            url: '{{ theme_product_review_more_url() }}',
            type: 'GET',
            data: { product_id: '{{ $product->id }}', items: items },
            beforeSend: function () { el.text('{{ __("Loading..") }}'); },
            success: function (data) {
                $('.all-reviews').html(data.markup).hide().fadeIn(800);
                el.text('{{ __("See More") }}');
                el.attr('data-items', Number(items) + 5);
            },
            error: function () { el.text('{{ __("See More") }}'); }
        });
    });

});
</script>
{!! theme_product_js($product_inventory_set, $additional_info_store, $campaign_product ?? null, $campaign_active ?? 0, $product) !!}
@endsection
