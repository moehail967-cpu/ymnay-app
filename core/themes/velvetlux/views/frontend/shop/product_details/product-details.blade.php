@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $product->name !!} @endsection
@section('page-title') {!! $product->name !!} @endsection

@section('meta-data') {!! render_page_meta_data($product) !!} @endsection

@section('style')
<link rel="stylesheet" href="{{ global_asset('assets/common/css/star-rating.min.css') }}">
<style>
.vl-tab-nav { display: flex; border-bottom: 1px solid var(--vl-border); margin-bottom: 24px; overflow-x: auto; scrollbar-width: none; }
.vl-tab-nav::-webkit-scrollbar { display: none; }
.vl-tab-nav-btn { padding: 14px 24px; font-size: 10px; font-weight: 600; color: var(--vl-muted); background: none; border: none; border-bottom: 2px solid transparent; margin-bottom: -1px; cursor: pointer; font-family: inherit; transition: all .2s; text-transform: uppercase; letter-spacing: 2px; }
.vl-tab-nav-btn.active { color: var(--vl-plum); border-bottom-color: var(--vl-plum); }
.vl-tab-panel { display: none; }
.vl-tab-panel.active { display: block; }
.vl-product-form .size-lists { display: flex; flex-wrap: wrap; gap: 8px; list-style: none; padding: 0; margin: 0; }
.vl-product-form .size-lists li { padding: 6px 16px; border: 1px solid var(--vl-border); font-size: 12px; font-weight: 600; cursor: pointer; color: var(--vl-muted); transition: all .2s; }
.vl-product-form .size-lists li:hover, .vl-product-form .size-lists li.active { border-color: var(--vl-champagne); color: var(--vl-champagne); background: rgba(212,184,150,.08); }
.vl-product-form .size-lists li.disabled { opacity: .4; pointer-events: none; }
.vl-product-form .input-title { font-size: 10px; font-weight: 700; color: var(--vl-champagne); display: block; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1.5px; }
.vl-product-form .form--input { display: none; }
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
<div class="vl-page-header">
    <div class="container">
        <div class="vl-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span>/</span>
            <a href="{{ theme_shop_url() }}">{{ __('Shop') }}</a>
            <span>/</span>
            <span>{{ $product->name }}</span>
        </div>
    </div>
</div>

{{-- Product Section --}}
<div class="container" style="padding-top:40px; padding-bottom:72px;">
    <div class="row g-5">

        {{-- Images --}}
        <div class="col-lg-5">
            <div class="vl-pd-main-img" id="shop_details_gallery_slider"
                 style="height:480px;overflow:hidden;position:relative;background:var(--vl-surface);display:flex;align-items:center;justify-content:center;">
                <div class="shop-details-thums long-img" data-src="{{ $main_img_url }}"
                     style="width:100%;height:100%;">
                    @if($main_img_url)
                        <img id="vl-main-product-img" src="{{ $main_img_url }}" alt="{{ $product->name }}"
                             style="width:100%;height:100%;object-fit:cover;">
                    @else
                        <div style="font-size:80px;color:var(--vl-champagne);">◆</div>
                    @endif
                </div>
            </div>
            <div class="vl-thumb-row mt-3">
                @foreach($image_array as $imgId)
                    @php $tdata = get_attachment_image_by_id($imgId, 'grid'); $turl = $tdata['img_url'] ?? null; @endphp
                    <div class="vl-thumb {{ $loop->first ? 'active' : '' }}"
                         onclick="vlSwitchImg(this, '{{ $turl }}')">
                        @if($turl)<img src="{{ $turl }}" alt="" style="width:100%;height:100%;object-fit:cover;">@else<span style="font-size:24px;color:var(--vl-champagne);">◆</span>@endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Info --}}
        <div class="col-lg-7">
            @if($product->badge)
                <span style="display:inline-block;margin-bottom:12px;font-size:9px;letter-spacing:2px;text-transform:uppercase;background:var(--vl-plum);color:var(--vl-champagne);padding:4px 12px;">{{ $product->badge->name }}</span>
            @endif

            <h1 class="vl-pd-title">{{ $product->name }}</h1>

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
            <div class="vl-pd-price mb-4">
                <span id="price" data-main-price="{{ $sale_price }}" data-currency-symbol="{{ site_currency_symbol() }}">
                    {{ amount_with_currency_symbol($sale_price) }}
                </span>
                @if($deleted_price)
                    <span class="vl-pd-orig">{{ amount_with_currency_symbol($deleted_price) }}</span>
                @endif
                @if($discount)
                    <span style="font-size:11px;background:rgba(212,184,150,.1);color:var(--vl-champagne);padding:3px 10px;letter-spacing:1px;font-weight:600;font-family:'Inter',sans-serif;">
                        {{ $discount }}% {{ __('OFF') }}
                    </span>
                @endif
            </div>

            {{-- Campaign countdown --}}
            @if($campaign_product !== null && $campaign_product->status !== 'draft')
                <div class="mb-4">
                    <h6 style="color:var(--vl-champagne);font-size:11px;letter-spacing:2px;text-transform:uppercase;font-family:'Inter',sans-serif;">{{ $campaign_name }}</h6>
                    @if(isset($campaign_active) && $campaign_active)
                        <div class="global-timer"></div>
                    @endif
                </div>
            @endif

            {{-- Product options form --}}
            <div class="vl-product-form">
                @include(include_theme_path('shop.product_details.partials.product-options'))
            </div>

            {{-- Trust badges --}}
            <div class="d-flex gap-4 flex-wrap mt-3" style="font-size:11px;color:var(--vl-muted);letter-spacing:.5px;font-family:'Inter',sans-serif;">
                <span><i class="las la-shield-alt" style="color:var(--vl-champagne);"></i> {{ __('Secure Checkout') }}</span>
                <span><i class="las la-sync" style="color:var(--vl-champagne);"></i> {{ __('Easy Returns') }}</span>
                <span><i class="las la-certificate" style="color:var(--vl-champagne);"></i> {{ __('Authenticity Guaranteed') }}</span>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div style="margin-top:56px;">
        <div class="vl-tab-nav">
            <button class="vl-tab-nav-btn active" data-target="vl-tab-desc">{{ __('Description') }}</button>
            <button class="vl-tab-nav-btn" data-target="vl-tab-reviews">{{ __('Reviews') }}</button>
            <button class="vl-tab-nav-btn" data-target="vl-tab-ship">{{ __('Ship & Return') }}</button>
        </div>

        <div id="vl-tab-desc" class="vl-tab-panel active" style="color:var(--vl-muted);font-size:14px;line-height:1.8;">
            @include(include_theme_path('shop.product_details.partials.product-description'))
        </div>
        <div id="vl-tab-reviews" class="vl-tab-panel">
            @include(include_theme_path('shop.product_details.partials.product-reviews'))
        </div>
        <div id="vl-tab-ship" class="vl-tab-panel">
            @include(include_theme_path('shop.product_details.partials.product-ship_return'))
        </div>
    </div>

    {{-- Related Products --}}
    @if($related_products->isNotEmpty())
    <div style="margin-top:56px;">
        <div style="font-size:10px;font-weight:400;letter-spacing:5px;text-transform:uppercase;color:var(--vl-champagne);margin-bottom:12px;font-family:'Inter',sans-serif;">{{ __('You May Also Like') }}</div>
        <div class="row g-3 mt-2">
            @foreach($related_products as $rp)
                @php
                    $rpdata    = theme_product_price($rp);
                    $rp_img    = get_attachment_image_by_id($rp->image_id ?? null, 'grid');
                    $rp_imgurl = $rp_img['img_url'] ?? null;
                @endphp
                <div class="col-6 col-md-3">
                    <div class="vl-card">
                        <div class="vl-card-img">
                            @if($rp_imgurl)
                                <img src="{{ $rp_imgurl }}" alt="{{ $rp->name }}" loading="lazy">
                            @endif
                            <div class="vl-card-overlay">
                                <a href="{{ theme_product_url($rp->slug) }}" class="vl-card-act-btn">{{ __('View') }}</a>
                            </div>
                        </div>
                        <div style="padding:12px;">
                            <div style="font-size:13px;color:var(--vl-ivory);">
                                <a href="{{ theme_product_url($rp->slug) }}" style="color:inherit;text-decoration:none;">
                                    {{ \Illuminate\Support\Str::words($rp->name, 6) }}
                                </a>
                            </div>
                            <div style="font-size:14px;color:var(--vl-champagne);margin-top:4px;">{{ amount_with_currency_symbol($rpdata['sale_price']) }}</div>
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
function vlSwitchImg(el, url) {
    document.querySelectorAll('.vl-thumb').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    var mainImg = document.getElementById('vl-main-product-img');
    if (mainImg && url) { mainImg.src = url; }
    var longImg = document.querySelector('.long-img');
    if (longImg && url) { longImg.setAttribute('data-src', url); if (mainImg) mainImg.src = url; }
}

$(function () {
    $(document).on('click', '.vl-tab-nav-btn', function () {
        var target = $(this).data('target');
        $('.vl-tab-nav-btn').removeClass('active');
        $(this).addClass('active');
        $('.vl-tab-panel').removeClass('active');
        $('#' + target).addClass('active');
    });

    if (typeof StarRating !== 'undefined') {
        new StarRating('.star-rating', {
            maxStars: 5, clearable: false,
            stars: function (el) {
                el.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><rect class="gl-star-full" width="19" height="19" x="2.5" y="2.5"/><polygon fill="#FFF" points="12 5.375 13.646 10.417 19 10.417 14.665 13.556 16.313 18.625 11.995 15.476 7.688 18.583 9.333 13.542 5 10.417 10.354 10.417"/></svg>';
            },
            classNames: { active: 'gl-active', base: 'gl-star-rating', selected: 'rating-selected' },
        });
    }

    @php if (!empty($campaign_product) && isset($campaign_active) && $campaign_active != 0) { $end_date = $campaign_product->campaign?->end_date; } @endphp
    if (typeof $.fn.syotimer !== 'undefined') {
        $('.global-timer').syotimer({ year: '{{ $end_date->year ?? 0 }}', month: '{{ $end_date->month ?? 0 }}', day: '{{ $end_date->day ?? 0 }}' });
    }

    $(document).on('click', '.plus', function () { var i = $(this).prev('.quantity-input'); if (i.val()) i[0].stepUp(1); });
    $(document).on('click', '.substract', function () { var i = $(this).next('.quantity-input'); if (i.val() > 1) i[0].stepDown(1); });

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

<script>
    function doCartAjax(e, pid) {
        e.preventDefault();
        if (!validateSelectedAttributes()) { toastr.error('{{ __("Please select all required options") }}'); return; }
        var qty = parseInt($('.quantity-input').val()) || 1;
        var variantId = getAttributesForCart();
        $.ajax({
            url: '{{ theme_add_to_cart_url() }}', type: 'POST',
            data: { _token: '{{ csrf_token() }}', id: pid, quantity: qty, variant_id: variantId },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                $('.loader').hide();
                if (data.type === 'success') { toastr.success(data.msg); if (data.cart_count !== undefined) { $('.cart-count').text(data.cart_count); } }
                else { toastr.error(data.msg ?? '{{ __("Something went wrong") }}'); }
            },
            error: function () { $('.loader').hide(); toastr.error('{{ __("Something went wrong") }}'); }
        });
    }
</script>
{!! theme_product_js(
    $product_inventory_set,
    $additional_info_store,
    $campaign_product ?? null,
    $campaign_active ?? 0,
    $product
) !!}
@endsection
