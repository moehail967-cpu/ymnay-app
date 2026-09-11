@extends('tenant.admin.admin-master')

@section('title') {{ __('Loyalty Points Settings') }} @endsection
@section('page-title') {{ __('Loyalty Points Settings') }} @endsection

@section('content')
<div class="col-12">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">
        <div class="flex items-center gap-3">
            <a href="{{ route('tenant.admin.loyalty-points.index') }}"
               class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 transition">
                <i class="mdi mdi-arrow-left text-base"></i>
            </a>
            <div>
                <h2 class="text-base font-bold text-dark">{{ __('Loyalty Program Settings') }}</h2>
                <p class="text-xs text-muted mt-0.5">{{ __('Configure earn rates, redemption rules, and expiry.') }}</p>
            </div>
        </div>
    </div>

    <form action="{{ route('tenant.admin.loyalty-points.settings.save') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- Settings form --}}
            <div class="lg:col-span-2 space-y-4">

                {{-- Enable --}}
                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-bold text-dark">{{ __('Enable Loyalty Points') }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">{{ __('Customers will earn and redeem points on your store.') }}</div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="enabled" value="0">
                            <input type="checkbox" name="enabled" value="1" class="sr-only peer"
                                   {{ $settings['enabled'] ? 'checked' : '' }}>
                            <div class="w-10 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-amber-400 rounded-full peer peer-checked:bg-amber-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-4"></div>
                        </label>
                    </div>
                </div>

                {{-- Earn rate --}}
                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <h3 class="text-sm font-bold text-dark mb-4">{{ __('Earning Rules') }}</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1">
                                {{ __('Points per $1 spent') }}
                            </label>
                            <input type="number" name="earn_rate" value="{{ $settings['earn_rate'] }}"
                                   min="0" step="1"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400">
                            <p class="text-xs text-gray-400 mt-1">{{ __('Set to 0 to disable earning on purchases.') }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1">
                                {{ __('Signup bonus points') }}
                            </label>
                            <input type="number" name="earn_on_signup" value="{{ $settings['earn_on_signup'] }}"
                                   min="0" step="1"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400">
                            <p class="text-xs text-gray-400 mt-1">{{ __('0 = no signup bonus.') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Redemption --}}
                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <h3 class="text-sm font-bold text-dark mb-4">{{ __('Redemption Rules') }}</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1">
                                {{ __('Points needed for $1 discount') }}
                            </label>
                            <input type="number" name="redeem_rate" value="{{ $settings['redeem_rate'] }}"
                                   min="1" step="1"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1">
                                {{ __('Minimum points to redeem') }}
                            </label>
                            <input type="number" name="min_points_redeem" value="{{ $settings['min_points_redeem'] }}"
                                   min="1" step="1"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1">
                                {{ __('Max % of cart payable with points') }}
                            </label>
                            <div class="relative">
                                <input type="number" name="max_redeem_percent" value="{{ $settings['max_redeem_percent'] }}"
                                       min="1" max="100" step="1"
                                       class="w-full border border-gray-200 rounded-xl px-3 py-2 pr-8 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400">
                                <span class="absolute right-3 top-2 text-sm text-gray-400">%</span>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">{{ __('Prevents full order payment with points.') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Expiry --}}
                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <h3 class="text-sm font-bold text-dark mb-4">{{ __('Point Expiry') }}</h3>
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1">
                            {{ __('Points expire after (days)') }}
                        </label>
                        <input type="number" name="point_expiry_days" value="{{ $settings['point_expiry_days'] }}"
                               min="0" step="1"
                               class="w-full sm:w-48 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400">
                        <p class="text-xs text-gray-400 mt-1">{{ __('Set to 0 to disable point expiry.') }}</p>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 text-white text-sm font-semibold hover:bg-amber-600 transition">
                        <i class="mdi mdi-content-save-outline text-base"></i>
                        {{ __('Save Settings') }}
                    </button>
                </div>
            </div>

            {{-- Reference sidebar --}}
            <div class="space-y-4">
                <div class="bg-amber-50 rounded-xl border border-amber-100 p-5">
                    <h4 class="text-sm font-bold text-amber-800 mb-3">
                        <i class="mdi mdi-lightbulb-outline mr-1"></i>{{ __('How It Works') }}
                    </h4>
                    <ul class="text-xs text-amber-700 space-y-2">
                        <li><strong>{{ __('Earning:') }}</strong> {{ __('Customer spends $100 → earns 100 points (at 1 pt/$1 rate)') }}</li>
                        <li><strong>{{ __('Redeeming:') }}</strong> {{ __('100 points → $1 off (at 100 pts/$1 rate)') }}</li>
                        <li><strong>{{ __('Cart widget:') }}</strong> {{ __('Appears on cart & checkout pages for logged-in customers with enough points') }}</li>
                        <li><strong>{{ __('Max %:') }}</strong> {{ __('Limits how much of an order can be paid with points') }}</li>
                    </ul>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <h4 class="text-sm font-bold text-dark mb-3">{{ __('Quick Reference') }}</h4>
                    <div class="space-y-2 text-xs text-gray-600">
                        <div class="flex justify-between">
                            <span>{{ __('Earn rate') }}</span>
                            <span class="font-semibold text-dark">{{ $settings['earn_rate'] }} {{ __('pt / $1') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>{{ __('Redeem rate') }}</span>
                            <span class="font-semibold text-dark">{{ $settings['redeem_rate'] }} {{ __('pts = $1') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>{{ __('Min to redeem') }}</span>
                            <span class="font-semibold text-dark">{{ $settings['min_points_redeem'] }} {{ __('pts') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>{{ __('Max per order') }}</span>
                            <span class="font-semibold text-dark">{{ $settings['max_redeem_percent'] }}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span>{{ __('Expiry') }}</span>
                            <span class="font-semibold text-dark">
                                {{ $settings['point_expiry_days'] > 0 ? $settings['point_expiry_days'].' '.__('days') : __('Never') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    @if(session('success')) toastr.success("{{ session('success') }}"); @endif
    @if(session('error'))   toastr.error("{{ session('error') }}");     @endif
});
</script>
@endsection
