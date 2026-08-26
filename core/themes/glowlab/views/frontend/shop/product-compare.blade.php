@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Compare Products') }} — {{ theme_site_name() }} @endsection
@section('page-title') {{ __('Compare') }} @endsection

@section('content')

{{-- Page Banner --}}
<div style="background:var(--gl-gold-pale);border-bottom:1px solid var(--gl-border);padding:36px 0 28px;">
    <div class="container">
        <h1 style="font-size:26px;font-weight:300;color:var(--gl-dark);margin-bottom:8px;letter-spacing:-.3px;">{{ __('Compare Products') }}</h1>
        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--gl-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--gl-gold);font-weight:600;text-decoration:none;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="opacity:.5;"></i>
            <span>{{ __('Compare') }}</span>
        </div>
    </div>
</div>

<div style="padding:40px 0 72px;">
<div class="container">

@php
    $compare_items = \Gloudemans\Shoppingcart\Facades\Cart::instance("compare")->content();
    $product_map   = collect($product_array ?? []);
@endphp

@if($compare_items->count())

<div class="table-responsive">
<table style="width:100%;border-collapse:collapse;background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);overflow:hidden;box-shadow:var(--gl-shadow);">
    <thead>
        <tr>
            <td style="padding:20px 24px;background:var(--gl-gold-pale);border-bottom:1px solid var(--gl-border);width:160px;font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gl-muted);"></td>
            @foreach($compare_items as $item)
            @php
                $slug = $product_map->firstWhere('id', $item->id)?->slug
                     ?? \Modules\Product\Entities\Product::find($item->id)?->slug ?? '';
            @endphp
            <th style="padding:20px 24px;background:var(--gl-gold-pale);border-bottom:1px solid var(--gl-border);border-left:1px solid var(--gl-border);text-align:center;font-weight:400;">
                <div style="display:flex;flex-direction:column;align-items:center;gap:10px;">
                    <div style="width:100px;height:100px;border-radius:var(--gl-radius);overflow:hidden;background:var(--gl-gold-pale);border:1px solid var(--gl-border);">
                        {!! render_image_markup_by_attachment_id($item->options->image ?? '', 'style="width:100%;height:100%;object-fit:cover;"', 'grid') !!}
                    </div>
                    <a href="{{ theme_product_url($slug) }}" style="font-size:13px;font-weight:500;color:var(--gl-dark);text-decoration:none;line-height:1.4;">{{ $item->name }}</a>
                    <button class="compare-remove-btn" data-product_id="{{ $item->rowId }}"
                            style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;border:1px solid var(--gl-border);border-radius:50px;background:transparent;color:var(--gl-muted);font-size:11px;cursor:pointer;transition:all .2s;"
                            onmouseover="this.style.borderColor='#e53e3e';this.style.color='#e53e3e'" onmouseout="this.style.borderColor='var(--gl-border)';this.style.color='var(--gl-muted)'">
                        <i class="mdi mdi-close"></i> {{ __('Remove') }}
                    </button>
                </div>
            </th>
            @endforeach
        </tr>
    </thead>

    <tbody>
        @php $row_style = 'padding:16px 24px;border-bottom:1px solid var(--gl-border);font-size:13px;color:var(--gl-dark);'; @endphp

        <tr>
            <td style="{{ $row_style }}font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--gl-muted);background:var(--gl-gold-pale);">{{ __('Price') }}</td>
            @foreach($compare_items as $item)
            <td style="{{ $row_style }}border-left:1px solid var(--gl-border);text-align:center;">
                <span style="font-size:18px;font-weight:600;color:var(--gl-dark);">{{ amount_with_currency_symbol($item->price) }}</span>
            </td>
            @endforeach
        </tr>

        @if($compare_items->contains(fn($i) => !empty($i->options->sku)))
        <tr>
            <td style="{{ $row_style }}font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--gl-muted);background:var(--gl-gold-pale);">{{ __('SKU') }}</td>
            @foreach($compare_items as $item)
            <td style="{{ $row_style }}border-left:1px solid var(--gl-border);text-align:center;color:var(--gl-muted);">{{ $item->options->sku ?? '—' }}</td>
            @endforeach
        </tr>
        @endif

        @if($compare_items->contains(fn($i) => !empty($i->options->color_name)))
        <tr>
            <td style="{{ $row_style }}font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--gl-muted);background:var(--gl-gold-pale);">{{ __('Color') }}</td>
            @foreach($compare_items as $item)
            <td style="{{ $row_style }}border-left:1px solid var(--gl-border);text-align:center;">
                @if(!empty($item->options->color_name))
                    <span style="display:inline-flex;align-items:center;gap:6px;justify-content:center;">
                        @if(!empty($item->options->color_code))
                            <span style="width:14px;height:14px;border-radius:50%;background:{{ $item->options->color_code }};border:1px solid var(--gl-border);display:inline-block;flex-shrink:0;"></span>
                        @endif
                        {{ $item->options->color_name }}
                    </span>
                @else —
                @endif
            </td>
            @endforeach
        </tr>
        @endif

        @if($compare_items->contains(fn($i) => !empty($i->options->size_name)))
        <tr>
            <td style="{{ $row_style }}font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--gl-muted);background:var(--gl-gold-pale);">{{ __('Size') }}</td>
            @foreach($compare_items as $item)
            <td style="{{ $row_style }}border-left:1px solid var(--gl-border);text-align:center;">{{ $item->options->size_name ?? '—' }}</td>
            @endforeach
        </tr>
        @endif

        @php
            $all_attr_keys = $compare_items->flatMap(fn($i) => array_keys((array)($i->options->attributes ?? [])))->unique();
        @endphp
        @foreach($all_attr_keys as $attr_key)
        <tr>
            <td style="{{ $row_style }}font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--gl-muted);background:var(--gl-gold-pale);">{{ $attr_key }}</td>
            @foreach($compare_items as $item)
            <td style="{{ $row_style }}border-left:1px solid var(--gl-border);text-align:center;">{{ $item->options->attributes[$attr_key] ?? '—' }}</td>
            @endforeach
        </tr>
        @endforeach

        @if($compare_items->contains(fn($i) => !empty($i->options['description'])))
        <tr>
            <td style="{{ $row_style }}font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--gl-muted);background:var(--gl-gold-pale);">{{ __('Description') }}</td>
            @foreach($compare_items as $item)
            <td style="{{ $row_style }}border-left:1px solid var(--gl-border);font-size:12px;color:var(--gl-muted);max-width:250px;">
                {!! $item->options['description'] ?? '—' !!}
            </td>
            @endforeach
        </tr>
        @endif

        <tr>
            <td style="{{ $row_style }}font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--gl-muted);background:var(--gl-gold-pale);">{{ __('Action') }}</td>
            @foreach($compare_items as $item)
            <td style="{{ $row_style }}border-left:1px solid var(--gl-border);text-align:center;">
                <button class="add-to-cart-btn" data-product_id="{{ $item->id }}"
                        style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;background:var(--gl-dark);color:#fff;border:none;border-radius:var(--gl-radius);font-size:11px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;cursor:pointer;transition:background .2s;"
                        onmouseover="this.style.background='var(--gl-gold)'" onmouseout="this.style.background='var(--gl-dark)'">
                    <i class="mdi mdi-cart-plus"></i> {{ __('Add to Cart') }}
                </button>
            </td>
            @endforeach
        </tr>
    </tbody>
</table>
</div>

<div style="margin-top:24px;display:flex;gap:12px;flex-wrap:wrap;">
    <a href="{{ theme_shop_url() }}"
       style="display:inline-flex;align-items:center;gap:8px;padding:11px 24px;border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);font-size:12px;font-weight:600;color:var(--gl-dark);text-decoration:none;transition:all .2s;"
       onmouseover="this.style.borderColor='var(--gl-dark)'" onmouseout="this.style.borderColor='var(--gl-border)'">
        <i class="mdi mdi-arrow-left"></i> {{ __('Continue Shopping') }}
    </a>
    <a href="{{ theme_cart_url() }}"
       style="display:inline-flex;align-items:center;gap:8px;padding:11px 24px;background:var(--gl-dark);color:#fff;border:1.5px solid var(--gl-dark);border-radius:var(--gl-radius);font-size:12px;font-weight:600;text-decoration:none;transition:background .2s;"
       onmouseover="this.style.background='var(--gl-gold)';this.style.borderColor='var(--gl-gold)'" onmouseout="this.style.background='var(--gl-dark)';this.style.borderColor='var(--gl-dark)'">
        <i class="mdi mdi-cart"></i> {{ __('View Cart') }}
    </a>
</div>

@else

{{-- Empty state --}}
<div style="text-align:center;padding:80px 0;">
    <div style="width:88px;height:88px;border-radius:50%;background:var(--gl-gold-pale);border:1.5px solid var(--gl-border);display:flex;align-items:center;justify-content:center;margin:0 auto 24px;font-size:38px;color:var(--gl-gold);">
        <i class="mdi mdi-compare"></i>
    </div>
    <div style="width:40px;height:2px;background:var(--gl-gold);border-radius:2px;margin:0 auto 20px;"></div>
    <h3 style="font-size:22px;font-weight:300;color:var(--gl-dark);margin-bottom:10px;letter-spacing:-.3px;">{{ __('No Products to Compare') }}</h3>
    <p style="font-size:14px;color:var(--gl-muted);margin-bottom:32px;">{{ __('Add products to compare by clicking the compare button on any product.') }}</p>
    <a href="{{ theme_shop_url() }}"
       style="display:inline-flex;align-items:center;gap:8px;padding:12px 28px;background:var(--gl-dark);color:#fff;border:none;border-radius:var(--gl-radius);font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;text-decoration:none;transition:background .2s;"
       onmouseover="this.style.background='var(--gl-gold)'" onmouseout="this.style.background='var(--gl-dark)'">
        <i class="mdi mdi-store"></i> {{ __('Browse Products') }}
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
            beforeSend: function () { $('.loader').show(); },
            success: function () {
                var ids = (sessionStorage.getItem('products') || '').split(',').filter(function (v) { return v && v !== String(rowId); });
                sessionStorage.setItem('products', ids.join(','));
                if (!ids.length) sessionStorage.removeItem('products');
                $('.loader').hide();
                location.reload();
            },
            error: function () { btn.prop('disabled', false).css('opacity', '1'); $('.loader').hide(); }
        });
    });
})(jQuery);
</script>
@endsection
