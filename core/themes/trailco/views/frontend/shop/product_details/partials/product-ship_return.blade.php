@if($product?->return_policy?->shipping_return_description)
<div style="font-size:14px;line-height:1.85;color:#444;">
    {!! $product->return_policy->shipping_return_description !!}
</div>
@else
<div class="d-flex flex-wrap gap-4">
    <div style="display:flex;align-items:flex-start;gap:12px;flex:1;min-width:200px;">
        <div style="width:44px;height:44px;border-radius:var(--tr-radius);background:var(--tr-cream);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="mdi mdi-truck-fast" style="font-size:22px;color:var(--tr-olive);"></i>
        </div>
        <div>
            <div style="font-size:14px;font-weight:700;color:var(--tr-bark);margin-bottom:4px;">{{ __('Fast Delivery') }}</div>
            <div style="font-size:13px;color:var(--tr-stone);">{{ __('Ships to your trailhead quickly and reliably.') }}</div>
        </div>
    </div>
    <div style="display:flex;align-items:flex-start;gap:12px;flex:1;min-width:200px;">
        <div style="width:44px;height:44px;border-radius:var(--tr-radius);background:var(--tr-cream);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="mdi mdi-refresh" style="font-size:22px;color:var(--tr-olive);"></i>
        </div>
        <div>
            <div style="font-size:14px;font-weight:700;color:var(--tr-bark);margin-bottom:4px;">{{ __('Easy Returns') }}</div>
            <div style="font-size:13px;color:var(--tr-stone);">{{ __('30-day return policy on all trail gear.') }}</div>
        </div>
    </div>
    <div style="display:flex;align-items:flex-start;gap:12px;flex:1;min-width:200px;">
        <div style="width:44px;height:44px;border-radius:var(--tr-radius);background:var(--tr-cream);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="mdi mdi-shield-check" style="font-size:22px;color:var(--tr-olive);"></i>
        </div>
        <div>
            <div style="font-size:14px;font-weight:700;color:var(--tr-bark);margin-bottom:4px;">{{ __('Secure Purchase') }}</div>
            <div style="font-size:13px;color:var(--tr-stone);">{{ __('Encrypted checkout for your peace of mind.') }}</div>
        </div>
    </div>
</div>
@endif
