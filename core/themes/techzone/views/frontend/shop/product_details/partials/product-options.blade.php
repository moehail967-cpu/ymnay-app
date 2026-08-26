{{-- Sizes --}}
@if($product->sizes?->isNotEmpty())
<div style="margin-bottom:16px;">
    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--tz-muted);margin-bottom:8px;">{{ __('Size') }}</div>
    <div class="d-flex flex-wrap gap-2">
        @foreach($product->sizes as $size)
        <label style="cursor:pointer;">
            <input type="radio" name="size_id" value="{{ $size->id }}" class="d-none size-attr-input">
            <span class="tz-attr-chip size-chip">{{ $size->name }}</span>
        </label>
        @endforeach
    </div>
</div>
@endif

{{-- Colors --}}
@if($product->colors?->isNotEmpty())
<div style="margin-bottom:16px;">
    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--tz-muted);margin-bottom:8px;">{{ __('Color') }}</div>
    <div class="d-flex flex-wrap gap-2">
        @foreach($product->colors as $color)
        <label style="cursor:pointer;display:flex;align-items:center;gap:6px;">
            <input type="radio" name="color_id" value="{{ $color->id }}" class="d-none color-attr-input">
            <span class="tz-color-chip" style="background:{{ $color->color_code ?? '#999' }};" title="{{ $color->name }}"></span>
            <span style="font-size:12px;color:var(--tz-muted);">{{ $color->name }}</span>
        </label>
        @endforeach
    </div>
</div>
@endif

{{-- Quantity + ATC --}}
<div class="d-flex align-items-center gap-3 flex-wrap" style="margin-bottom:16px;">
    <div style="display:flex;align-items:center;border:1px solid var(--tz-border);border-radius:var(--tz-radius-sm);overflow:hidden;background:var(--tz-mid);">
        <button type="button" class="tz-qty-btn qty-minus" style="width:38px;height:42px;background:transparent;border:none;font-size:18px;cursor:pointer;color:var(--tz-muted);transition:color .2s;" onmouseover="this.style.color='var(--tz-blue)'" onmouseout="this.style.color='var(--tz-muted)'"><i class="mdi mdi-minus"></i></button>
        <input type="number" name="qty" id="quantity" class="quantity" value="1" min="1" max="{{ $stock_count ?: 1 }}"
               style="width:52px;text-align:center;border:0;border-left:1px solid var(--tz-border);border-right:1px solid var(--tz-border);height:42px;font-family:var(--tz-font);font-size:15px;font-weight:700;outline:none;background:var(--tz-mid);color:var(--tz-text);">
        <button type="button" class="tz-qty-btn qty-plus" style="width:38px;height:42px;background:transparent;border:none;font-size:18px;cursor:pointer;color:var(--tz-muted);transition:color .2s;" onmouseover="this.style.color='var(--tz-blue)'" onmouseout="this.style.color='var(--tz-muted)'"><i class="mdi mdi-plus"></i></button>
    </div>

    <button type="button" class="add-to-cart-btn" data-product_id="{{ $product->id }}"
            style="display:flex;align-items:center;gap:8px;background:var(--tz-blue);color:#fff;border:0;padding:11px 22px;border-radius:var(--tz-radius-sm);font-size:14px;font-weight:700;cursor:pointer;font-family:var(--tz-font);transition:background .2s;"
            onmouseover="this.style.background='var(--tz-blue-deep)'" onmouseout="this.style.background='var(--tz-blue)'">
        <i class="mdi mdi-cart-plus"></i> {{ __('Add to Cart') }}
    </button>

    <button type="button" id="tz_buy_now_btn"
            style="display:flex;align-items:center;gap:8px;background:transparent;color:var(--tz-blue);border:1px solid var(--tz-blue);padding:11px 22px;border-radius:var(--tz-radius-sm);font-size:14px;font-weight:700;cursor:pointer;font-family:var(--tz-font);transition:all .2s;"
            onmouseover="this.style.background='var(--tz-blue)';this.style.color='#fff'" onmouseout="this.style.background='transparent';this.style.color='var(--tz-blue)'">
        <i class="mdi mdi-lightning-bolt"></i> {{ __('Buy Now') }}
    </button>
</div>

{{-- Delivery Options --}}
@if($product->product_delivery_option != null && $product->product_delivery_option->count())
<div class="mt-3 mb-3" style="display:flex;flex-direction:column;gap:10px;padding:14px;background:var(--tz-mid,#1a1f2e);border:1px solid var(--tz-border,#2a3040);border-radius:var(--tz-radius-sm,6px);">
    @foreach($product->product_delivery_option as $option)
    <div style="display:flex;align-items:flex-start;gap:12px;">
        <span style="font-size:18px;color:var(--tz-blue,#3B82F6);flex-shrink:0;margin-top:2px;"><i class="{{ $option->icon }}"></i></span>
        <div>
            <strong style="display:block;font-size:12px;font-weight:700;color:var(--tz-text,#e0e0e0);">{{ $option->title }}</strong>
            <span style="display:block;font-size:11px;color:var(--tz-muted,#888);margin-top:1px;">{{ $option->sub_title }}</span>
        </div>
    </div>
    @endforeach
</div>
@endif

<div class="d-flex gap-2 flex-wrap">
    <button type="button" class="add-to-wishlist-btn" data-product_id="{{ $product->id }}"
            style="display:flex;align-items:center;gap:6px;background:transparent;color:var(--tz-muted);border:1px solid var(--tz-border);padding:8px 16px;border-radius:var(--tz-radius-sm);font-size:13px;cursor:pointer;font-family:var(--tz-font);transition:all .2s;"
            onmouseover="this.style.borderColor='var(--tz-blue)';this.style.color='var(--tz-blue)'" onmouseout="this.style.borderColor='var(--tz-border)';this.style.color='var(--tz-muted)'">
        <i class="mdi mdi-heart-outline"></i> {{ __('Wishlist') }}
    </button>
    <button type="button" class="compare-btn" data-product_id="{{ $product->id }}"
            style="display:flex;align-items:center;gap:6px;background:transparent;color:var(--tz-muted);border:1px solid var(--tz-border);padding:8px 16px;border-radius:var(--tz-radius-sm);font-size:13px;cursor:pointer;font-family:var(--tz-font);transition:all .2s;"
            onmouseover="this.style.borderColor='var(--tz-blue)';this.style.color='var(--tz-blue)'" onmouseout="this.style.borderColor='var(--tz-border)';this.style.color='var(--tz-muted)'">
        <i class="mdi mdi-compare-horizontal"></i> {{ __('Compare') }}
    </button>
</div>

@section('scripts')
@parent
<style>
.tz-attr-chip { display:inline-block;padding:5px 14px;border:1px solid var(--tz-border);border-radius:var(--tz-radius-sm);font-family:var(--tz-font);font-size:13px;cursor:pointer;transition:all .2s;color:var(--tz-text);background:var(--tz-mid); }
input:checked + .tz-attr-chip { border-color:var(--tz-blue);color:var(--tz-blue);background:var(--tz-blue-glow); }
.tz-color-chip { display:inline-block;width:22px;height:22px;border-radius:50%;border:2px solid var(--tz-border);transition:border-color .2s; }
input:checked ~ .tz-color-chip { border-color:var(--tz-blue); }
</style>
<script>
$(function(){
    $(document).on('click','.qty-minus',function(){ var i=$('#quantity'),v=parseInt(i.val())-1; if(v>=1)i.val(v); });
    $(document).on('click','.qty-plus',function(){ var i=$('#quantity'),v=parseInt(i.val())+1,mx=parseInt(i.attr('max'))||999; if(v<=mx)i.val(v); });

    $(document).on('click','.add-to-cart-btn',function(){
        var btn=$(this),pid=btn.data('product_id'),qty=$('#quantity').val()||1;
        var size=$('input[name=size_id]:checked').val()||'';
        var color=$('input[name=color_id]:checked').val()||'';
        $.post('{{ theme_add_to_cart_url() }}',{_token:'{{ theme_csrf() }}',product_id:pid,quantity:qty,size_id:size,color_id:color},function(d){
            if(d.type==='success'){toastr.success(d.msg);}else{toastr.error(d.msg);}
        });
    });

    $(document).on('click','#tz_buy_now_btn',function(){
        var btn=$(this),pid='{{ $product->id }}',qty=$('#quantity').val()||1;
        var size=$('input[name=size_id]:checked').val()||'';
        var color=$('input[name=color_id]:checked').val()||'';
        btn.html('<i class="mdi mdi-loading mdi-spin"></i> {{ __("Please Wait…") }}').prop('disabled',true);
        $.ajax({
            url:'{{ theme_buy_now_url() }}',type:'POST',
            data:{_token:'{{ theme_csrf() }}',product_id:pid,quantity:qty,selected_size:size,selected_color:color,product_variant:''},
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
