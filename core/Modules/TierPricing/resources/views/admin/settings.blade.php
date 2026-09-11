@extends('tenant.admin.admin-master')

@section('title') {{ __('Tier Pricing Settings') }} @endsection
@section('page-title') {{ __('Tier Pricing Settings') }} @endsection

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
            <h2 class="text-base font-bold text-dark">{{ __('Tier Pricing Settings') }}</h2>
            <p class="text-xs text-muted mt-0.5">{{ __('Control bulk pricing behavior across the store.') }}</p>
        </div>
    </div>

    <form action="{{ route('tenant.admin.tier-pricing.settings.save') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            <div class="lg:col-span-2 space-y-4">

                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-bold text-dark">{{ __('Enable Tier Pricing') }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">{{ __('Automatically discount cart items when quantity thresholds are met.') }}</div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="enabled" value="0">
                            <input type="checkbox" name="enabled" value="1" class="sr-only peer"
                                   {{ $settings['enabled'] ? 'checked' : '' }}>
                            <div class="w-10 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-blue-400 rounded-full peer peer-checked:bg-blue-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-4"></div>
                        </label>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-bold text-dark">{{ __('Show Tier Table on Product Pages') }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">{{ __('Display a pricing breakdown table on product detail pages.') }}</div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="show_widget_on_product" value="0">
                            <input type="checkbox" name="show_widget_on_product" value="1" class="sr-only peer"
                                   {{ $settings['show_widget_on_product'] ? 'checked' : '' }}>
                            <div class="w-10 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-blue-400 rounded-full peer peer-checked:bg-blue-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-4"></div>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition">
                        <i class="mdi mdi-content-save-outline text-base"></i>
                        {{ __('Save Settings') }}
                    </button>
                </div>
            </div>

            <div>
                <div class="bg-blue-50 rounded-xl border border-blue-100 p-5">
                    <h4 class="text-sm font-bold text-blue-800 mb-3">
                        <i class="mdi mdi-lightbulb-outline mr-1"></i>{{ __('How It Works') }}
                    </h4>
                    <ol class="text-xs text-blue-700 space-y-2 list-decimal list-inside">
                        <li>{{ __('Admin sets tiers per product (min qty + discount).') }}</li>
                        <li>{{ __('Customer adds items to cart.') }}</li>
                        <li>{{ __('Cart total filter detects the highest qualifying tier.') }}</li>
                        <li>{{ __('Savings are deducted automatically — no coupon needed.') }}</li>
                    </ol>
                </div>
            </div>

        </div>
    </form>

</div>
@endsection
