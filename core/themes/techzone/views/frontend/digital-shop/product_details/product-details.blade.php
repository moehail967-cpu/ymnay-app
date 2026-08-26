@extends('tenant.frontend.frontend-page-master')

@section('title') {!! $product->name !!} @endsection
@section('page-title') {!! $product->name !!} @endsection

@section('meta-data') {!! render_page_meta_data($product) !!} @endsection

@section('style')
<link rel="stylesheet" href="{{ global_asset('assets/common/css/star-rating.min.css') }}">
@endsection

@section('content')
@php
    $data          = theme_product_price($product);
    $sale_price    = $data['sale_price'];
    $regular_price = $data['regular_price'];
    $discount      = $data['discount'];
    $main_img      = get_attachment_image_by_id($product->image_id ?? null, 'large');
    $main_img_url  = $main_img['img_url'] ?? null;
    $stock_count   = optional($product->inventory)->stock_count ?? 0;
    $stock_count   = $stock_count > 0 ? $stock_count : 0;
    $image_array   = [(int)$product->image_id];
    foreach ($product->gallery_images ?? [] as $gi) { $image_array[] = $gi->id; }
@endphp

<div class="tz-page-banner">
    <div class="container tz-page-banner-content">
        <h1>{{ \Illuminate\Support\Str::words($product->name, 8) }}</h1>
        <div class="tz-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right sep"></i>
            <a href="{{ theme_digital_shop_url() }}">{{ __('Digital Shop') }}</a>
            <i class="mdi mdi-chevron-right sep"></i>
            <span class="current">{{ \Illuminate\Support\Str::words($product->name, 6) }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding-top:28px;padding-bottom:60px;">
    <div class="row g-5">
        {{-- Images --}}
        <div class="col-lg-5">
            <div style="background:var(--tz-card);border:1px solid var(--tz-border);border-radius:var(--tz-radius);overflow:hidden;aspect-ratio:1;display:flex;align-items:center;justify-content:center;padding:20px;">
                @if($main_img_url)
                    <img id="tz-main-product-img" src="{{ $main_img_url }}" alt="{{ $product->name }}" style="max-width:100%;max-height:100%;object-fit:contain;">
                @else
                    <span style="font-size:80px;color:var(--tz-blue);"><i class="las la-compact-disc"></i></span>
                @endif
            </div>
            @if(count($image_array) > 1)
            <div style="display:flex;gap:8px;margin-top:12px;flex-wrap:wrap;">
                @foreach($image_array as $imgId)
                    @php $tdata = get_attachment_image_by_id($imgId, 'grid'); $turl = $tdata['img_url'] ?? null; @endphp
                    <div onclick="if('{{ $turl }}'){document.getElementById('tz-main-product-img').src='{{ $turl }}'}"
                         style="width:72px;height:72px;border:2px solid {{ $loop->first ? 'var(--tz-blue)' : 'var(--tz-border)' }};border-radius:var(--tz-radius-sm);overflow:hidden;cursor:pointer;background:var(--tz-card);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        @if($turl)<img src="{{ $turl }}" alt="" style="width:100%;height:100%;object-fit:contain;">@else<span style="font-size:20px;color:var(--tz-blue);"><i class="las la-compact-disc"></i></span>@endif
                    </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Info --}}
        <div class="col-lg-7">
            <h1 style="font-size:clamp(20px,2.5vw,28px);font-weight:700;color:#fff;line-height:1.3;margin-bottom:12px;">{{ $product->name }}</h1>

            <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
                {!! theme_star_rating($product) !!}
            </div>

            <div class="mb-4 d-flex align-items-baseline gap-2 flex-wrap">
                <span style="font-size:28px;font-weight:800;color:var(--tz-blue);">{{ amount_with_currency_symbol($sale_price) }}</span>
                @if($regular_price)
                    <span style="font-size:16px;color:var(--tz-muted);text-decoration:line-through;">{{ amount_with_currency_symbol($regular_price) }}</span>
                @endif
                @if($discount)
                    <span style="background:var(--sz-green,#22c55e);color:#fff;font-size:12px;font-weight:700;padding:3px 10px;border-radius:var(--tz-radius-sm);">{{ $discount }}% {{ __('OFF') }}</span>
                @endif
            </div>

            <div style="border-top:1px solid var(--tz-border);padding-top:20px;">
                {{-- Qty + ATC + Buy Now --}}
                <div class="d-flex align-items-center gap-3 flex-wrap" style="margin-bottom:16px;">
                    <div style="display:flex;align-items:center;border:1px solid var(--tz-border);border-radius:var(--tz-radius-sm);overflow:hidden;background:var(--tz-mid);">
                        <button type="button" class="qty-minus" style="width:38px;height:42px;background:transparent;border:none;font-size:18px;cursor:pointer;color:var(--tz-muted);"><i class="mdi mdi-minus"></i></button>
                        <input type="number" name="qty" id="qty" class="quantity" value="1" min="1" max="{{ $stock_count ?: 1 }}"
                               style="width:52px;text-align:center;border:0;border-left:1px solid var(--tz-border);border-right:1px solid var(--tz-border);height:42px;font-family:var(--tz-font);font-size:15px;font-weight:700;outline:none;background:var(--tz-mid);color:var(--tz-text);">
                        <button type="button" class="qty-plus" style="width:38px;height:42px;background:transparent;border:none;font-size:18px;cursor:pointer;color:var(--tz-muted);"><i class="mdi mdi-plus"></i></button>
                    </div>

                    <button type="button" class="add-to-cart-btn" data-product_id="{{ $product->id }}"
                            style="display:flex;align-items:center;gap:8px;background:var(--tz-blue);color:#fff;border:0;padding:11px 22px;border-radius:var(--tz-radius-sm);font-size:14px;font-weight:700;cursor:pointer;font-family:var(--tz-font);transition:background .2s;">
                        <i class="mdi mdi-cart-plus"></i> {{ __('Add to Cart') }}
                    </button>

                    <button type="button" id="tz_buy_now_btn"
                            style="display:flex;align-items:center;gap:8px;background:transparent;color:var(--tz-blue);border:1px solid var(--tz-blue);padding:11px 22px;border-radius:var(--tz-radius-sm);font-size:14px;font-weight:700;cursor:pointer;font-family:var(--tz-font);transition:all .2s;">
                        <i class="mdi mdi-lightning-bolt"></i> {{ __('Buy Now') }}
                    </button>
                </div>

                <div class="d-flex gap-2">
                    <button type="button" class="add-to-wishlist-btn" data-product_id="{{ $product->id }}"
                            style="display:flex;align-items:center;gap:6px;background:transparent;color:var(--tz-muted);border:1px solid var(--tz-border);padding:8px 16px;border-radius:var(--tz-radius-sm);font-size:13px;cursor:pointer;font-family:var(--tz-font);transition:all .2s;">
                        <i class="mdi mdi-heart-outline"></i> {{ __('Wishlist') }}
                    </button>
                    <button type="button" class="add-to-compare-btn" data-product_id="{{ $product->id }}"
                            style="display:flex;align-items:center;gap:6px;background:transparent;color:var(--tz-muted);border:1px solid var(--tz-border);padding:8px 16px;border-radius:var(--tz-radius-sm);font-size:13px;cursor:pointer;font-family:var(--tz-font);transition:all .2s;">
                        <i class="mdi mdi-compare-horizontal"></i> {{ __('Compare') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Description --}}
    <div style="margin-top:48px;background:var(--tz-card);border:1px solid var(--tz-border);border-radius:var(--tz-radius);padding:28px;">
        <h3 style="font-size:16px;font-weight:700;color:#fff;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid var(--tz-border);">{{ __('Description') }}</h3>
        <div style="color:var(--tz-text);line-height:1.8;font-size:14px;">{!! $product->description !!}</div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(function(){
    $(document).on('click','.qty-minus',function(){ var i=$('#qty'),v=parseInt(i.val())-1; if(v>=1)i.val(v); });
    $(document).on('click','.qty-plus',function(){ var i=$('#qty'),v=parseInt(i.val())+1,mx=parseInt(i.attr('max'))||999; if(v<=mx)i.val(v); });

    $(document).on('click','.add-to-cart-btn',function(){
        var btn=$(this),pid=btn.data('product_id'),qty=$('#qty').val()||1;
        $.post('{{ theme_add_to_cart_url() }}',{_token:'{{ theme_csrf() }}',product_id:pid,quantity:qty},function(d){
            if(d.type==='success'){toastr.success(d.msg);}else{toastr.error(d.msg);}
        });
    });

    $(document).on('click','.add-to-compare-btn',function(){
        $.get('{{ theme_add_to_compare_url() }}',{product_id:$(this).data('product_id')},function(d){
            if(d.type==='success'){toastr.success(d.msg);}else{toastr.error(d.msg);}
        });
    });

    $(document).on('click','#tz_buy_now_btn',function(){
        var btn=$(this),pid='{{ $product->id }}',qty=$('#qty').val()||1;
        btn.html('<i class="mdi mdi-loading mdi-spin"></i> {{ __("Please Wait…") }}').prop('disabled',true);
        $.ajax({
            url:'{{ theme_buy_now_url() }}',type:'POST',
            data:{_token:'{{ theme_csrf() }}',product_id:pid,quantity:qty,product_variant:''},
            success:function(d){
                if(d.type==='success'){ window.location.href=d.redirect; }
                else if(d.type==='warning'){ toastr.warning(d.quantity_msg); btn.html('<i class="mdi mdi-lightning-bolt"></i> {{ __("Buy Now") }}').prop('disabled',false); }
                else{ toastr.error(d.error_msg||'{{ __("Something went wrong") }}'); btn.html('<i class="mdi mdi-lightning-bolt"></i> {{ __("Buy Now") }}').prop('disabled',false); }
            },
            error:function(){ toastr.error('{{ __("Something went wrong") }}'); btn.html('<i class="mdi mdi-lightning-bolt"></i> {{ __("Buy Now") }}').prop('disabled',false); }
        });
    });
});
</script>
@endsection
