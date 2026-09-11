@extends('tenant.frontend.frontend-page-master')
@section('title') {{ __('Compare Products') }} @endsection
@section('page-title') {{ __('Compare Products') }} @endsection

@section('content')

<div class="tn-page-banner">
    <div class="container">
        <h1>{{ __('Compare Products') }}</h1>
        <nav style="margin-top:8px;display:flex;align-items:center;gap:8px;font-size:13px;color:var(--tn-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--tn-muted);text-decoration:none;">{{ __('Home') }}</a>
            <span>/</span>
            <span style="color:var(--tn-purple);font-weight:600;">{{ __('Compare') }}</span>
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

<div class="tn-compare-section">
    <div class="container">

        @if(count($compare_products) > 0)

        <div class="table-responsive tn-compare-table-wrap">
            <table class="tn-compare-table">
                <thead>
                    <tr>
                        <th class="tn-compare-head-label"></th>
                        @foreach($compare_products as $product)
                        <th class="tn-compare-head-cell">
                            @php $img = theme_product_image($product->image_id ?? null, 'grid'); @endphp
                            <div class="tn-compare-product-card">
                                <a href="{{ theme_product_url($product->slug) }}" class="tn-compare-img-link">
                                    @if($img)
                                        <img src="{{ $img }}" alt="{{ $product->name }}" class="tn-compare-product-img">
                                    @else
                                        <div class="tn-compare-img-placeholder"><i class="las la-box"></i></div>
                                    @endif
                                </a>
                                <div class="tn-compare-product-name">
                                    <a href="{{ theme_product_url($product->slug) }}">{{ \Illuminate\Support\Str::words($product->name, 7) }}</a>
                                </div>
                                <button class="tn-compare-remove compare-remove-btn"
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
                    <tr class="tn-compare-row">
                        <td class="tn-compare-label"><i class="las la-tag"></i> {{ __('Price') }}</td>
                        @foreach($compare_products as $product)
                        @php $pdata = theme_product_price($product); @endphp
                        <td class="tn-compare-cell">
                            <div class="tn-compare-price-wrap">
                                <span class="tn-compare-price">{{ amount_with_currency_symbol($pdata['sale_price']) }}</span>
                                @if($pdata['regular_price'] && $pdata['regular_price'] != $pdata['sale_price'])
                                    <span class="tn-compare-old-price">{{ amount_with_currency_symbol($pdata['regular_price']) }}</span>
                                @endif
                                @if($pdata['discount'])
                                    <span class="tn-compare-discount-badge">{{ $pdata['discount'] }}% {{ __('off') }}</span>
                                @endif
                            </div>
                        </td>
                        @endforeach
                    </tr>

                    {{-- Rating --}}
                    <tr class="tn-compare-row tn-compare-row-alt">
                        <td class="tn-compare-label"><i class="las la-star"></i> {{ __('Rating') }}</td>
                        @foreach($compare_products as $product)
                        <td class="tn-compare-cell">
                            {!! theme_star_rating($product) !!}
                        </td>
                        @endforeach
                    </tr>

                    {{-- SKU --}}
                    <tr class="tn-compare-row">
                        <td class="tn-compare-label"><i class="las la-barcode"></i> {{ __('SKU') }}</td>
                        @foreach($compare_products as $product)
                        <td class="tn-compare-cell tn-compare-muted">
                            {{ $product->inventory?->sku ?? '--' }}
                        </td>
                        @endforeach
                    </tr>

                    {{-- Category --}}
                    <tr class="tn-compare-row tn-compare-row-alt">
                        <td class="tn-compare-label"><i class="las la-th-large"></i> {{ __('Category') }}</td>
                        @foreach($compare_products as $product)
                        <td class="tn-compare-cell tn-compare-muted">
                            @if($product->category)
                                <a href="{{ theme_category_url($product->category->slug) }}" class="tn-compare-cat-link">
                                    {{ $product->category->name }}
                                </a>
                            @else
                                --
                            @endif
                        </td>
                        @endforeach
                    </tr>

                    {{-- Availability --}}
                    <tr class="tn-compare-row">
                        <td class="tn-compare-label"><i class="las la-boxes"></i> {{ __('Availability') }}</td>
                        @foreach($compare_products as $product)
                        @php $in_stock = theme_product_in_stock($product); @endphp
                        <td class="tn-compare-cell">
                            @if($in_stock)
                                <span class="tn-compare-in-stock"><i class="las la-check-circle"></i> {{ __('In Stock') }}</span>
                            @else
                                <span class="tn-compare-out-stock"><i class="las la-times-circle"></i> {{ __('Out of Stock') }}</span>
                            @endif
                        </td>
                        @endforeach
                    </tr>

                    {{-- Add to Cart --}}
                    <tr class="tn-compare-row tn-compare-row-alt tn-compare-row-atc">
                        <td class="tn-compare-label"></td>
                        @foreach($compare_products as $product)
                        <td class="tn-compare-cell">
                            <a href="{{ theme_product_url($product->slug) }}"
                               class="tn-btn tn-btn-primary tn-btn-sm tn-compare-atc-btn">
                                <i class="las la-cart-plus"></i> {{ __('Add to Cart') }}
                            </a>
                        </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="tn-compare-footer">
            <a href="{{ theme_shop_url() }}" class="tn-btn tn-btn-outline">
                <i class="las la-arrow-left"></i> {{ __('Continue Shopping') }}
            </a>
        </div>

        @else

        <div class="tn-compare-empty">
            <div class="tn-compare-empty-icon">
                <i class="mdi mdi-compare-horizontal"></i>
            </div>
            <h2>{{ __('No Products to Compare') }}</h2>
            <p>{{ __('Browse our shop and add products to compare them side by side.') }}</p>
            <a href="{{ theme_shop_url() }}" class="tn-btn tn-btn-primary">
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
