@extends('tenant.frontend.frontend-page-master')
@section('title') {{ __('Compare Products') }} @endsection
@section('page-title') {{ __('Compare Products') }} @endsection

@section('content')

<div class="el-page-banner">
    <div class="container">
        <h1 class="el-page-banner-title">{{ __('Compare Products') }}</h1>
        <nav style="margin-top:10px;display:flex;align-items:center;gap:8px;font-size:13px;color:rgba(255,255,255,.6);">
            <a href="{{ theme_home_url() }}" style="color:rgba(255,255,255,.7);text-decoration:none;">{{ __('Home') }}</a>
            <span>/</span>
            <span style="color:#fff;">{{ __('Compare') }}</span>
        </nav>
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

<div class="el-compare-section">
    <div class="container">

        @if(count($compare_products) > 0)

        <div class="table-responsive el-compare-table-wrap">
            <table class="el-compare-table">
                <thead>
                    <tr>
                        <th class="el-compare-head-label"></th>
                        @foreach($compare_products as $product)
                        <th class="el-compare-head-cell">
                            @php $img = theme_product_image($product->image_id ?? null, 'grid'); @endphp
                            <div class="el-compare-product-card">
                                <a href="{{ theme_product_url($product->slug) }}" class="el-compare-img-link">
                                    @if($img)
                                        <img src="{{ $img }}" alt="{{ $product->name }}" class="el-compare-product-img">
                                    @else
                                        <div class="el-compare-img-placeholder"><i class="las la-laptop"></i></div>
                                    @endif
                                </a>
                                <div class="el-compare-product-name">
                                    <a href="{{ theme_product_url($product->slug) }}">{{ \Illuminate\Support\Str::words($product->name, 7) }}</a>
                                </div>
                                <button class="el-compare-remove compare-remove-btn"
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
                    <tr class="el-compare-row">
                        <td class="el-compare-label"><i class="las la-tag"></i> {{ __('Price') }}</td>
                        @foreach($compare_products as $product)
                        @php $pdata = theme_product_price($product); @endphp
                        <td class="el-compare-cell">
                            <div class="el-compare-price-wrap">
                                <span class="el-compare-price">{{ amount_with_currency_symbol($pdata['sale_price']) }}</span>
                                @if($pdata['regular_price'] && $pdata['regular_price'] != $pdata['sale_price'])
                                    <span class="el-compare-old-price">{{ amount_with_currency_symbol($pdata['regular_price']) }}</span>
                                @endif
                                @if($pdata['discount'])
                                    <span class="el-compare-discount-badge">{{ $pdata['discount'] }}% {{ __('off') }}</span>
                                @endif
                            </div>
                        </td>
                        @endforeach
                    </tr>

                    {{-- Rating --}}
                    <tr class="el-compare-row el-compare-row-alt">
                        <td class="el-compare-label"><i class="las la-star"></i> {{ __('Rating') }}</td>
                        @foreach($compare_products as $product)
                        <td class="el-compare-cell">
                            {!! theme_star_rating($product) !!}
                        </td>
                        @endforeach
                    </tr>

                    {{-- SKU --}}
                    <tr class="el-compare-row">
                        <td class="el-compare-label"><i class="las la-barcode"></i> {{ __('SKU') }}</td>
                        @foreach($compare_products as $product)
                        <td class="el-compare-cell el-compare-muted">
                            {{ $product->inventory?->sku ?? '--' }}
                        </td>
                        @endforeach
                    </tr>

                    {{-- Category --}}
                    <tr class="el-compare-row el-compare-row-alt">
                        <td class="el-compare-label"><i class="las la-th-large"></i> {{ __('Category') }}</td>
                        @foreach($compare_products as $product)
                        <td class="el-compare-cell el-compare-muted">
                            @if($product->category)
                                <a href="{{ theme_category_url($product->category->slug) }}" class="el-compare-cat-link">
                                    {{ $product->category->name }}
                                </a>
                            @else
                                --
                            @endif
                        </td>
                        @endforeach
                    </tr>

                    {{-- Availability --}}
                    <tr class="el-compare-row">
                        <td class="el-compare-label"><i class="las la-boxes"></i> {{ __('Availability') }}</td>
                        @foreach($compare_products as $product)
                        @php $in_stock = theme_product_in_stock($product); @endphp
                        <td class="el-compare-cell">
                            @if($in_stock)
                                <span class="el-compare-in-stock"><i class="las la-check-circle"></i> {{ __('In Stock') }}</span>
                            @else
                                <span class="el-compare-out-stock"><i class="las la-times-circle"></i> {{ __('Out of Stock') }}</span>
                            @endif
                        </td>
                        @endforeach
                    </tr>

                    {{-- Add to Cart --}}
                    <tr class="el-compare-row el-compare-row-alt el-compare-row-atc">
                        <td class="el-compare-label"></td>
                        @foreach($compare_products as $product)
                        <td class="el-compare-cell">
                            <a href="{{ theme_product_url($product->slug) }}"
                               class="el-btn el-btn-primary el-btn-sm el-compare-atc-btn">
                                <i class="las la-cart-plus"></i> {{ __('Add to Cart') }}
                            </a>
                        </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="el-compare-footer">
            <a href="{{ theme_shop_url() }}" class="el-btn el-btn-ghost">
                <i class="las la-arrow-left"></i> {{ __('Continue Shopping') }}
            </a>
        </div>

        @else

        <div class="el-compare-empty">
            <div class="el-compare-empty-icon">
                <i class="mdi mdi-compare-horizontal"></i>
            </div>
            <h2>{{ __('No Products to Compare') }}</h2>
            <p>{{ __('Browse our shop and add products to compare them side by side.') }}</p>
            <a href="{{ theme_shop_url() }}" class="el-btn el-btn-primary">
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
