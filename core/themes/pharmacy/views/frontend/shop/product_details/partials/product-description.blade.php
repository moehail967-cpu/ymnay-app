{{-- Pharmacy: Product description tab --}}
<div style="font-size:15px;line-height:1.8;color:var(--pf-dark);">
    {!! $product->description ?? '<p style="color:var(--pf-muted);">' . __('No description available.') . '</p>' !!}
</div>
