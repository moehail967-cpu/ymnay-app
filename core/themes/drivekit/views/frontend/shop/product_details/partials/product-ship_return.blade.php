<div class="dk-tab-panel">
    @if($product?->return_policy?->shipping_return_description)
        {!! $product->return_policy->shipping_return_description !!}
    @else
        <div class="dk-trust-bar" style="border:none;padding:0;margin:0;flex-direction:column;gap:16px;">
            <span><i class="mdi mdi-truck-fast"></i> {{ __('Same-day dispatch on orders placed before 2PM') }}</span>
            <span><i class="mdi mdi-refresh"></i> {{ __('30-day hassle-free returns on all parts') }}</span>
            <span><i class="mdi mdi-shield-check"></i> {{ __('2-year warranty on all tools and equipment') }}</span>
        </div>
    @endif
</div>
