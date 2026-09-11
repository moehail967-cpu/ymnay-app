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
    $main_img = get_attachment_image_by_id($product->image_id ?? null, 'large');
    $main_img_url = $main_img['img_url'] ?? null;
@endphp

{{-- Breadcrumb --}}
<div class="sz-page-banner">
    <div class="container">
        <h1>{{ \Illuminate\Support\Str::words($product->name, 6) }}</h1>
        <div class="sz-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep"><i class="mdi mdi-chevron-right"></i></span>
            <a href="{{ theme_shop_url() }}">{{ __('Shop') }}</a>
            <span class="sep"><i class="mdi mdi-chevron-right"></i></span>
            <span class="current">{{ \Illuminate\Support\Str::words($product->name, 4) }}</span>
        </div>
    </div>
</div>

{{-- Product Section --}}
<div class="container" style="padding-top:36px;padding-bottom:64px;">
    <div class="row g-5">

        {{-- Images --}}
        <div class="col-lg-5">
            <div id="shop_details_gallery_slider" style="background:var(--sz-white);border:2px solid var(--sz-border);border-radius:var(--sz-radius-xl);overflow:hidden;position:relative;">
                <div class="shop-details-thums long-img" data-src="{{ $main_img_url }}" style="height:400px;display:flex;align-items:center;justify-content:center;padding:20px;">
                    @if($main_img_url)
                        <img id="sz-main-product-img" src="{{ $main_img_url }}" alt="{{ $product->name }}" style="max-height:100%;max-width:100%;object-fit:contain;">
                    @else
                        <i class="mdi mdi-dumbbell" style="font-size:96px;color:var(--sz-red);opacity:.25;"></i>
                    @endif
                </div>
            </div>
            <div style="display:flex;gap:8px;margin-top:10px;flex-wrap:wrap;">
                @foreach($image_array as $imgId)
                    @php $tdata = get_attachment_image_by_id($imgId, 'grid'); $turl = $tdata['img_url'] ?? null; @endphp
                    <div onclick="szSwitchImg(this, '{{ $turl }}')"
                         style="width:72px;height:72px;border:2px solid {{ $loop->first ? 'var(--sz-red)' : 'var(--sz-border)' }};border-radius:var(--sz-radius);overflow:hidden;cursor:pointer;display:flex;align-items:center;justify-content:center;background:var(--sz-white);transition:border-color .2s;"
                         class="sz-product-thumb {{ $loop->first ? 'active' : '' }}">
                        @if($turl)<img src="{{ $turl }}" alt="" style="width:100%;height:100%;object-fit:cover;">@else<i class="mdi mdi-dumbbell" style="font-size:24px;color:var(--sz-red);opacity:.3;"></i>@endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Info --}}
        <div class="col-lg-7">
            @if($product->badge)
                <span class="sz-card-badge" style="position:static;display:inline-block;margin-bottom:12px;font-size:12px;padding:5px 14px;background:var(--sz-navy);">{{ $product->badge->name }}</span>
            @endif

            <h1 style="font-family:var(--sz-font-head);font-size:28px;font-weight:700;color:var(--sz-dark);text-transform:uppercase;letter-spacing:1px;margin-bottom:12px;">{{ $product->name }}</h1>

            <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
                {!! theme_star_rating($product) !!}
                <span id="stock" data-stock-text='{!! $stock_count > 0 ? "<span class=\"text-success\">".__("In Stock")."</span>" : "<span class=\"text-danger\">".__("Out of Stock")."</span>" !!}'>
                    {!! $stock_count > 0 ? '<span class="text-success"><i class="mdi mdi-check-circle"></i> '.__('In Stock').'</span>' : '<span class="text-danger"><i class="mdi mdi-close-circle"></i> '.__('Out of Stock').'</span>' !!}
                </span>
            </div>

            <div class="mb-4 d-flex align-items-baseline gap-3 flex-wrap">
                <span class="sz-price-sale" id="price" style="font-size:30px;" data-main-price="{{ $sale_price }}" data-currency-symbol="{{ site_currency_symbol() }}">
                    {{ amount_with_currency_symbol($sale_price) }}
                </span>
                @if($deleted_price)
                    <span style="font-size:16px;color:var(--sz-muted);text-decoration:line-through;">{{ amount_with_currency_symbol($deleted_price) }}</span>
                @endif
                @if($discount)
                    <span class="sz-card-badge" style="position:static;display:inline-block;font-size:12px;padding:4px 12px;background:var(--sz-red);">{{ $discount }}% {{ __('OFF') }}</span>
                @endif
            </div>

            @if($campaign_product !== null && $campaign_product->status !== 'draft')
                <div class="mb-4">
                    <div style="font-family:var(--sz-font-head);color:var(--sz-red);font-weight:700;margin-bottom:6px;text-transform:uppercase;letter-spacing:1px;">{{ $campaign_name }}</div>
                    @if(isset($campaign_active) && $campaign_active)
                        <div class="global-timer"></div>
                    @endif
                </div>
            @endif

            <div style="border-top:2px solid var(--sz-border);padding-top:20px;">
                @include(include_theme_path('shop.product_details.partials.product-options'))
            </div>

            <div class="d-flex gap-4 flex-wrap mt-4 pt-3" style="border-top:1px solid var(--sz-border);font-size:12px;font-family:var(--sz-font-head);text-transform:uppercase;letter-spacing:1px;color:var(--sz-muted);">
                <span><i class="mdi mdi-shield-check" style="color:var(--sz-red);"></i> {{ __('Secure Checkout') }}</span>
                <span><i class="mdi mdi-refresh" style="color:var(--sz-red);"></i> {{ __('Easy Returns') }}</span>
                <span><i class="mdi mdi-truck-fast" style="color:var(--sz-red);"></i> {{ __('Fast Delivery') }}</span>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div style="margin-top:56px;">
        <div class="sz-tab-nav" style="display:flex;gap:0;border-bottom:3px solid var(--sz-border);margin-bottom:0;">
            @foreach([[__('Description'),'sz-tab-desc'],[__('Reviews'),'sz-tab-reviews'],[__('Shipping'),'sz-tab-ship']] as $i => [$label,$id])
            <button class="sz-tab-btn {{ $i===0?'active':'' }}" data-target="{{ $id }}"
                    style="font-family:var(--sz-font-head);font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:2px;padding:12px 24px;border:none;background:transparent;cursor:pointer;color:{{ $i===0?'var(--sz-red)':'var(--sz-muted)' }};border-bottom:3px solid {{ $i===0?'var(--sz-red)':'transparent' }};margin-bottom:-3px;transition:all .2s;">
                {{ $label }}
            </button>
            @endforeach
        </div>

        <div id="sz-tab-desc" class="sz-tab-panel" style="background:var(--sz-white);border:2px solid var(--sz-border);border-top:none;padding:28px;border-radius:0 0 var(--sz-radius-xl) var(--sz-radius-xl);">
            @include(include_theme_path('shop.product_details.partials.product-description'))
        </div>
        <div id="sz-tab-reviews" class="sz-tab-panel" style="display:none;background:var(--sz-white);border:2px solid var(--sz-border);border-top:none;padding:28px;border-radius:0 0 var(--sz-radius-xl) var(--sz-radius-xl);">
            @include(include_theme_path('shop.product_details.partials.product-reviews'))
        </div>
        <div id="sz-tab-ship" class="sz-tab-panel" style="display:none;background:var(--sz-white);border:2px solid var(--sz-border);border-top:none;padding:28px;border-radius:0 0 var(--sz-radius-xl) var(--sz-radius-xl);">
            @include(include_theme_path('shop.product_details.partials.product-ship_return'))
        </div>
    </div>

    {{-- Related Products --}}
    @if($related_products->isNotEmpty())
    <div style="margin-top:56px;">
        <div class="sz-sec-heading">
            <span class="sz-sec-title">{{ __('Related Products') }}</span>
        </div>
        <div class="row g-3">
            @foreach($related_products as $rp)
                @php
                    $rpdata    = theme_product_price($rp);
                    $rp_img    = get_attachment_image_by_id($rp->image_id ?? null, 'grid');
                    $rp_imgurl = $rp_img['img_url'] ?? null;
                @endphp
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="sz-card">
                        <div class="sz-card-img">
                            @if($rp_imgurl)
                                <a href="{{ theme_product_url($rp->slug) }}"><img src="{{ $rp_imgurl }}" alt="{{ $rp->name }}" loading="lazy"></a>
                            @else
                                <a href="{{ theme_product_url($rp->slug) }}" class="sz-img-ph"><i class="mdi mdi-dumbbell" style="font-size:40px;color:var(--sz-red);opacity:.3;"></i></a>
                            @endif
                        </div>
                        <div class="sz-card-body">
                            <div class="sz-card-name"><a href="{{ theme_product_url($rp->slug) }}">{{ \Illuminate\Support\Str::words($rp->name, 5) }}</a></div>
                            <div class="sz-card-price"><span class="sz-price-sale">{{ amount_with_currency_symbol($rpdata['sale_price']) }}</span></div>
                            <a href="{{ theme_product_url($rp->slug) }}" class="sz-btn sz-btn-outline sz-btn-sm w-100" style="justify-content:center;">
                                <i class="mdi mdi-eye"></i> {{ __('View') }}
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
function szSwitchImg(el, url) {
    document.querySelectorAll('.sz-product-thumb').forEach(t => { t.classList.remove('active'); t.style.borderColor='var(--sz-border)'; });
    el.classList.add('active'); el.style.borderColor='var(--sz-red)';
    var mainImg = document.getElementById('sz-main-product-img');
    if (mainImg && url) { mainImg.src = url; }
    var longImg = document.querySelector('.long-img');
    if (longImg && url) { longImg.setAttribute('data-src', url); }
}

$(function () {
    $(document).on('click', '.sz-tab-btn', function () {
        var target = $(this).data('target');
        $('.sz-tab-btn').css({'color':'var(--sz-muted)','border-bottom-color':'transparent'}).removeClass('active');
        $(this).css({'color':'var(--sz-red)','border-bottom-color':'var(--sz-red)'}).addClass('active');
        $('.sz-tab-panel').hide();
        $('#' + target).show();
    });

    if (typeof StarRating !== 'undefined') {
        new StarRating('.star-rating', { maxStars: 5, clearable: false, classNames: { active: 'sz-active', base: 'sz-star-rating', selected: 'rating-selected' } });
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
{!! theme_product_js($product_inventory_set, $additional_info_store, $campaign_product ?? null, $campaign_active ?? 0, $product) !!}
@endsection
