@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $product->name !!} @endsection
@section('page-title') {!! $product->name !!} @endsection
@section('meta-data') {!! render_page_meta_data($product) !!} @endsection

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
            ? max(0, $campaign_product->units_for_sale - (int) $campaign_product->sold_count)
            : null)
        : optional($product->inventory)->stock_count;
    $stock_count = $stock_count > 0 ? $stock_count : 0;
    if ($campaign_product) {
        $campaign_title  = \Modules\Campaign\Entities\Campaign::select('id','title')->where('id', $campaign_product?->id)->first();
        $campaign_active = !empty($campaign_product) && empty($data['campaign_name']) ? 1 : 0;
    }
    $quickView   = false;
    $image_array = [(int)$product->image_id];
    foreach ($product->gallery_images ?? [] as $gi) { $image_array[] = $gi->id; }
    $main_img     = get_attachment_image_by_id($product->image_id ?? null, 'large');
    $main_img_url = $main_img['img_url'] ?? null;
    $final_price  = calculatePrice($sale_price, $product);
@endphp

{{-- Breadcrumb --}}
<div class="container ar-pd-breadcrumb-wrap">
    <div class="ar-breadcrumb">
        <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
        <span class="ar-breadcrumb-sep"><i class="mdi mdi-chevron-right"></i></span>
        <a href="{{ theme_shop_url() }}">{{ __('Shop') }}</a>
        <span class="ar-breadcrumb-sep"><i class="mdi mdi-chevron-right"></i></span>
        <span class="ar-breadcrumb-current">{{ $product->name }}</span>
    </div>
</div>

<div class="container ar-pd-section">
    <div class="row g-5">

        {{-- ===== Images ===== --}}
        <div class="col-lg-5">
            <div class="ar-pd-gallery" id="shop_details_gallery_slider">
                {{-- Thumbnail list --}}
                <div class="ar-pd-thumbs-list">
                    @foreach($image_array as $imgId)
                        @php
                            $tdata = get_attachment_image_by_id($imgId, 'grid');
                            $turl  = $tdata['img_url'] ?? null;
                        @endphp
                        <div class="ar-pd-thumb {{ $loop->first ? 'active' : '' }}" onclick="arSwitchImg(this, '{{ $turl }}')">
                            @if($turl)
                                <img src="{{ $turl }}" alt="">
                            @else
                                <span class="ar-pd-thumb-placeholder">◆</span>
                            @endif
                        </div>
                    @endforeach
                </div>
                {{-- Main image — must keep .long-img for JS syncImage() --}}
                <div class="ar-pd-main-img-wrap">
                    <div class="shop-details-thums long-img" data-src="{{ $main_img_url }}">
                        @if($main_img_url)
                            <img id="ar-main-product-img" src="{{ $main_img_url }}" alt="{{ $product->name }}" class="ar-pd-main-img-fill">
                        @else
                            <div class="ar-pd-img-placeholder">◆</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== Info ===== --}}
        <div class="col-lg-7">
            @if($product->badge)
                <span class="ar-pd-badge-inline">{{ $product->badge->name }}</span>
            @endif

            <h1 class="ar-pd-title">{{ $product->name }}</h1>

            {{-- Stars + stock --}}
            <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
                {!! render_product_star_rating_markup_with_count($product) !!}
                <span id="stock"
                      data-stock-text='{!! $stock_count > 0 ? "<span class=\"text-success\">".__("In Stock")."</span>" : "<span class=\"text-danger\">".__("Out of Stock")."</span>" !!}'>
                    {!! $stock_count > 0
                        ? '<span class="text-success"><i class="mdi mdi-check-circle-outline"></i> '.__('In Stock').'</span>'
                        : '<span class="text-danger"><i class="mdi mdi-close-circle-outline"></i> '.__('Out of Stock').'</span>' !!}
                </span>
            </div>

            {{-- Price --}}
            <div class="mb-4 d-flex align-items-baseline gap-2 flex-wrap">
                <span class="ar-pd-price"
                      id="price"
                      data-main-price="{{ $final_price }}"
                      data-currency-symbol="{{ site_currency_symbol() }}">
                    {{ amount_with_currency_symbol($final_price) }}
                </span>
                @if($deleted_price)
                    <span class="ar-pd-was">{{ amount_with_currency_symbol($deleted_price) }}</span>
                @endif
                @if($discount)
                    <span class="ar-pd-discount-badge">{{ $discount }}% {{ __('OFF') }}</span>
                @endif
            </div>

            {{-- Campaign countdown --}}
            @if($campaign_product !== null && $campaign_product->status !== 'draft')
                <div class="mb-4">
                    <h6 class="ar-pd-campaign-title">{{ $campaign_name }}</h6>
                    @if(isset($campaign_active) && $campaign_active)
                        <div class="global-timer"></div>
                    @endif
                </div>
            @endif

            {{-- Product form: sizes / colors / attrs / qty / ATC --}}
            <div class="ar-product-form">
                @include(include_theme_path('shop.product_details.partials.product-options'))
            </div>

            {{-- Trust badges --}}
            <div class="d-flex gap-3 flex-wrap mt-4 ar-pd-trust">
                <span><i class="mdi mdi-shield-check-outline ar-pd-trust-icon ar-pd-trust-secure"></i> {{ __('Secure Checkout') }}</span>
                <span><i class="mdi mdi-refresh ar-pd-trust-icon ar-pd-trust-returns"></i> {{ __('Easy Returns') }}</span>
                <span><i class="mdi mdi-truck-fast-outline ar-pd-trust-icon ar-pd-trust-delivery"></i> {{ __('Fast Delivery') }}</span>
            </div>

            {{-- Product meta --}}
            <div class="ar-pd-meta-list mt-4">
                @if($product->inventory?->sku)
                    <div class="ar-pd-meta-item">{{ __('SKU') }}: <span>{{ $product->inventory->sku }}</span></div>
                @endif
                @if($product->category?->name)
                    <div class="ar-pd-meta-item">{{ __('Category') }}:
                        <span><a href="{{ dynamicRoute($product->category->slug) }}">{{ $product->category->name }}</a></span>
                        @if($product->subCategory?->slug)
                            / <span><a href="{{ dynamicRoute($product->subCategory->slug) }}">{{ $product->subCategory->name }}</a></span>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ===== Tabs: Description / Reviews / Ship & Return ===== --}}
    <div class="ar-pd-tabs-wrap">
        <div class="ar-tab-nav">
            <button class="ar-tab-btn active" data-target="ar-tab-desc">{{ __('Description') }}</button>
            <button class="ar-tab-btn" data-target="ar-tab-reviews">{{ __('Reviews') }}</button>
            <button class="ar-tab-btn" data-target="ar-tab-ship">{{ __('Ship & Return') }}</button>
        </div>
        <div id="ar-tab-desc" class="ar-tab-panel active">
            @include(include_theme_path('shop.product_details.partials.product-description'))
        </div>
        <div id="ar-tab-reviews" class="ar-tab-panel">
            @include(include_theme_path('shop.product_details.partials.product-reviews'))
        </div>
        <div id="ar-tab-ship" class="ar-tab-panel">
            @include(include_theme_path('shop.product_details.partials.product-ship_return'))
        </div>
    </div>

    {{-- ===== Related Products ===== --}}
    @if($related_products->isNotEmpty())
    <div class="ar-pd-related">
        <h3 class="ar-pd-related-title">{{ __('You Might Also Like') }}</h3>
        <div class="row g-3">
            @foreach($related_products as $rp)
                @php
                    $rp_data   = get_product_dynamic_price($rp);
                    $rp_img    = get_attachment_image_by_id($rp->image_id ?? null, 'grid');
                    $rp_imgurl = $rp_img['img_url'] ?? null;
                @endphp
                <div class="col-6 col-md-3">
                    <div class="ar-card">
                        <div class="ar-card-img">
                            @if($rp_imgurl)
                                <a href="{{ dynamicRoute($rp->slug) }}">
                                    <img src="{{ $rp_imgurl }}" alt="{{ $rp->name }}" loading="lazy">
                                </a>
                            @else
                                <a href="{{ dynamicRoute($rp->slug) }}" class="ar-card-placeholder">◆</a>
                            @endif
                        </div>
                        <div class="ar-card-body">
                            <div class="ar-card-name">
                                <a href="{{ dynamicRoute($rp->slug) }}">
                                    {{ \Illuminate\Support\Str::words($rp->name, 6) }}
                                </a>
                            </div>
                            <div class="ar-card-price">
                                <span class="ar-price-sale">{{ amount_with_currency_symbol($rp_data['sale_price']) }}</span>
                                @if(!empty($rp_data['regular_price']))
                                    <span class="ar-price-orig">{{ amount_with_currency_symbol($rp_data['regular_price']) }}</span>
                                @endif
                            </div>
                            <a href="{{ dynamicRoute($rp->slug) }}" class="ar-card-atc">
                                <i class="mdi mdi-eye-outline"></i> {{ __('View') }}
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
function arSwitchImg(el, url) {
    document.querySelectorAll('.ar-pd-thumb').forEach(function (t) { t.classList.remove('active'); });
    el.classList.add('active');
    var mainImg = document.getElementById('ar-main-product-img');
    var longImg = document.querySelector('.long-img');
    if (longImg && url) { longImg.setAttribute('data-src', url); }
    if (mainImg && url) { mainImg.src = url; }
}

$(function () {
    // Tab switching
    $(document).on('click', '.ar-tab-btn', function () {
        var target = $(this).data('target');
        $('.ar-tab-btn').removeClass('active');
        $(this).addClass('active');
        $('.ar-tab-panel').removeClass('active');
        $('#' + target).addClass('active');
    });

    // Star rating
    if (typeof StarRating !== 'undefined') {
        new StarRating('.star-rating', {
            maxStars: 5, clearable: false,
            stars: function (el) {
                el.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><rect class="gl-star-full" width="19" height="19" x="2.5" y="2.5"/><polygon fill="#FFF" points="12 5.375 13.646 10.417 19 10.417 14.665 13.556 16.313 18.625 11.995 15.476 7.688 18.583 9.333 13.542 5 10.417 10.354 10.417"/></svg>';
            },
            classNames: { active: 'gl-active', base: 'gl-star-rating', selected: 'rating-selected' },
        });
    }

    // Countdown
    @php if (!empty($campaign_product) && isset($campaign_active) && $campaign_active != 0) { $end_date = $campaign_product->campaign?->end_date; } @endphp
    if (typeof $.fn.syotimer !== 'undefined') {
        $('.global-timer').syotimer({ year: '{{ $end_date->year ?? 0 }}', month: '{{ $end_date->month ?? 0 }}', day: '{{ $end_date->day ?? 0 }}' });
    }

    // Quantity stepper
    $(document).on('click', '.plus',      function () { var i = $(this).prev('.quantity-input'); if (i.val()) i[0].stepUp(1); });
    $(document).on('click', '.substract', function () { var i = $(this).next('.quantity-input'); if (+i.val() > 1) i[0].stepDown(1); });

    // Review submit
    $(document).on('click', '#review-submit-btn', function (e) {
        e.preventDefault();
        var btn = $(this);
        $.ajax({
            url: '{{ theme_product_review_url() }}', type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                product_id: '{{ $product->id }}',
                review_text: $('#review-text').val(),
                rating: $('.rating-selected').data('value')
            },
            beforeSend: function () { btn.text('{{ __("Submitting..") }}'); },
            success: function (data) {
                if (data.type === 'success') { toastr.success(data.msg); setTimeout(function () { location.reload(); }, 300); }
                else { toastr.error(data.msg); }
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
            url: '{{ theme_product_review_more_url() }}', type: 'GET',
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
{!! theme_product_js(
    $product_inventory_set,
    $additional_info_store,
    $campaign_product ?? null,
    $campaign_active ?? 0,
    $product
) !!}
@endsection
