@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $product->name !!} @endsection

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

{{-- Breadcrumb --}}
<div style="background:var(--gc-warm);border-bottom:1px solid var(--gc-border);padding:14px 0;">
    <div class="container">
        <div style="display:flex;align-items:center;gap:10px;font-size:12px;color:var(--gc-muted);font-style:italic;">
            <a href="{{ theme_home_url() }}" style="color:var(--gc-rose);text-decoration:none;">{{ __('Home') }}</a>
            <span>—</span>
            <a href="{{ theme_shop_url() }}" style="color:var(--gc-rose);text-decoration:none;">{{ __('Shop') }}</a>
            <span>—</span>
            <span>{{ \Illuminate\Support\Str::words($product->name, 6) }}</span>
        </div>
    </div>
</div>

{{-- Product Section --}}
<div class="container" style="padding-top:40px;padding-bottom:72px;">
    <div class="row g-5">

        {{-- Images --}}
        <div class="col-lg-5">
            <div style="background:var(--gc-warm);border:1px solid var(--gc-border);border-radius:var(--gc-radius);overflow:hidden;" id="shop_details_gallery_slider">
                <div class="shop-details-thums long-img" data-src="{{ $main_img_url }}" style="aspect-ratio:3/4;display:flex;align-items:center;justify-content:center;padding:28px;">
                    @if($main_img_url)
                        <img id="gc-main-product-img" src="{{ $main_img_url }}" alt="{{ $product->name }}" style="max-width:100%;max-height:100%;object-fit:contain;">
                    @else
                        <i class="las la-gem" style="font-size:80px;"></i>
                    @endif
                </div>
            </div>
            <div style="display:flex;gap:10px;margin-top:12px;flex-wrap:wrap;">
                @foreach($image_array as $imgId)
                    @php $tdata = get_attachment_image_by_id($imgId, 'grid'); $turl = $tdata['img_url'] ?? null; @endphp
                    <div onclick="gcSwitchImg(this, '{{ $turl }}')"
                         style="width:72px;height:72px;border-radius:var(--gc-radius);overflow:hidden;border:2px solid {{ $loop->first ? 'var(--gc-rose)' : 'var(--gc-border)' }};background:var(--gc-warm);cursor:pointer;display:flex;align-items:center;justify-content:center;padding:4px;transition:border-color .2s;"
                         class="gc-thumb {{ $loop->first ? 'active' : '' }}">
                        @if($turl)<img src="{{ $turl }}" alt="" style="max-width:100%;max-height:100%;object-fit:contain;">@else<i class="las la-gem" style="font-size:20px;"></i>@endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Info --}}
        <div class="col-lg-7">
            @if($product->badge)
                <span style="display:inline-block;background:var(--gc-rose);color:#fff;font-size:9px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;padding:4px 14px;border-radius:3px;margin-bottom:12px;">{{ $product->badge->name }}</span>
            @endif

            <span style="display:block;font-size:10px;font-weight:400;letter-spacing:3px;text-transform:uppercase;color:var(--gc-rose);margin-bottom:8px;">{{ __('Artisan Collection') }}</span>
            <h1 style="font-size:30px;font-weight:400;color:var(--gc-dark);line-height:1.2;margin-bottom:12px;font-style:italic;">{{ $product->name }}</h1>

            <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
                {!! theme_star_rating($product) !!}
                <span id="stock" data-stock-text='{!! $stock_count > 0 ? "<span style=\"color:#27AE60\">".__("In Stock")."</span>" : "<span style=\"color:#E53935\">".__("Out of Stock")."</span>" !!}'>
                    {!! $stock_count > 0 ? '<span style="color:#27AE60;font-size:13px;"><i class="las la-check-circle"></i> '.__('In Stock').'</span>' : '<span style="color:#E53935;font-size:13px;"><i class="las la-times-circle"></i> '.__('Out of Stock').'</span>' !!}
                </span>
            </div>

            <div style="display:flex;align-items:baseline;gap:12px;flex-wrap:wrap;margin-bottom:24px;padding-bottom:20px;border-bottom:1px solid var(--gc-border);">
                <span class="gc-price" style="font-size:28px;"
                      id="price" data-main-price="{{ $sale_price }}" data-currency-symbol="{{ site_currency_symbol() }}">
                    {{ amount_with_currency_symbol($sale_price) }}
                </span>
                @if($deleted_price)
                    <span style="font-size:16px;color:var(--gc-muted);text-decoration:line-through;font-style:italic;">{{ amount_with_currency_symbol($deleted_price) }}</span>
                @endif
                @if($discount)
                    <span style="background:var(--gc-rose);color:#fff;font-size:10px;font-weight:700;padding:4px 12px;border-radius:3px;letter-spacing:.5px;">{{ $discount }}% {{ __('OFF') }}</span>
                @endif
            </div>

            @if($campaign_product !== null && $campaign_product->status !== 'draft')
                <div style="margin-bottom:16px;">
                    <span style="font-size:13px;font-style:italic;color:var(--gc-rose);">{{ $campaign_name }}</span>
                    @if(isset($campaign_active) && $campaign_active)
                        <div class="global-timer mt-2"></div>
                    @endif
                </div>
            @endif

            <div style="padding:20px;background:var(--gc-warm);border-radius:var(--gc-radius);border:1px solid var(--gc-border);margin-bottom:20px;">
                @include(include_theme_path('shop.product_details.partials.product-options'))
            </div>

            <div style="display:flex;gap:20px;flex-wrap:wrap;font-size:12px;color:var(--gc-muted);font-style:italic;margin-top:16px;">
                <span><i class="las la-award" style="color:var(--gc-rose);font-style:normal;"></i> {{ __('Hallmarked Certified') }}</span>
                <span><i class="las la-shield-alt" style="color:var(--gc-rose);font-style:normal;"></i> {{ __('Authenticity Guaranteed') }}</span>
                <span><i class="las la-sync" style="color:var(--gc-rose);font-style:normal;"></i> {{ __('Easy Returns') }}</span>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div style="margin-top:60px;">
        <div style="display:flex;gap:0;border-bottom:1px solid var(--gc-border);">
            <button class="gc-tab-btn active" data-target="gc-tab-desc"
                    style="padding:12px 28px;border:none;background:none;font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gc-rose);border-bottom:2px solid var(--gc-rose);margin-bottom:-1px;cursor:pointer;font-family:Georgia,serif;">
                {{ __('Description') }}
            </button>
            <button class="gc-tab-btn" data-target="gc-tab-reviews"
                    style="padding:12px 28px;border:none;background:none;font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gc-muted);border-bottom:2px solid transparent;margin-bottom:-1px;cursor:pointer;font-family:Georgia,serif;">
                {{ __('Reviews') }}
            </button>
            <button class="gc-tab-btn" data-target="gc-tab-ship"
                    style="padding:12px 28px;border:none;background:none;font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gc-muted);border-bottom:2px solid transparent;margin-bottom:-1px;cursor:pointer;font-family:Georgia,serif;">
                {{ __('Shipping') }}
            </button>
        </div>

        <div style="background:var(--gc-ivory);border:1px solid var(--gc-border);border-top:none;border-radius:0 0 var(--gc-radius) var(--gc-radius);padding:28px;">
            <div id="gc-tab-desc" class="gc-tab-panel">
                @include(include_theme_path('shop.product_details.partials.product-description'))
            </div>
            <div id="gc-tab-reviews" class="gc-tab-panel" style="display:none;">
                @include(include_theme_path('shop.product_details.partials.product-reviews'))
            </div>
            <div id="gc-tab-ship" class="gc-tab-panel" style="display:none;">
                @include(include_theme_path('shop.product_details.partials.product-ship_return'))
            </div>
        </div>
    </div>

    {{-- Related Products --}}
    @if($related_products->isNotEmpty())
    <div style="margin-top:64px;">
        <div style="text-align:center;margin-bottom:32px;">
            <span style="display:block;font-size:10px;font-weight:700;letter-spacing:3px;text-transform:uppercase;color:var(--gc-rose);margin-bottom:8px;">{{ __('Crafted for You') }}</span>
            <h2 style="font-size:24px;font-weight:400;color:var(--gc-dark);font-style:italic;">{{ __('You May Also Love') }}</h2>
        </div>
        <div class="row g-3">
            @foreach($related_products as $rp)
                @php
                    $rpdata = theme_product_price($rp);
                    $rp_img = get_attachment_image_by_id($rp->image_id ?? null, 'grid');
                    $rp_imgurl = $rp_img['img_url'] ?? null;
                @endphp
                <div class="col-6 col-md-3">
                    <div class="gc-card">
                        <div class="gc-card-img">
                            @if($rp_imgurl)
                                <a href="{{ theme_product_url($rp->slug) }}"><img src="{{ $rp_imgurl }}" alt="{{ $rp->name }}" loading="lazy"></a>
                            @else
                                <a href="{{ theme_product_url($rp->slug) }}" style="font-size:40px;display:flex;align-items:center;justify-content:center;"><i class="las la-gem"></i></a>
                            @endif
                        </div>
                        <div class="gc-card-body">
                            <div class="gc-card-name"><a href="{{ theme_product_url($rp->slug) }}" style="color:inherit;text-decoration:none;">{{ \Illuminate\Support\Str::words($rp->name, 6) }}</a></div>
                            <div class="gc-card-footer">
                                <span class="gc-price">{{ amount_with_currency_symbol($rpdata['sale_price']) }}</span>
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
function gcSwitchImg(el, url) {
    document.querySelectorAll('.gc-thumb').forEach(function(t) { t.style.borderColor = 'var(--gc-border)'; });
    el.style.borderColor = 'var(--gc-rose)';
    var mainImg = document.getElementById('gc-main-product-img');
    var longImg = document.querySelector('.long-img');
    if (mainImg && url) { mainImg.src = url; }
    if (longImg && url) { longImg.setAttribute('data-src', url); }
}

$(function () {
    $(document).on('click', '.gc-tab-btn', function () {
        var target = $(this).data('target');
        $('.gc-tab-btn').css({'color':'var(--gc-muted)','border-bottom-color':'transparent'});
        $(this).css({'color':'var(--gc-rose)','border-bottom-color':'var(--gc-rose)'});
        $('.gc-tab-panel').hide();
        $('#' + target).show();
    });

    if (typeof StarRating !== 'undefined') { new StarRating('.star-rating', { maxStars: 5, clearable: false }); }

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
            beforeSend: function () { btn.text('{{ __("Submitting..") }}'); },
            success: function (data) { if (data.type === 'success') { toastr.success(data.msg); setTimeout(() => location.reload(), 300); } else { toastr.error(data.msg); } btn.text('{{ __("Submit Review") }}'); },
            error: function (data) { $.each(data.responseJSON?.errors ?? {}, (v, i) => toastr.error(i)); btn.text('{{ __("Submit Review") }}'); }
        });
    });
});
</script>
{!! theme_product_js($product_inventory_set, $additional_info_store, $campaign_product ?? null, $campaign_active ?? 0, $product) !!}
@endsection
