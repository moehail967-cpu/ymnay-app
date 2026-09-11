{{-- PawHaus: Product description tab --}}
<div style="font-size:15px;line-height:1.8;color:var(--ph-dark);">
    {!! $product->description ?? '<p style="color:var(--ph-muted);">'.__('No description available.').'</p>' !!}
</div>

@if(!empty($product->short_description))
<div style="margin-top:20px;padding:16px;background:var(--ph-terra-light);border-radius:var(--ph-radius);border-left:4px solid var(--ph-terra);">
    <p style="margin:0;font-size:14px;color:var(--ph-dark);line-height:1.7;">{!! $product->short_description !!}</p>
</div>
@endif
