{{-- KidVille: shipping & return tab --}}
@if($product?->return_policy?->shipping_return_description)
    <div style="font-size:14px;color:var(--kv-dark);line-height:1.85;">
        {!! $product->return_policy->shipping_return_description !!}
    </div>
@else
    <div style="display:flex;flex-direction:column;gap:14px;">
        <div style="display:flex;align-items:flex-start;gap:14px;padding:18px;background:var(--kv-light);border:2px solid var(--kv-border);border-radius:var(--kv-radius);">
            <i class="las la-truck" style="font-size:26px;color:var(--kv-blue);flex-shrink:0;margin-top:2px;"></i>
            <div>
                <div style="font-size:13px;font-weight:800;color:var(--kv-dark);margin-bottom:4px;">{{ __('Fast & Safe Delivery') }}</div>
                <p style="font-size:13px;color:var(--kv-muted);margin:0;line-height:1.65;">{{ __('Every toy is carefully packed and shipped within 2–3 business days.') }}</p>
            </div>
        </div>
        <div style="display:flex;align-items:flex-start;gap:14px;padding:18px;background:var(--kv-light);border:2px solid var(--kv-border);border-radius:var(--kv-radius);">
            <i class="las la-undo-alt" style="font-size:26px;color:var(--kv-green);flex-shrink:0;margin-top:2px;"></i>
            <div>
                <div style="font-size:13px;font-weight:800;color:var(--kv-dark);margin-bottom:4px;">{{ __('30-Day Returns') }}</div>
                <p style="font-size:13px;color:var(--kv-muted);margin:0;line-height:1.65;">{{ __("Not happy? Return within 30 days for a full refund. Kids' smiles guaranteed!") }}</p>
            </div>
        </div>
        <div style="display:flex;align-items:flex-start;gap:14px;padding:18px;background:var(--kv-light);border:2px solid var(--kv-border);border-radius:var(--kv-radius);">
            <i class="las la-shield-alt" style="font-size:26px;color:var(--kv-red);flex-shrink:0;margin-top:2px;"></i>
            <div>
                <div style="font-size:13px;font-weight:800;color:var(--kv-dark);margin-bottom:4px;">{{ __('Safety Certified') }}</div>
                <p style="font-size:13px;color:var(--kv-muted);margin:0;line-height:1.65;">{{ __('All products meet child safety standards and come with quality assurance.') }}</p>
            </div>
        </div>
    </div>
@endif
