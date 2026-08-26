@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $product->name !!} @endsection
@section('page-title') {!! $product->name !!} @endsection
@section('meta-data') {!! render_page_meta_data($product) !!} @endsection

@section('style')
<link rel="stylesheet" href="{{ global_asset('assets/common/css/star-rating.min.css') }}">
@endsection

@section('content')
@php
    $data               = theme_product_price($product);
    $campaign_name      = $data['campaign_name'];
    $data_regular_price = $data['regular_price'];
    $data_sale_price    = $data['sale_price'];
    $discount           = $data['discount'];
    $campaign_product   = $product?->campaign_product;
    $sale_price         = $data_sale_price;
    $deleted_price      = $data_regular_price;
    $stock_count        = $campaign_product
        ? ($campaign_product->units_for_sale !== null
            ? max(0, $campaign_product->units_for_sale - (int) $campaign_product->sold_count)
            : null)
        : optional($product->inventory)->stock_count;
    $stock_count        = $stock_count > 0 ? $stock_count : 0;
    if ($campaign_product) {
        $campaign_title = \Modules\Campaign\Entities\Campaign::select('id','title')->where('id', $campaign_product?->id)->first();
        $campaign_active     = !empty($campaign_product) && empty($data['campaign_name']) ? 1 : 0;
    }
    $quickView = false;
    $image_array = [(int)$product->image_id];
    foreach ($product->gallery_images ?? [] as $gi) { $image_array[] = $gi->id; }
    $main_img     = get_attachment_image_by_id($product->image_id ?? null, 'large');
    $main_img_url = $main_img['img_url'] ?? null;
@endphp

{{-- Breadcrumb --}}
<div class="dk-breadcrumb-bar">
    <div class="container">
        <div class="dk-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span><i class="mdi mdi-chevron-right"></i></span>
            <a href="{{ theme_shop_url() }}">{{ __('Shop') }}</a>
            <span><i class="mdi mdi-chevron-right"></i></span>
            {{ \Illuminate\Support\Str::words($product->name, 5) }}
        </div>
    </div>
</div>

{{-- Product Hero --}}
<section class="py-5" style="background:var(--dk-carbon);">
    <div class="container">
        <div class="row g-4">

            {{-- Gallery --}}
            <div class="col-lg-6">
                <div id="shop_details_gallery_slider" class="dk-product-main-img" data-src="{{ $main_img_url }}">
                    @if($main_img_url)
                        <img id="dk-main-product-img" src="{{ $main_img_url }}" alt="{{ $product->name }}">
                    @else
                        <i class="mdi mdi-car-wrench dk-no-img-icon"></i>
                    @endif
                </div>

                @if(count($image_array) > 1)
                <div class="dk-thumbs">
                    @foreach($image_array as $imgId)
                        @php $tdata = get_attachment_image_by_id($imgId, 'grid'); $turl = $tdata['img_url'] ?? null; @endphp
                        <div onclick="dkSwitchImg(this, '{{ $turl }}')"
                             class="dk-thumb {{ $loop->first ? 'active' : '' }}"
                             data-src="{{ $turl }}">
                            @if($turl)
                                <img src="{{ $turl }}" alt="">
                            @else
                                <i class="mdi mdi-car-wrench" style="font-size:22px;color:var(--dk-red);opacity:.3;"></i>
                            @endif
                        </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Product Info --}}
            <div class="col-lg-6">
                <div class="dk-product-info">

                    @if($product->category)
                        <div class="dk-card-cat mb-2">{{ $product->category->name }}</div>
                    @endif

                    <h1 class="dk-product-title">{{ $product->name }}</h1>

                    {{-- Price row --}}
                    <div class="dk-product-price-row">
                        <span class="dk-product-price" id="price"
                              data-main-price="{{ $sale_price }}"
                              data-currency-symbol="{{ site_currency_symbol() }}">
                            {{ amount_with_currency_symbol($sale_price) }}
                        </span>
                        @if($deleted_price)
                            <span class="dk-product-orig">{{ amount_with_currency_symbol($deleted_price) }}</span>
                        @endif
                        @if($discount)
                            <span class="dk-product-save">{{ __('SAVE') }} {{ $discount }}%</span>
                        @endif
                    </div>

                    {{-- Stars + Stock --}}
                    <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
                        <div class="dk-stars">{!! theme_star_rating($product) !!}</div>
                        <span id="stock"
                              data-stock-text='{!! $stock_count > 0 ? "<span class=\"dk-stock-in\"><i class=\"mdi mdi-check-circle\"></i> ".__("In Stock")."</span>" : "<span class=\"dk-stock-out\"><i class=\"mdi mdi-close-circle\"></i> ".__("Out of Stock")."</span>" !!}'>
                            @if($stock_count > 0)
                                <span class="dk-stock-in"><i class="mdi mdi-check-circle"></i> {{ __('In Stock') }} ({{ $stock_count }})</span>
                            @else
                                <span class="dk-stock-out"><i class="mdi mdi-close-circle"></i> {{ __('Out of Stock') }}</span>
                            @endif
                        </span>
                    </div>

                    {{-- Meta (SKU, Brand, Category) --}}
                    <div class="dk-meta-row">
                        @if($product->inventory?->sku)
                            <div class="dk-meta-item"><strong>{{ __('SKU') }}:</strong> {{ $product->inventory->sku }}</div>
                        @endif
                        @if($product->brand?->name)
                            <div class="dk-meta-item"><strong>{{ __('Brand') }}:</strong> {{ $product->brand->name }}</div>
                        @endif
                        @if($product->category?->name)
                            <div class="dk-meta-item"><strong>{{ __('Category') }}:</strong> {{ $product->category->name }}</div>
                        @endif
                    </div>

                    {{-- Campaign bar --}}
                    @if($campaign_product !== null && $campaign_product->status !== 'draft')
                    <div class="dk-campaign-bar">
                        <div class="dk-campaign-bar-label"><i class="mdi mdi-fire"></i> {{ $campaign_name }}</div>
                        @if(isset($campaign_active) && $campaign_active) <div class="global-timer"></div> @endif
                    </div>
                    @endif

                    {{-- Short description --}}
                    @if($product->short_description)
                        <p class="dk-product-desc">{{ $product->short_description }}</p>
                    @endif

                    {{-- Options (size, color, qty, ATC) --}}
                    @include(include_theme_path('shop.product_details.partials.product-options'))

                    {{-- Trust bar --}}
                    <div class="dk-trust-bar">
                        <span><i class="mdi mdi-shield-check"></i> {{ __('Secure Checkout') }}</span>
                        <span><i class="mdi mdi-refresh"></i> {{ __('Easy Returns') }}</span>
                        <span><i class="mdi mdi-truck-fast"></i> {{ __('Fast Delivery') }}</span>
                    </div>

                    {{-- Meta footer --}}
                    <div class="dk-product-meta-footer">
                        @if($product->category?->name)
                            <div class="dk-product-meta-item"><strong>{{ __('Category') }}</strong>{{ $product->category->name }}</div>
                        @endif
                        @if($product->inventory?->sku)
                            <div class="dk-product-meta-item"><strong>{{ __('Part Number') }}</strong>{{ $product->inventory->sku }}</div>
                        @endif
                        @if($product->uom)
                            <div class="dk-product-meta-item"><strong>{{ __('Unit') }}</strong>{{ $product->uom?->quantity }} {{ $product->uom?->uom_details?->name }}</div>
                        @endif
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

{{-- Tabs Section --}}
<section class="py-5" style="background:var(--dk-surface);border-top:1px solid var(--dk-border);">
    <div class="container">

        <div class="dk-tab-strip">
            <button class="dk-tab-btn active" data-target="dk-tab-desc">{{ __('Description') }}</button>
            <button class="dk-tab-btn" data-target="dk-tab-reviews">{{ __('Reviews') }}</button>
            <button class="dk-tab-btn" data-target="dk-tab-ship">{{ __('Shipping & Returns') }}</button>
        </div>

        <div id="dk-tab-desc">
            @include(include_theme_path('shop.product_details.partials.product-description'))
        </div>
        <div id="dk-tab-reviews" style="display:none;">
            @include(include_theme_path('shop.product_details.partials.product-reviews'))
        </div>
        <div id="dk-tab-ship" style="display:none;">
            @include(include_theme_path('shop.product_details.partials.product-ship_return'))
        </div>

    </div>
</section>

{{-- Related Products --}}
@if($related_products->isNotEmpty())
<section class="py-5" style="background:var(--dk-carbon);">
    <div class="container">
        <h2 class="dk-related-products-title">{{ __('Related') }} <span>{{ __('Parts') }}</span></h2>
        <div class="row g-3">
            @foreach($related_products as $rp)
                @php
                    $rpdata   = theme_product_price($rp);
                    $rp_img   = get_attachment_image_by_id($rp->image_id ?? null, 'grid');
                    $rp_url   = $rp_img['img_url'] ?? null;
                    $rp_badge = $rp->badge?->name ?? null;
                @endphp
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="dk-card">
                        <div class="dk-card-img">
                            @if($rp_url)
                                <a href="{{ theme_product_url($rp->slug) }}">
                                    <img src="{{ $rp_url }}" alt="{{ $rp->name }}" loading="lazy">
                                </a>
                            @else
                                <a href="{{ theme_product_url($rp->slug) }}" class="dk-card-no-img">
                                    <i class="mdi mdi-car-wrench"></i>
                                </a>
                            @endif
                            @if($rpdata['discount'])
                                <span class="dk-card-badge dk-badge-sale">{{ $rpdata['discount'] }}% {{ __('off') }}</span>
                            @elseif($rp_badge)
                                <span class="dk-card-badge dk-badge-hot">{{ $rp_badge }}</span>
                            @endif
                            <button class="dk-card-wishlist add-to-wishlist-btn" data-product_id="{{ $rp->id }}">
                                <i class="mdi mdi-heart-outline"></i>
                            </button>
                        </div>
                        <div class="dk-card-body">
                            @if($rp->category)
                                <div class="dk-card-cat">{{ $rp->category->name }}</div>
                            @endif
                            <a href="{{ theme_product_url($rp->slug) }}" class="dk-card-name">
                                {{ \Illuminate\Support\Str::words($rp->name, 6) }}
                            </a>
                            <div class="dk-card-price">
                                <span class="dk-price-sale">{{ amount_with_currency_symbol($rpdata['sale_price']) }}</span>
                                @if($rpdata['regular_price'])
                                    <span class="dk-price-orig">{{ amount_with_currency_symbol($rpdata['regular_price']) }}</span>
                                @endif
                            </div>
                            <a href="{{ theme_product_url($rp->slug) }}" class="dk-card-atc">
                                <i class="mdi mdi-eye"></i> {{ __('View Part') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<div id="product-modal"></div>
@endsection

@section('scripts')
<script>
function dkSwitchImg(el, url) {
    document.querySelectorAll('.dk-thumb').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    var mainImg = document.getElementById('dk-main-product-img');
    if (mainImg && url) mainImg.src = url;
}

$(function () {
    $(document).on('click', '.dk-tab-btn', function () {
        var target = $(this).data('target');
        $('.dk-tab-btn').removeClass('active');
        $(this).addClass('active');
        $(this).closest('.dk-tab-strip').nextAll('[id^="dk-tab"]').hide();
        $('#' + target).show();
    });

    if (typeof StarRating !== 'undefined') {
        new StarRating('.star-rating', { maxStars: 5, clearable: false, classNames: { active: 'dk-active', base: 'dk-star-rating', selected: 'rating-selected' } });
    }

    @php if (!empty($campaign_product) && isset($campaign_active) && $campaign_active != 0) { $end_date = $campaign_product->campaign?->end_date; } @endphp
    if (typeof $.fn.syotimer !== 'undefined') {
        $('.global-timer').syotimer({ year: '{{ $end_date->year ?? 0 }}', month: '{{ $end_date->month ?? 0 }}', day: '{{ $end_date->day ?? 0 }}' });
    }

    $(document).on('click', '.plus', function () { var i = $(this).prev('.quantity-input'); if (i.val()) i[0].stepUp(1); });
    $(document).on('click', '.substract', function () { var i = $(this).next('.quantity-input'); if (i.val() > 1) i[0].stepDown(1); });

    $(document).on('click', '#review-submit-btn', function (e) {
        e.preventDefault(); var btn = $(this);
        $.ajax({ url: '{{ theme_product_review_url() }}', type: 'POST',
            data: { _token: '{{ csrf_token() }}', product_id: '{{ $product->id }}', review_text: $('#review-text').val(), rating: $('.rating-selected').data('value') },
            beforeSend: function () { btn.text('{{ __("Submitting..") }}'); },
            success: function (data) { if (data.type === 'success') { toastr.success(data.msg); setTimeout(() => location.reload(), 300); } else { toastr.error(data.msg); } btn.html('<i class="mdi mdi-send"></i> {{ __("Submit Review") }}'); },
            error: function (data) { $.each(data.responseJSON?.errors ?? {}, (v, i) => toastr.error(i)); btn.html('<i class="mdi mdi-send"></i> {{ __("Submit Review") }}'); }
        });
    });

    $(document).on('click', '.see-more-review', function () {
        var el = $(this), items = el.attr('data-items');
        $.ajax({ url: '{{ theme_product_review_more_url() }}', type: 'GET', data: { product_id: '{{ $product->id }}', items: items },
            beforeSend: function () { el.text('{{ __("Loading..") }}'); },
            success: function (data) { $('.all-reviews').html(data.markup).hide().fadeIn(800); el.html('<i class="mdi mdi-chevron-down"></i> {{ __("See More") }}'); el.attr('data-items', Number(items)+5); }
        });
    });

});
</script>
{{-- theme_product_js provides: attribute_store, syncImage, syncPrice, syncStock, selectedAttributeSearch,
     getAttributesForCart, validateSelectedAttributes, and all ATC/wishlist/compare/buy-now handlers --}}
{!! theme_product_js($product_inventory_set, $additional_info_store, $campaign_product ?? null, $campaign_active ?? 0, $product) !!}
<script>
function syncImage(selected_options) {
    var h = MD5(JSON.stringify(selected_options));
    var mainImg = document.getElementById('dk-main-product-img');
    if (!mainImg) return;
    var gallery = document.getElementById('shop_details_gallery_slider');
    var orig = gallery ? gallery.dataset.src : null;
    if (additional_info_store[h] && additional_info_store[h].image) {
        mainImg.src = additional_info_store[h].image;
    } else if (orig) {
        mainImg.src = orig;
    }
}
</script>
@endsection
