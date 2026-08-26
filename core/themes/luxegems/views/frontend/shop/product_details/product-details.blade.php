@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $product->name !!} @endsection

@section('meta-data') {!! render_page_meta_data($product) !!} @endsection

@section('style')
<link rel="stylesheet" href="{{ global_asset('assets/common/css/star-rating.min.css') }}">
<style>
.lg-tab-nav { display: flex; border-bottom: 1px solid var(--lx-border); margin-bottom: 24px; overflow-x: auto; scrollbar-width: none; }
.lg-tab-nav::-webkit-scrollbar { display: none; }
.lg-tab-nav-btn { padding: 12px 24px; font-size: 10px; font-weight: 600; color: var(--lx-muted); background: none; border: none; border-bottom: 2px solid transparent; margin-bottom: -1px; cursor: pointer; font-family: inherit; transition: all .2s; text-transform: uppercase; letter-spacing: 2px; }
.lg-tab-nav-btn.active { color: var(--lx-gold); border-bottom-color: var(--lx-gold); }
.lg-tab-panel { display: none; }
.lg-tab-panel.active { display: block; }
.lg-product-form .size-lists { display: flex; flex-wrap: wrap; gap: 8px; list-style: none; padding: 0; margin: 0; }
.lg-product-form .size-lists li { padding: 6px 16px; border: 1px solid var(--lx-border); font-size: 12px; font-weight: 600; cursor: pointer; color: var(--lx-muted); transition: all .2s; }
.lg-product-form .size-lists li:hover, .lg-product-form .size-lists li.active { border-color: var(--lx-gold); color: var(--lx-gold); background: rgba(201,168,76,.08); }
.lg-product-form .size-lists li.disabled { opacity: .4; pointer-events: none; }
.lg-product-form .input-title { font-size: 10px; font-weight: 700; color: var(--lx-gold); display: block; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1.5px; }
.lg-product-form .form--input { display: none; }
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
<div class="lg-breadcrumb-bar">
    <div class="container">
        <div class="lg-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <a href="{{ theme_shop_url() }}">{{ __('Shop') }}</a>
            <span class="sep">/</span>
            <span class="active">{{ $product->name }}</span>
        </div>
    </div>
</div>

{{-- Product Section --}}
<div class="container" style="padding-top:40px; padding-bottom:72px;">
    <div class="row g-5">

        {{-- Images --}}
        <div class="col-lg-5">
            <div class="lg-prod-main-img" id="shop_details_gallery_slider"
                 style="height:420px;overflow:hidden;position:relative;">
                <div class="shop-details-thums long-img" data-src="{{ $main_img_url }}"
                     style="width:100%;height:100%;">
                    @if($main_img_url)
                        <img id="lg-main-product-img" src="{{ $main_img_url }}" alt="{{ $product->name }}"
                             style="width:100%;height:100%;object-fit:cover;">
                    @else
                        <div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:80px;">💎</div>
                    @endif
                </div>
            </div>
            <div class="lg-thumb-row">
                @foreach($image_array as $imgId)
                    @php $tdata = get_attachment_image_by_id($imgId, 'grid'); $turl = $tdata['img_url'] ?? null; @endphp
                    <div class="lg-thumb {{ $loop->first ? 'active' : '' }}"
                         onclick="lgSwitchImg(this, '{{ $turl }}')">
                        @if($turl)<img src="{{ $turl }}" alt="" style="width:100%;height:100%;object-fit:cover;">@else<span style="font-size:24px;">💎</span>@endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Info --}}
        <div class="col-lg-7">
            @if($product->badge)
                <span class="lx-card-badge" style="position:static;display:inline-block;margin-bottom:12px;">{{ $product->badge->name }}</span>
            @endif

            <h1 class="lg-prod-title">{{ $product->name }}</h1>

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
            <div class="lg-prod-price mb-4">
                <span id="price" data-main-price="{{ $sale_price }}" data-currency-symbol="{{ site_currency_symbol() }}">
                    {{ amount_with_currency_symbol($sale_price) }}
                </span>
                @if($deleted_price)
                    <span class="lx-price-old" style="font-size:18px;text-decoration:line-through;color:var(--lx-muted);">
                        {{ amount_with_currency_symbol($deleted_price) }}
                    </span>
                @endif
                @if($discount)
                    <span style="font-size:11px;background:rgba(201,168,76,.15);color:var(--lx-gold);padding:3px 10px;letter-spacing:1px;font-weight:600;">
                        {{ $discount }}% {{ __('OFF') }}
                    </span>
                @endif
            </div>

            {{-- Campaign countdown --}}
            @if($campaign_product !== null && $campaign_product->status !== 'draft')
                <div class="mb-4">
                    <h6 style="color:var(--lx-gold);font-size:12px;letter-spacing:1px;text-transform:uppercase;">{{ $campaign_name }}</h6>
                    @if(isset($campaign_active) && $campaign_active)
                        <div class="global-timer"></div>
                    @endif
                </div>
            @endif

            {{-- Product options form --}}
            <div class="lg-product-form">
                @include(include_theme_path('shop.product_details.partials.product-options'))
            </div>

            {{-- Trust badges --}}
            <div class="d-flex gap-4 flex-wrap mt-3" style="font-size:11px;color:var(--lx-muted);letter-spacing:.5px;">
                <span><i class="las la-shield-alt" style="color:var(--lx-gold);"></i> {{ __('Secure Checkout') }}</span>
                <span><i class="las la-sync" style="color:var(--lx-gold);"></i> {{ __('Easy Returns') }}</span>
                <span><i class="las la-certificate" style="color:var(--lx-gold);"></i> {{ __('Authenticity Guaranteed') }}</span>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div style="margin-top:56px;">
        <div class="lg-tab-nav">
            <button class="lg-tab-nav-btn active" data-target="lg-tab-desc">{{ __('Description') }}</button>
            <button class="lg-tab-nav-btn" data-target="lg-tab-reviews">{{ __('Reviews') }}</button>
            <button class="lg-tab-nav-btn" data-target="lg-tab-ship">{{ __('Ship & Return') }}</button>
        </div>

        <div id="lg-tab-desc" class="lg-tab-panel active" style="color:var(--lx-muted);font-size:14px;line-height:1.8;">
            @include(include_theme_path('shop.product_details.partials.product-description'))
        </div>
        <div id="lg-tab-reviews" class="lg-tab-panel">
            @include(include_theme_path('shop.product_details.partials.product-reviews'))
        </div>
        <div id="lg-tab-ship" class="lg-tab-panel">
            @include(include_theme_path('shop.product_details.partials.product-ship_return'))
        </div>
    </div>

    {{-- Related Products --}}
    @if($related_products->isNotEmpty())
    <div style="margin-top:56px;">
        <div class="lx-hero-eyebrow">{{ __('You May Also Like') }}</div>
        <div class="row g-3 mt-2">
            @foreach($related_products as $rp)
                @php
                    $rpdata    = theme_product_price($rp);
                    $rp_img    = get_attachment_image_by_id($rp->image_id ?? null, 'grid');
                    $rp_imgurl = $rp_img['img_url'] ?? null;
                @endphp
                <div class="col-6 col-md-3">
                    <div class="lx-card">
                        <div class="lx-card-img">
                            @if($rp_imgurl)
                                <img src="{{ $rp_imgurl }}" alt="{{ $rp->name }}" loading="lazy">
                            @endif
                            <div class="lx-card-actions">
                                <a href="{{ theme_product_url($rp->slug) }}" class="lx-card-act-btn">
                                    <i class="las la-eye"></i> {{ __('View') }}
                                </a>
                            </div>
                        </div>
                        <div class="lx-card-body">
                            <div class="lx-card-name">
                                <a href="{{ theme_product_url($rp->slug) }}" style="color:inherit;text-decoration:none;">
                                    {{ \Illuminate\Support\Str::words($rp->name, 6) }}
                                </a>
                            </div>
                            <div class="lx-card-price-row">
                                <span class="lx-price">{{ amount_with_currency_symbol($rpdata['sale_price']) }}</span>
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
function lgSwitchImg(el, url) {
    document.querySelectorAll('.lg-thumb').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    var mainImg = document.getElementById('lg-main-product-img');
    if (mainImg && url) { mainImg.src = url; }
    var longImg = document.querySelector('.long-img');
    if (longImg && url) { longImg.setAttribute('data-src', url); if (mainImg) mainImg.src = url; }
}

$(function () {
    $(document).on('click', '.lg-tab-nav-btn', function () {
        var target = $(this).data('target');
        $('.lg-tab-nav-btn').removeClass('active');
        $(this).addClass('active');
        $('.lg-tab-panel').removeClass('active');
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
{!! theme_product_js(
    $product_inventory_set,
    $additional_info_store,
    $campaign_product ?? null,
    $campaign_active ?? 0,
    $product
) !!}
@endsection
