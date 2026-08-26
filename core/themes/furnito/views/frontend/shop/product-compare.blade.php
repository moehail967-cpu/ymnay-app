@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Compare Products') }} @endsection
@section('page-title') {{ __('Compare Products') }} @endsection

@section('content')

<div class="fn-page-banner">
    <div class="container">
        <h1>{{ __('Compare Products') }}</h1>
        <div class="fn-breadcrumb" style="margin-top:10px;display:flex;align-items:center;gap:8px;font-size:13px;">
            <a href="{{ theme_home_url() }}" style="color:rgba(255,255,255,.75);text-decoration:none;">{{ __('Home') }}</a>
            <span style="color:rgba(255,255,255,.4);">/</span>
            <span style="color:#fff;">{{ __('Compare') }}</span>
        </div>
    </div>
</div>

<div style="background:#f8f7f4;padding:48px 0 80px;">
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
<table class="fn-compare-table">
    <thead>
        <tr>
            <td class="fn-compare-label-col"></td>
            @foreach($compare_products as $product)
            <th class="fn-compare-product-col">
                <div class="fn-compare-product-head">
                    <div class="fn-compare-thumb">
                        @php $imgUrl = theme_product_image($product->image_id ?? null, 'grid'); @endphp
                        @if($imgUrl)
                            <img src="{{ $imgUrl }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:cover;">
                        @else
                            <div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:36px;color:var(--fn-muted);"><i class="las la-couch"></i></div>
                        @endif
                    </div>
                    <a href="{{ theme_product_url($product->slug) }}" class="fn-compare-product-name">{{ \Illuminate\Support\Str::words($product->name, 6) }}</a>
                    <button class="fn-compare-remove compare-remove-btn" data-product_id="{{ $compare_row_ids[$product->id] ?? '' }}">
                        <i class="las la-times"></i> {{ __('Remove') }}
                    </button>
                </div>
            </th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        {{-- Price --}}
        <tr class="fn-compare-row">
            <td class="fn-compare-attr"><i class="las la-tag"></i> {{ __('Price') }}</td>
            @foreach($compare_products as $product)
            @php $pdata = theme_product_price($product); @endphp
            <td class="fn-compare-val">
                <span class="fn-compare-price">{{ amount_with_currency_symbol($pdata['sale_price']) }}</span>
                @if($pdata['regular_price'])
                    <span style="display:block;font-size:12px;color:var(--fn-muted);text-decoration:line-through;">{{ amount_with_currency_symbol($pdata['regular_price']) }}</span>
                @endif
            </td>
            @endforeach
        </tr>

        {{-- Rating --}}
        <tr class="fn-compare-row fn-compare-row-alt">
            <td class="fn-compare-attr"><i class="las la-star"></i> {{ __('Rating') }}</td>
            @foreach($compare_products as $product)
            <td class="fn-compare-val">{!! theme_star_rating($product) !!}</td>
            @endforeach
        </tr>

        {{-- SKU --}}
        <tr class="fn-compare-row">
            <td class="fn-compare-attr"><i class="las la-barcode"></i> {{ __('SKU') }}</td>
            @foreach($compare_products as $product)
            <td class="fn-compare-val fn-compare-muted">{{ $product->sku ?? '—' }}</td>
            @endforeach
        </tr>

        {{-- Category --}}
        <tr class="fn-compare-row fn-compare-row-alt">
            <td class="fn-compare-attr"><i class="las la-folder"></i> {{ __('Category') }}</td>
            @foreach($compare_products as $product)
            <td class="fn-compare-val fn-compare-muted">{{ $product->category?->name ?? '—' }}</td>
            @endforeach
        </tr>

        {{-- Availability --}}
        <tr class="fn-compare-row">
            <td class="fn-compare-attr"><i class="las la-warehouse"></i> {{ __('Availability') }}</td>
            @foreach($compare_products as $product)
            @php $in_stock = optional($product->inventory)->stock_count > 0; @endphp
            <td class="fn-compare-val">
                @if($in_stock)
                    <span style="color:#2e7d32;font-weight:700;"><i class="las la-check-circle"></i> {{ __('In Stock') }}</span>
                @else
                    <span style="color:#c62828;font-weight:700;"><i class="las la-times-circle"></i> {{ __('Out of Stock') }}</span>
                @endif
            </td>
            @endforeach
        </tr>

        {{-- Action --}}
        <tr class="fn-compare-row fn-compare-row-alt">
            <td class="fn-compare-attr"></td>
            @foreach($compare_products as $product)
            <td class="fn-compare-val">
                <button class="fn-btn fn-btn-gold fn-btn-sm add-to-cart-btn" data-product_id="{{ $product->id }}">
                    <i class="las la-cart-plus"></i> {{ __('Add to Cart') }}
                </button>
            </td>
            @endforeach
        </tr>
    </tbody>
</table>
</div>

<div style="margin-top:28px;">
    <a href="{{ theme_shop_url() }}" class="fn-btn fn-btn-outline">
        <i class="las la-arrow-left"></i> {{ __('Continue Shopping') }}
    </a>
</div>

@else

<div class="fn-compare-empty">
    <div class="fn-compare-empty-icon">
        <i class="las la-exchange-alt"></i>
    </div>
    <h3>{{ __('No Products to Compare') }}</h3>
    <p>{{ __('Add products to compare their features side by side.') }}</p>
    <a href="{{ theme_shop_url() }}" class="fn-btn fn-btn-gold">
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
