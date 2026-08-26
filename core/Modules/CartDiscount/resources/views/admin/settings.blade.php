@extends('tenant.admin.admin-master')

@section('title') {{ __('Cart Discounts') }} @endsection

@section('content')

{{-- Page header --}}
<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-percent text-primary text-lg"></i>
        </div>
        <div>
            <h3 class="text-base font-bold text-dark font-urbanist">{{ __('Cart Discounts') }}</h3>
            <p class="text-xs text-muted">{{ __('Define spend thresholds that unlock automatic discounts at checkout') }}</p>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="mb-5 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm flex items-center gap-2">
        <i class="mdi mdi-check-circle-outline"></i> {{ session('success') }}
    </div>
@endif

{{-- How it works collapsible --}}
<div x-data="{ open: false }" class="bg-surface rounded-xl border border-primary/30 mb-6 overflow-hidden">
    <button type="button" @click="open = !open"
            class="w-full flex items-center gap-3 px-5 py-4 text-left bg-primary/5 hover:bg-primary/10 transition-colors">
        <i class="mdi mdi-book-open-variant text-primary"></i>
        <span class="text-sm font-semibold text-primary">{{ __('How Cart Discounts Work') }}</span>
        <span class="text-xs text-muted ml-2">{{ __('Click to expand') }}</span>
        <i class="mdi text-primary ml-auto text-base" :class="open ? 'mdi-chevron-up' : 'mdi-chevron-down'"></i>
    </button>
    <div x-show="open" x-transition class="px-5 py-4 grid grid-cols-1 md:grid-cols-3 gap-6 border-t border-main">
        <div>
            <p class="text-xs font-bold text-dark mb-1 flex items-center gap-1"><i class="mdi mdi-stairs-up text-primary"></i> {{ __('Tiers') }}</p>
            <p class="text-xs text-muted mb-2">{{ __('Define spending thresholds. When a cart total reaches a threshold, the discount activates automatically — no coupon needed.') }}</p>
            <ul class="text-xs text-muted list-disc pl-4 space-y-0.5">
                <li>$100 → Free Shipping</li>
                <li>$200 → 10% off</li>
                <li>$400 → 20% off</li>
            </ul>
        </div>
        <div>
            <p class="text-xs font-bold text-dark mb-1 flex items-center gap-1"><i class="mdi mdi-chart-bar text-green-600"></i> {{ __('Progress Bar Widget') }}</p>
            <p class="text-xs text-muted">{{ __('A widget shows customers how close they are to the next tier. Progress bars fill as cart value increases.') }}</p>
        </div>
        <div>
            <p class="text-xs font-bold text-dark mb-1 flex items-center gap-1"><i class="mdi mdi-cog-outline text-muted"></i> {{ __('Discount Types') }}</p>
            <ul class="text-xs text-muted list-disc pl-4 space-y-0.5">
                <li><strong>{{ __('Free Shipping') }}</strong> — {{ __('reward message, no price reduction') }}</li>
                <li><strong>{{ __('Percentage') }}</strong> — {{ __('e.g. 10% off $200 = $180') }}</li>
                <li><strong>{{ __('Flat Amount') }}</strong> — {{ __('e.g. $20 off') }}</li>
            </ul>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('tenant.admin.cart-discount.settings.save') }}" id="cd-form">
    @csrf

    {{-- General settings --}}
    <div class="bg-surface rounded-xl border border-main shadow-main mb-6">
        <div class="px-5 py-4 border-b border-main">
            <p class="text-sm font-bold text-dark">{{ __('General') }}</p>
        </div>
        <div class="px-5 py-5 space-y-5">
            {{-- Enable toggle --}}
            <label class="flex items-center gap-3 cursor-pointer">
                <div class="relative">
                    <input type="checkbox" name="enabled" class="sr-only peer"
                           {{ ($options['enabled'] ?? '0') === '1' ? 'checked' : '' }}>
                    <div class="w-10 h-5 bg-subtle rounded-full peer peer-checked:bg-primary
                                after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all
                                peer-checked:after:translate-x-5"></div>
                </div>
                <span class="text-sm font-medium text-dark">{{ __('Enable Cart Discounts') }}</span>
            </label>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-muted mb-1.5">{{ __('Widget Heading') }}</label>
                    <input type="text" name="widget_title"
                           value="{{ $options['widget_title'] ?? 'Unlock Rewards' }}"
                           class="w-full px-3 py-2 text-sm border border-main rounded-lg bg-surface text-dark focus:outline-none focus:border-primary transition-colors">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-muted mb-1.5">{{ __('Progress Bar Colour') }}</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="widget_accent_color"
                               value="{{ $options['widget_accent_color'] ?? '#6366f1' }}"
                               class="h-9 w-16 rounded-lg border border-main cursor-pointer">
                        <span class="text-xs text-muted">{{ __('Shown on the cart progress bar') }}</span>
                    </div>
                </div>
            </div>

            <label class="flex items-center gap-3 cursor-pointer">
                <div class="relative">
                    <input type="checkbox" name="show_locked_tiers" class="sr-only peer"
                           {{ ($options['show_locked_tiers'] ?? '1') === '1' ? 'checked' : '' }}>
                    <div class="w-10 h-5 bg-subtle rounded-full peer peer-checked:bg-primary
                                after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all
                                peer-checked:after:translate-x-5"></div>
                </div>
                <span class="text-sm font-medium text-dark">{{ __('Show locked tiers (dimmed upcoming milestones)') }}</span>
            </label>

            <label class="flex items-center gap-3 cursor-pointer">
                <div class="relative">
                    <input type="checkbox" name="stack_discounts" class="sr-only peer"
                           {{ ($options['stack_discounts'] ?? '0') === '1' ? 'checked' : '' }}>
                    <div class="w-10 h-5 bg-subtle rounded-full peer peer-checked:bg-primary
                                after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all
                                peer-checked:after:translate-x-5"></div>
                </div>
                <span class="text-sm font-medium text-dark">{{ __('Stack all unlocked discounts (instead of highest only)') }}</span>
            </label>
        </div>
    </div>

    {{-- Discount Tiers --}}
    <div class="bg-surface rounded-xl border border-main shadow-main mb-6">
        <div class="px-5 py-4 border-b border-main flex items-center justify-between">
            <p class="text-sm font-bold text-dark">{{ __('Discount Tiers') }}</p>
            <button type="button" id="cd-add-tier"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary text-white text-xs font-semibold hover:bg-primary/90 transition-colors">
                <i class="mdi mdi-plus text-sm"></i> {{ __('Add Tier') }}
            </button>
        </div>
        <div class="px-5 py-5 space-y-3" id="cd-tiers-container">
            @php $tiers = $options['tiers'] ?? []; @endphp
            @if(empty($tiers))
                @php $tiers = [['threshold'=>100,'type'=>'free_shipping','value'=>0,'label'=>'Free Shipping']]; @endphp
            @endif
            @foreach($tiers as $tier)
            <div class="cd-tier-item flex flex-wrap items-end gap-3 p-4 bg-secondary rounded-xl border border-main">
                <div class="flex-1 min-w-[100px]">
                    <label class="block text-xs font-semibold text-muted mb-1">{{ __('Threshold ($)') }}</label>
                    <input type="number" name="tier_threshold[]" value="{{ $tier['threshold'] }}"
                           required min="0" step="0.01"
                           class="w-full px-3 py-2 text-sm border border-main rounded-lg bg-surface text-dark focus:outline-none focus:border-primary">
                </div>
                <div class="flex-1 min-w-[140px]">
                    <label class="block text-xs font-semibold text-muted mb-1">{{ __('Discount Type') }}</label>
                    <select name="tier_type[]"
                            class="w-full px-3 py-2 text-sm border border-main rounded-lg bg-surface text-dark focus:outline-none focus:border-primary">
                        @foreach(['free_shipping'=>'Free Shipping','percentage'=>'Percentage (%)','flat'=>'Flat Amount ($)'] as $v => $l)
                            <option value="{{ $v }}" {{ ($tier['type'] ?? '') === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-[90px]">
                    <label class="block text-xs font-semibold text-muted mb-1">{{ __('Value') }}</label>
                    <input type="number" name="tier_value[]" value="{{ $tier['value'] ?? 0 }}"
                           min="0" step="0.01"
                           class="w-full px-3 py-2 text-sm border border-main rounded-lg bg-surface text-dark focus:outline-none focus:border-primary">
                </div>
                <div class="flex-[2] min-w-[160px]">
                    <label class="block text-xs font-semibold text-muted mb-1">{{ __('Label (shown on widget)') }}</label>
                    <input type="text" name="tier_label[]" value="{{ $tier['label'] ?? '' }}"
                           class="w-full px-3 py-2 text-sm border border-main rounded-lg bg-surface text-dark focus:outline-none focus:border-primary">
                </div>
                <button type="button"
                        class="cd-remove-tier flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg border border-red-200 text-red-500 hover:bg-red-50 transition-colors">
                    <i class="mdi mdi-delete text-base"></i>
                </button>
            </div>
            @endforeach
        </div>
    </div>

    <div class="flex justify-end">
        <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-primary/90 transition-colors shadow-sm">
            <i class="mdi mdi-content-save text-base"></i> {{ __('Save Settings') }}
        </button>
    </div>
</form>

@endsection

@section('scripts')
<script>
(function () {
    var tierTemplate = `<div class="cd-tier-item flex flex-wrap items-end gap-3 p-4 bg-secondary rounded-xl border border-main">
        <div class="flex-1 min-w-[100px]">
            <label class="block text-xs font-semibold text-muted mb-1">{{ __('Threshold ($)') }}</label>
            <input type="number" name="tier_threshold[]" value="" required min="0" step="0.01"
                   class="w-full px-3 py-2 text-sm border border-main rounded-lg bg-surface text-dark focus:outline-none focus:border-primary">
        </div>
        <div class="flex-1 min-w-[140px]">
            <label class="block text-xs font-semibold text-muted mb-1">{{ __('Discount Type') }}</label>
            <select name="tier_type[]" class="w-full px-3 py-2 text-sm border border-main rounded-lg bg-surface text-dark focus:outline-none focus:border-primary">
                <option value="free_shipping">{{ __('Free Shipping') }}</option>
                <option value="percentage">{{ __('Percentage (%)') }}</option>
                <option value="flat">{{ __('Flat Amount ($)') }}</option>
            </select>
        </div>
        <div class="flex-1 min-w-[90px]">
            <label class="block text-xs font-semibold text-muted mb-1">{{ __('Value') }}</label>
            <input type="number" name="tier_value[]" value="0" min="0" step="0.01"
                   class="w-full px-3 py-2 text-sm border border-main rounded-lg bg-surface text-dark focus:outline-none focus:border-primary">
        </div>
        <div class="flex-[2] min-w-[160px]">
            <label class="block text-xs font-semibold text-muted mb-1">{{ __('Label (shown on widget)') }}</label>
            <input type="text" name="tier_label[]" value=""
                   class="w-full px-3 py-2 text-sm border border-main rounded-lg bg-surface text-dark focus:outline-none focus:border-primary">
        </div>
        <button type="button" class="cd-remove-tier flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg border border-red-200 text-red-500 hover:bg-red-50 transition-colors">
            <i class="mdi mdi-delete text-base"></i>
        </button>
    </div>`;

    document.getElementById('cd-add-tier').addEventListener('click', function () {
        var container = document.getElementById('cd-tiers-container');
        var el = document.createElement('div');
        el.innerHTML = tierTemplate;
        container.appendChild(el.firstElementChild);
    });

    document.getElementById('cd-tiers-container').addEventListener('click', function (e) {
        var btn = e.target.closest('.cd-remove-tier');
        if (!btn) return;
        var row = btn.closest('.cd-tier-item');
        if (document.querySelectorAll('.cd-tier-item').length > 1) {
            row.remove();
        }
    });
})();
</script>
@endsection
