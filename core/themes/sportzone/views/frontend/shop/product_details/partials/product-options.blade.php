{{-- Sizes --}}
@if($product->sizes?->isNotEmpty())
<div style="margin-bottom:16px;">
    <div style="font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);margin-bottom:8px;">{{ __('Size') }}</div>
    <div class="d-flex flex-wrap gap-2">
        @foreach($product->sizes as $size)
        <label style="cursor:pointer;">
            <input type="radio" name="size_id" value="{{ $size->id }}" class="d-none size-attr-input">
            <span class="sz-attr-chip size-chip">{{ $size->name }}</span>
        </label>
        @endforeach
    </div>
</div>
@endif

{{-- Colors --}}
@if($product->colors?->isNotEmpty())
<div style="margin-bottom:16px;">
    <div style="font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);margin-bottom:8px;">{{ __('Color') }}</div>
    <div class="d-flex flex-wrap gap-2">
        @foreach($product->colors as $color)
        <label style="cursor:pointer;display:flex;align-items:center;gap:6px;">
            <input type="radio" name="color_id" value="{{ $color->id }}" class="d-none color-attr-input">
            <span class="sz-color-chip" style="background:{{ $color->color_code ?? '#999' }};" title="{{ $color->name }}"></span>
            <span style="font-size:12px;color:var(--sz-muted);">{{ $color->name }}</span>
        </label>
        @endforeach
    </div>
</div>
@endif

{{-- Quantity + ATC --}}
<div class="d-flex align-items-center gap-3 flex-wrap" style="margin-bottom:16px;">
    <div style="display:flex;align-items:center;border:2px solid var(--sz-border);border-radius:var(--sz-radius);overflow:hidden;">
        <button type="button" class="sz-qty-btn qty-minus" style="width:38px;height:42px;background:var(--sz-bg);border:none;font-size:18px;cursor:pointer;color:var(--sz-muted);"><i class="mdi mdi-minus"></i></button>
        <input type="number" name="qty" id="quantity" class="quantity" value="1" min="1" max="{{ $stock_count ?: 1 }}"
               style="width:52px;text-align:center;border:0;border-left:2px solid var(--sz-border);border-right:2px solid var(--sz-border);height:42px;font-family:var(--sz-font-body);font-size:15px;font-weight:700;outline:none;">
        <button type="button" class="sz-qty-btn qty-plus" style="width:38px;height:42px;background:var(--sz-bg);border:none;font-size:18px;cursor:pointer;color:var(--sz-muted);"><i class="mdi mdi-plus"></i></button>
    </div>

    <button type="button" class="sz-btn sz-btn-red add-to-cart-btn" data-product_id="{{ $product->id }}" style="gap:8px;">
        <i class="mdi mdi-cart-plus"></i> {{ __('Add to Cart') }}
    </button>

    <button type="button" class="sz-btn sz-btn-navy" id="sz_buy_now_btn" style="gap:8px;">
        <i class="mdi mdi-lightning-bolt"></i> {{ __('Buy Now') }}
    </button>
</div>

{{-- Delivery Options --}}
@if($product->product_delivery_option != null && $product->product_delivery_option->count())
<div class="mt-3 mb-3" style="display:flex;flex-direction:column;gap:10px;padding:14px;background:var(--sz-bg,#f8f8f8);border:2px solid var(--sz-border,#e5e5e5);border-radius:var(--sz-radius,4px);">
    @foreach($product->product_delivery_option as $option)
    <div style="display:flex;align-items:flex-start;gap:12px;">
        <span style="font-size:18px;color:var(--sz-red,#E8372A);flex-shrink:0;margin-top:2px;"><i class="{{ $option->icon }}"></i></span>
        <div>
            <strong style="display:block;font-size:12px;font-weight:700;color:var(--sz-dark,#1a1a1a);font-family:var(--sz-font-head,sans-serif);text-transform:uppercase;letter-spacing:.5px;">{{ $option->title }}</strong>
            <span style="display:block;font-size:11px;color:var(--sz-muted,#888);margin-top:1px;">{{ $option->sub_title }}</span>
        </div>
    </div>
    @endforeach
</div>
@endif

<div class="d-flex gap-2 flex-wrap">
    <button type="button" class="sz-btn sz-btn-outline sz-btn-sm add-to-wishlist-btn" data-product_id="{{ $product->id }}" style="gap:6px;">
        <i class="mdi mdi-heart-outline"></i> {{ __('Wishlist') }}
    </button>
    <button type="button" class="sz-btn sz-btn-outline sz-btn-sm compare-btn" data-product_id="{{ $product->id }}" style="gap:6px;">
        <i class="mdi mdi-compare-horizontal"></i> {{ __('Compare') }}
    </button>
</div>

@section('scripts')
@parent
<style>
.sz-attr-chip { display:inline-block;padding:5px 14px;border:2px solid var(--sz-border);font-family:var(--sz-font-head);font-size:13px;letter-spacing:1px;text-transform:uppercase;cursor:pointer;transition:all .2s; }
input:checked + .sz-attr-chip { border-color:var(--sz-red);color:var(--sz-red); }
.sz-color-chip { display:inline-block;width:22px;height:22px;border-radius:50%;border:2px solid var(--sz-border);transition:border-color .2s; }
input:checked ~ .sz-color-chip { border-color:var(--sz-navy); }
</style>
<script>
$(function(){
    $(document).on('click','.qty-minus',function(){var i=$('#quantity'),v=parseInt(i.val())-1;if(v>=1)i.val(v);});
    $(document).on('click','.qty-plus',function(){var i=$('#quantity'),v=parseInt(i.val())+1,mx=parseInt(i.attr('max'))||999;if(v<=mx)i.val(v);});

    $(document).on('click','.add-to-cart-btn',function(){
        var btn=$(this), pid=btn.data('product_id'), qty=$('#quantity').val()||1;
        var size=$('input[name=size_id]:checked').val()||'';
        var color=$('input[name=color_id]:checked').val()||'';
        $.post('{{ theme_add_to_cart_url() }}',{_token:'{{ theme_csrf() }}',product_id:pid,quantity:qty,size_id:size,color_id:color},function(d){
            if(d.type==='success'){toastr.success(d.msg);}else{toastr.error(d.msg);}
        });
    });

    $(document).on('click','#sz_buy_now_btn',function(){
        var btn=$(this), pid='{{ $product->id }}', qty=$('#quantity').val()||1;
        var size=$('input[name=size_id]:checked').val()||'';
        var color=$('input[name=color_id]:checked').val()||'';
        btn.html('<i class="mdi mdi-loading mdi-spin"></i> {{ __("Please Wait…") }}').prop('disabled',true);
        $.ajax({
            url:'{{ theme_buy_now_url() }}',
            type:'POST',
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
