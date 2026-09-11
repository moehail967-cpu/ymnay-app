@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $product->name !!} @endsection
@section('page-title') {!! $product->name !!} @endsection

@section('meta-data') {!! render_page_meta_data($product) !!} @endsection

@section('style')
<link rel="stylesheet" href="{{ global_asset('assets/common/css/star-rating.min.css') }}">
<style>
.fp-thumb-grid    { display: grid; grid-template-columns: 80px 1fr; gap: 12px; }
.fp-thumb-list    { display: flex; flex-direction: column; gap: 8px; }
.fp-thumb         { width: 80px; height: 80px; border-radius: var(--fp-radius); border: 2px solid var(--fp-border); overflow: hidden; cursor: pointer; transition: border-color .2s; background: var(--fp-card); }
.fp-thumb.active, .fp-thumb:hover { border-color: var(--fp-green); }
.fp-thumb img     { width: 100%; height: 100%; object-fit: cover; }
.fp-main-img      { border-radius: var(--fp-radius); border: 1px solid var(--fp-border); overflow: hidden; background: var(--fp-card); aspect-ratio: 1; }
.fp-main-img img  { width: 100%; height: 100%; object-fit: cover; }
.fp-product-title { font-size: clamp(20px,3vw,28px); font-weight: 900; color: var(--fp-white); margin-bottom: 10px; font-family: 'Barlow Condensed', sans-serif; text-transform: uppercase; letter-spacing: 1px; }
.fp-product-price { font-size: 30px; font-weight: 900; color: var(--fp-green); }
.fp-product-price-old { font-size: 16px; color: var(--fp-muted); text-decoration: line-through; margin-left: 8px; }
.fp-tab-nav       { display: flex; gap: 0; border-bottom: 2px solid var(--fp-border); margin-bottom: 24px; overflow-x: auto; scrollbar-width: none; }
.fp-tab-nav::-webkit-scrollbar { display: none; }
.fp-tab-nav-btn   { padding: 12px 24px; font-size: 13px; font-weight: 700; color: var(--fp-muted); background: none; border: none; border-bottom: 3px solid transparent; margin-bottom: -2px; cursor: pointer; font-family: 'Barlow Condensed', sans-serif; text-transform: uppercase; letter-spacing: 1px; transition: all .2s; }
.fp-tab-nav-btn.active { color: var(--fp-green); border-bottom-color: var(--fp-green); }
.fp-tab-panel     { display: none; color: var(--fp-muted); }
.fp-tab-panel.active { display: block; }
.fp-tab-panel * { color: var(--fp-muted) !important; background-color: transparent !important; }
.fp-tab-panel a { color: var(--fp-green) !important; }
.fp-tab-panel table, .fp-tab-panel td, .fp-tab-panel th { border-color: var(--fp-border) !important; }
.fp-review-item   { padding: 20px 0; border-bottom: 1px solid var(--fp-border); }
.fp-review-item:last-child { border-bottom: none; }
.fp-product-form .size-lists { display: flex; flex-wrap: wrap; gap: 8px; list-style: none; padding: 0; margin: 0; }
.fp-product-form .size-lists li { padding: 6px 16px; border: 1.5px solid var(--fp-border); border-radius: 4px; font-size: 13px; font-weight: 700; cursor: pointer; color: var(--fp-muted); transition: all .2s; }
.fp-product-form .size-lists li:hover, .fp-product-form .size-lists li.active { border-color: var(--fp-green); color: var(--fp-green); background: rgba(0,255,65,.08); }
.fp-product-form .size-lists li.disabled { opacity: .45; pointer-events: none; }
.fp-product-form .color-list li { width: 28px; height: 28px; border-radius: 50%; padding: 0; }
.fp-product-form .input-title { font-size: 12px; font-weight: 700; color: var(--fp-muted); text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 8px; }
.fp-product-form .form--input { display: none; }
</style>
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

    $image_array = [(int)$product->image_id];
    foreach ($product->gallery_images ?? [] as $gi) { $image_array[] = $gi->id; }
    $main_img = get_attachment_image_by_id($product->image_id ?? null, 'large');
    $main_img_url = $main_img['img_url'] ?? null;
@endphp

{{-- Breadcrumb --}}
<div class="fp-page-hero" style="padding:16px 0;">
    <div class="container">
        <ul class="fp-breadcrumb-list">
            <li><a href="{{ theme_home_url() }}">{{ __('Home') }}</a></li>
            <li><a href="{{ theme_shop_url() }}">{{ __('Shop') }}</a></li>
            <li>{{ \Illuminate\Support\Str::words($product->name, 6) }}</li>
        </ul>
    </div>
</div>

{{-- Product Section --}}
<div class="container" style="padding-top:28px; padding-bottom:60px;">
    <div class="row g-5">

        {{-- Images --}}
        <div class="col-lg-5">
            <div class="fp-thumb-grid" id="shop_details_gallery_slider">
                {{-- Thumbnails --}}
                <div class="fp-thumb-list">
                    @foreach($image_array as $imgId)
                        @php $tdata = get_attachment_image_by_id($imgId, 'grid'); $turl = $tdata['img_url'] ?? null; @endphp
                        <div class="fp-thumb {{ $loop->first ? 'active' : '' }}" onclick="fpSwitchImg(this, '{{ $turl }}')">
                            @if($turl)
                                <img src="{{ $turl }}" alt="">
                            @else
                                <i class="mdi mdi-dumbbell" style="font-size:28px;color:var(--fp-green);display:flex;align-items:center;justify-content:center;height:100%;"></i>
                            @endif
                        </div>
                    @endforeach
                </div>
                {{-- Main image --}}
                <div class="fp-main-img">
                    <div class="shop-details-thums long-img" data-src="{{ $main_img_url }}">
                        @if($main_img_url)
                            <img id="fp-main-product-img" src="{{ $main_img_url }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:cover;">
                        @else
                            <div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:80px;">
                                <i class="mdi mdi-dumbbell" style="color:var(--fp-green);opacity:.3;"></i>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Info --}}
        <div class="col-lg-7">
            @if($product->badge)
                <span class="fp-card-badge fp-badge-hot" style="position:static;display:inline-block;margin-bottom:12px;">{{ $product->badge->name }}</span>
            @endif

            <h1 class="fp-product-title">{{ $product->name }}</h1>

            {{-- Stars + stock --}}
            <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
                {!! theme_star_rating($product) !!}
                <span id="stock"
                      data-stock-text='{!! $stock_count > 0 ? "<span class=\"text-success\">".__("In Stock")."</span>" : "<span class=\"text-danger\">".__("Out of Stock")."</span>" !!}'>
                    {!! $stock_count > 0 ? '<span class="text-success"><i class="mdi mdi-check-circle-outline"></i> '.__('In Stock').'</span>' : '<span class="text-danger"><i class="mdi mdi-close-circle-outline"></i> '.__('Out of Stock').'</span>' !!}
                </span>
            </div>

            {{-- Price --}}
            <div class="mb-4 d-flex align-items-baseline gap-2 flex-wrap">
                <span class="fp-product-price"
                      id="price"
                      data-main-price="{{ $sale_price }}"
                      data-currency-symbol="{{ site_currency_symbol() }}">
                    {{ amount_with_currency_symbol($sale_price) }}
                </span>
                @if($deleted_price)
                    <span class="fp-product-price-old">{{ amount_with_currency_symbol($deleted_price) }}</span>
                @endif
                @if($discount)
                    <span style="font-size:13px;background:rgba(0,255,65,.15);color:var(--fp-green);padding:3px 10px;border-radius:4px;font-weight:700;border:1px solid rgba(0,255,65,.3);">{{ $discount }}% {{ __('OFF') }}</span>
                @endif
            </div>

            {{-- Campaign countdown --}}
            @if($campaign_product !== null && $campaign_product->status !== 'draft')
                <div class="mb-4">
                    <h6 style="color:var(--fp-green);">{{ $campaign_name }}</h6>
                    @if(isset($campaign_active) && $campaign_active)
                        <div class="global-timer"></div>
                    @endif
                </div>
            @endif

            {{-- Product options form --}}
            <div class="fp-product-form">
                @include(include_theme_path('shop.product_details.partials.product-options'))
            </div>

            {{-- Trust pills --}}
            <div class="d-flex gap-3 flex-wrap mt-4" style="font-size:12px;color:var(--fp-muted);">
                <span><i class="mdi mdi-shield-check-outline" style="color:var(--fp-green);"></i> {{ __('Secure Checkout') }}</span>
                <span><i class="mdi mdi-refresh" style="color:var(--fp-green);"></i> {{ __('Easy Returns') }}</span>
                <span><i class="mdi mdi-truck-fast-outline" style="color:var(--fp-green);"></i> {{ __('Fast Delivery') }}</span>
                <span><i class="mdi mdi-certificate-outline" style="color:var(--fp-green);"></i> {{ __('Authentic Products') }}</span>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div style="margin-top:56px;">
        <div class="fp-tab-nav">
            <button class="fp-tab-nav-btn active" data-target="fp-tab-desc">{{ __('Description') }}</button>
            <button class="fp-tab-nav-btn" data-target="fp-tab-reviews">{{ __('Reviews') }}</button>
            <button class="fp-tab-nav-btn" data-target="fp-tab-ship">{{ __('Ship & Return') }}</button>
        </div>

        <div id="fp-tab-desc" class="fp-tab-panel active">
            @include(include_theme_path('shop.product_details.partials.product-description'))
        </div>
        <div id="fp-tab-reviews" class="fp-tab-panel">
            @include(include_theme_path('shop.product_details.partials.product-reviews'))
        </div>
        <div id="fp-tab-ship" class="fp-tab-panel">
            @include(include_theme_path('shop.product_details.partials.product-ship_return'))
        </div>
    </div>

    {{-- Related Products --}}
    @if($related_products->isNotEmpty())
    <div style="margin-top:56px;">
        <div class="fp-sec-heading">
            <div class="fp-sec-title">{{ __('You Might Also Like') }}</div>
        </div>
        <div class="row g-3 mt-2">
            @foreach($related_products as $rp)
                @php
                    $rpdata    = theme_product_price($rp);
                    $rp_img    = get_attachment_image_by_id($rp->image_id ?? null, 'grid');
                    $rp_imgurl = $rp_img['img_url'] ?? null;
                @endphp
                <div class="col-6 col-md-3">
                    <div class="fp-card">
                        <div class="fp-card-img">
                            @if($rp_imgurl)
                                <a href="{{ theme_product_url($rp->slug) }}"><img src="{{ $rp_imgurl }}" alt="{{ $rp->name }}" loading="lazy"></a>
                            @else
                                <a href="{{ theme_product_url($rp->slug) }}" class="fp-img-ph">
                                    <i class="mdi mdi-dumbbell" style="font-size:36px;color:var(--fp-green);opacity:.3;"></i>
                                </a>
                            @endif
                        </div>
                        <div class="fp-card-body">
                            <div class="fp-card-title"><a href="{{ theme_product_url($rp->slug) }}">{{ \Illuminate\Support\Str::words($rp->name, 6) }}</a></div>
                            <div class="fp-card-footer">
                                <div><div class="fp-price">{{ amount_with_currency_symbol($rpdata['sale_price']) }}</div></div>
                                <a href="{{ theme_product_url($rp->slug) }}" class="fp-atc-btn" title="{{ __('View Product') }}"><i class="mdi mdi-eye-outline"></i></a>
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
<script>
function fpSwitchImg(el, url) {
    document.querySelectorAll('.fp-thumb').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    var longImg = document.querySelector('.long-img');
    var mainImg = document.getElementById('fp-main-product-img');
    if (longImg && url) { longImg.setAttribute('data-src', url); if (mainImg) mainImg.src = url; }
}

$(function () {
    // Tab switching
    $(document).on('click', '.fp-tab-nav-btn', function () {
        var target = $(this).data('target');
        $('.fp-tab-nav-btn').removeClass('active');
        $(this).addClass('active');
        $('.fp-tab-panel').removeClass('active');
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
            data: { _token: '{{ csrf_token() }}', product_id: '{{ $product->id }}', review_text: $('#review-text').val(), rating: $('.rating-selected').data('value') },
            beforeSend: function () { toastr.warning('{{ __("Submitting please wait.") }}'); btn.text('{{ __("Submitting..") }}'); },
            success: function (data) {
                if (data.type === 'success') { toastr.success(data.msg); setTimeout(() => location.reload(), 300); }
                else { toastr.error(data.msg); }
                btn.text('{{ __("Submit Review") }}');
            },
            error: function (data) { $.each(data.responseJSON?.errors ?? {}, (v, i) => toastr.error(i)); btn.text('{{ __("Submit Review") }}'); }
        });
    });

    // See more reviews
    $(document).on('click', '.see-more-review', function () {
        var el = $(this), items = el.attr('data-items');
        $.ajax({ url: '{{ theme_product_review_more_url() }}', type: 'GET', data: { product_id: '{{ $product->id }}', items: items },
            beforeSend: function () { el.text('{{ __("Loading..") }}'); },
            success: function (data) { $('.all-reviews').html(data.markup).hide().fadeIn(800); el.text('{{ __("See More") }}'); el.attr('data-items', Number(items)+5); },
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
