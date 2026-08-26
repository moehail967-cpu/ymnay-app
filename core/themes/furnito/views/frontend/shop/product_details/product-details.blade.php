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
        $campaign_title  = \Modules\Campaign\Entities\Campaign::select('id','title')->where('id', $campaign_product?->id)->first();
        $campaign_active = !empty($campaign_product) && empty($data['campaign_name']) ? 1 : 0;
    }
    $quickView = false;

    // Image gallery
    $image_array = [(int)$product->image_id];
    foreach ($product->gallery_images ?? [] as $gi) { $image_array[] = $gi->id; }
    $main_img     = get_attachment_image_by_id($product->image_id ?? null, 'large');
    $main_img_url = $main_img['img_url'] ?? null;
@endphp

{{-- Breadcrumb --}}
<div class="container fn-pd-breadcrumb">
    <div class="fn-breadcrumb">
        <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
        <span><i class="las la-angle-right"></i></span>
        <a href="{{ theme_shop_url() }}">{{ __('Shop') }}</a>
        <span><i class="las la-angle-right"></i></span>
        <span class="current">{{ $product->name }}</span>
    </div>
</div>

{{-- Product Section --}}
<div class="container fn-pd-section">
    <div class="row g-5">

        {{-- ===== Images ===== --}}
        <div class="col-lg-5">
            <div class="fn-thumb-row" id="shop_details_gallery_slider">
                {{-- Thumbnails --}}
                <div class="fn-thumb-list">
                    @foreach($image_array as $imgId)
                        @php
                            $tdata = get_attachment_image_by_id($imgId, 'grid');
                            $turl  = $tdata['img_url'] ?? null;
                        @endphp
                        <div class="fn-thumb {{ $loop->first ? 'active' : '' }}"
                             onclick="fnSwitchImg(this, '{{ $turl }}')">
                            @if($turl)
                                <img src="{{ $turl }}" alt="">
                            @else
                                <span class="fn-pd-thumb-placeholder"><i class="las la-couch"></i></span>
                            @endif
                        </div>
                    @endforeach
                </div>
                {{-- Main image — .long-img required by theme_product_js() syncImage() --}}
                <div class="fn-pd-main-img-wrap">
                    <div class="shop-details-thums long-img" data-src="{{ $main_img_url }}">
                        @if($main_img_url)
                            <img id="fn-main-product-img"
                                 src="{{ $main_img_url }}"
                                 alt="{{ $product->name }}"
                                 class="fn-pd-main-img-fill">
                        @else
                            <div class="fn-pd-img-placeholder"><i class="las la-couch la-3x"></i></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== Info ===== --}}
        <div class="col-lg-7">
            @if($product->badge)
                <span class="fn-card-badge fn-pd-badge-inline">{{ $product->badge->name }}</span>
            @endif

            <h1 class="fn-pd-title">{{ $product->name }}</h1>

            {{-- Stars + stock --}}
            <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
                {!! theme_star_rating($product) !!}
                <span id="stock"
                      data-stock-text='{!! $stock_count > 0 ? "<span class=\"text-success\">".__("In Stock")."</span>" : "<span class=\"text-danger\">".__("Out of Stock")."</span>" !!}'>
                    {!! $stock_count > 0
                        ? '<span class="text-success"><i class="las la-check-circle"></i> '.__('In Stock').'</span>'
                        : '<span class="text-danger"><i class="las la-times-circle"></i> '.__('Out of Stock').'</span>' !!}
                </span>
            </div>

            {{-- Price --}}
            <div class="mb-4 d-flex align-items-baseline gap-2 flex-wrap">
                <span class="fn-pd-price"
                      id="price"
                      data-main-price="{{ $sale_price }}"
                      data-currency-symbol="{{ site_currency_symbol() }}">
                    {{ amount_with_currency_symbol($sale_price) }}
                </span>
                @if($deleted_price)
                    <span class="fn-pd-was">{{ amount_with_currency_symbol($deleted_price) }}</span>
                @endif
                @if($discount)
                    <span class="fn-pd-discount-badge">{{ $discount }}% {{ __('OFF') }}</span>
                @endif
            </div>

            {{-- Campaign countdown --}}
            @if($campaign_product !== null && $campaign_product->status !== 'draft')
                <div class="mb-4">
                    <h6 class="fn-pd-campaign-title">{{ $campaign_name }}</h6>
                    @if(isset($campaign_active) && $campaign_active)
                        <div class="global-timer"></div>
                    @endif
                </div>
            @endif

            {{-- Product options form (sizes, colors, qty, cart buttons) --}}
            <div class="fn-product-form">
                @include(include_theme_path('shop.product_details.partials.product-options'))
            </div>

            {{-- Trust pills --}}
            <div class="d-flex gap-3 flex-wrap mt-4 fn-pd-trust">
                <span><i class="las la-shield-alt fn-pd-trust-secure"></i> {{ __('Secure Checkout') }}</span>
                <span><i class="las la-redo-alt fn-pd-trust-returns"></i> {{ __('Easy Returns') }}</span>
                <span><i class="las la-truck fn-pd-trust-delivery"></i> {{ __('Fast Delivery') }}</span>
            </div>

            {{-- Product meta --}}
            <div class="fn-product-meta mt-4">
                @if($product->sku ?? null)
                    <div class="fn-meta-row">
                        <span class="fn-meta-label">{{ __('SKU') }}:</span>
                        <span>{{ $product->sku }}</span>
                    </div>
                @endif
                @if($product->category?->name)
                    <div class="fn-meta-row">
                        <span class="fn-meta-label">{{ __('Category') }}:</span>
                        <span>{{ $product->category->name }}</span>
                    </div>
                @endif
                @if($product->tags?->isNotEmpty())
                    <div class="fn-meta-row">
                        <span class="fn-meta-label">{{ __('Tags') }}:</span>
                        <span>
                            @foreach($product->tags as $tag)
                                {{ $tag->tag_name }}{{ !$loop->last ? ',' : '' }}
                            @endforeach
                        </span>
                    </div>
                @endif
                @if($product->inventory?->stock_count !== null)
                    <div class="fn-meta-row">
                        <span class="fn-meta-label">{{ __('Items Left') }}:</span>
                        <span id="item_left"
                              data-stock-text="{{ $stock_count > 0 ? __('Only!') . ' ' . $stock_count . ' ' . __('Item Left!') : __('No Item Left!') }}">
                            {{ $stock_count > 0 ? __('Only!') . ' ' . $stock_count . ' ' . __('Item Left!') : __('No Item Left!') }}
                        </span>
                    </div>
                @else
                    <span id="item_left" data-stock-text="" style="display:none;"></span>
                @endif
            </div>
        </div>
    </div>

    {{-- ===== Tabs: Description / Reviews / Ship & Return ===== --}}
    <div class="fn-pd-tabs-wrap">
        <div class="fn-tab-nav">
            <button class="fn-tab-btn active" data-target="fn-tab-desc">{{ __('Description') }}</button>
            <button class="fn-tab-btn" data-target="fn-tab-reviews">{{ __('Reviews') }}</button>
            <button class="fn-tab-btn" data-target="fn-tab-ship">{{ __('Ship & Return') }}</button>
        </div>

        <div id="fn-tab-desc" class="fn-tab-panel active">
            @include(include_theme_path('shop.product_details.partials.product-description'))
        </div>
        <div id="fn-tab-reviews" class="fn-tab-panel">
            @include(include_theme_path('shop.product_details.partials.product-reviews'))
        </div>
        <div id="fn-tab-ship" class="fn-tab-panel">
            @include(include_theme_path('shop.product_details.partials.product-ship_return'))
        </div>
    </div>

    {{-- ===== Related Products ===== --}}
    @if($related_products->isNotEmpty())
    <div class="fn-pd-related">
        <h3 class="fn-pd-related-title">{{ __('You Might Also Like') }}</h3>
        <div class="row g-3">
            @foreach($related_products as $rp)
                @php
                    $rpdata    = theme_product_price($rp);
                    $rp_img    = get_attachment_image_by_id($rp->image_id ?? null, 'grid');
                    $rp_imgurl = $rp_img['img_url'] ?? null;
                @endphp
                <div class="col-6 col-md-3">
                    <div class="fn-card">
                        <div class="fn-card-img">
                            <a href="{{ theme_product_url($rp->slug) }}">
                                @if($rp_imgurl)
                                    <img src="{{ $rp_imgurl }}" alt="{{ $rp->name }}" loading="lazy">
                                @else
                                    <div class="fn-card-img-placeholder"><i class="las la-couch"></i></div>
                                @endif
                            </a>
                        </div>
                        <div class="fn-card-body">
                            <div class="fn-card-name">
                                <a href="{{ theme_product_url($rp->slug) }}">
                                    {{ \Illuminate\Support\Str::words($rp->name, 6) }}
                                </a>
                            </div>
                            <div class="fn-card-price">
                                <span class="fn-price-sale">{{ amount_with_currency_symbol($rpdata['sale_price']) }}</span>
                            </div>
                            <a href="{{ theme_product_url($rp->slug) }}" class="fn-card-atc">
                                <i class="las la-eye"></i> {{ __('View') }}
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
function fnSwitchImg(el, url) {
    document.querySelectorAll('.fn-thumb').forEach(function (t) { t.classList.remove('active'); });
    el.classList.add('active');
    var mainImg = document.getElementById('fn-main-product-img');
    var longImg = document.querySelector('.long-img');
    if (url) {
        if (mainImg) mainImg.src = url;
        if (longImg) longImg.setAttribute('data-src', url);
    }
}

$(function () {

    // Tab switching
    $(document).on('click', '.fn-tab-btn', function () {
        var target = $(this).data('target');
        $('.fn-tab-btn').removeClass('active');
        $(this).addClass('active');
        $('.fn-tab-panel').removeClass('active');
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
        $('.global-timer').syotimer({
            year:  '{{ $end_date->year ?? 0 }}',
            month: '{{ $end_date->month ?? 0 }}',
            day:   '{{ $end_date->day ?? 0 }}'
        });
    }

    // Quantity stepper
    $(document).on('click', '.plus', function () {
        var i = $(this).prev('.quantity-input');
        if (i.val()) i[0].stepUp(1);
    });
    $(document).on('click', '.substract', function () {
        var i = $(this).next('.quantity-input');
        if (i.val() > 1) i[0].stepDown(1);
    });

    // Review submit
    $(document).on('click', '#review-submit-btn', function (e) {
        e.preventDefault();
        var btn = $(this);
        $.ajax({
            url: '{{ theme_product_review_url() }}',
            type: 'POST',
            data: {
                _token:      '{{ csrf_token() }}',
                product_id:  '{{ $product->id }}',
                review_text: $('#review-text').val(),
                rating:      $('.rating-selected').data('value')
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
{!! theme_product_js(
    $product_inventory_set,
    $additional_info_store,
    $campaign_product ?? null,
    $campaign_active ?? 0,
    $product
) !!}
@endsection
