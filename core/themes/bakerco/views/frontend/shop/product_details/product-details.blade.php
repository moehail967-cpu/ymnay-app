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
    $campaign_percentage = $discount;
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

    // Image gallery
    $image_array = [(int)$product->image_id];
    foreach ($product->gallery_images ?? [] as $gi) { $image_array[] = $gi->id; }
    $main_img     = get_attachment_image_by_id($product->image_id ?? null, 'large');
    $main_img_url = $main_img['img_url'] ?? null;
@endphp

{{-- Breadcrumb --}}
<div class="container bk-pd-breadcrumb">
    <div class="bk-breadcrumb">
        <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
        <span class="sep"><i class="mdi mdi-chevron-right"></i></span>
        <a href="{{ theme_shop_url() }}">{{ __('Shop') }}</a>
        <span class="sep"><i class="mdi mdi-chevron-right"></i></span>
        <span class="current">{{ $product->name }}</span>
    </div>
</div>

{{-- Product Section --}}
<div class="container bk-pd-section">
    <div class="row g-5">

        {{-- ===== Images ===== --}}
        <div class="col-lg-5">
            <div class="bk-thumb-row" id="shop_details_gallery_slider">
                {{-- Thumbnails --}}
                <div class="bk-thumb-list">
                    @foreach($image_array as $imgId)
                        @php $tdata = get_attachment_image_by_id($imgId, 'grid'); $turl = $tdata['img_url'] ?? null; @endphp
                        <div class="bk-thumb {{ $loop->first ? 'active' : '' }}" onclick="bkSwitchImg(this, '{{ $turl }}')">
                            @if($turl)
                                <img src="{{ $turl }}" alt="">
                            @else
                                <span class="bk-pd-thumb-placeholder">🥐</span>
                            @endif
                        </div>
                    @endforeach
                </div>
                {{-- Main image — must keep .long-img for JS syncImage() --}}
                <div class="bk-pd-main-img">
                    <div class="shop-details-thums long-img" data-src="{{ $main_img_url }}">
                        @if($main_img_url)
                            <img id="bk-main-product-img" src="{{ $main_img_url }}" alt="{{ $product->name }}" class="bk-pd-main-img-fill">
                        @else
                            <div class="bk-pd-img-placeholder">🥐</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== Info ===== --}}
        <div class="col-lg-7">
            @if($product->badge)
                <span class="bk-card-badge bk-pd-badge-inline">{{ $product->badge->name }}</span>
            @endif

            <h1 class="bk-pd-title">{{ $product->name }}</h1>

            {{-- Stars + stock --}}
            <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
                {!! theme_star_rating($product) !!}
                <span id="stock"
                      data-stock-text='{!! $stock_count > 0 ? "<span class=\"text-success\">".__("In Stock")."</span>" : "<span class=\"text-danger\">".__("Out of Stock")."</span>" !!}'>
                    {!! $stock_count > 0
                        ? '<span class="text-success"><i class="mdi mdi-check-circle-outline"></i> '.__('In Stock').'</span>'
                        : '<span class="text-danger"><i class="mdi mdi-close-circle-outline"></i> '.__('Out of Stock').'</span>' !!}
                </span>
            </div>

            {{-- Price --}}
            <div class="mb-4 d-flex align-items-baseline gap-2 flex-wrap">
                <span class="bk-pd-price"
                      id="price"
                      data-main-price="{{ $sale_price }}"
                      data-currency-symbol="{{ site_currency_symbol() }}">
                    {{ amount_with_currency_symbol($sale_price) }}
                </span>
                @if($deleted_price)
                    <span class="bk-pd-was">{{ amount_with_currency_symbol($deleted_price) }}</span>
                @endif
                @if($discount)
                    <span class="bk-pd-discount-badge">{{ $discount }}% {{ __('OFF') }}</span>
                @endif
            </div>

            {{-- Campaign countdown --}}
            @if($campaign_product !== null && $campaign_product->status !== 'draft')
                <div class="mb-4">
                    <h6 class="bk-pd-campaign-title">{{ $campaign_name }}</h6>
                    @if(isset($campaign_active) && $campaign_active)
                        <div class="global-timer"></div>
                    @endif
                </div>
            @endif

            {{-- Product options form (sizes, colors, attributes, qty, add-to-cart) --}}
            <div class="bk-product-form">
                @include(include_theme_path('shop.product_details.partials.product-options'))
            </div>

            {{-- Trust pills --}}
            <div class="d-flex gap-3 flex-wrap mt-4 bk-pd-trust">
                <span><i class="mdi mdi-shield-check-outline bk-pd-trust-secure"></i> {{ __('Secure Checkout') }}</span>
                <span><i class="mdi mdi-refresh bk-pd-trust-returns"></i> {{ __('Easy Returns') }}</span>
                <span><i class="mdi mdi-truck-fast-outline bk-pd-trust-delivery"></i> {{ __('Fast Delivery') }}</span>
            </div>

            {{-- Product meta --}}
            <div class="bk-product-meta bk-product-meta-list mt-4">
                @if($product->sku ?? null)
                    <div>{{ __('SKU') }}: <span>{{ $product->sku }}</span></div>
                @endif
                @if($product->category?->name)
                    <div>{{ __('Category') }}: <span>{{ $product->category->name }}</span></div>
                @endif
                @if($product->tags?->isNotEmpty())
                    <div>{{ __('Tags') }}:
                        @foreach($product->tags as $tag)
                            <span>{{ $tag->tag_name }}{{ !$loop->last ? ',' : '' }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ===== Tabs: Description / Reviews / Ship & Return ===== --}}
    <div class="bk-pd-tabs-wrap">
        <div class="bk-tab-nav">
            <button class="bk-tab-btn active" data-target="bk-tab-desc">{{ __('Description') }}</button>
            <button class="bk-tab-btn" data-target="bk-tab-reviews">{{ __('Reviews') }}</button>
            <button class="bk-tab-btn" data-target="bk-tab-ship">{{ __('Ship & Return') }}</button>
        </div>

        <div id="bk-tab-desc" class="bk-tab-panel active">
            @include(include_theme_path('shop.product_details.partials.product-description'))
        </div>
        <div id="bk-tab-reviews" class="bk-tab-panel">
            @include(include_theme_path('shop.product_details.partials.product-reviews'))
        </div>
        <div id="bk-tab-ship" class="bk-tab-panel">
            @include(include_theme_path('shop.product_details.partials.product-ship_return'))
        </div>
    </div>

    {{-- ===== Related Products ===== --}}
    @if($related_products->isNotEmpty())
    <div class="bk-pd-related">
        <h3 class="bk-pd-related-title">{{ __('You Might Also Like') }}</h3>
        <div class="row g-3">
            @foreach($related_products as $rp)
                @php
                    $rpdata    = theme_product_price($rp);
                    $rp_img    = get_attachment_image_by_id($rp->image_id ?? null, 'grid');
                    $rp_imgurl = $rp_img['img_url'] ?? null;
                @endphp
                <div class="col-6 col-md-3">
                    <div class="bk-card">
                        <div class="bk-card-img">
                            @if($rp_imgurl)
                                <a href="{{ theme_product_url($rp->slug) }}">
                                    <img src="{{ $rp_imgurl }}" alt="{{ $rp->name }}" loading="lazy">
                                </a>
                            @else
                                <a href="{{ theme_product_url($rp->slug) }}" class="bk-placeholder bk-pd-related-placeholder">🥐</a>
                            @endif
                        </div>
                        <div class="bk-card-body">
                            <div class="bk-card-name">
                                <a href="{{ theme_product_url($rp->slug) }}" class="bk-pd-related-link">
                                    {{ \Illuminate\Support\Str::words($rp->name, 6) }}
                                </a>
                            </div>
                            <div class="bk-card-price">
                                <span class="bk-price-sale">{{ amount_with_currency_symbol($rpdata['sale_price']) }}</span>
                            </div>
                            <a href="{{ theme_product_url($rp->slug) }}" class="bk-card-atc bk-pd-related-link">
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
function bkSwitchImg(el, url) {
    document.querySelectorAll('.bk-thumb').forEach(function (t) { t.classList.remove('active'); });
    el.classList.add('active');
    var mainImg = document.getElementById('bk-main-product-img');
    if (mainImg && url) { mainImg.src = url; }
    // also update .long-img data-src for JS syncImage()
    var longImg = document.querySelector('.long-img');
    if (longImg && url) { longImg.setAttribute('data-src', url); if (mainImg) mainImg.src = url; }
}

$(function () {
    // Tab switching
    $(document).on('click', '.bk-tab-btn', function () {
        var target = $(this).data('target');
        $('.bk-tab-btn').removeClass('active');
        $(this).addClass('active');
        $('.bk-tab-panel').removeClass('active');
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
    $(document).on('click', '.plus', function () { var i = $(this).prev('.quantity-input'); if (i.val()) i[0].stepUp(1); });
    $(document).on('click', '.substract', function () { var i = $(this).next('.quantity-input'); if (i.val() > 1) i[0].stepDown(1); });

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
            beforeSend: function () { toastr.warning('{{ __("Submitting please wait.") }}'); btn.text('{{ __("Submitting..") }}'); },
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
