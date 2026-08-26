@extends('tenant.admin.admin-master')

@section('title') {{ __('Bundle Settings') }} @endsection
@section('page-title') {{ __('Bundle Settings') }} @endsection

@section('content')
<div class="col-12">

    <x-msg.flash />
    <x-msg.error />

    <div class="flex items-center gap-3 mb-5">
        <a href="{{ route('tenant.admin.product-bundles.index') }}"
           class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 transition">
            <i class="mdi mdi-arrow-left text-base"></i>
        </a>
        <div>
            <h2 class="text-base font-bold text-dark">{{ __('Bundle Settings') }}</h2>
            <p class="text-xs text-muted mt-0.5">{{ __('Control how product bundles behave in your store.') }}</p>
        </div>
    </div>

    <form action="{{ route('tenant.admin.product-bundles.settings.save') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            <div class="lg:col-span-2 space-y-4">

                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-bold text-dark">{{ __('Enable Product Bundles') }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">{{ __('Automatically apply bundle discounts when all products are in the cart.') }}</div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="enabled" value="0">
                            <input type="checkbox" name="enabled" value="1" class="sr-only peer"
                                   {{ $settings['enabled'] ? 'checked' : '' }}>
                            <div class="w-10 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-violet-400 rounded-full peer peer-checked:bg-violet-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-4"></div>
                        </label>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-bold text-dark">{{ __('Show Bundle Widget on Product Pages') }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">{{ __('Display bundle upsell cards on individual product detail pages.') }}</div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="show_widget_on_product" value="0">
                            <input type="checkbox" name="show_widget_on_product" value="1" class="sr-only peer"
                                   {{ $settings['show_widget_on_product'] ? 'checked' : '' }}>
                            <div class="w-10 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-violet-400 rounded-full peer peer-checked:bg-violet-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-4"></div>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-violet-600 text-white text-sm font-semibold hover:bg-violet-700 transition">
                        <i class="mdi mdi-content-save-outline text-base"></i>
                        {{ __('Save Settings') }}
                    </button>
                </div>
            </div>

            <div class="space-y-4">
                <div class="bg-violet-50 rounded-xl border border-violet-100 p-5">
                    <h4 class="text-sm font-bold text-violet-800 mb-3">
                        <i class="mdi mdi-lightbulb-outline mr-1"></i>{{ __('Bundle Tips') }}
                    </h4>
                    <ul class="text-xs text-violet-700 space-y-2 list-disc list-inside">
                        <li>{{ __('Create bundles of complementary products (e.g. camera + memory card + case).') }}</li>
                        <li>{{ __('Use a 5–15% percentage discount to incentivize the full bundle purchase.') }}</li>
                        <li>{{ __('Bundles appear as widgets on each product\'s detail page.') }}</li>
                        <li>{{ __('Discount applies automatically — no coupon needed.') }}</li>
                    </ul>
                </div>
            </div>

        </div>
    </form>

</div>
@endsection
