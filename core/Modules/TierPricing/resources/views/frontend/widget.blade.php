<div class="tp-widget" id="tp-widget">
    <div class="tp-widget-header">
        <i class="mdi mdi-layers-triple-outline tp-widget-icon"></i>
        <span class="tp-widget-title">{{ __('Bulk Pricing') }}</span>
    </div>

    <table class="tp-table">
        <thead>
            <tr>
                <th>{{ __('Quantity') }}</th>
                <th>{{ __('Price per unit') }}</th>
                <th>{{ __('You save') }}</th>
            </tr>
        </thead>
        <tbody>
            {{-- First row: regular price (1+) --}}
            <tr class="tp-row-regular">
                <td>1+</td>
                <td>{{ amount_with_currency_symbol($regularPrice) }}</td>
                <td class="tp-save-none">—</td>
            </tr>
            @foreach($tiers as $tier)
                @php
                    $tierPrice = $service->calculateTierPrice($tier, $regularPrice);
                    $saving    = $regularPrice - $tierPrice;
                @endphp
                <tr>
                    <td>
                        <span class="tp-qty-badge">{{ $tier->min_qty }}+</span>
                        @if($tier->label) <span class="tp-tier-label">{{ $tier->label }}</span> @endif
                    </td>
                    <td class="tp-price-cell">{{ amount_with_currency_symbol($tierPrice) }}</td>
                    <td class="tp-saving">
                        @if($tier->discount_type === 'percentage')
                            {{ $tier->discount_value }}%
                        @else
                            {{ amount_with_currency_symbol(max(0, $saving)) }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<style>
.tp-widget{background:#eff6ff;border:1.5px solid #bfdbfe;border-radius:12px;padding:14px;margin:16px 0;font-family:inherit}
.tp-widget-header{display:flex;align-items:center;gap:8px;margin-bottom:12px}
.tp-widget-icon{font-size:18px;color:#2563eb}
.tp-widget-title{font-size:14px;font-weight:700;color:#1e3a5f}
.tp-table{width:100%;border-collapse:collapse;font-size:13px}
.tp-table thead tr{background:#dbeafe}
.tp-table th{padding:7px 10px;text-align:left;font-size:11px;font-weight:700;color:#1d4ed8;text-transform:uppercase;letter-spacing:.04em}
.tp-table td{padding:7px 10px;border-top:1px solid #e0ecff;color:#374151}
.tp-row-regular td{color:#9ca3af;font-size:12px}
.tp-price-cell{font-weight:700;color:#1e40af}
.tp-saving{color:#15803d;font-weight:700}
.tp-save-none{color:#d1d5db}
.tp-qty-badge{display:inline-block;background:#2563eb;color:#fff;font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px}
.tp-tier-label{font-size:11px;color:#6b7280;margin-left:4px}
</style>
