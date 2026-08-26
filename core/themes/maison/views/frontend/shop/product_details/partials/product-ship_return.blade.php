{{-- Maison: shipping & returns tab --}}
@if($product?->return_policy?->shipping_return_description)
    <div style="font-size:14px;color:var(--ms-charcoal);line-height:1.8;">
        {!! $product->return_policy->shipping_return_description !!}
    </div>
@else
<div class="row g-3">

    {{-- Free Shipping --}}
    <div class="col-md-4">
        <div style="background:#fff;border:1px solid var(--ms-border);border-radius:var(--ms-radius);padding:24px;text-align:center;height:100%;box-shadow:var(--ms-shadow);">
            <div style="width:52px;height:52px;border-radius:50%;background:var(--ms-warm);border:1px solid var(--ms-border);display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                <i class="las la-truck" style="font-size:24px;color:var(--ms-olive);"></i>
            </div>
            <div style="font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--ms-dark);margin-bottom:8px;">
                {{ __('Free Shipping') }}
            </div>
            <p style="font-size:12px;color:var(--ms-muted);line-height:1.6;margin:0;">
                {{ __('On all orders above a qualifying threshold. Fast and reliable delivery to your door.') }}
            </p>
        </div>
    </div>

    {{-- Returns --}}
    <div class="col-md-4">
        <div style="background:#fff;border:1px solid var(--ms-border);border-radius:var(--ms-radius);padding:24px;text-align:center;height:100%;box-shadow:var(--ms-shadow);">
            <div style="width:52px;height:52px;border-radius:50%;background:var(--ms-warm);border:1px solid var(--ms-border);display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                <i class="las la-sync-alt" style="font-size:24px;color:var(--ms-olive);"></i>
            </div>
            <div style="font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--ms-dark);margin-bottom:8px;">
                {{ __('30-Day Returns') }}
            </div>
            <p style="font-size:12px;color:var(--ms-muted);line-height:1.6;margin:0;">
                {{ __('Not satisfied? Return within 30 days for a hassle-free refund or exchange.') }}
            </p>
        </div>
    </div>

    {{-- Authenticity --}}
    <div class="col-md-4">
        <div style="background:#fff;border:1px solid var(--ms-border);border-radius:var(--ms-radius);padding:24px;text-align:center;height:100%;box-shadow:var(--ms-shadow);">
            <div style="width:52px;height:52px;border-radius:50%;background:var(--ms-warm);border:1px solid var(--ms-border);display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                <i class="las la-certificate" style="font-size:24px;color:var(--ms-olive);"></i>
            </div>
            <div style="font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--ms-dark);margin-bottom:8px;">
                {{ __('Authenticity Guarantee') }}
            </div>
            <p style="font-size:12px;color:var(--ms-muted);line-height:1.6;margin:0;">
                {{ __('Every product is genuine and sourced from verified artisans and suppliers.') }}
            </p>
        </div>
    </div>

</div>
@endif
