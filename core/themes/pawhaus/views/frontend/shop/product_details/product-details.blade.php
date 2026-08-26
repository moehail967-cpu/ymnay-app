@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $product->name !!} @endsection
@section('page-title') {!! $product->name !!} @endsection

@section('meta-data') {!! render_page_meta_data($product) !!} @endsection

@section('style')
<link rel="stylesheet" href="{{ global_asset('assets/common/css/star-rating.min.css') }}">
<style>
.ph-product-form .size-lists { display: flex; flex-wrap: wrap; gap: 8px; list-style: none; padding: 0; margin: 0; }
.ph-product-form .size-lists li { padding: 6px 16px; border: 2px solid var(--ph-border); border-radius: 50px; font-size: 13px; font-weight: 700; cursor: pointer; color: var(--ph-muted); transition: all .2s; }
.ph-product-form .size-lists li:hover, .ph-product-form .size-lists li.active { border-color: var(--ph-terra); color: var(--ph-terra); background: var(--ph-terra-light); }
.ph-product-form .size-lists li.disabled { opacity: .45; pointer-events: none; }
.ph-product-form .input-title { font-size: 13px; font-weight: 700; color: var(--ph-dark); display: block; margin-bottom: 8px; }
.ph-product-form .form--input { display: none; }
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
<div class="container" style="padding: 20px 0 0;">
    <div class="ph-breadcrumb">
        <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
        <span class="sep">/</span>
        <a href="{{ theme_shop_url() }}">{{ __('Shop') }}</a>
        <span class="sep">/</span>
        <span class="current">{{ $product->name }}</span>
    </div>
</div>

{{-- Product Section --}}
<div class="container" style="padding-top:28px; padding-bottom:60px;">
    <div class="row g-5">

        {{-- Images --}}
        <div class="col-lg-5">
            <div class="ph-thumb-grid" id="shop_details_gallery_slider">
                <div class="ph-thumb-list">
                    @foreach($image_array as $imgId)
                        @php $tdata = get_attachment_image_by_id($imgId, 'grid'); $turl = $tdata['img_url'] ?? null; @endphp
                        <div class="ph-thumb {{ $loop->first ? 'active' : '' }}" onclick="phSwitchImg(this, '{{ $turl }}')">
                            @if($turl)<img src="{{ $turl }}" alt="">@else<span style="font-size:28px;display:flex;align-items:center;justify-content:center;height:100%;">🐾</span>@endif
                        </div>
                    @endforeach
                </div>
                <div class="ph-main-img">
                    <div class="shop-details-thums long-img" data-src="{{ $main_img_url }}">
                        @if($main_img_url)
                            <img id="ph-main-product-img" src="{{ $main_img_url }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:cover;">
                        @else
                            <div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:80px;">🐾</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Info --}}
        <div class="col-lg-7">
            @if($product->badge)
                <span class="ph-card-badge" style="position:static;display:inline-block;margin-bottom:12px;">{{ $product->badge->name }}</span>
            @endif

            <h1 class="ph-product-title">{{ $product->name }}</h1>

            <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
                {!! theme_star_rating($product) !!}
                <span id="stock"
                      data-stock-text='{!! $stock_count > 0 ? "<span class=\"text-success\">".__("In Stock")."</span>" : "<span class=\"text-danger\">".__("Out of Stock")."</span>" !!}'>
                    {!! $stock_count > 0 ? '<span class="text-success"><i class="las la-check-circle"></i> '.__('In Stock').'</span>' : '<span class="text-danger"><i class="las la-times-circle"></i> '.__('Out of Stock').'</span>' !!}
                </span>
            </div>

            <div class="mb-4 d-flex align-items-baseline gap-2 flex-wrap">
                <span class="ph-product-price"
                      id="price"
                      data-main-price="{{ $sale_price }}"
                      data-currency-symbol="{{ site_currency_symbol() }}">
                    {{ amount_with_currency_symbol($sale_price) }}
                </span>
                @if($deleted_price)
                    <span class="ph-product-price-old">{{ amount_with_currency_symbol($deleted_price) }}</span>
                @endif
                @if($discount)
                    <span style="font-size:13px;background:var(--ph-sage-light);color:var(--ph-sage);padding:3px 10px;border-radius:50px;font-weight:700;">{{ $discount }}% {{ __('OFF') }}</span>
                @endif
            </div>

            @if($campaign_product !== null && $campaign_product->status !== 'draft')
                <div class="mb-4">
                    <h6>{{ $campaign_name }}</h6>
                    @if(isset($campaign_active) && $campaign_active)
                        <div class="global-timer"></div>
                    @endif
                </div>
            @endif

            <div class="ph-product-form">
                @include(include_theme_path('shop.product_details.partials.product-options'))
            </div>

            <div class="d-flex gap-3 flex-wrap mt-3" style="font-size:12px;color:var(--ph-muted);">
                <span><i class="las la-shield-alt" style="color:var(--ph-sage);"></i> {{ __('Secure Checkout') }}</span>
                <span><i class="las la-sync" style="color:var(--ph-terra);"></i> {{ __('Easy Returns') }}</span>
                <span><i class="las la-truck" style="color:var(--ph-terra);"></i> {{ __('Fast Delivery') }}</span>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div style="margin-top:56px;">
        <div class="ph-tab-nav">
            <button class="ph-tab-nav-btn active" data-target="ph-tab-desc">{{ __('Description') }}</button>
            <button class="ph-tab-nav-btn" data-target="ph-tab-reviews">{{ __('Reviews') }}</button>
            <button class="ph-tab-nav-btn" data-target="ph-tab-ship">{{ __('Ship & Return') }}</button>
        </div>

        <div id="ph-tab-desc" class="ph-tab-panel active">
            @include(include_theme_path('shop.product_details.partials.product-description'))
        </div>
        <div id="ph-tab-reviews" class="ph-tab-panel">
            @include(include_theme_path('shop.product_details.partials.product-reviews'))
        </div>
        <div id="ph-tab-ship" class="ph-tab-panel">
            @include(include_theme_path('shop.product_details.partials.product-ship_return'))
        </div>
    </div>

    {{-- Related Products --}}
    @if($related_products->isNotEmpty())
    <div style="margin-top:56px;">
        <div class="ph-sec-heading">
            <div class="ph-section-title">{{ __('You Might Also Like') }}</div>
        </div>
        <div class="row g-3 mt-2">
            @foreach($related_products as $rp)
                @php
                    $rpdata    = theme_product_price($rp);
                    $rp_img    = get_attachment_image_by_id($rp->image_id ?? null, 'grid');
                    $rp_imgurl = $rp_img['img_url'] ?? null;
                @endphp
                <div class="col-6 col-md-3">
                    <div class="ph-card">
                        <div class="ph-card-img">
                            @if($rp_imgurl)
                                <a href="{{ theme_product_url($rp->slug) }}"><img src="{{ $rp_imgurl }}" alt="{{ $rp->name }}" loading="lazy"></a>
                            @else
                                <a href="{{ theme_product_url($rp->slug) }}" style="font-size:48px;">🐾</a>
                            @endif
                        </div>
                        <div class="ph-card-body">
                            <div class="ph-card-name"><a href="{{ theme_product_url($rp->slug) }}">{{ \Illuminate\Support\Str::words($rp->name, 6) }}</a></div>
                            <div class="ph-card-price">
                                <span class="ph-price-sale">{{ amount_with_currency_symbol($rpdata['sale_price']) }}</span>
                            </div>
                            <a href="{{ theme_product_url($rp->slug) }}" class="ph-card-atc"><i class="las la-eye"></i> {{ __('View') }}</a>
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
function phSwitchImg(el, url) {
    document.querySelectorAll('.ph-thumb').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    var mainImg = document.getElementById('ph-main-product-img');
    if (mainImg && url) { mainImg.src = url; }
    var longImg = document.querySelector('.long-img');
    if (longImg && url) { longImg.setAttribute('data-src', url); if (mainImg) mainImg.src = url; }
}

$(function () {
    $(document).on('click', '.ph-tab-nav-btn', function () {
        var target = $(this).data('target');
        $('.ph-tab-nav-btn').removeClass('active');
        $(this).addClass('active');
        $('.ph-tab-panel').removeClass('active');
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
