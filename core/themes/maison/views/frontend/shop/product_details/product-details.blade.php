@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $product->name !!} @endsection
@section('page-title') {!! $product->name !!} @endsection

@section('meta-data') {!! render_page_meta_data($product) !!} @endsection

@section('style')
<link rel="stylesheet" href="{{ global_asset('assets/common/css/star-rating.min.css') }}">
<style>
.ms-tab-nav {
    display: flex;
    border-bottom: 1px solid var(--ms-border);
    gap: 0;
    margin-bottom: 0;
    overflow-x: auto;
    scrollbar-width: none;
}
.ms-tab-nav::-webkit-scrollbar { display: none; }
.ms-tab-btn {
    padding: 12px 24px;
    border: none;
    background: transparent;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: var(--ms-muted);
    cursor: pointer;
    border-bottom: 2px solid transparent;
    margin-bottom: -1px;
    transition: color .2s, border-color .2s;
    font-family: inherit;
}
.ms-tab-btn.active {
    color: var(--ms-dark);
    border-bottom-color: var(--ms-linen);
}
.ms-tab-panel { display: none; padding: 32px 0; }
.ms-tab-panel.active { display: block; }
.ms-thumb {
    width: 76px;
    height: 76px;
    border: 1.5px solid var(--ms-border);
    border-radius: var(--ms-radius);
    overflow: hidden;
    cursor: pointer;
    background: var(--ms-warm);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: border-color .2s;
}
.ms-thumb.active { border-color: var(--ms-linen-d); }
.ms-thumb:hover { border-color: var(--ms-linen); }
.ms-thumb img { width: 100%; height: 100%; object-fit: cover; }
.ms-qty-wrap {
    display: flex;
    align-items: center;
    border: 1px solid var(--ms-border);
    border-radius: var(--ms-radius);
    overflow: hidden;
    width: fit-content;
}
.ms-qty-btn {
    width: 36px;
    height: 40px;
    border: none;
    background: var(--ms-warm);
    cursor: pointer;
    font-size: 16px;
    color: var(--ms-charcoal);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background .2s;
}
.ms-qty-btn:hover { background: var(--ms-surface); }
.ms-qty-input {
    width: 52px;
    height: 40px;
    border: 0;
    border-left: 1px solid var(--ms-border);
    border-right: 1px solid var(--ms-border);
    text-align: center;
    font-size: 14px;
    font-weight: 600;
    color: var(--ms-dark);
    font-family: inherit;
    background: #fff;
}
.ms-btn-cart {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 13px 28px;
    background: var(--ms-dark);
    color: #fff;
    border: none;
    border-radius: var(--ms-radius);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    cursor: pointer;
    transition: background .2s;
    font-family: inherit;
}
.ms-btn-cart:hover { background: var(--ms-linen-d); }
.ms-btn-wishlist {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 13px 18px;
    background: transparent;
    color: var(--ms-charcoal);
    border: 1px solid var(--ms-border);
    border-radius: var(--ms-radius);
    font-size: 12px;
    font-weight: 600;
    letter-spacing: .06em;
    text-transform: uppercase;
    cursor: pointer;
    transition: all .2s;
    font-family: inherit;
}
.ms-btn-wishlist:hover { border-color: var(--ms-linen); color: var(--ms-linen-d); }
.ms-attr-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 8px;
    padding: 0;
    list-style: none;
}
.ms-attr-list li {
    padding: 6px 16px;
    border: 1px solid var(--ms-border);
    border-radius: var(--ms-radius);
    font-size: 13px;
    color: var(--ms-charcoal);
    cursor: pointer;
    transition: all .2s;
    font-weight: 500;
}
.ms-attr-list li:hover { border-color: var(--ms-linen); color: var(--ms-linen-d); }
.ms-attr-list li.active { background: var(--ms-dark); color: #fff; border-color: var(--ms-dark); }
.ms-attr-list li.disabled { opacity: .4; pointer-events: none; }
.ms-related-card {
    background: #fff;
    border: 1px solid var(--ms-border);
    border-radius: var(--ms-radius);
    overflow: hidden;
    box-shadow: var(--ms-shadow);
    transition: box-shadow .25s;
}
.ms-related-card:hover { box-shadow: 0 18px 50px -10px rgba(200,184,154,.4); }
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
    $main_img     = get_attachment_image_by_id($product->image_id ?? null, 'large');
    $main_img_url = $main_img['img_url'] ?? null;
@endphp

{{-- Breadcrumb --}}
<div style="background:var(--ms-warm);border-bottom:1px solid var(--ms-border);padding:14px 0;">
    <div class="container">
        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--ms-muted);letter-spacing:.04em;">
            <a href="{{ theme_home_url() }}" style="color:var(--ms-linen-d);text-decoration:none;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="font-size:14px;color:var(--ms-border);"></i>
            <a href="{{ theme_shop_url() }}" style="color:var(--ms-linen-d);text-decoration:none;">{{ __('Shop') }}</a>
            <i class="mdi mdi-chevron-right" style="font-size:14px;color:var(--ms-border);"></i>
            <span>{{ \Illuminate\Support\Str::words($product->name, 6) }}</span>
        </div>
    </div>
</div>

{{-- Product Section --}}
<div class="container" style="padding-top:48px;padding-bottom:72px;">
    <div class="row g-5">

        {{-- Image Gallery --}}
        <div class="col-lg-5">
            <div style="background:var(--ms-warm);border:1px solid var(--ms-border);border-radius:var(--ms-radius);overflow:hidden;aspect-ratio:1;display:flex;align-items:center;justify-content:center;position:relative;" id="shop_details_gallery_slider">
                <div class="shop-details-thums long-img" data-src="{{ $main_img_url }}" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                    @if($main_img_url)
                        <img id="ms-main-product-img" src="{{ $main_img_url }}" alt="{{ $product->name }}"
                             style="width:100%;height:100%;object-fit:cover;transition:transform .4s;">
                    @else
                        <i class="mdi mdi-image-outline" style="font-size:80px;color:var(--ms-border);"></i>
                    @endif
                </div>
                @if($discount)
                    <span style="position:absolute;top:14px;left:14px;background:var(--ms-olive);color:#fff;font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;padding:4px 12px;border-radius:2px;">
                        −{{ $discount }}%
                    </span>
                @endif
            </div>
            <div style="display:flex;gap:10px;margin-top:12px;flex-wrap:wrap;">
                @foreach($image_array as $imgId)
                    @php $tdata = get_attachment_image_by_id($imgId, 'grid'); $turl = $tdata['img_url'] ?? null; @endphp
                    <div class="ms-thumb {{ $loop->first ? 'active' : '' }}" onclick="msSwitchImg(this, '{{ $turl }}')">
                        @if($turl)
                            <img src="{{ $turl }}" alt="">
                        @else
                            <i class="mdi mdi-image-outline" style="font-size:22px;color:var(--ms-border);"></i>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Product Info --}}
        <div class="col-lg-7">

            @if($product->badge)
                <span style="display:inline-block;background:var(--ms-surface);color:var(--ms-charcoal);font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;padding:4px 12px;border-radius:2px;margin-bottom:12px;">
                    {{ $product->badge->name }}
                </span>
            @endif

            <h1 style="font-size:clamp(22px,3vw,32px);font-weight:300;color:var(--ms-dark);line-height:1.25;margin-bottom:16px;letter-spacing:-.01em;">
                {{ $product->name }}
            </h1>

            <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:20px;">
                {!! theme_star_rating($product) !!}
                <span id="stock" data-stock-text='{!! $stock_count > 0 ? "<span style=\"color:var(--ms-olive)\">".__("In Stock")."</span>" : "<span style=\"color:#C0392B\">".__("Out of Stock")."</span>" !!}' style="font-size:13px;font-weight:500;">
                    {!! $stock_count > 0 ? '<span style="color:var(--ms-olive);font-size:13px;"><i class="mdi mdi-check-circle-outline"></i> '.__('In Stock').'</span>' : '<span style="color:#C0392B;font-size:13px;"><i class="mdi mdi-close-circle-outline"></i> '.__('Out of Stock').'</span>' !!}
                </span>
                @if($stock_count > 0)
                    <span id="item_left" style="font-size:12px;color:var(--ms-muted);" data-stock-text="{{ $stock_count }} {{ __('items left') }}">{{ $stock_count }} {{ __('items left') }}</span>
                @endif
            </div>

            <div style="display:flex;align-items:baseline;gap:12px;flex-wrap:wrap;margin-bottom:24px;padding-bottom:24px;border-bottom:1px solid var(--ms-border);">
                <span id="price" style="font-size:30px;font-weight:500;color:var(--ms-linen-d);"
                      data-main-price="{{ $sale_price }}" data-currency-symbol="{{ site_currency_symbol() }}">
                    {{ amount_with_currency_symbol($sale_price) }}
                </span>
                @if($deleted_price)
                    <span style="font-size:17px;color:var(--ms-muted);text-decoration:line-through;">
                        {{ amount_with_currency_symbol($deleted_price) }}
                    </span>
                @endif
            </div>

            @if($campaign_product !== null && $campaign_product->status !== 'draft')
                <div style="margin-bottom:20px;padding:14px 16px;background:var(--ms-warm);border:1px solid var(--ms-border);border-radius:var(--ms-radius);">
                    <div style="font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--ms-charcoal);margin-bottom:6px;">{{ $campaign_name }}</div>
                    @if(isset($campaign_active) && $campaign_active)
                        <div class="global-timer" style="font-size:13px;color:var(--ms-muted);"></div>
                    @endif
                </div>
            @endif

            {{-- Attributes --}}
            <div style="margin-bottom:24px;">
                @include(include_theme_path('shop.product_details.partials.product-options'))
            </div>

            {{-- Trust Badges --}}
            <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;padding:16px 0;border-top:1px solid var(--ms-border);font-size:12px;color:var(--ms-muted);">
                <span style="display:flex;align-items:center;gap:5px;">
                    <i class="las la-shield-alt" style="color:var(--ms-olive);font-size:15px;"></i>
                    {{ __('Secure Checkout') }}
                </span>
                <span style="display:flex;align-items:center;gap:5px;">
                    <i class="las la-sync-alt" style="color:var(--ms-linen-d);font-size:15px;"></i>
                    {{ __('Easy Returns') }}
                </span>
                <span style="display:flex;align-items:center;gap:5px;">
                    <i class="las la-truck" style="color:var(--ms-charcoal);font-size:15px;"></i>
                    {{ __('Crafted with Care') }}
                </span>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div style="margin-top:64px;">
        <div class="ms-tab-nav">
            <button class="ms-tab-btn active" data-target="ms-tab-desc">{{ __('Description') }}</button>
            <button class="ms-tab-btn" data-target="ms-tab-reviews">{{ __('Reviews') }}</button>
            <button class="ms-tab-btn" data-target="ms-tab-ship">{{ __('Shipping & Returns') }}</button>
        </div>

        <div id="ms-tab-desc" class="ms-tab-panel active">
            @include(include_theme_path('shop.product_details.partials.product-description'))
        </div>
        <div id="ms-tab-reviews" class="ms-tab-panel">
            @include(include_theme_path('shop.product_details.partials.product-reviews'))
        </div>
        <div id="ms-tab-ship" class="ms-tab-panel">
            @include(include_theme_path('shop.product_details.partials.product-ship_return'))
        </div>
    </div>

    {{-- Related Products --}}
    @if($related_products->isNotEmpty())
    <div style="margin-top:72px;">
        <div style="text-align:center;margin-bottom:36px;">
            <div style="font-size:11px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:8px;">{{ __('You May Also Love') }}</div>
            <h2 style="font-size:26px;font-weight:300;color:var(--ms-dark);margin:0;">{{ __('Related Pieces') }}</h2>
        </div>
        <div class="row g-3">
            @foreach($related_products as $rp)
                @php
                    $rpdata    = theme_product_price($rp);
                    $rp_img    = get_attachment_image_by_id($rp->image_id ?? null, 'grid');
                    $rp_imgurl = $rp_img['img_url'] ?? null;
                @endphp
                <div class="col-6 col-md-3">
                    <div class="ms-related-card">
                        <div style="aspect-ratio:1;overflow:hidden;background:var(--ms-warm);">
                            @if($rp_imgurl)
                                <a href="{{ theme_product_url($rp->slug) }}">
                                    <img src="{{ $rp_imgurl }}" alt="{{ $rp->name }}" loading="lazy"
                                         style="width:100%;height:100%;object-fit:cover;transition:transform .4s;"
                                         onmouseover="this.style.transform='scale(1.06)'"
                                         onmouseout="this.style.transform='scale(1)'">
                                </a>
                            @else
                                <div style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;">
                                    <i class="mdi mdi-image-outline" style="font-size:42px;color:var(--ms-border);"></i>
                                </div>
                            @endif
                        </div>
                        <div style="padding:14px;">
                            <div style="font-size:13px;font-weight:500;color:var(--ms-dark);margin-bottom:6px;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">
                                <a href="{{ theme_product_url($rp->slug) }}" style="color:inherit;text-decoration:none;"
                                   onmouseover="this.style.color='var(--ms-linen-d)'"
                                   onmouseout="this.style.color='var(--ms-dark)'">
                                    {{ \Illuminate\Support\Str::words($rp->name, 6) }}
                                </a>
                            </div>
                            <div style="font-size:15px;font-weight:500;color:var(--ms-linen-d);">
                                {{ amount_with_currency_symbol($rpdata['sale_price']) }}
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
function msSwitchImg(el, url) {
    document.querySelectorAll('.ms-thumb').forEach(function (t) { t.classList.remove('active'); });
    el.classList.add('active');
    var mainImg = document.getElementById('ms-main-product-img');
    if (mainImg && url) { mainImg.src = url; }
    var longImg = document.querySelector('.long-img');
    if (longImg && url) { longImg.setAttribute('data-src', url); }
}

$(function () {
    $(document).on('click', '.ms-tab-btn', function () {
        var target = $(this).data('target');
        $('.ms-tab-btn').removeClass('active');
        $(this).addClass('active');
        $('.ms-tab-panel').removeClass('active');
        $('#' + target).addClass('active');
    });

    if (typeof StarRating !== 'undefined') {
        new StarRating('.star-rating', {
            maxStars: 5, clearable: false,
            classNames: { active: 'ms-active', base: 'ms-star-rating', selected: 'rating-selected' },
        });
    }

    @php if (!empty($campaign_product) && isset($campaign_active) && $campaign_active != 0) { $end_date = $campaign_product->campaign?->end_date; } @endphp
    if (typeof $.fn.syotimer !== 'undefined') {
        $('.global-timer').syotimer({ year: '{{ $end_date->year ?? 0 }}', month: '{{ $end_date->month ?? 0 }}', day: '{{ $end_date->day ?? 0 }}' });
    }

    $(document).on('click', '.plus', function () { var i = $(this).prev('.ms-qty-input'); if (i.val()) i[0].stepUp(1); });
    $(document).on('click', '.substract', function () { var i = $(this).next('.ms-qty-input'); if (parseInt(i.val()) > 1) i[0].stepDown(1); });

    $(document).on('click', '#review-submit-btn', function (e) {
        e.preventDefault();
        var btn = $(this);
        $.ajax({
            url: '{{ theme_product_review_url() }}', type: 'POST',
            data: { _token: '{{ csrf_token() }}', product_id: '{{ $product->id }}', review_text: $('#review-text').val(), rating: $('.rating-selected').data('value') },
            beforeSend: function () { toastr.warning('{{ __("Submitting, please wait.") }}'); btn.text('{{ __("Submitting…") }}'); },
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
            beforeSend: function () { el.text('{{ __("Loading…") }}'); },
            success: function (data) { $('.all-reviews').html(data.markup).hide().fadeIn(600); el.text('{{ __("See More") }}'); el.attr('data-items', Number(items)+5); },
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
