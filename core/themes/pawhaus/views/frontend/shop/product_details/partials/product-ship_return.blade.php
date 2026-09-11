{{-- PawHaus: Shipping & Returns tab --}}
<div class="row g-3">
    <div class="col-md-4">
        <div style="background:var(--ph-terra-light);border-radius:var(--ph-radius);padding:24px;text-align:center;">
            <i class="las la-truck" style="font-size:36px;color:var(--ph-terra);display:block;margin-bottom:12px;"></i>
            <div style="font-size:14px;font-weight:800;color:var(--ph-dark);margin-bottom:6px;">{{ __('Free Shipping') }}</div>
            <p style="font-size:13px;color:var(--ph-muted);margin:0;">{{ __('On orders over the minimum threshold. Delivered to your door within 3–5 business days.') }}</p>
        </div>
    </div>
    <div class="col-md-4">
        <div style="background:var(--ph-sage-light);border-radius:var(--ph-radius);padding:24px;text-align:center;">
            <i class="las la-sync" style="font-size:36px;color:var(--ph-sage);display:block;margin-bottom:12px;"></i>
            <div style="font-size:14px;font-weight:800;color:var(--ph-dark);margin-bottom:6px;">{{ __('Easy Returns') }}</div>
            <p style="font-size:13px;color:var(--ph-muted);margin:0;">{{ __('Not satisfied? Return within 30 days for a full refund. No questions asked.') }}</p>
        </div>
    </div>
    <div class="col-md-4">
        <div style="background:var(--ph-cream);border:2px solid var(--ph-border);border-radius:var(--ph-radius);padding:24px;text-align:center;">
            <i class="las la-shield-alt" style="font-size:36px;color:var(--ph-terra);display:block;margin-bottom:12px;"></i>
            <div style="font-size:14px;font-weight:800;color:var(--ph-dark);margin-bottom:6px;">{{ __('Secure Payment') }}</div>
            <p style="font-size:13px;color:var(--ph-muted);margin:0;">{{ __('Your payment information is encrypted and secure at all times.') }}</p>
        </div>
    </div>
</div>

@if(!empty($product->refund_policy))
<div style="margin-top:24px;padding:20px;background:var(--ph-white);border:2px solid var(--ph-border);border-radius:var(--ph-radius);">
    <div style="font-size:14px;font-weight:800;color:var(--ph-dark);margin-bottom:10px;"><i class="las la-file-alt" style="color:var(--ph-terra);"></i> {{ __('Refund Policy') }}</div>
    <div style="font-size:14px;color:var(--ph-muted);line-height:1.7;">{!! $product->refund_policy !!}</div>
</div>
@endif
