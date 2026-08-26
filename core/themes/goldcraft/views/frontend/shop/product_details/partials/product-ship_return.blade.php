{{-- GoldCraft: shipping & return tab --}}
@if($product?->return_policy?->shipping_return_description)
    <div style="font-size:14px;color:var(--gc-dark);line-height:1.85;font-style:italic;">
        {!! $product->return_policy->shipping_return_description !!}
    </div>
@else
    <div style="display:flex;flex-direction:column;gap:14px;">
        <div style="display:flex;align-items:flex-start;gap:14px;padding:16px;background:var(--gc-warm);border:1px solid var(--gc-border);border-radius:var(--gc-radius);">
            <i class="las la-truck" style="font-size:22px;color:var(--gc-rose);flex-shrink:0;margin-top:2px;"></i>
            <div>
                <div style="font-size:12px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;color:var(--gc-dark);margin-bottom:4px;">{{ __('Careful Delivery') }}</div>
                <p style="font-size:13px;color:var(--gc-muted);margin:0;line-height:1.65;font-style:italic;">{{ __('Each piece is individually wrapped and dispatched within 2–3 business days.') }}</p>
            </div>
        </div>
        <div style="display:flex;align-items:flex-start;gap:14px;padding:16px;background:var(--gc-warm);border:1px solid var(--gc-border);border-radius:var(--gc-radius);">
            <i class="las la-undo-alt" style="font-size:22px;color:var(--gc-gold);flex-shrink:0;margin-top:2px;"></i>
            <div>
                <div style="font-size:12px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;color:var(--gc-dark);margin-bottom:4px;">{{ __('30-Day Returns') }}</div>
                <p style="font-size:13px;color:var(--gc-muted);margin:0;line-height:1.65;font-style:italic;">{{ __('Not in love? Return within 30 days for a full refund. No questions asked.') }}</p>
            </div>
        </div>
        <div style="display:flex;align-items:flex-start;gap:14px;padding:16px;background:var(--gc-warm);border:1px solid var(--gc-border);border-radius:var(--gc-radius);">
            <i class="las la-gem" style="font-size:22px;color:var(--gc-rose);flex-shrink:0;margin-top:2px;"></i>
            <div>
                <div style="font-size:12px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;color:var(--gc-dark);margin-bottom:4px;">{{ __('Authenticity Certificate') }}</div>
                <p style="font-size:13px;color:var(--gc-muted);margin:0;line-height:1.65;font-style:italic;">{{ __('Every piece comes with a hallmark certificate of authenticity and quality assurance.') }}</p>
            </div>
        </div>
    </div>
@endif
