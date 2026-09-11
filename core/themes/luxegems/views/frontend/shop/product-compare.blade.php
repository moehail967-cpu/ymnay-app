@extends('tenant.frontend.frontend-page-master')
@section('title') {{ __('Compare') }} @endsection
@section('page-title') {{ __('Compare') }} @endsection

@section('content')

{{-- Page Header --}}
<div style="background:var(--lx-dark);border-bottom:1px solid var(--lx-border);padding:48px 0 32px;">
    <div class="container" style="text-align:center;">
        <p style="font-size:10px;font-weight:600;letter-spacing:4px;text-transform:uppercase;color:var(--lx-gold);margin-bottom:12px;">
            {{ __('Side by Side') }}
        </p>
        <h1 style="font-size:clamp(24px,3vw,42px);font-weight:200;color:var(--lx-white);letter-spacing:2px;margin-bottom:16px;">
            {{ __('Compare Products') }}
        </h1>
        <div class="lg-breadcrumb" style="justify-content:center;">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <span class="active">{{ __('Compare') }}</span>
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

<div style="background:var(--lx-black);padding:48px 0 80px;">
    <div class="container">

        @if(count($compare_products) > 0)

        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;min-width:560px;border:1px solid var(--lx-border);">

                {{-- Product images / header row --}}
                <thead>
                    <tr>
                        <th style="width:180px;padding:20px 24px;background:var(--lx-surface);border-bottom:1px solid var(--lx-border);border-right:1px solid var(--lx-border);"></th>
                        @foreach($compare_products as $cp)
                        <th style="padding:24px 20px;text-align:center;background:var(--lx-card);border-bottom:1px solid var(--lx-border);border-right:1px solid var(--lx-border);">
                            @php $ci = get_attachment_image_by_id($cp->image_id ?? null, 'grid'); $cim = $ci['img_url'] ?? null; @endphp
                            <div style="width:110px;height:110px;margin:0 auto 14px;overflow:hidden;border:1px solid var(--lx-border);">
                                @if($cim)
                                    <img src="{{ $cim }}" alt="{{ $cp->name }}" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    <div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:40px;color:var(--lx-gold);background:var(--lx-surface);"><i class="las la-gem"></i></div>
                                @endif
                            </div>
                            <div style="font-size:13px;font-weight:400;color:var(--lx-white);letter-spacing:.3px;margin-bottom:12px;line-height:1.4;">
                                <a href="{{ theme_product_url($cp->slug) }}" style="color:inherit;text-decoration:none;">{{ \Illuminate\Support\Str::words($cp->name, 6) }}</a>
                            </div>
                            <button class="compare-remove-btn"
                                    data-product_id="{{ $compare_row_ids[$cp->id] ?? '' }}"
                                    style="display:inline-flex;align-items:center;gap:5px;font-size:10px;letter-spacing:1.5px;text-transform:uppercase;background:transparent;border:1px solid var(--lx-border);color:var(--lx-muted);padding:6px 14px;cursor:pointer;transition:all .2s;font-family:inherit;"
                                    onmouseover="this.style.borderColor='var(--lx-gold)';this.style.color='var(--lx-gold)'"
                                    onmouseout="this.style.borderColor='var(--lx-border)';this.style.color='var(--lx-muted)'">
                                <i class="las la-times"></i> {{ __('Remove') }}
                            </button>
                        </th>
                        @endforeach
                    </tr>
                </thead>

                <tbody>
                    {{-- Price --}}
                    <tr>
                        <td style="padding:16px 24px;background:var(--lx-surface);border-bottom:1px solid var(--lx-border);border-right:1px solid var(--lx-border);font-size:10px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:var(--lx-gold);">{{ __('Price') }}</td>
                        @foreach($compare_products as $cp)
                        @php $cpd = theme_product_price($cp); @endphp
                        <td style="padding:16px 20px;text-align:center;background:var(--lx-card);border-bottom:1px solid var(--lx-border);border-right:1px solid var(--lx-border);">
                            <span style="font-size:18px;font-weight:300;color:var(--lx-gold);letter-spacing:.5px;">{{ amount_with_currency_symbol($cpd['sale_price']) }}</span>
                            @if($cpd['discount'] > 0)
                                <span style="display:block;font-size:12px;color:var(--lx-muted);text-decoration:line-through;margin-top:2px;">{{ amount_with_currency_symbol($cpd['regular_price']) }}</span>
                            @endif
                        </td>
                        @endforeach
                    </tr>

                    {{-- Rating --}}
                    <tr>
                        <td style="padding:16px 24px;background:var(--lx-dark);border-bottom:1px solid var(--lx-border);border-right:1px solid var(--lx-border);font-size:10px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:var(--lx-gold);">{{ __('Rating') }}</td>
                        @foreach($compare_products as $cp)
                        <td style="padding:16px 20px;text-align:center;background:var(--lx-black);border-bottom:1px solid var(--lx-border);border-right:1px solid var(--lx-border);">
                            {!! theme_star_rating($cp) !!}
                        </td>
                        @endforeach
                    </tr>

                    {{-- SKU --}}
                    <tr>
                        <td style="padding:16px 24px;background:var(--lx-surface);border-bottom:1px solid var(--lx-border);border-right:1px solid var(--lx-border);font-size:10px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:var(--lx-gold);">{{ __('SKU') }}</td>
                        @foreach($compare_products as $cp)
                        <td style="padding:16px 20px;text-align:center;background:var(--lx-card);border-bottom:1px solid var(--lx-border);border-right:1px solid var(--lx-border);font-size:12px;color:var(--lx-muted);letter-spacing:.5px;">
                            {{ $cp->inventory?->sku ?? '—' }}
                        </td>
                        @endforeach
                    </tr>

                    {{-- Category --}}
                    <tr>
                        <td style="padding:16px 24px;background:var(--lx-dark);border-bottom:1px solid var(--lx-border);border-right:1px solid var(--lx-border);font-size:10px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:var(--lx-gold);">{{ __('Category') }}</td>
                        @foreach($compare_products as $cp)
                        <td style="padding:16px 20px;text-align:center;background:var(--lx-black);border-bottom:1px solid var(--lx-border);border-right:1px solid var(--lx-border);font-size:12px;color:var(--lx-muted);">
                            @if($cp->category)
                                <a href="{{ theme_category_url($cp->category->slug) }}" style="color:var(--lx-white);text-decoration:none;font-size:12px;letter-spacing:.5px;">{{ $cp->category->name }}</a>
                            @else
                                —
                            @endif
                        </td>
                        @endforeach
                    </tr>

                    {{-- Stock --}}
                    <tr>
                        <td style="padding:16px 24px;background:var(--lx-surface);border-bottom:1px solid var(--lx-border);border-right:1px solid var(--lx-border);font-size:10px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:var(--lx-gold);">{{ __('Stock') }}</td>
                        @foreach($compare_products as $cp)
                        @php $in_stock = theme_product_in_stock($cp); @endphp
                        <td style="padding:16px 20px;text-align:center;background:var(--lx-card);border-bottom:1px solid var(--lx-border);border-right:1px solid var(--lx-border);">
                            @if($in_stock)
                                <span style="font-size:11px;letter-spacing:1.5px;text-transform:uppercase;color:#6fcf97;"><i class="las la-check-circle"></i> {{ __('In Stock') }}</span>
                            @else
                                <span style="font-size:11px;letter-spacing:1.5px;text-transform:uppercase;color:#eb5757;"><i class="las la-times-circle"></i> {{ __('Out of Stock') }}</span>
                            @endif
                        </td>
                        @endforeach
                    </tr>

                    {{-- Add to Cart --}}
                    <tr>
                        <td style="padding:16px 24px;background:var(--lx-dark);border-right:1px solid var(--lx-border);"></td>
                        @foreach($compare_products as $cp)
                        <td style="padding:20px;text-align:center;background:var(--lx-black);border-right:1px solid var(--lx-border);">
                            <a href="{{ theme_product_url($cp->slug) }}"
                               style="display:inline-flex;align-items:center;gap:8px;font-size:10px;font-weight:600;letter-spacing:2px;text-transform:uppercase;padding:13px 28px;background:var(--lx-gold);color:#080808;text-decoration:none;transition:background .2s;"
                               onmouseover="this.style.background='var(--lx-gold-l)'"
                               onmouseout="this.style.background='var(--lx-gold)'">
                                <i class="las la-shopping-bag"></i> {{ __('Add to Cart') }}
                            </a>
                        </td>
                        @endforeach
                    </tr>
                </tbody>

            </table>
        </div>

        <div style="margin-top:32px;text-align:center;">
            <a href="{{ theme_shop_url() }}"
               style="display:inline-flex;align-items:center;gap:8px;font-size:10px;font-weight:600;letter-spacing:2px;text-transform:uppercase;padding:13px 28px;background:transparent;color:var(--lx-muted);border:1px solid var(--lx-border);text-decoration:none;transition:all .2s;"
               onmouseover="this.style.borderColor='var(--lx-gold)';this.style.color='var(--lx-gold)'"
               onmouseout="this.style.borderColor='var(--lx-border)';this.style.color='var(--lx-muted)'">
                <i class="las la-arrow-left"></i> {{ __('Continue Shopping') }}
            </a>
        </div>

        @else

        <div style="padding:80px 0;text-align:center;">
            <div style="width:100px;height:100px;border:1px solid var(--lx-border);display:flex;align-items:center;justify-content:center;margin:0 auto 28px;font-size:44px;color:var(--lx-gold);">
                <i class="las la-exchange-alt"></i>
            </div>
            <p style="font-size:10px;font-weight:600;letter-spacing:4px;text-transform:uppercase;color:var(--lx-gold);margin-bottom:12px;">{{ __('Nothing here yet') }}</p>
            <h2 style="font-size:28px;font-weight:200;color:var(--lx-white);letter-spacing:2px;margin-bottom:10px;">{{ __('No Products to Compare') }}</h2>
            <p style="font-size:13px;color:var(--lx-muted);margin-bottom:32px;">{{ __('Browse our collection and add products to compare them side by side.') }}</p>
            <a href="{{ theme_shop_url() }}" class="lx-btn lx-btn-primary">
                <i class="las la-gem"></i> {{ __('Explore Collection') }}
            </a>
        </div>

        @endif

    </div>
</div>

@endsection

@section('scripts')
<script>
$(function(){
    $(document).on('click', '.compare-remove-btn', function(){
        var rowId = $(this).data('product_id');
        var btn = $(this);
        btn.prop('disabled', true).css('opacity', '.5');
        $.ajax({
            url: '{{ theme_compare_remove_url() }}', type: 'GET',
            data: { product_id: rowId },
            success: function(){
                var ids = (sessionStorage.getItem('products') || '').split(',').filter(function(v){ return v && v !== String(rowId); });
                sessionStorage.setItem('products', ids.join(','));
                if (!ids.length) sessionStorage.removeItem('products');
                location.reload();
            },
            error: function(){ btn.prop('disabled', false).css('opacity', '1'); }
        });
    });
});
</script>
@endsection
