@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $product->name !!} @endsection
@section('page-title') {!! $product->name !!} @endsection
@section('meta-data') {!! render_page_meta_data($product) !!} @endsection

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

<div style="background:var(--tr-bark);padding:24px 0;border-bottom:2px solid var(--tr-olive);">
    <div class="container">
        <h1 style="font-size:24px;font-weight:900;color:#fff;margin-bottom:6px;">{{ \Illuminate\Support\Str::words($product->name, 6) }}</h1>
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:rgba(255,255,255,.6);">
            <a href="{{ theme_home_url() }}" style="color:var(--tr-sand);font-weight:600;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right"></i>
            <a href="{{ theme_shop_url() }}" style="color:var(--tr-sand);font-weight:600;">{{ __('Shop') }}</a>
            <i class="mdi mdi-chevron-right"></i>
            <span>{{ \Illuminate\Support\Str::words($product->name, 4) }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding-top:36px;padding-bottom:64px;">
    <div class="row g-5">

        <div class="col-lg-5">
            <div id="shop_details_gallery_slider" style="background:#fff;border:1px solid var(--tr-border);border-radius:var(--tr-radius);overflow:hidden;position:relative;">
                <div class="shop-details-thums long-img" data-src="{{ $main_img_url }}" style="height:400px;display:flex;align-items:center;justify-content:center;padding:20px;">
                    @if($main_img_url)
                        <img id="tc-main-product-img" src="{{ $main_img_url }}" alt="{{ $product->name }}" style="max-height:100%;max-width:100%;object-fit:contain;">
                    @else
                        <i class="mdi mdi-hiking" style="font-size:96px;color:var(--tr-olive);opacity:.2;"></i>
                    @endif
                </div>
            </div>
            <div style="display:flex;gap:8px;margin-top:10px;flex-wrap:wrap;">
                @foreach($image_array as $imgId)
                    @php $tdata = get_attachment_image_by_id($imgId, 'grid'); $turl = $tdata['img_url'] ?? null; @endphp
                    <div onclick="tcSwitchImg(this, '{{ $turl }}')"
                         style="width:72px;height:72px;border:1.5px solid {{ $loop->first ? 'var(--tr-olive)' : 'var(--tr-border)' }};border-radius:var(--tr-radius);overflow:hidden;cursor:pointer;display:flex;align-items:center;justify-content:center;background:#fff;transition:border-color .2s;"
                         class="tc-product-thumb {{ $loop->first ? 'active' : '' }}">
                        @if($turl)<img src="{{ $turl }}" alt="" style="width:100%;height:100%;object-fit:cover;">@else<i class="mdi mdi-hiking" style="font-size:24px;color:var(--tr-olive);opacity:.3;"></i>@endif
                    </div>
                @endforeach
            </div>
        </div>

        <div class="col-lg-7">
            @if($product->badge)
                <span style="display:inline-block;background:var(--tr-terra);color:#fff;font-size:11px;font-weight:700;padding:4px 14px;border-radius:50px;margin-bottom:12px;text-transform:uppercase;letter-spacing:.5px;">{{ $product->badge->name }}</span>
            @endif

            <h1 style="font-size:26px;font-weight:900;color:var(--tr-bark);margin-bottom:12px;line-height:1.2;">{{ $product->name }}</h1>

            <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
                {!! theme_star_rating($product) !!}
                <span id="stock" data-stock-text='{!! $stock_count > 0 ? "<span class=\"text-success\">".__("In Stock")."</span>" : "<span class=\"text-danger\">".__("Out of Stock")."</span>" !!}'>
                    {!! $stock_count > 0 ? '<span class="text-success"><i class="mdi mdi-check-circle"></i> '.__('In Stock').'</span>' : '<span class="text-danger"><i class="mdi mdi-close-circle"></i> '.__('Out of Stock').'</span>' !!}
                </span>
            </div>

            <div class="mb-4 d-flex align-items-baseline gap-3 flex-wrap">
                <span style="font-size:28px;font-weight:900;color:var(--tr-bark);" id="price" data-main-price="{{ $sale_price }}" data-currency-symbol="{{ site_currency_symbol() }}">
                    {{ amount_with_currency_symbol($sale_price) }}
                </span>
                @if($deleted_price)
                    <span style="font-size:16px;color:var(--tr-stone);text-decoration:line-through;">{{ amount_with_currency_symbol($deleted_price) }}</span>
                @endif
                @if($discount)
                    <span style="display:inline-block;background:var(--tr-terra);color:#fff;font-size:12px;font-weight:700;padding:4px 12px;border-radius:6px;">-{{ $discount }}% {{ __('OFF') }}</span>
                @endif
            </div>

            @if($campaign_product !== null && $campaign_product->status !== 'draft')
                <div class="mb-4">
                    <div style="font-size:13px;font-weight:700;color:var(--tr-terra);margin-bottom:6px;">{{ $campaign_name }}</div>
                    @if(isset($campaign_active) && $campaign_active) <div class="global-timer"></div> @endif
                </div>
            @endif

            <div style="border-top:1px solid var(--tr-border);padding-top:20px;">
                @include(include_theme_path('shop.product_details.partials.product-options'))
            </div>

            <div class="d-flex gap-4 flex-wrap mt-4 pt-3" style="border-top:1px solid var(--tr-border);font-size:12px;color:var(--tr-stone);font-weight:600;">
                <span><i class="mdi mdi-shield-check" style="color:var(--tr-olive);"></i> {{ __('Secure Checkout') }}</span>
                <span><i class="mdi mdi-refresh" style="color:var(--tr-olive);"></i> {{ __('Easy Returns') }}</span>
                <span><i class="mdi mdi-truck-fast" style="color:var(--tr-olive);"></i> {{ __('Fast Delivery') }}</span>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div style="margin-top:56px;">
        <div class="tc-tab-nav" style="display:flex;gap:4px;border-bottom:2px solid var(--tr-border);">
            @foreach([[__('Description'),'tc-tab-desc'],[__('Reviews'),'tc-tab-reviews'],[__('Shipping'),'tc-tab-ship']] as $i => [$label,$id])
            <button class="tc-tab-btn {{ $i===0?'active':'' }}" data-target="{{ $id }}"
                    style="font-size:13px;font-weight:700;padding:12px 24px;border:none;background:transparent;cursor:pointer;color:{{ $i===0?'var(--tr-olive)':'var(--tr-stone)' }};border-bottom:2px solid {{ $i===0?'var(--tr-olive)':'transparent' }};margin-bottom:-2px;transition:all .2s;">
                {{ $label }}
            </button>
            @endforeach
        </div>
        <div id="tc-tab-desc" class="tc-tab-panel" style="background:#fff;border:1px solid var(--tr-border);border-top:none;padding:28px;border-radius:0 0 var(--tr-radius) var(--tr-radius);">
            @include(include_theme_path('shop.product_details.partials.product-description'))
        </div>
        <div id="tc-tab-reviews" class="tc-tab-panel" style="display:none;background:#fff;border:1px solid var(--tr-border);border-top:none;padding:28px;border-radius:0 0 var(--tr-radius) var(--tr-radius);">
            @include(include_theme_path('shop.product_details.partials.product-reviews'))
        </div>
        <div id="tc-tab-ship" class="tc-tab-panel" style="display:none;background:#fff;border:1px solid var(--tr-border);border-top:none;padding:28px;border-radius:0 0 var(--tr-radius) var(--tr-radius);">
            @include(include_theme_path('shop.product_details.partials.product-ship_return'))
        </div>
    </div>

    @if($related_products->isNotEmpty())
    <div style="margin-top:56px;">
        <h2 style="font-size:22px;font-weight:900;color:var(--tr-bark);margin-bottom:20px;">{{ __('You May Also Need') }}</h2>
        <div class="row g-3">
            @foreach($related_products as $rp)
                @php $rpdata = theme_product_price($rp); $rp_img = get_attachment_image_by_id($rp->image_id ?? null, 'grid'); $rp_imgurl = $rp_img['img_url'] ?? null; @endphp
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="tc-card h-100 d-flex flex-column">
                        <div class="tc-card-img">
                            @if($rp_imgurl)
                                <a href="{{ theme_product_url($rp->slug) }}"><img src="{{ $rp_imgurl }}" alt="{{ $rp->name }}" loading="lazy"></a>
                            @else
                                <a href="{{ theme_product_url($rp->slug) }}" class="tc-card-placeholder">
                                    <i class="mdi mdi-hiking"></i>
                                </a>
                            @endif
                        </div>
                        <div class="tc-card-body d-flex flex-column flex-grow-1">
                            <div class="tc-card-name flex-grow-1">
                                <a href="{{ theme_product_url($rp->slug) }}" class="tc-card-name-link">{{ \Illuminate\Support\Str::words($rp->name, 5) }}</a>
                            </div>
                            <div class="tc-card-price mt-auto">
                                <span class="tc-price-sale">{{ amount_with_currency_symbol($rpdata['sale_price']) }}</span>
                            </div>
                            <div class="tc-card-actions">
                                <a href="{{ theme_product_url($rp->slug) }}" class="tc-card-atc">
                                    <i class="las la-eye"></i> {{ __('View') }}
                                </a>
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
function tcSwitchImg(el, url) {
    document.querySelectorAll('.tc-product-thumb').forEach(t => { t.classList.remove('active'); t.style.borderColor='var(--tr-border)'; });
    el.classList.add('active'); el.style.borderColor='var(--tr-olive)';
    var mainImg = document.getElementById('tc-main-product-img');
    if (mainImg && url) { mainImg.src = url; }
    var longImg = document.querySelector('.long-img');
    if (longImg && url) { longImg.setAttribute('data-src', url); }
}

$(function () {
    $(document).on('click', '.tc-tab-btn', function () {
        var target = $(this).data('target');
        $('.tc-tab-btn').css({'color':'var(--tr-stone)','border-bottom-color':'transparent'}).removeClass('active');
        $(this).css({'color':'var(--tr-olive)','border-bottom-color':'var(--tr-olive)'}).addClass('active');
        $('.tc-tab-panel').hide(); $('#' + target).show();
    });

    if (typeof StarRating !== 'undefined') {
        new StarRating('.star-rating', { maxStars: 5, clearable: false, classNames: { active: 'tc-active', base: 'tc-star-rating', selected: 'rating-selected' } });
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
            beforeSend: function () { toastr.warning('{{ __("Submitting please wait.") }}'); btn.text('{{ __("Submitting..") }}'); },
            success: function (data) { if (data.type === 'success') { toastr.success(data.msg); setTimeout(() => location.reload(), 300); } else { toastr.error(data.msg); } btn.text('{{ __("Submit Review") }}'); },
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
</script>{!! theme_product_js($product_inventory_set, $additional_info_store, $campaign_product ?? null, $campaign_active ?? 0, $product) !!}
@endsection
