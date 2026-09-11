@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Compare Products') }} — {{ theme_site_name() }} @endsection
@section('page-title') {{ __('Compare') }} @endsection

@section('content')

{{-- Page Banner --}}
<div class="dk-page-banner">
    <div class="dk-page-banner-overlay"></div>
    <div class="dk-page-banner-bar"></div>
    <div class="container dk-page-banner-inner">
        <h1 class="dk-page-banner-title">{{ __('Compare Products') }}</h1>
        <nav class="dk-page-banner-nav">
            <a href="{{ theme_home_url() }}" class="dk-page-banner-link">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right dk-page-banner-sep"></i>
            <span class="dk-page-banner-current">{{ __('Compare') }}</span>
        </nav>
    </div>
</div>

<section style="background:var(--dk-carbon);padding:40px 0 72px;">
<div class="container">

@php
    $compare_items    = \Gloudemans\Shoppingcart\Facades\Cart::instance("compare")->content();
    $compare_products = [];
    $compare_row_ids  = [];
    foreach ($compare_items as $item) {
        $p = \Modules\Product\Entities\Product::with(['inventory', 'category'])->find($item->id);
        if ($p) {
            $compare_products[]      = $p;
            $compare_row_ids[$p->id] = $item->rowId;
        }
    }
@endphp

@if(count($compare_products) > 0)

<div class="table-responsive">
<table class="dk-compare-table">
    {{-- Product header row --}}
    <thead>
        <tr>
            <td class="dk-compare-attr-col dk-compare-attr" style="border-top:0;"></td>
            @foreach($compare_products as $product)
            <th class="dk-compare-product-col" style="border-top:0;">
                <div class="dk-compare-product-head">
                    <div class="dk-compare-thumb">
                        @php $imgUrl = theme_product_image($product->image_id ?? null, 'grid'); @endphp
                        @if($imgUrl)
                            <img src="{{ $imgUrl }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:cover;">
                        @else
                            <div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:36px;color:var(--dk-silver);opacity:.4;"><i class="mdi mdi-car"></i></div>
                        @endif
                    </div>
                    <a href="{{ theme_product_url($product->slug) }}" class="dk-compare-product-name">{{ \Illuminate\Support\Str::words($product->name, 6) }}</a>
                    <button class="dk-compare-remove compare-remove-btn" data-product_id="{{ $compare_row_ids[$product->id] ?? '' }}">
                        <i class="mdi mdi-close"></i> {{ __('Remove') }}
                    </button>
                </div>
            </th>
            @endforeach
        </tr>
    </thead>

    <tbody>
        {{-- Price --}}
        <tr>
            <td class="dk-compare-attr">{{ __('Price') }}</td>
            @foreach($compare_products as $product)
            @php $pdata = theme_product_price($product); @endphp
            <td class="dk-compare-val">
                <span class="dk-compare-price">{{ amount_with_currency_symbol($pdata['sale_price']) }}</span>
                @if($pdata['regular_price'])
                    <span style="display:block;font-size:12px;color:var(--dk-silver);text-decoration:line-through;">{{ amount_with_currency_symbol($pdata['regular_price']) }}</span>
                @endif
            </td>
            @endforeach
        </tr>

        {{-- Rating --}}
        <tr>
            <td class="dk-compare-attr">{{ __('Rating') }}</td>
            @foreach($compare_products as $product)
            <td class="dk-compare-val">{!! theme_star_rating($product) !!}</td>
            @endforeach
        </tr>

        {{-- SKU --}}
        <tr>
            <td class="dk-compare-attr">{{ __('SKU') }}</td>
            @foreach($compare_products as $product)
            <td class="dk-compare-val" style="color:var(--dk-silver);font-size:12px;">{{ $product->sku ?? '—' }}</td>
            @endforeach
        </tr>

        {{-- Category --}}
        <tr>
            <td class="dk-compare-attr">{{ __('Category') }}</td>
            @foreach($compare_products as $product)
            <td class="dk-compare-val" style="color:var(--dk-silver);font-size:13px;">{{ $product->category?->name ?? '—' }}</td>
            @endforeach
        </tr>

        {{-- Availability --}}
        <tr>
            <td class="dk-compare-attr">{{ __('Availability') }}</td>
            @foreach($compare_products as $product)
            @php $in_stock = optional($product->inventory)->stock_count > 0; @endphp
            <td class="dk-compare-val">
                @if($in_stock)
                    <span style="color:#4caf50;font-size:13px;font-weight:600;"><i class="mdi mdi-check-circle"></i> {{ __('In Stock') }}</span>
                @else
                    <span style="color:#f44336;font-size:13px;font-weight:600;"><i class="mdi mdi-close-circle"></i> {{ __('Out of Stock') }}</span>
                @endif
            </td>
            @endforeach
        </tr>

        {{-- Action --}}
        <tr>
            <td class="dk-compare-attr">{{ __('Action') }}</td>
            @foreach($compare_products as $product)
            <td class="dk-compare-val">
                <button class="dk-btn dk-btn-red dk-btn-sm add-to-cart-btn" data-product_id="{{ $product->id }}" style="font-size:11px;">
                    <i class="mdi mdi-cart-plus"></i> {{ __('Add to Cart') }}
                </button>
            </td>
            @endforeach
        </tr>
    </tbody>
</table>
</div>

<div class="mt-4 d-flex gap-3 flex-wrap">
    <a href="{{ theme_shop_url() }}" class="dk-btn dk-btn-ghost">
        <i class="mdi mdi-arrow-left"></i> {{ __('Continue Shopping') }}
    </a>
</div>

@else

{{-- Empty state --}}
<div style="text-align:center;padding:80px 0;">
    <div style="font-size:56px;margin-bottom:16px;opacity:.3;"><i class="mdi mdi-compare"></i></div>
    <h3 style="font-family:var(--dk-font-head);font-size:20px;font-weight:800;text-transform:uppercase;color:var(--dk-white);margin-bottom:8px;">{{ __('No Products to Compare') }}</h3>
    <p style="color:var(--dk-silver);font-size:14px;margin-bottom:24px;">{{ __('Add products to compare by clicking the compare button on any product.') }}</p>
    <a href="{{ theme_shop_url() }}" class="dk-btn dk-btn-red">
        <i class="mdi mdi-store"></i> {{ __('Browse Products') }}
    </a>
</div>

@endif

</div>
</section>

@endsection

@section('scripts')
<script>
(function ($) {
    'use strict';
    $(document).on('click', '.compare-remove-btn', function () {
        var rowId = $(this).data('product_id');
        var btn = $(this);
        btn.prop('disabled', true).css('opacity', '.5');
        $.ajax({
            url: '{{ theme_compare_remove_url() }}',
            type: 'GET',
            data: { product_id: rowId },
            success: function () {
                var ids = (sessionStorage.getItem('products') || '').split(',').filter(function (v) { return v && v !== String(rowId); });
                sessionStorage.setItem('products', ids.join(','));
                if (!ids.length) sessionStorage.removeItem('products');
                location.reload();
            },
            error: function () { btn.prop('disabled', false).css('opacity', '1'); }
        });
    });
})(jQuery);
</script>
@endsection
