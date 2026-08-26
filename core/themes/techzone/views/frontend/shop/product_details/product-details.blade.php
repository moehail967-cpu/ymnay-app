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
    $image_array = [(int)$product->image_id];
    foreach ($product->gallery_images ?? [] as $gi) { $image_array[] = $gi->id; }
    $main_img = get_attachment_image_by_id($product->image_id ?? null, 'large');
    $main_img_url = $main_img['img_url'] ?? null;
@endphp

<div class="tz-page-banner">
    <div class="container tz-page-banner-content">
        <h1>{{ \Illuminate\Support\Str::words($product->name, 8) }}</h1>
        <div class="tz-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right sep"></i>
            <a href="{{ theme_shop_url() }}">{{ __('Shop') }}</a>
            <i class="mdi mdi-chevron-right sep"></i>
            <span class="current">{{ \Illuminate\Support\Str::words($product->name, 6) }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding-top:28px;padding-bottom:60px;">
    <div class="row g-5">

        {{-- Images --}}
        <div class="col-lg-5">
            <div style="background:var(--tz-card);border:1px solid var(--tz-border);border-radius:var(--tz-radius);overflow:hidden;aspect-ratio:1;display:flex;align-items:center;justify-content:center;padding:20px;" id="shop_details_gallery_slider">
                <div class="shop-details-thums long-img" data-src="{{ $main_img_url }}" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                    @if($main_img_url)
                        <img id="tz-main-product-img" src="{{ $main_img_url }}" alt="{{ $product->name }}" style="max-width:100%;max-height:100%;object-fit:contain;">
                    @else
                        <span style="font-size:80px;color:var(--tz-blue);">💻</span>
                    @endif
                </div>
            </div>
            <div style="display:flex;gap:8px;margin-top:12px;flex-wrap:wrap;">
                @foreach($image_array as $imgId)
                    @php $tdata = get_attachment_image_by_id($imgId, 'grid'); $turl = $tdata['img_url'] ?? null; @endphp
                    <div onclick="tzSwitchImg(this, '{{ $turl }}')"
                         style="width:72px;height:72px;border:2px solid {{ $loop->first ? 'var(--tz-blue)' : 'var(--tz-border)' }};border-radius:var(--tz-radius-sm);overflow:hidden;cursor:pointer;background:var(--tz-card);display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:border-color .2s;"
                         class="tz-thumb {{ $loop->first ? 'active' : '' }}">
                        @if($turl)<img src="{{ $turl }}" alt="" style="width:100%;height:100%;object-fit:contain;">@else<span style="font-size:22px;color:var(--tz-blue);">💻</span>@endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Info --}}
        <div class="col-lg-7">
            @if($product->badge)
                <span style="display:inline-block;background:var(--tz-orange);color:#fff;font-size:11px;font-weight:700;padding:4px 12px;border-radius:var(--tz-radius-sm);margin-bottom:10px;">{{ $product->badge->name }}</span>
            @endif

            <h1 style="font-size:clamp(20px,2.5vw,28px);font-weight:700;color:#fff;line-height:1.3;margin-bottom:12px;">{{ $product->name }}</h1>

            <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
                {!! theme_star_rating($product) !!}
                <span id="stock" data-stock-text='{!! $stock_count > 0 ? "<span class=\"text-success\">".__("In Stock")."</span>" : "<span class=\"text-danger\">".__("Out of Stock")."</span>" !!}'>
                    {!! $stock_count > 0 ? '<span class="text-success"><i class="mdi mdi-check-circle"></i> '.__('In Stock').'</span>' : '<span class="text-danger"><i class="mdi mdi-close-circle"></i> '.__('Out of Stock').'</span>' !!}
                </span>
            </div>

            <div class="mb-4 d-flex align-items-baseline gap-2 flex-wrap">
                <span style="font-size:28px;font-weight:800;color:var(--tz-blue);" id="price" data-main-price="{{ $sale_price }}" data-currency-symbol="{{ site_currency_symbol() }}">
                    {{ amount_with_currency_symbol($sale_price) }}
                </span>
                @if($deleted_price)
                    <span style="font-size:16px;color:var(--tz-muted);text-decoration:line-through;">{{ amount_with_currency_symbol($deleted_price) }}</span>
                @endif
                @if($discount)
                    <span style="background:var(--tz-green);color:#fff;font-size:12px;font-weight:700;padding:3px 10px;border-radius:var(--tz-radius-sm);">{{ $discount }}% {{ __('OFF') }}</span>
                @endif
            </div>

            @if($campaign_product !== null && $campaign_product->status !== 'draft')
                <div class="mb-4">
                    <h6 style="color:var(--tz-orange);">{{ $campaign_name }}</h6>
                    @if(isset($campaign_active) && $campaign_active)
                        <div class="global-timer"></div>
                    @endif
                </div>
            @endif

            <div style="border-top:1px solid var(--tz-border);padding-top:20px;">
                @include(include_theme_path('shop.product_details.partials.product-options'))
            </div>

            <div class="d-flex gap-4 flex-wrap mt-4" style="font-size:12px;color:var(--tz-muted);">
                <span><i class="mdi mdi-shield-check" style="color:var(--tz-green);"></i> {{ __('Secure Checkout') }}</span>
                <span><i class="mdi mdi-refresh" style="color:var(--tz-blue);"></i> {{ __('Easy Returns') }}</span>
                <span><i class="mdi mdi-truck-fast-outline" style="color:var(--tz-blue);"></i> {{ __('Fast Delivery') }}</span>
                <span><i class="mdi mdi-warranty" style="color:var(--tz-orange);"></i> {{ __('Warranty') }}</span>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div style="margin-top:56px;">
        <div class="tz-tab-nav" style="display:flex;gap:0;border-bottom:2px solid var(--tz-border);margin-bottom:24px;">
            @foreach([['tz-tab-desc', __('Description')], ['tz-tab-reviews', __('Reviews')], ['tz-tab-ship', __('Shipping & Returns')]] as $tab)
            <button class="tz-tab-btn {{ $loop->first ? 'active' : '' }}" data-target="{{ $tab[0] }}"
                    style="padding:12px 24px;background:transparent;border:0;border-bottom:2px solid {{ $loop->first ? 'var(--tz-blue)' : 'transparent' }};margin-bottom:-2px;font-size:13px;font-weight:600;color:{{ $loop->first ? 'var(--tz-blue)' : 'var(--tz-muted)' }};cursor:pointer;font-family:var(--tz-font);transition:all .2s;">
                {{ $tab[1] }}
            </button>
            @endforeach
        </div>

        <div id="tz-tab-desc" class="tz-tab-panel" style="display:block;">
            @include(include_theme_path('shop.product_details.partials.product-description'))
        </div>
        <div id="tz-tab-reviews" class="tz-tab-panel" style="display:none;">
            @include(include_theme_path('shop.product_details.partials.product-reviews'))
        </div>
        <div id="tz-tab-ship" class="tz-tab-panel" style="display:none;">
            @include(include_theme_path('shop.product_details.partials.product-ship_return'))
        </div>
    </div>

    {{-- Related Products --}}
    @if($related_products->isNotEmpty())
    <div style="margin-top:56px;">
        <div class="tz-section-heading">
            <h2>{{ __('Related Products') }}</h2>
        </div>
        <div class="row g-3">
            @foreach($related_products as $rp)
                @php $rpdata = theme_product_price($rp); $rp_img = get_attachment_image_by_id($rp->image_id ?? null, 'grid'); $rp_imgurl = $rp_img['img_url'] ?? null; @endphp
                <div class="col-6 col-md-3">
                    <div style="background:var(--tz-card);border:1px solid var(--tz-border);border-radius:var(--tz-radius);overflow:hidden;transition:border-color .2s;" onmouseover="this.style.borderColor='var(--tz-blue)'" onmouseout="this.style.borderColor='var(--tz-border)'">
                        <div style="aspect-ratio:1;background:var(--tz-surface);display:flex;align-items:center;justify-content:center;padding:16px;">
                            @if($rp_imgurl)<a href="{{ theme_product_url($rp->slug) }}"><img src="{{ $rp_imgurl }}" alt="{{ $rp->name }}" loading="lazy" style="max-width:100%;max-height:140px;object-fit:contain;"></a>
                            @else<a href="{{ theme_product_url($rp->slug) }}" style="font-size:48px;color:var(--tz-blue);">💻</a>@endif
                        </div>
                        <div style="padding:12px;">
                            <div style="font-size:13px;font-weight:600;color:var(--tz-text);margin-bottom:6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><a href="{{ theme_product_url($rp->slug) }}" style="color:inherit;">{{ \Illuminate\Support\Str::words($rp->name, 6) }}</a></div>
                            <div style="font-size:14px;font-weight:700;color:var(--tz-blue);">{{ amount_with_currency_symbol($rpdata['sale_price']) }}</div>
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
function tzSwitchImg(el, url) {
    document.querySelectorAll('.tz-thumb').forEach(t => { t.style.borderColor = 'var(--tz-border)'; t.classList.remove('active'); });
    el.style.borderColor = 'var(--tz-blue)'; el.classList.add('active');
    var mainImg = document.getElementById('tz-main-product-img');
    if (mainImg && url) mainImg.src = url;
    var longImg = document.querySelector('.long-img');
    if (longImg && url) longImg.setAttribute('data-src', url);
}

$(function () {
    $(document).on('click', '.tz-tab-btn', function () {
        var target = $(this).data('target');
        $('.tz-tab-btn').css({'color': 'var(--tz-muted)', 'border-bottom-color': 'transparent'}).removeClass('active');
        $(this).css({'color': 'var(--tz-blue)', 'border-bottom-color': 'var(--tz-blue)'}).addClass('active');
        $('.tz-tab-panel').hide();
        $('#' + target).show();
    });

    if (typeof StarRating !== 'undefined') {
        new StarRating('.star-rating', {
            maxStars: 5, clearable: false,
            classNames: { active: 'tz-active', base: 'tz-star-rating', selected: 'rating-selected' },
        });
    }

    @php if (!empty($campaign_product) && isset($campaign_active) && $campaign_active != 0) { $end_date = $campaign_product->campaign?->end_date; } @endphp
    if (typeof $.fn.syotimer !== 'undefined') {
        $('.global-timer').syotimer({ year: '{{ $end_date->year ?? 0 }}', month: '{{ $end_date->month ?? 0 }}', day: '{{ $end_date->day ?? 0 }}' });
    }

    $(document).on('click', '.plus', function () { var i = $(this).prev('.quantity-input'); if (i.val()) i[0].stepUp(1); });
    $(document).on('click', '.substract', function () { var i = $(this).next('.quantity-input'); if (i.val() > 1) i[0].stepDown(1); });

    $(document).on('click', '#review-submit-btn', function (e) {
        e.preventDefault(); var btn = $(this);
        $.ajax({
            url: '{{ theme_product_review_url() }}', type: 'POST',
            data: { _token: '{{ csrf_token() }}', product_id: '{{ $product->id }}', review_text: $('#review-text').val(), rating: $('.rating-selected').data('value') },
            beforeSend: function () { toastr.warning('{{ __("Submitting please wait.") }}'); btn.text('{{ __("Submitting..") }}'); },
            success: function (data) { if (data.type === 'success') { toastr.success(data.msg); setTimeout(() => location.reload(), 300); } else { toastr.error(data.msg); } btn.text('{{ __("Submit Review") }}'); },
            error: function (data) { $.each(data.responseJSON?.errors ?? {}, (v, i) => toastr.error(i)); btn.text('{{ __("Submit Review") }}'); }
        });
    });

    $(document).on('click', '.see-more-review', function () {
        var el = $(this), items = el.attr('data-items');
        $.ajax({ url: '{{ theme_product_review_more_url() }}', type: 'GET', data: { product_id: '{{ $product->id }}', items: items },
            beforeSend: function () { el.text('{{ __("Loading..") }}'); },
            success: function (data) { $('.all-reviews').html(data.markup).hide().fadeIn(800); el.text('{{ __("See More") }}'); el.attr('data-items', Number(items)+5); }
        });
    });
});
</script>
{!! theme_product_js($product_inventory_set, $additional_info_store, $campaign_product ?? null, $campaign_active ?? 0, $product) !!}
@endsection
