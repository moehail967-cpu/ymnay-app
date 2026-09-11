@extends('tenant.admin.admin-master')

@section('title') {{ __('FOMO Notifications') }} @endsection

@section('content')

{{-- Page header --}}
<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-bell-ring text-primary text-lg"></i>
        </div>
        <div>
            <h3 class="text-base font-bold text-dark font-urbanist">{{ __('FOMO Notifications') }}</h3>
            <p class="text-xs text-muted">{{ __('Show recent purchase popups to boost conversions') }}</p>
        </div>
        <div class="ml-auto flex items-center gap-2">
            <a href="{{ route('tenant.admin.fomo-notifications.entries') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-main text-xs font-semibold text-muted hover:text-dark hover:border-primary/40 transition-colors">
                <i class="mdi mdi-format-list-bulleted text-sm"></i> {{ __('Entries') }}
            </a>
            <a href="{{ route('tenant.admin.fomo-notifications.preview') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-main text-xs font-semibold text-muted hover:text-dark hover:border-primary/40 transition-colors">
                <i class="mdi mdi-eye text-sm"></i> {{ __('Preview') }}
            </a>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="mb-5 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm flex items-center gap-2">
        <i class="mdi mdi-check-circle-outline"></i> {{ session('success') }}
    </div>
@endif

{{-- How it works --}}
<div x-data="{ open: false }" class="bg-surface rounded-xl border border-primary/30 mb-6 overflow-hidden">
    <button type="button" @click="open = !open"
            class="w-full flex items-center gap-3 px-5 py-4 text-left bg-primary/5 hover:bg-primary/10 transition-colors">
        <i class="mdi mdi-book-open-variant text-primary"></i>
        <span class="text-sm font-semibold text-primary">{{ __('How It Works') }}</span>
        <span class="text-xs text-muted ml-2">{{ __('Click to expand') }}</span>
        <i class="mdi text-primary ml-auto text-base" :class="open ? 'mdi-chevron-up' : 'mdi-chevron-down'"></i>
    </button>
    <div x-show="open" x-transition class="px-5 py-4 grid grid-cols-1 md:grid-cols-3 gap-6 border-t border-main">
        <div>
            <p class="text-xs font-bold text-dark mb-1 flex items-center gap-1">
                <i class="mdi mdi-numeric-1-circle text-primary"></i> {{ __('Choose a Data Source') }}
            </p>
            <ul class="text-xs text-muted list-disc pl-4 space-y-1">
                <li><strong>Real Orders</strong> — actual recent purchases, names anonymised (e.g. "Sarah F.")</li>
                <li><strong>Synthetic</strong> — custom entries you define under Synthetic Entries</li>
                <li><strong>Mixed</strong> — real orders first; pads with synthetic if needed</li>
            </ul>
        </div>
        <div>
            <p class="text-xs font-bold text-dark mb-1 flex items-center gap-1">
                <i class="mdi mdi-numeric-2-circle text-primary"></i> {{ __('Configure Timing') }}
            </p>
            <ul class="text-xs text-muted list-disc pl-4 space-y-1">
                <li><strong>Initial Delay</strong> — seconds before first popup</li>
                <li><strong>Display Duration</strong> — seconds each popup is visible</li>
                <li><strong>Interval</strong> — seconds between popups</li>
                <li>Enable <strong>Loop</strong> to restart after the last entry</li>
            </ul>
        </div>
        <div>
            <p class="text-xs font-bold text-dark mb-1 flex items-center gap-1">
                <i class="mdi mdi-numeric-3-circle text-primary"></i> {{ __('Add Synthetic Entries') }}
            </p>
            <ul class="text-xs text-muted list-disc pl-4 space-y-1">
                <li>Go to <a href="{{ route('tenant.admin.fomo-notifications.entries') }}" class="text-primary underline">Synthetic Entries</a> to add custom notifications</li>
                <li>Each entry has: product, variant, display name, and location</li>
                <li>Use <a href="{{ route('tenant.admin.fomo-notifications.preview') }}" class="text-primary underline">Preview</a> to see the widget live</li>
            </ul>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('tenant.admin.fomo-notifications.settings.save') }}">
    @csrf

    {{-- General --}}
    <div class="bg-surface rounded-xl border border-main shadow-main mb-6">
        <div class="px-5 py-4 border-b border-main">
            <p class="text-sm font-bold text-dark">{{ __('General') }}</p>
        </div>
        <div class="px-5 py-5 space-y-5">
            <label class="flex items-center gap-3 cursor-pointer">
                <div class="relative">
                    <input type="checkbox" name="enabled" class="sr-only peer"
                           {{ ($options['enabled'] ?? '0') === '1' ? 'checked' : '' }}>
                    <div class="w-10 h-5 bg-subtle rounded-full peer peer-checked:bg-primary
                                after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all
                                peer-checked:after:translate-x-5"></div>
                </div>
                <span class="text-sm font-medium text-dark">{{ __('Enable FOMO Notifications') }}</span>
            </label>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-muted mb-1.5">{{ __('Data Source') }}</label>
                    <select name="data_source"
                            class="w-full px-3 py-2 text-sm border border-main rounded-lg bg-surface text-dark focus:outline-none focus:border-primary transition-colors">
                        @foreach(['real' => 'Real Orders', 'synthetic' => 'Synthetic', 'mixed' => 'Mixed'] as $val => $lbl)
                            <option value="{{ $val }}" {{ ($options['data_source'] ?? 'mixed') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-muted mb-1.5">{{ __('Recent Orders to Show') }}</label>
                    <input type="number" name="real_order_count" min="1" max="50"
                           value="{{ $options['real_order_count'] ?? 10 }}"
                           class="w-full px-3 py-2 text-sm border border-main rounded-lg bg-surface text-dark focus:outline-none focus:border-primary transition-colors">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-muted mb-1.5">{{ __('Min Real Orders (Mixed mode)') }}</label>
                    <input type="number" name="min_real_orders" min="1"
                           value="{{ $options['min_real_orders'] ?? 3 }}"
                           class="w-full px-3 py-2 text-sm border border-main rounded-lg bg-surface text-dark focus:outline-none focus:border-primary transition-colors">
                </div>
            </div>
        </div>
    </div>

    {{-- Timing --}}
    <div class="bg-surface rounded-xl border border-main shadow-main mb-6">
        <div class="px-5 py-4 border-b border-main">
            <p class="text-sm font-bold text-dark">{{ __('Timing') }}</p>
        </div>
        <div class="px-5 py-5 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-muted mb-1.5">{{ __('Initial Delay (seconds)') }}</label>
                    <input type="number" name="display_delay" min="0"
                           value="{{ $options['display_delay'] ?? 5 }}"
                           class="w-full px-3 py-2 text-sm border border-main rounded-lg bg-surface text-dark focus:outline-none focus:border-primary transition-colors">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-muted mb-1.5">{{ __('Display Duration (seconds)') }}</label>
                    <input type="number" name="display_duration" min="1"
                           value="{{ $options['display_duration'] ?? 6 }}"
                           class="w-full px-3 py-2 text-sm border border-main rounded-lg bg-surface text-dark focus:outline-none focus:border-primary transition-colors">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-muted mb-1.5">{{ __('Interval Between Popups (seconds)') }}</label>
                    <input type="number" name="cycle_interval" min="1"
                           value="{{ $options['cycle_interval'] ?? 15 }}"
                           class="w-full px-3 py-2 text-sm border border-main rounded-lg bg-surface text-dark focus:outline-none focus:border-primary transition-colors">
                </div>
            </div>

            <label class="flex items-center gap-3 cursor-pointer">
                <div class="relative">
                    <input type="checkbox" name="loop" class="sr-only peer"
                           {{ ($options['loop'] ?? '1') === '1' ? 'checked' : '' }}>
                    <div class="w-10 h-5 bg-subtle rounded-full peer peer-checked:bg-primary
                                after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all
                                peer-checked:after:translate-x-5"></div>
                </div>
                <span class="text-sm font-medium text-dark">{{ __('Loop (restart from beginning after last entry)') }}</span>
            </label>
        </div>
    </div>

    {{-- Display Options --}}
    <div class="bg-surface rounded-xl border border-main shadow-main mb-6">
        <div class="px-5 py-4 border-b border-main">
            <p class="text-sm font-bold text-dark">{{ __('Display Options') }}</p>
        </div>
        <div class="px-5 py-5 space-y-5">
            <label class="flex items-center gap-3 cursor-pointer">
                <div class="relative">
                    <input type="checkbox" name="anonymise_names" class="sr-only peer"
                           {{ ($options['anonymise_names'] ?? '1') === '1' ? 'checked' : '' }}>
                    <div class="w-10 h-5 bg-subtle rounded-full peer peer-checked:bg-primary
                                after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all
                                peer-checked:after:translate-x-5"></div>
                </div>
                <span class="text-sm font-medium text-dark">{{ __('Anonymise Customer Names (show "Sarah F." instead of full name)') }}</span>
            </label>

            <div>
                <label class="block text-xs font-semibold text-muted mb-2">{{ __('Show On Pages') }}</label>
                @php $activePgs = json_decode($options['show_on_pages'] ?? '["all"]', true) ?? ['all']; @endphp
                <div class="flex flex-wrap gap-3">
                    @foreach(['all' => 'All Pages', 'home' => 'Home', 'shop' => 'Shop', 'product' => 'Product', 'cart' => 'Cart', 'checkout' => 'Checkout'] as $v => $l)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="show_on_pages[]" value="{{ $v }}"
                                   {{ in_array($v, $activePgs) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-main text-primary accent-primary">
                            <span class="text-sm text-dark">{{ $l }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('tenant.admin.fomo-notifications.preview') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-main text-sm font-semibold text-muted hover:text-dark hover:border-primary/40 transition-colors">
            <i class="mdi mdi-eye text-base"></i> {{ __('Preview Widget') }}
        </a>
        <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-primary/90 transition-colors shadow-sm">
            <i class="mdi mdi-content-save text-base"></i> {{ __('Save Settings') }}
        </button>
    </div>
</form>

@endsection
