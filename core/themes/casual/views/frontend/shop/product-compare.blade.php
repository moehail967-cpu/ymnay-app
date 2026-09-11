@extends('tenant.frontend.frontend-page-master')
@section('title') {{ __('Compare Products') }} @endsection
@section('page-title') {{ __('Compare Products') }} @endsection

@section('content')

<div class="cs-page-banner">
    <div class="container">
        <h1 class="cs-page-banner-title">{{ __('Compare Products') }}</h1>
        <div class="cs-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="cs-breadcrumb-sep"><i class="las la-angle-right"></i></span>
            <span class="cs-breadcrumb-current">{{ __('Compare') }}</span>
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

<div class="cs-compare-section">
    <div class="container">

        @if(count($compare_products) > 0)

        <div class="table-responsive cs-compare-table-wrap">
            <table class="cs-compare-table">
                <thead>
                    <tr>
                        <th class="cs-compare-head-label"></th>
                        @foreach($compare_products as $product)
                        <th class="cs-compare-head-cell">
                            @php $img = theme_product_image($product->image_id ?? null, 'grid'); @endphp
                            <div class="cs-compare-product-card">
                                <a href="{{ theme_product_url($product->slug) }}" class="cs-compare-img-link">
                                    @if($img)
                                        <img src="{{ $img }}" alt="{{ $product->name }}" class="cs-compare-product-img">
                                    @else
                                        <div class="cs-compare-img-placeholder"><i class="las la-shopping-bag"></i></div>
                                    @endif
                                </a>
                                <div class="cs-compare-product-name">
                                    <a href="{{ theme_product_url($product->slug) }}">{{ \Illuminate\Support\Str::words($product->name, 7) }}</a>
                                </div>
                                <button class="cs-compare-remove compare-remove-btn"
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
                    <tr class="cs-compare-row">
                        <td class="cs-compare-label"><i class="las la-tag"></i> {{ __('Price') }}</td>
                        @foreach($compare_products as $product)
                        @php $pdata = theme_product_price($product); @endphp
                        <td class="cs-compare-cell">
                            <div class="cs-compare-price-wrap">
                                <span class="cs-compare-price-accent">{{ amount_with_currency_symbol($pdata['sale_price']) }}</span>
                                @if($pdata['regular_price'] && $pdata['regular_price'] != $pdata['sale_price'])
                                    <span class="cs-compare-old-price">{{ amount_with_currency_symbol($pdata['regular_price']) }}</span>
                                @endif
                                @if($pdata['discount'])
                                    <span class="cs-compare-discount-badge">{{ $pdata['discount'] }}% {{ __('off') }}</span>
                                @endif
                            </div>
                        </td>
                        @endforeach
                    </tr>

                    {{-- Rating --}}
                    <tr class="cs-compare-row cs-compare-row-alt">
                        <td class="cs-compare-label"><i class="las la-star"></i> {{ __('Rating') }}</td>
                        @foreach($compare_products as $product)
                        <td class="cs-compare-cell">
                            {!! theme_star_rating($product) !!}
                        </td>
                        @endforeach
                    </tr>

                    {{-- SKU --}}
                    <tr class="cs-compare-row">
                        <td class="cs-compare-label"><i class="las la-barcode"></i> {{ __('SKU') }}</td>
                        @foreach($compare_products as $product)
                        <td class="cs-compare-cell cs-compare-muted">
                            {{ $product->inventory?->sku ?? '--' }}
                        </td>
                        @endforeach
                    </tr>

                    {{-- Category --}}
                    <tr class="cs-compare-row cs-compare-row-alt">
                        <td class="cs-compare-label"><i class="las la-th-large"></i> {{ __('Category') }}</td>
                        @foreach($compare_products as $product)
                        <td class="cs-compare-cell cs-compare-muted">
                            @if($product->category)
                                <a href="{{ theme_category_url($product->category->slug) }}" class="cs-compare-cat-link">
                                    {{ $product->category->name }}
                                </a>
                            @else
                                --
                            @endif
                        </td>
                        @endforeach
                    </tr>

                    {{-- Availability --}}
                    <tr class="cs-compare-row">
                        <td class="cs-compare-label"><i class="las la-boxes"></i> {{ __('Availability') }}</td>
                        @foreach($compare_products as $product)
                        @php $in_stock = theme_product_in_stock($product); @endphp
                        <td class="cs-compare-cell">
                            @if($in_stock)
                                <span class="cs-compare-in-stock"><i class="las la-check-circle"></i> {{ __('In Stock') }}</span>
                            @else
                                <span class="cs-compare-out-stock"><i class="las la-times-circle"></i> {{ __('Out of Stock') }}</span>
                            @endif
                        </td>
                        @endforeach
                    </tr>

                    {{-- Add to Cart --}}
                    <tr class="cs-compare-row cs-compare-row-alt cs-compare-row-atc">
                        <td class="cs-compare-label"></td>
                        @foreach($compare_products as $product)
                        <td class="cs-compare-cell">
                            <a href="{{ theme_product_url($product->slug) }}"
                               class="cs-compare-atc-btn">
                                <i class="las la-cart-plus"></i> {{ __('Add to Cart') }}
                            </a>
                        </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="cs-compare-footer">
            <a href="{{ theme_shop_url() }}" class="cs-compare-continue-btn">
                <i class="las la-arrow-left"></i> {{ __('Continue Shopping') }}
            </a>
        </div>

        @else

        <div class="cs-compare-empty-state">
            <div class="cs-compare-empty-icon">
                <i class="mdi mdi-compare-horizontal"></i>
            </div>
            <h2>{{ __('No Products to Compare') }}</h2>
            <p>{{ __('Browse our shop and add products to compare them side by side.') }}</p>
            <a href="{{ theme_shop_url() }}" class="cs-compare-atc-btn">
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
