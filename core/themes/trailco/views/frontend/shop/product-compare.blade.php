@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Compare Products') }} @endsection
@section('page-title') {{ __('Compare') }} @endsection

@section('content')
{{-- Banner --}}
<div style="background:var(--tr-bark);padding:32px 0;">
    <div class="container">
        <h1 style="font-size:26px;font-weight:800;color:#fff;margin-bottom:8px;">{{ __('Compare Products') }}</h1>
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;">
            <a href="{{ theme_home_url() }}" style="color:rgba(255,255,255,.7);text-decoration:none;">{{ __('Home') }}</a>
            <span style="color:rgba(255,255,255,.45);"><i class="mdi mdi-chevron-right"></i></span>
            <span style="color:var(--tr-sand);">{{ __('Compare') }}</span>
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

            {{-- Product images + names + remove --}}
            <tr>
                <td style="width:160px;padding:16px;background:var(--tr-cream);border:1px solid var(--tr-border);font-size:11px;font-weight:700;color:var(--tr-stone);text-transform:uppercase;letter-spacing:1px;"></td>
                @foreach($compare_products as $product)
                @php $imgUrl = theme_product_image($product->image_id ?? null, 'grid'); @endphp
                <td style="padding:16px;background:#fff;border:1px solid var(--tr-border);text-align:center;vertical-align:top;">
                    <div style="width:110px;height:110px;margin:0 auto 12px;border:1px solid var(--tr-border);border-radius:var(--tr-radius);overflow:hidden;background:var(--tr-cream);">
                        @if($imgUrl)
                            <img src="{{ $imgUrl }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:cover;">
                        @else
                            <div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:36px;color:var(--tr-stone);">
                                <i class="mdi mdi-image-off-outline"></i>
                            </div>
                        @endif
                    </div>
                    <a href="{{ theme_product_url($product->slug) }}" style="font-size:14px;font-weight:700;color:var(--tr-bark);text-decoration:none;display:block;margin-bottom:10px;">{{ \Illuminate\Support\Str::words($product->name, 6) }}</a>
                    <button class="compare-remove-btn" data-product_id="{{ $compare_row_ids[$product->id] ?? '' }}"
                            style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;background:transparent;border:1px solid var(--tr-border);border-radius:var(--tr-radius);font-size:11px;color:var(--tr-stone);cursor:pointer;">
                        <i class="mdi mdi-close"></i> {{ __('Remove') }}
                    </button>
                </td>
                @endforeach
            </tr>

            {{-- Price --}}
            <tr>
                <td style="padding:12px 16px;background:var(--tr-cream);border:1px solid var(--tr-border);font-size:11px;font-weight:700;color:var(--tr-stone);text-transform:uppercase;letter-spacing:.8px;">{{ __('Price') }}</td>
                @foreach($compare_products as $product)
                @php $pdata = theme_product_price($product); @endphp
                <td style="padding:12px 16px;border:1px solid var(--tr-border);text-align:center;background:#fff;">
                    <span style="font-size:17px;font-weight:800;color:var(--tr-olive);">{{ amount_with_currency_symbol($pdata['sale_price']) }}</span>
                    @if($pdata['regular_price'])
                        <span style="display:block;font-size:12px;color:var(--tr-stone);text-decoration:line-through;">{{ amount_with_currency_symbol($pdata['regular_price']) }}</span>
                    @endif
                </td>
                @endforeach
            </tr>

            {{-- Rating --}}
            <tr>
                <td style="padding:12px 16px;background:var(--tr-cream);border:1px solid var(--tr-border);font-size:11px;font-weight:700;color:var(--tr-stone);text-transform:uppercase;letter-spacing:.8px;">{{ __('Rating') }}</td>
                @foreach($compare_products as $product)
                <td style="padding:12px 16px;border:1px solid var(--tr-border);text-align:center;background:#fff;">
                    {!! theme_star_rating($product) !!}
                </td>
                @endforeach
            </tr>

            {{-- SKU --}}
            <tr>
                <td style="padding:12px 16px;background:var(--tr-cream);border:1px solid var(--tr-border);font-size:11px;font-weight:700;color:var(--tr-stone);text-transform:uppercase;letter-spacing:.8px;">{{ __('SKU') }}</td>
                @foreach($compare_products as $product)
                <td style="padding:12px 16px;border:1px solid var(--tr-border);text-align:center;background:#fff;color:var(--tr-stone);font-size:13px;">{{ $product->sku ?? '—' }}</td>
                @endforeach
            </tr>

            {{-- Category --}}
            <tr>
                <td style="padding:12px 16px;background:var(--tr-cream);border:1px solid var(--tr-border);font-size:11px;font-weight:700;color:var(--tr-stone);text-transform:uppercase;letter-spacing:.8px;">{{ __('Category') }}</td>
                @foreach($compare_products as $product)
                <td style="padding:12px 16px;border:1px solid var(--tr-border);text-align:center;background:#fff;color:var(--tr-stone);font-size:13px;">{{ $product->category?->name ?? '—' }}</td>
                @endforeach
            </tr>

            {{-- Availability --}}
            <tr>
                <td style="padding:12px 16px;background:var(--tr-cream);border:1px solid var(--tr-border);font-size:11px;font-weight:700;color:var(--tr-stone);text-transform:uppercase;letter-spacing:.8px;">{{ __('Availability') }}</td>
                @foreach($compare_products as $product)
                @php $in_stock = optional($product->inventory)->stock_count > 0; @endphp
                <td style="padding:12px 16px;border:1px solid var(--tr-border);text-align:center;background:#fff;font-size:13px;">
                    @if($in_stock)
                        <span style="color:#2e7d32;font-weight:700;"><i class="mdi mdi-check-circle"></i> {{ __('In Stock') }}</span>
                    @else
                        <span style="color:#c62828;font-weight:700;"><i class="mdi mdi-close-circle"></i> {{ __('Out of Stock') }}</span>
                    @endif
                </td>
                @endforeach
            </tr>

            {{-- Action --}}
            <tr>
                <td style="padding:12px 16px;background:var(--tr-cream);border:1px solid var(--tr-border);"></td>
                @foreach($compare_products as $product)
                <td style="padding:12px 16px;border:1px solid var(--tr-border);text-align:center;background:#fff;">
                    <button class="add-to-cart-btn" data-product_id="{{ $product->id }}"
                            style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:var(--tr-olive);color:#fff;border:none;border-radius:var(--tr-radius);font-size:12px;font-weight:700;cursor:pointer;">
                        <i class="mdi mdi-cart-plus"></i> {{ __('Add to Cart') }}
                    </button>
                </td>
                @endforeach
            </tr>

        </table>
    </div>

    <div style="margin-top:24px;">
        <a href="{{ theme_shop_url() }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:10px 20px;border:1.5px solid var(--tr-olive);color:var(--tr-olive);border-radius:var(--tr-radius);font-size:13px;font-weight:700;text-decoration:none;">
            <i class="mdi mdi-arrow-left"></i> {{ __('Continue Shopping') }}
        </a>
    </div>

    @else
    <div style="text-align:center;padding:80px 0;">
        <i class="mdi mdi-compare" style="font-size:72px;color:var(--tr-border);display:block;margin-bottom:20px;"></i>
        <h2 style="font-size:26px;font-weight:800;color:var(--tr-bark);margin-bottom:10px;">{{ __('Nothing to Compare') }}</h2>
        <p style="color:var(--tr-stone);margin-bottom:28px;">{{ __('Add products to compare from the shop page.') }}</p>
        <a href="{{ theme_shop_url() }}"
           style="display:inline-flex;align-items:center;gap:8px;padding:12px 24px;background:var(--tr-olive);color:#fff;border-radius:var(--tr-radius);font-size:14px;font-weight:700;text-decoration:none;">
            <i class="mdi mdi-store-outline"></i> {{ __('Browse Products') }}
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
