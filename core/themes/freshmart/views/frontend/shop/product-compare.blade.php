@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Compare Products') }} @endsection
@section('page-title') {{ __('Compare') }} @endsection

@section('content')
<div class="fm-page-banner">
    <div class="container">
        <h1 style="font-size:28px;font-weight:800;color:var(--fm-dark);margin-bottom:8px;">{{ __('Compare Products') }}</h1>
        <div class="fm-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep"><i class="las la-angle-right" style="font-size:11px;"></i></span>
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

<div class="container" style="padding:48px 0 72px;">
    @if(count($compare_products) > 0)
    <div class="table-responsive">
        <table style="width:100%;border-collapse:collapse;min-width:600px;">

            {{-- Header: product images + names + remove --}}
            <tr>
                <td style="width:160px;padding:16px;background:var(--fm-surface);border:1px solid var(--fm-border);font-size:12px;font-weight:700;color:var(--fm-green);text-transform:uppercase;letter-spacing:1px;"></td>
                @foreach($compare_products as $product)
                @php $imgUrl = theme_product_image($product->image_id ?? null, 'grid'); @endphp
                <td style="padding:16px;background:#fff;border:1px solid var(--fm-border);text-align:center;vertical-align:top;">
                    <div style="width:110px;height:110px;margin:0 auto 12px;border:1px solid var(--fm-border);border-radius:10px;overflow:hidden;background:var(--fm-surface);">
                        @if($imgUrl)
                            <img src="{{ $imgUrl }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:cover;">
                        @else
                            <div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:36px;color:var(--fm-border);"><i class="las la-leaf"></i></div>
                        @endif
                    </div>
                    <a href="{{ theme_product_url($product->slug) }}" style="font-size:14px;font-weight:700;color:var(--fm-dark);text-decoration:none;display:block;margin-bottom:10px;">{{ \Illuminate\Support\Str::words($product->name, 6) }}</a>
                    <button class="compare-remove-btn" data-product_id="{{ $compare_row_ids[$product->id] ?? '' }}"
                            style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;background:transparent;border:1px solid var(--fm-border);border-radius:6px;font-size:11px;color:var(--fm-muted);cursor:pointer;">
                        <i class="las la-times"></i> {{ __('Remove') }}
                    </button>
                </td>
                @endforeach
            </tr>

            {{-- Price --}}
            <tr>
                <td style="padding:12px 16px;background:var(--fm-surface);border:1px solid var(--fm-border);font-size:12px;font-weight:700;color:var(--fm-muted);text-transform:uppercase;letter-spacing:.8px;">{{ __('Price') }}</td>
                @foreach($compare_products as $product)
                @php $pdata = theme_product_price($product); @endphp
                <td style="padding:12px 16px;border:1px solid var(--fm-border);text-align:center;background:#fff;">
                    <span style="font-size:17px;font-weight:800;color:var(--fm-green);">{{ amount_with_currency_symbol($pdata['sale_price']) }}</span>
                    @if($pdata['regular_price'])
                        <span style="display:block;font-size:12px;color:var(--fm-muted);text-decoration:line-through;">{{ amount_with_currency_symbol($pdata['regular_price']) }}</span>
                    @endif
                </td>
                @endforeach
            </tr>

            {{-- Rating --}}
            <tr>
                <td style="padding:12px 16px;background:var(--fm-surface);border:1px solid var(--fm-border);font-size:12px;font-weight:700;color:var(--fm-muted);text-transform:uppercase;letter-spacing:.8px;">{{ __('Rating') }}</td>
                @foreach($compare_products as $product)
                <td style="padding:12px 16px;border:1px solid var(--fm-border);text-align:center;background:#fff;">
                    {!! theme_star_rating($product) !!}
                </td>
                @endforeach
            </tr>

            {{-- SKU --}}
            <tr>
                <td style="padding:12px 16px;background:var(--fm-surface);border:1px solid var(--fm-border);font-size:12px;font-weight:700;color:var(--fm-muted);text-transform:uppercase;letter-spacing:.8px;">{{ __('SKU') }}</td>
                @foreach($compare_products as $product)
                <td style="padding:12px 16px;border:1px solid var(--fm-border);text-align:center;background:#fff;color:var(--fm-muted);font-size:13px;">{{ $product->sku ?? '—' }}</td>
                @endforeach
            </tr>

            {{-- Category --}}
            <tr>
                <td style="padding:12px 16px;background:var(--fm-surface);border:1px solid var(--fm-border);font-size:12px;font-weight:700;color:var(--fm-muted);text-transform:uppercase;letter-spacing:.8px;">{{ __('Category') }}</td>
                @foreach($compare_products as $product)
                <td style="padding:12px 16px;border:1px solid var(--fm-border);text-align:center;background:#fff;color:var(--fm-muted);font-size:13px;">{{ $product->category?->name ?? '—' }}</td>
                @endforeach
            </tr>

            {{-- Availability --}}
            <tr>
                <td style="padding:12px 16px;background:var(--fm-surface);border:1px solid var(--fm-border);font-size:12px;font-weight:700;color:var(--fm-muted);text-transform:uppercase;letter-spacing:.8px;">{{ __('Availability') }}</td>
                @foreach($compare_products as $product)
                @php $in_stock = optional($product->inventory)->stock_count > 0; @endphp
                <td style="padding:12px 16px;border:1px solid var(--fm-border);text-align:center;background:#fff;font-size:13px;">
                    @if($in_stock)
                        <span style="color:#2e7d32;font-weight:700;"><i class="las la-check-circle"></i> {{ __('In Stock') }}</span>
                    @else
                        <span style="color:#c62828;font-weight:700;"><i class="las la-times-circle"></i> {{ __('Out of Stock') }}</span>
                    @endif
                </td>
                @endforeach
            </tr>

            {{-- Add to cart row --}}
            <tr>
                <td style="padding:12px 16px;background:var(--fm-surface);border:1px solid var(--fm-border);"></td>
                @foreach($compare_products as $product)
                <td style="padding:12px 16px;border:1px solid var(--fm-border);text-align:center;background:#fff;">
                    <button class="fm-btn fm-btn-green fm-btn-sm add-to-cart-btn" data-product_id="{{ $product->id }}">
                        <i class="las la-cart-plus"></i> {{ __('Add to Cart') }}
                    </button>
                </td>
                @endforeach
            </tr>

        </table>
    </div>

    <div style="margin-top:24px;">
        <a href="{{ theme_shop_url() }}" class="fm-btn fm-btn-outline">
            <i class="las la-arrow-left"></i> {{ __('Continue Shopping') }}
        </a>
    </div>

    @else
    <div style="text-align:center;padding:80px 0;">
        <i class="las la-not-equal" style="font-size:72px;color:var(--fm-border);display:block;margin-bottom:20px;"></i>
        <h2 style="font-size:26px;font-weight:800;color:var(--fm-dark);margin-bottom:10px;">{{ __('Nothing to Compare') }}</h2>
        <p style="color:var(--fm-muted);margin-bottom:28px;">{{ __('Add products to compare from the shop page.') }}</p>
        <a href="{{ theme_shop_url() }}" class="fm-btn fm-btn-green">
            <i class="las la-store"></i> {{ __('Browse Products') }}
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
