@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Compare Products') }} @endsection
@section('page-title') {{ __('Compare Products') }} @endsection

@section('content')

<div class="ar-page-banner">
    <div class="container">
        <h1>{{ __('Compare Products') }}</h1>
        <nav aria-label="breadcrumb" style="margin-top:10px;">
            <ol style="list-style:none;padding:0;margin:0;display:flex;align-items:center;gap:8px;font-size:13px;">
                <li><a href="{{ theme_home_url() }}" style="color:rgba(255,255,255,.7);text-decoration:none;">{{ __('Home') }}</a></li>
                <li style="color:rgba(255,255,255,.45);">/</li>
                <li style="color:#fff;font-weight:600;">{{ __('Compare') }}</li>
            </ol>
        </nav>
    </div>
</div>

<div style="background:var(--ar-bg);padding:48px 0 80px;">
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
<table class="ar-compare-table">
    <thead>
        <tr>
            <td class="ar-compare-label-col"></td>
            @foreach($compare_products as $product)
            <th class="ar-compare-product-col">
                <div class="ar-compare-product-head">
                    <div class="ar-compare-thumb">
                        @php $imgUrl = theme_product_image($product->image_id ?? null, 'grid'); @endphp
                        @if($imgUrl)
                            <img src="{{ $imgUrl }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:cover;">
                        @else
                            <div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:36px;color:var(--ar-border);"><i class="las la-spray-can"></i></div>
                        @endif
                    </div>
                    <a href="{{ theme_product_url($product->slug) }}" class="ar-compare-product-name">{{ \Illuminate\Support\Str::words($product->name, 6) }}</a>
                    <button class="ar-compare-remove compare-remove-btn" data-product_id="{{ $compare_row_ids[$product->id] ?? '' }}">
                        <i class="las la-times"></i> {{ __('Remove') }}
                    </button>
                </div>
            </th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        {{-- Price --}}
        <tr class="ar-compare-row">
            <td class="ar-compare-attr"><i class="las la-tag"></i> {{ __('Price') }}</td>
            @foreach($compare_products as $product)
            @php $pdata = theme_product_price($product); @endphp
            <td class="ar-compare-val">
                <span class="ar-compare-price">{{ amount_with_currency_symbol($pdata['sale_price']) }}</span>
                @if($pdata['regular_price'])
                    <span style="display:block;font-size:12px;color:var(--ar-muted);text-decoration:line-through;">{{ amount_with_currency_symbol($pdata['regular_price']) }}</span>
                @endif
            </td>
            @endforeach
        </tr>

        {{-- Rating --}}
        <tr class="ar-compare-row ar-compare-row-alt">
            <td class="ar-compare-attr"><i class="las la-star"></i> {{ __('Rating') }}</td>
            @foreach($compare_products as $product)
            <td class="ar-compare-val">{!! theme_star_rating($product) !!}</td>
            @endforeach
        </tr>

        {{-- SKU --}}
        <tr class="ar-compare-row">
            <td class="ar-compare-attr"><i class="las la-barcode"></i> {{ __('SKU') }}</td>
            @foreach($compare_products as $product)
            <td class="ar-compare-val ar-compare-muted">{{ $product->sku ?? '—' }}</td>
            @endforeach
        </tr>

        {{-- Category --}}
        <tr class="ar-compare-row ar-compare-row-alt">
            <td class="ar-compare-attr"><i class="las la-folder"></i> {{ __('Category') }}</td>
            @foreach($compare_products as $product)
            <td class="ar-compare-val ar-compare-muted">{{ $product->category?->name ?? '—' }}</td>
            @endforeach
        </tr>

        {{-- Availability --}}
        <tr class="ar-compare-row">
            <td class="ar-compare-attr"><i class="las la-warehouse"></i> {{ __('Availability') }}</td>
            @foreach($compare_products as $product)
            @php $in_stock = optional($product->inventory)->stock_count > 0; @endphp
            <td class="ar-compare-val">
                @if($in_stock)
                    <span style="color:#2e7d32;font-weight:700;"><i class="las la-check-circle"></i> {{ __('In Stock') }}</span>
                @else
                    <span style="color:#c62828;font-weight:700;"><i class="las la-times-circle"></i> {{ __('Out of Stock') }}</span>
                @endif
            </td>
            @endforeach
        </tr>

        {{-- Action --}}
        <tr class="ar-compare-row ar-compare-row-alt">
            <td class="ar-compare-attr"></td>
            @foreach($compare_products as $product)
            <td class="ar-compare-val">
                <button class="ar-btn ar-btn-red ar-btn-sm add-to-cart-btn" data-product_id="{{ $product->id }}">
                    <i class="las la-cart-plus"></i> {{ __('Add to Cart') }}
                </button>
            </td>
            @endforeach
        </tr>
    </tbody>
</table>
</div>

<div style="margin-top:28px;">
    <a href="{{ theme_shop_url() }}" class="ar-btn ar-btn-outline">
        <i class="las la-arrow-left"></i> {{ __('Continue Shopping') }}
    </a>
</div>

@else

<div class="ar-compare-empty">
    <div class="ar-compare-empty-icon">
        <i class="las la-exchange-alt"></i>
    </div>
    <h3>{{ __('No Products to Compare') }}</h3>
    <p>{{ __('Add products to compare their features side by side.') }}</p>
    <a href="{{ theme_shop_url() }}" class="ar-btn ar-btn-red">
        <i class="las la-store"></i> {{ __('Browse Products') }}
    </a>
</div>

@endif

</div>
</div>

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
