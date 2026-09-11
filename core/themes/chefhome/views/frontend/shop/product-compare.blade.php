@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Compare Products') }} @endsection
@section('page-title') {{ __('Compare Products') }} @endsection

@section('content')
<div class="ch-page-banner">
    <div class="container ch-page-banner-content">
        <h1>{{ __('Compare Products') }}</h1>
        <div class="ch-breadcrumb" style="margin-top:8px;">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
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

<div class="container" style="padding:36px 0 72px;">
    @if(count($compare_products) > 0)
    <div class="table-responsive">
        <table class="ch-compare-table" style="width:100%;border-collapse:collapse;background:#fff;border:1px solid var(--ch-border);border-radius:var(--ch-radius);overflow:hidden;">
            <thead>
                <tr style="border-bottom:1px solid var(--ch-border);">
                    <th style="padding:20px;font-weight:700;color:var(--ch-dark);background:#f8f8f8;width:160px;text-align:left;font-size:12px;text-transform:uppercase;letter-spacing:.5px;"></th>
                    @foreach($compare_products as $product)
                    <th style="padding:20px;text-align:center;border-left:1px solid var(--ch-border);vertical-align:top;font-weight:400;">
                        @php $img = theme_product_image($product->image_id ?? null, 'grid'); @endphp
                        <div style="display:flex;flex-direction:column;align-items:center;gap:10px;">
                            <div style="width:110px;height:110px;overflow:hidden;border-radius:var(--ch-radius-sm);border:1px solid var(--ch-border);background:var(--ch-warm);">
                                @if($img)
                                    <img src="{{ $img }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    <div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:36px;color:var(--ch-border);"><i class="las la-utensils"></i></div>
                                @endif
                            </div>
                            <a href="{{ theme_product_url($product->slug) }}" style="font-weight:700;color:var(--ch-dark);text-decoration:none;font-size:14px;">{{ \Illuminate\Support\Str::words($product->name, 6) }}</a>
                            <button class="compare-remove-btn ch-btn ch-btn-sm" data-product_id="{{ $compare_row_ids[$product->id] ?? '' }}"
                                    style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;border:1px solid var(--ch-border);background:transparent;border-radius:var(--ch-radius-sm);font-size:11px;color:var(--ch-muted);cursor:pointer;">
                                <i class="las la-times"></i> {{ __('Remove') }}
                            </button>
                        </div>
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                {{-- Price --}}
                <tr style="border-bottom:1px solid var(--ch-border);">
                    <td style="padding:16px 20px;font-weight:700;color:var(--ch-dark);background:#f8f8f8;font-size:12px;text-transform:uppercase;letter-spacing:.5px;">{{ __('Price') }}</td>
                    @foreach($compare_products as $product)
                    @php $pdata = theme_product_price($product); @endphp
                    <td style="padding:16px 20px;border-left:1px solid var(--ch-border);text-align:center;">
                        <span style="font-size:17px;font-weight:900;color:var(--ch-red);">{{ amount_with_currency_symbol($pdata['sale_price']) }}</span>
                        @if($pdata['regular_price'])
                            <span style="display:block;font-size:12px;color:var(--ch-muted);text-decoration:line-through;">{{ amount_with_currency_symbol($pdata['regular_price']) }}</span>
                        @endif
                    </td>
                    @endforeach
                </tr>

                {{-- Rating --}}
                <tr style="border-bottom:1px solid var(--ch-border);">
                    <td style="padding:16px 20px;font-weight:700;color:var(--ch-dark);background:#f8f8f8;font-size:12px;text-transform:uppercase;letter-spacing:.5px;">{{ __('Rating') }}</td>
                    @foreach($compare_products as $product)
                    <td style="padding:16px 20px;border-left:1px solid var(--ch-border);text-align:center;">
                        {!! theme_star_rating($product) !!}
                    </td>
                    @endforeach
                </tr>

                {{-- SKU --}}
                <tr style="border-bottom:1px solid var(--ch-border);">
                    <td style="padding:16px 20px;font-weight:700;color:var(--ch-dark);background:#f8f8f8;font-size:12px;text-transform:uppercase;letter-spacing:.5px;">{{ __('SKU') }}</td>
                    @foreach($compare_products as $product)
                    <td style="padding:16px 20px;border-left:1px solid var(--ch-border);text-align:center;color:var(--ch-muted);font-size:13px;">{{ $product->sku ?? '—' }}</td>
                    @endforeach
                </tr>

                {{-- Category --}}
                <tr style="border-bottom:1px solid var(--ch-border);">
                    <td style="padding:16px 20px;font-weight:700;color:var(--ch-dark);background:#f8f8f8;font-size:12px;text-transform:uppercase;letter-spacing:.5px;">{{ __('Category') }}</td>
                    @foreach($compare_products as $product)
                    <td style="padding:16px 20px;border-left:1px solid var(--ch-border);text-align:center;color:var(--ch-muted);font-size:13px;">{{ $product->category?->name ?? '—' }}</td>
                    @endforeach
                </tr>

                {{-- Availability --}}
                <tr style="border-bottom:1px solid var(--ch-border);">
                    <td style="padding:16px 20px;font-weight:700;color:var(--ch-dark);background:#f8f8f8;font-size:12px;text-transform:uppercase;letter-spacing:.5px;">{{ __('Availability') }}</td>
                    @foreach($compare_products as $product)
                    @php $in_stock = optional($product->inventory)->stock_count > 0; @endphp
                    <td style="padding:16px 20px;border-left:1px solid var(--ch-border);text-align:center;">
                        @if($in_stock)
                            <span style="color:#2e7d32;font-weight:700;font-size:13px;"><i class="las la-check-circle"></i> {{ __('In Stock') }}</span>
                        @else
                            <span style="color:#c62828;font-weight:700;font-size:13px;"><i class="las la-times-circle"></i> {{ __('Out of Stock') }}</span>
                        @endif
                    </td>
                    @endforeach
                </tr>

                {{-- Action --}}
                <tr>
                    <td style="padding:16px 20px;background:#f8f8f8;"></td>
                    @foreach($compare_products as $product)
                    <td style="padding:16px 20px;border-left:1px solid var(--ch-border);text-align:center;">
                        <button class="ch-btn ch-btn-red ch-btn-sm add-to-cart-btn" data-product_id="{{ $product->id }}"
                                style="display:inline-flex;align-items:center;gap:6px;">
                            <i class="las la-cart-plus"></i> {{ __('Add to Cart') }}
                        </button>
                    </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>

    <div style="margin-top:28px;">
        <a href="{{ theme_shop_url() }}" class="ch-btn ch-btn-ghost">
            <i class="las la-arrow-left"></i> {{ __('Continue Shopping') }}
        </a>
    </div>

    @else
    <div style="text-align:center;padding:60px 20px;">
        <div style="font-size:56px;margin-bottom:16px;color:var(--ch-border);"><i class="las la-exchange-alt"></i></div>
        <h2 style="font-size:20px;font-weight:900;color:var(--ch-dark);margin-bottom:12px;">{{ __('No products to compare') }}</h2>
        <p style="color:var(--ch-muted);margin-bottom:24px;">{{ __('Add products to compare them side by side') }}</p>
        <a href="{{ theme_shop_url() }}" class="ch-btn ch-btn-red">{{ __('Browse Products') }}</a>
    </div>
    @endif
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
