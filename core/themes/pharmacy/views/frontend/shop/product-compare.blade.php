@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Compare Products') }} @endsection
@section('page-title') {{ __('Compare Products') }} @endsection

@section('content')
<div style="background:var(--pf-teal-light);padding:36px 0 28px;">
    <div class="container">
        <h2 style="font-size:26px;font-weight:700;color:var(--pf-dark);margin-bottom:8px;">{{ __('Compare Products') }}</h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--pf-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--pf-teal);font-weight:600;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right"></i>
            <span>{{ __('Compare') }}</span>
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

<div class="container" style="padding:40px 0 80px;">
@if(count($compare_products) > 0)

<div class="table-responsive">
    <table style="width:100%;border-collapse:collapse;background:var(--pf-white);">

        {{-- Images --}}
        <tr>
            <td style="width:160px;padding:16px;background:var(--pf-bg);border:1.5px solid var(--pf-border);font-size:13px;font-weight:700;color:var(--pf-dark);"></td>
            @foreach($compare_products as $product)
            @php $imgUrl = theme_product_image($product->image_id ?? null, 'grid'); @endphp
            <td style="padding:16px;text-align:center;border:1.5px solid var(--pf-border);vertical-align:top;">
                <div style="width:90px;height:90px;border-radius:var(--pf-radius);overflow:hidden;background:var(--pf-teal-light);margin:0 auto 10px;">
                    @if($imgUrl)
                        <img src="{{ $imgUrl }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:cover;">
                    @else
                        <div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:32px;"><i class="las la-capsules" style="color:var(--pf-teal);"></i></div>
                    @endif
                </div>
                <div style="font-size:14px;font-weight:700;color:var(--pf-dark);margin-bottom:6px;">
                    <a href="{{ theme_product_url($product->slug) }}" style="color:inherit;text-decoration:none;">{{ \Illuminate\Support\Str::words($product->name, 6) }}</a>
                </div>
                <button class="compare-remove-btn pf-btn pf-btn-ghost pf-btn-sm" data-product_id="{{ $compare_row_ids[$product->id] ?? '' }}" style="font-size:11px;">
                    <i class="las la-times"></i> {{ __('Remove') }}
                </button>
            </td>
            @endforeach
        </tr>

        {{-- Price --}}
        <tr>
            <td style="padding:14px 16px;background:var(--pf-bg);border:1.5px solid var(--pf-border);font-size:13px;font-weight:700;color:var(--pf-dark);">{{ __('Price') }}</td>
            @foreach($compare_products as $product)
            @php $pdata = theme_product_price($product); @endphp
            <td style="padding:14px 16px;text-align:center;border:1.5px solid var(--pf-border);">
                <span style="font-size:18px;font-weight:800;color:var(--pf-teal);">{{ amount_with_currency_symbol($pdata['sale_price']) }}</span>
                @if($pdata['regular_price'])
                    <span style="display:block;font-size:12px;color:var(--pf-muted);text-decoration:line-through;">{{ amount_with_currency_symbol($pdata['regular_price']) }}</span>
                @endif
            </td>
            @endforeach
        </tr>

        {{-- Rating --}}
        <tr>
            <td style="padding:14px 16px;background:var(--pf-bg);border:1.5px solid var(--pf-border);font-size:13px;font-weight:700;color:var(--pf-dark);">{{ __('Rating') }}</td>
            @foreach($compare_products as $product)
            <td style="padding:14px 16px;text-align:center;border:1.5px solid var(--pf-border);">
                {!! theme_star_rating($product) !!}
            </td>
            @endforeach
        </tr>

        {{-- SKU --}}
        <tr>
            <td style="padding:14px 16px;background:var(--pf-bg);border:1.5px solid var(--pf-border);font-size:13px;font-weight:700;color:var(--pf-dark);">{{ __('SKU') }}</td>
            @foreach($compare_products as $product)
            <td style="padding:14px 16px;text-align:center;border:1.5px solid var(--pf-border);font-size:13px;color:var(--pf-muted);">{{ $product->sku ?? '—' }}</td>
            @endforeach
        </tr>

        {{-- Category --}}
        <tr>
            <td style="padding:14px 16px;background:var(--pf-bg);border:1.5px solid var(--pf-border);font-size:13px;font-weight:700;color:var(--pf-dark);">{{ __('Category') }}</td>
            @foreach($compare_products as $product)
            <td style="padding:14px 16px;text-align:center;border:1.5px solid var(--pf-border);font-size:13px;color:var(--pf-muted);">{{ $product->category?->name ?? '—' }}</td>
            @endforeach
        </tr>

        {{-- Stock --}}
        <tr>
            <td style="padding:14px 16px;background:var(--pf-bg);border:1.5px solid var(--pf-border);font-size:13px;font-weight:700;color:var(--pf-dark);">{{ __('Availability') }}</td>
            @foreach($compare_products as $product)
            @php $in_stock = optional($product->inventory)->stock_count > 0; @endphp
            <td style="padding:14px 16px;text-align:center;border:1.5px solid var(--pf-border);font-size:13px;">
                @if($in_stock)
                    <span style="color:var(--pf-teal);font-weight:700;"><i class="las la-check-circle"></i> {{ __('In Stock') }}</span>
                @else
                    <span style="color:#e53e3e;font-weight:700;"><i class="las la-times-circle"></i> {{ __('Out of Stock') }}</span>
                @endif
            </td>
            @endforeach
        </tr>

        {{-- Add to Cart --}}
        <tr>
            <td style="padding:14px 16px;background:var(--pf-bg);border:1.5px solid var(--pf-border);font-size:13px;font-weight:700;color:var(--pf-dark);">{{ __('Action') }}</td>
            @foreach($compare_products as $product)
            <td style="padding:14px 16px;text-align:center;border:1.5px solid var(--pf-border);">
                <button class="add-to-cart-btn pf-btn pf-btn-teal pf-btn-sm" data-product_id="{{ $product->id }}">
                    <i class="las la-cart-plus"></i> {{ __('Add to Cart') }}
                </button>
            </td>
            @endforeach
        </tr>

    </table>
</div>

<div style="margin-top:24px;">
    <a href="{{ theme_shop_url() }}" class="pf-btn pf-btn-outline">
        <i class="las la-arrow-left"></i> {{ __('Continue Shopping') }}
    </a>
</div>

@else
<div style="text-align:center;padding:80px 20px;">
    <i class="las la-exchange-alt" style="font-size:64px;color:var(--pf-border);display:block;margin-bottom:20px;"></i>
    <h2 style="font-size:22px;font-weight:700;color:var(--pf-dark);margin-bottom:10px;">{{ __('No Products to Compare') }}</h2>
    <p style="font-size:14px;color:var(--pf-muted);margin-bottom:28px;">{{ __('Add products to compare their features side by side.') }}</p>
    <a href="{{ theme_shop_url() }}" class="pf-btn pf-btn-teal">
        <i class="las la-store"></i> {{ __('Go to Shop') }}
    </a>
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
