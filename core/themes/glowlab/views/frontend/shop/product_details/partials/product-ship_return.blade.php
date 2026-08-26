{{-- GlowLab: shipping & return tab --}}
@if($product?->return_policy?->shipping_return_description)
    <div style="font-size:14px;color:var(--gl-dark);line-height:1.8;">
        {!! $product->return_policy->shipping_return_description !!}
    </div>
@else
    <div style="display:flex;flex-direction:column;gap:16px;">
        <div style="display:flex;align-items:flex-start;gap:14px;padding:16px;background:var(--gl-gold-pale);border:1px solid var(--gl-border);border-radius:var(--gl-radius);">
            <i class="mdi mdi-truck" style="font-size:24px;color:var(--gl-gold);flex-shrink:0;margin-top:2px;"></i>
            <div>
                <div style="font-size:13px;font-weight:700;color:var(--gl-dark);margin-bottom:4px;">{{ __('Fast & Reliable Delivery') }}</div>
                <p style="font-size:13px;color:var(--gl-muted);margin:0;line-height:1.6;">{{ __('Same-day dispatch on orders placed before 2PM. Delivery within 3-5 business days.') }}</p>
            </div>
        </div>
        <div style="display:flex;align-items:flex-start;gap:14px;padding:16px;background:var(--gl-gold-pale);border:1px solid var(--gl-border);border-radius:var(--gl-radius);">
            <i class="mdi mdi-refresh" style="font-size:24px;color:var(--gl-sage);flex-shrink:0;margin-top:2px;"></i>
            <div>
                <div style="font-size:13px;font-weight:700;color:var(--gl-dark);margin-bottom:4px;">{{ __('30-Day Returns') }}</div>
                <p style="font-size:13px;color:var(--gl-muted);margin:0;line-height:1.6;">{{ __('Hassle-free returns within 30 days of purchase. No questions asked.') }}</p>
            </div>
        </div>
        <div style="display:flex;align-items:flex-start;gap:14px;padding:16px;background:var(--gl-gold-pale);border:1px solid var(--gl-border);border-radius:var(--gl-radius);">
            <i class="mdi mdi-shield-check-outline" style="font-size:24px;color:var(--gl-gold);flex-shrink:0;margin-top:2px;"></i>
            <div>
                <div style="font-size:13px;font-weight:700;color:var(--gl-dark);margin-bottom:4px;">{{ __('Authenticity Guaranteed') }}</div>
                <p style="font-size:13px;color:var(--gl-muted);margin:0;line-height:1.6;">{{ __('All products are 100% authentic, dermatologist-tested, and clean-ingredient certified.') }}</p>
            </div>
        </div>
    </div>
@endif
