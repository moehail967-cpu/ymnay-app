@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Compare Products') }} @endsection
@section('page-title') {{ __('Compare Products') }} @endsection

@section('content')

<div class="bk-page-banner">
    <div class="container">
        <h1>{{ __('Compare Products') }}</h1>
        <nav class="bk-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="bk-breadcrumb-sep">/</span>
            <span class="bk-breadcrumb-current">{{ __('Compare') }}</span>
        </nav>
    </div>
</div>

@php
    $compare_items    = \Gloudemans\Shoppingcart\Facades\Cart::instance("compare")->content();
    $compare_products = [];
    $compare_row_ids  = [];
    foreach ($compare_items as $item) {
        $p = \Modules\Product\Entities\Product::with(['badge', 'category', 'inventory', 'campaign_product'])->find($item->id);
        if ($p) {
            $compare_products[]      = $p;
            $compare_row_ids[$p->id] = $item->rowId;
        }
    }
@endphp

<div class="bk-compare-wrap">
    <div class="container">
        @if(count($compare_products) > 0)

        <div class="bk-compare-table-wrap">
            <div style="overflow-x:auto;">
            <table class="bk-compare-table">
                <thead>
                    <tr>
                        <th class="bk-compare-head-label"></th>
                        @foreach($compare_products as $product)
                        <th class="bk-compare-head-cell">
                            <div class="bk-compare-product-card">
                                @php $img = theme_product_image($product->image_id ?? null, 'grid'); @endphp
                                <a href="{{ theme_product_url($product->slug) }}" class="bk-compare-img-link">
                                    @if($img)
                                        <img src="{{ $img }}" alt="{{ $product->name }}" class="bk-compare-product-img">
                                    @else
                                        <div class="bk-compare-img-placeholder"><i class="las la-bread-slice"></i></div>
                                    @endif
                                </a>
                                <div class="bk-compare-product-name">
                                    <a href="{{ theme_product_url($product->slug) }}">{{ \Illuminate\Support\Str::words($product->name, 6) }}</a>
                                </div>
                                <button class="bk-compare-remove compare-remove-btn"
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
                    <tr class="bk-compare-row">
                        <th class="bk-compare-label">{{ __('Price') }}</th>
                        @foreach($compare_products as $product)
                        @php $pdata = theme_product_price($product); @endphp
                        <td class="bk-compare-cell">
                            <div class="bk-compare-price-wrap">
                                <span class="bk-compare-price">{{ amount_with_currency_symbol($pdata['sale_price']) }}</span>
                                @if($pdata['regular_price'])
                                    <span class="bk-compare-old-price">{{ amount_with_currency_symbol($pdata['regular_price']) }}</span>
                                @endif
                                @if($pdata['discount'])
                                    <span class="bk-compare-discount-badge">{{ $pdata['discount'] }}% {{ __('off') }}</span>
                                @endif
                            </div>
                        </td>
                        @endforeach
                    </tr>

                    {{-- Rating --}}
                    <tr class="bk-compare-row">
                        <th class="bk-compare-label">{{ __('Rating') }}</th>
                        @foreach($compare_products as $product)
                        <td class="bk-compare-cell">
                            {!! theme_star_rating($product) !!}
                        </td>
                        @endforeach
                    </tr>

                    {{-- SKU --}}
                    <tr class="bk-compare-row">
                        <th class="bk-compare-label">{{ __('SKU') }}</th>
                        @foreach($compare_products as $product)
                        <td class="bk-compare-cell bk-compare-muted">
                            {{ $product->sku ?? '--' }}
                        </td>
                        @endforeach
                    </tr>

                    {{-- Category --}}
                    <tr class="bk-compare-row">
                        <th class="bk-compare-label">{{ __('Category') }}</th>
                        @foreach($compare_products as $product)
                        <td class="bk-compare-cell bk-compare-muted">
                            {{ $product->category?->name ?? '--' }}
                        </td>
                        @endforeach
                    </tr>

                    {{-- Stock --}}
                    <tr class="bk-compare-row">
                        <th class="bk-compare-label">{{ __('Availability') }}</th>
                        @foreach($compare_products as $product)
                        @php $stock = optional($product->inventory)->stock_count ?? 0; @endphp
                        <td class="bk-compare-cell">
                            @if($stock > 0)
                                <span class="bk-compare-in-stock"><i class="las la-check-circle"></i> {{ __('In Stock') }}</span>
                            @else
                                <span class="bk-compare-out-stock"><i class="las la-times-circle"></i> {{ __('Out of Stock') }}</span>
                            @endif
                        </td>
                        @endforeach
                    </tr>

                    {{-- Add to Cart --}}
                    <tr class="bk-compare-row-atc">
                        <th class="bk-compare-label"></th>
                        @foreach($compare_products as $product)
                        <td class="bk-compare-cell">
                            <button class="add-to-cart-btn bk-btn bk-btn-primary bk-compare-atc bk-compare-atc-btn"
                                    data-product_id="{{ $product->id }}">
                                <i class="las la-cart-plus"></i> {{ __('Add to Cart') }}
                            </button>
                        </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
            </div>
        </div>

        <div class="bk-compare-footer">
            <a href="{{ theme_shop_url() }}" class="bk-btn bk-btn-outline">
                <i class="las la-arrow-left"></i> {{ __('Continue Shopping') }}
            </a>
        </div>

        @else
        <div class="bk-compare-empty">
            <div class="bk-compare-empty-icon"><i class="las la-balance-scale"></i></div>
            <h2>{{ __('No Products to Compare') }}</h2>
            <p>{{ __('Browse our shop and add products to compare them side by side.') }}</p>
            <a href="{{ theme_shop_url() }}" class="bk-btn bk-btn-primary">{{ __('Browse Products') }}</a>
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
