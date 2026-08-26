<div class="pb-bundles-wrap" id="pb-bundles-wrap">
    @foreach($bundles as $bundle)
    <div class="pb-bundle-card">
        <div class="pb-bundle-header">
            <i class="mdi mdi-package-variant-closed pb-bundle-icon"></i>
            <span class="pb-bundle-title">{{ $bundle->name }}</span>
            <span class="pb-bundle-badge">
                @if($bundle->discount_type === 'percentage')
                    {{ $bundle->discount_value }}% {{ __('OFF') }}
                @else
                    {{ amount_with_currency_symbol($bundle->discount_value) }} {{ __('OFF') }}
                @endif
            </span>
        </div>

        @if($bundle->description)
            <p class="pb-bundle-desc">{{ $bundle->description }}</p>
        @endif

        <div class="pb-bundle-products">
            @foreach($bundle->products as $prod)
                <a href="{{ theme_product_url($prod->slug) }}" class="pb-prod-chip">
                    <span class="pb-prod-name">{{ $prod->name }}</span>
                </a>
                @if(! $loop->last)
                    <span class="pb-prod-plus">+</span>
                @endif
            @endforeach
        </div>

        <div class="pb-bundle-cta">
            <i class="mdi mdi-cart-plus pb-cta-icon"></i>
            <span>{{ __('Add all to cart for the bundle discount') }}</span>
        </div>
    </div>
    @endforeach
</div>

<style>
.pb-bundles-wrap{margin:16px 0;display:flex;flex-direction:column;gap:12px}
.pb-bundle-card{background:#faf5ff;border:1.5px solid #e9d5ff;border-radius:12px;padding:14px}
.pb-bundle-header{display:flex;align-items:center;gap:8px;margin-bottom:8px}
.pb-bundle-icon{font-size:18px;color:#7c3aed}
.pb-bundle-title{font-size:14px;font-weight:700;color:#1f2937;flex:1}
.pb-bundle-badge{background:#7c3aed;color:#fff;font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px;white-space:nowrap}
.pb-bundle-desc{font-size:12px;color:#6b7280;margin:0 0 10px;line-height:1.5}
.pb-bundle-products{display:flex;align-items:center;flex-wrap:wrap;gap:6px;margin-bottom:10px}
.pb-prod-chip{background:#ede9fe;color:#5b21b6;font-size:12px;font-weight:600;padding:4px 10px;border-radius:20px;text-decoration:none;transition:background .15s}
.pb-prod-chip:hover{background:#ddd6fe}
.pb-prod-plus{font-size:14px;font-weight:700;color:#9ca3af}
.pb-bundle-cta{display:flex;align-items:center;gap:6px;font-size:12px;color:#7c3aed;font-weight:600;padding-top:8px;border-top:1px solid #e9d5ff}
.pb-cta-icon{font-size:16px}
</style>
