{{-- FreshMart: ship & return tab --}}
@if($product?->return_policy?->shipping_return_description)
<div style="font-size:14px;line-height:1.8;color:var(--fm-dark);">
    {!! $product->return_policy->shipping_return_description !!}
</div>
@else
<div class="d-flex align-items-start gap-3" style="color:var(--fm-muted);font-size:14px;">
    <i class="las la-truck" style="font-size:32px;color:var(--fm-green);flex-shrink:0;"></i>
    <div>
        <div style="font-weight:700;color:var(--fm-dark);margin-bottom:4px;">{{ __('Shipping & Returns') }}</div>
        <p>{{ __('No shipping & return policy has been added for this product.') }}</p>
    </div>
</div>
@endif
