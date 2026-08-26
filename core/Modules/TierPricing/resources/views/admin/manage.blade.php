@extends('tenant.admin.admin-master')

@section('title') {{ __('Tier Pricing') }} — {{ $product->name }} @endsection
@section('page-title') {{ __('Tier Pricing') }} @endsection

@section('content')
<div class="col-12">

    <x-msg.flash />
    <x-msg.error />

    <div class="flex items-center gap-3 mb-5">
        <a href="{{ route('tenant.admin.tier-pricing.index') }}"
           class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 transition">
            <i class="mdi mdi-arrow-left text-base"></i>
        </a>
        <div>
            <h2 class="text-base font-bold text-dark">{{ __('Set Bulk Pricing') }}: {{ $product->name }}</h2>
            <p class="text-xs text-muted mt-0.5">{{ __('Define quantity thresholds and discounts. Lower rows override higher-tier discounts.') }}</p>
        </div>
    </div>

    <form action="{{ route('tenant.admin.tier-pricing.save', $product->id) }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl border border-gray-200 p-5">

                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-dark">{{ __('Pricing Tiers') }}</h3>
                        <button type="button" id="add-tier-btn"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 text-xs font-semibold hover:bg-blue-100 transition">
                            <i class="mdi mdi-plus text-sm"></i>
                            {{ __('Add Tier') }}
                        </button>
                    </div>

                    {{-- Header row --}}
                    <div class="grid grid-cols-12 gap-3 mb-2 px-1">
                        <div class="col-span-2 text-[10px] font-bold text-gray-500 uppercase tracking-wider">{{ __('Min Qty') }}</div>
                        <div class="col-span-3 text-[10px] font-bold text-gray-500 uppercase tracking-wider">{{ __('Discount Type') }}</div>
                        <div class="col-span-3 text-[10px] font-bold text-gray-500 uppercase tracking-wider">{{ __('Value') }}</div>
                        <div class="col-span-3 text-[10px] font-bold text-gray-500 uppercase tracking-wider">{{ __('Label (optional)') }}</div>
                        <div class="col-span-1"></div>
                    </div>

                    <div id="tiers-container" class="space-y-2">
                        @forelse($tiers as $i => $tier)
                        <div class="tier-row grid grid-cols-12 gap-3 items-center">
                            <div class="col-span-2">
                                <input type="number" name="tiers[{{ $i }}][min_qty]" value="{{ $tier->min_qty }}"
                                       min="2" placeholder="{{ __('e.g. 5') }}"
                                       class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            </div>
                            <div class="col-span-3">
                                <select name="tiers[{{ $i }}][discount_type]"
                                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                                    <option value="percentage"  {{ $tier->discount_type === 'percentage'  ? 'selected' : '' }}>{{ __('% Off') }}</option>
                                    <option value="flat"        {{ $tier->discount_type === 'flat'        ? 'selected' : '' }}>{{ __('Flat Off') }}</option>
                                    <option value="fixed_price" {{ $tier->discount_type === 'fixed_price' ? 'selected' : '' }}>{{ __('Fixed Price') }}</option>
                                </select>
                            </div>
                            <div class="col-span-3">
                                <input type="number" name="tiers[{{ $i }}][discount_value]" value="{{ $tier->discount_value }}"
                                       min="0" step="0.01" placeholder="0"
                                       class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            </div>
                            <div class="col-span-3">
                                <input type="text" name="tiers[{{ $i }}][label]" value="{{ $tier->label }}"
                                       placeholder="{{ __('e.g. Wholesale') }}"
                                       class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            </div>
                            <div class="col-span-1 flex justify-center">
                                <button type="button" class="remove-tier text-gray-400 hover:text-red-500 transition">
                                    <i class="mdi mdi-close-circle text-lg"></i>
                                </button>
                            </div>
                        </div>
                        @empty
                        {{-- Start with two blank rows --}}
                        <div class="tier-row grid grid-cols-12 gap-3 items-center">
                            <div class="col-span-2">
                                <input type="number" name="tiers[0][min_qty]" min="2" placeholder="5"
                                       class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            </div>
                            <div class="col-span-3">
                                <select name="tiers[0][discount_type]"
                                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                                    <option value="percentage">{{ __('% Off') }}</option>
                                    <option value="flat">{{ __('Flat Off') }}</option>
                                    <option value="fixed_price">{{ __('Fixed Price') }}</option>
                                </select>
                            </div>
                            <div class="col-span-3">
                                <input type="number" name="tiers[0][discount_value]" min="0" step="0.01" placeholder="10"
                                       class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            </div>
                            <div class="col-span-3">
                                <input type="text" name="tiers[0][label]" placeholder="{{ __('e.g. Bulk') }}"
                                       class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            </div>
                            <div class="col-span-1 flex justify-center">
                                <button type="button" class="remove-tier text-gray-400 hover:text-red-500 transition">
                                    <i class="mdi mdi-close-circle text-lg"></i>
                                </button>
                            </div>
                        </div>
                        <div class="tier-row grid grid-cols-12 gap-3 items-center">
                            <div class="col-span-2">
                                <input type="number" name="tiers[1][min_qty]" min="2" placeholder="10"
                                       class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            </div>
                            <div class="col-span-3">
                                <select name="tiers[1][discount_type]"
                                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                                    <option value="percentage">{{ __('% Off') }}</option>
                                    <option value="flat">{{ __('Flat Off') }}</option>
                                    <option value="fixed_price">{{ __('Fixed Price') }}</option>
                                </select>
                            </div>
                            <div class="col-span-3">
                                <input type="number" name="tiers[1][discount_value]" min="0" step="0.01" placeholder="20"
                                       class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            </div>
                            <div class="col-span-3">
                                <input type="text" name="tiers[1][label]" placeholder="{{ __('e.g. Wholesale') }}"
                                       class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                            </div>
                            <div class="col-span-1 flex justify-center">
                                <button type="button" class="remove-tier text-gray-400 hover:text-red-500 transition">
                                    <i class="mdi mdi-close-circle text-lg"></i>
                                </button>
                            </div>
                        </div>
                        @endforelse
                    </div>

                    <div class="mt-5 flex justify-end">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition">
                            <i class="mdi mdi-content-save-outline text-base"></i>
                            {{ __('Save Tiers') }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <h4 class="text-sm font-bold text-dark mb-2">{{ __('Product') }}</h4>
                    <p class="text-sm text-gray-700">{{ $product->name }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ __('Base price') }}: {{ amount_with_currency_symbol($product->regular_price) }}</p>
                </div>

                <div class="bg-blue-50 rounded-xl border border-blue-100 p-5">
                    <h4 class="text-sm font-bold text-blue-800 mb-3">
                        <i class="mdi mdi-lightbulb-outline mr-1"></i>{{ __('Tips') }}
                    </h4>
                    <ul class="text-xs text-blue-700 space-y-2 list-disc list-inside">
                        <li>{{ __('Min Qty is the minimum units needed to trigger this tier.') }}</li>
                        <li>{{ __('The highest qualifying tier applies.') }}</li>
                        <li><strong>% Off</strong>: {{ __('percentage off the regular price') }}</li>
                        <li><strong>{{ __('Flat Off') }}</strong>: {{ __('fixed amount deducted') }}</li>
                        <li><strong>{{ __('Fixed Price') }}</strong>: {{ __('sets exact per-unit price') }}</li>
                    </ul>
                </div>
            </div>

        </div>
    </form>

</div>
@endsection

@section('scripts')
<script>
(function () {
    var container = document.getElementById('tiers-container');
    var addBtn    = document.getElementById('add-tier-btn');
    var rowIndex  = container.querySelectorAll('.tier-row').length;

    function addRow() {
        var idx = rowIndex++;
        var row = document.createElement('div');
        row.className = 'tier-row grid grid-cols-12 gap-3 items-center';
        row.innerHTML = `
            <div class="col-span-2">
                <input type="number" name="tiers[${idx}][min_qty]" min="2" placeholder="{{ __('qty') }}"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div class="col-span-3">
                <select name="tiers[${idx}][discount_type]"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="percentage">{{ __("% Off") }}</option>
                    <option value="flat">{{ __("Flat Off") }}</option>
                    <option value="fixed_price">{{ __("Fixed Price") }}</option>
                </select>
            </div>
            <div class="col-span-3">
                <input type="number" name="tiers[${idx}][discount_value]" min="0" step="0.01" placeholder="0"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div class="col-span-3">
                <input type="text" name="tiers[${idx}][label]" placeholder="{{ __("optional label") }}"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div class="col-span-1 flex justify-center">
                <button type="button" class="remove-tier text-gray-400 hover:text-red-500 transition">
                    <i class="mdi mdi-close-circle text-lg"></i>
                </button>
            </div>
        `;
        container.appendChild(row);
    }

    addBtn.addEventListener('click', addRow);

    container.addEventListener('click', function (e) {
        var btn = e.target.closest('.remove-tier');
        if (btn) {
            btn.closest('.tier-row').remove();
        }
    });
}());
</script>
@endsection
