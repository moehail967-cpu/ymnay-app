@extends('tenant.frontend.frontend-page-master')
@section('title') {{ __('Compare Products') }} @endsection
@section('page-title') {{ __('Compare Products') }} @endsection

@section('content')

<div class="bp-page-banner">
    <div class="container">
        <h1>{{ __('Compare Products') }}</h1>
        <div class="bp-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span>/</span>
            <span class="current">{{ __('Compare') }}</span>
        </div>
    </div>
</div>

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

<div class="bp-compare-section">
    <div class="container">

        @if(count($compare_products) > 0)

        <div class="table-responsive bp-compare-table-wrap">
            <table class="bp-compare-table">
                <thead>
                    <tr>
                        <th class="bp-compare-head-label"></th>
                        @foreach($compare_products as $product)
                        <th class="bp-compare-head-cell">
                            @php $img = theme_product_image($product->image_id ?? null, 'grid'); @endphp
                            <div class="bp-compare-product-card">
                                <a href="{{ theme_product_url($product->slug) }}" class="bp-compare-img-link">
                                    @if($img)
                                        <img src="{{ $img }}" alt="{{ $product->name }}" class="bp-compare-product-img">
                                    @else
                                        <div class="bp-compare-img-placeholder"><i class="las la-book"></i></div>
                                    @endif
                                </a>
                                <div class="bp-compare-product-name">
                                    <a href="{{ theme_product_url($product->slug) }}">{{ \Illuminate\Support\Str::words($product->name, 7) }}</a>
                                </div>
                                <button class="bp-compare-remove compare-remove-btn"
                                        data-product_id="{{ $compare_row_ids[$product->id] ?? '' }}">
                                    <i class="las la-times"></i> {{ __('Remove') }}
                                </button>
                            </div>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    {{-- Price --}}
                    <tr class="bp-compare-row">
                        <td class="bp-compare-label"><i class="las la-tag"></i> {{ __('Price') }}</td>
                        @foreach($compare_products as $product)
                        @php $pdata = theme_product_price($product); @endphp
                        <td class="bp-compare-cell">
                            <div class="bp-compare-price-wrap">
                                <span class="bp-compare-price">{{ amount_with_currency_symbol($pdata['sale_price']) }}</span>
                                @if($pdata['regular_price'] && $pdata['regular_price'] != $pdata['sale_price'])
                                    <span class="bp-compare-old-price">{{ amount_with_currency_symbol($pdata['regular_price']) }}</span>
                                @endif
                                @if($pdata['discount'])
                                    <span class="bp-compare-discount-badge">{{ $pdata['discount'] }}% {{ __('off') }}</span>
                                @endif
                            </div>
                        </td>
                        @endforeach
                    </tr>

                    {{-- Rating --}}
                    <tr class="bp-compare-row bp-compare-row-alt">
                        <td class="bp-compare-label"><i class="las la-star"></i> {{ __('Rating') }}</td>
                        @foreach($compare_products as $product)
                        <td class="bp-compare-cell">
                            {!! theme_star_rating($product) !!}
                        </td>
                        @endforeach
                    </tr>

                    {{-- SKU --}}
                    <tr class="bp-compare-row">
                        <td class="bp-compare-label"><i class="las la-barcode"></i> {{ __('SKU') }}</td>
                        @foreach($compare_products as $product)
                        <td class="bp-compare-cell bp-compare-muted">
                            {{ $product->inventory?->sku ?? '--' }}
                        </td>
                        @endforeach
                    </tr>

                    {{-- Category --}}
                    <tr class="bp-compare-row bp-compare-row-alt">
                        <td class="bp-compare-label"><i class="las la-th-large"></i> {{ __('Category') }}</td>
                        @foreach($compare_products as $product)
                        <td class="bp-compare-cell bp-compare-muted">
                            @if($product->category)
                                <a href="{{ theme_category_url($product->category->slug) }}" class="bp-compare-cat-link">
                                    {{ $product->category->name }}
                                </a>
                            @else
                                --
                            @endif
                        </td>
                        @endforeach
                    </tr>

                    {{-- Availability --}}
                    <tr class="bp-compare-row">
                        <td class="bp-compare-label"><i class="las la-boxes"></i> {{ __('Availability') }}</td>
                        @foreach($compare_products as $product)
                        @php $in_stock = theme_product_in_stock($product); @endphp
                        <td class="bp-compare-cell">
                            @if($in_stock)
                                <span class="bp-compare-in-stock"><i class="las la-check-circle"></i> {{ __('In Stock') }}</span>
                            @else
                                <span class="bp-compare-out-stock"><i class="las la-times-circle"></i> {{ __('Out of Stock') }}</span>
                            @endif
                        </td>
                        @endforeach
                    </tr>

                    {{-- Add to Cart --}}
                    <tr class="bp-compare-row bp-compare-row-alt bp-compare-row-atc">
                        <td class="bp-compare-label"></td>
                        @foreach($compare_products as $product)
                        <td class="bp-compare-cell">
                            <a href="{{ theme_product_url($product->slug) }}"
                               class="bp-btn bp-btn-green bp-btn-sm bp-compare-atc-btn">
                                <i class="las la-cart-plus"></i> {{ __('Add to Cart') }}
                            </a>
                        </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="bp-compare-footer">
            <a href="{{ theme_shop_url() }}" class="bp-btn bp-btn-outline">
                <i class="las la-arrow-left"></i> {{ __('Continue Shopping') }}
            </a>
        </div>

        @else

        <div class="bp-compare-empty">
            <div class="bp-compare-empty-icon">
                <i class="mdi mdi-compare-horizontal"></i>
            </div>
            <span class="bp-compare-empty-tag">{{ __('Nothing here yet') }}</span>
            <h2>{{ __('No Products to Compare') }}</h2>
            <p>{{ __('Browse our shop and add products to compare them side by side.') }}</p>
            <a href="{{ theme_shop_url() }}" class="bp-btn bp-btn-green">
                <i class="las la-store"></i> {{ __('Browse Products') }}
            </a>
        </div>

        @endif

    </div>
</div>

@endsection

@section('scripts')
<script>
$(function () {
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
});
</script>
@endsection
