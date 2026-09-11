@extends('tenant.admin.admin-master')
@section('title') {{__('Edit Shipping Method')}} @endsection

@section('content')

    <x-landlord-error-msg/>
    <x-landlord-flash-msg/>

    <div class="max-w-3xl">
        <div class="bg-surface rounded-xl shadow-main overflow-hidden mb-6">

            {{-- Card Header --}}
            <div class="px-5 sm:px-6 py-4 border-b border-main flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-truck-check text-primary text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Edit Shipping Method')}}</h3>
                        <p class="text-xs text-muted">{{__('Update shipping method configuration')}}</p>
                    </div>
                </div>
                @can('shipping-method-list')
                <a href="{{route('tenant.admin.shipping.method.all')}}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-main text-sm font-medium text-brand hover:border-primary hover:text-primary transition">
                    <i class="mdi mdi-arrow-left text-base"></i> {{__('All Methods')}}
                </a>
                @endcan
            </div>

            @can('shipping-method-edit')
            <div class="p-5 sm:p-6">
                <form action="{{route('tenant.admin.shipping.method.update')}}" method="post">
                    @csrf
                    <input type="hidden" name="id" value="{{ $item->id }}">

                    {{-- Section: Basic Info --}}
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Zone')}}</label>
                            <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                                <i class="mdi mdi-map-marker-radius text-lg text-primary"></i>
                                <select name="zone_id" id="zone_id"
                                        class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                                    @foreach ($all_zones as $zone)
                                        <option value="{{ $zone->id }}" @if($item->zone_id == $zone->id) selected @endif>{{ $zone->name }}</option>
                                    @endforeach
                                </select>
                                <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Title')}}</label>
                            <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                                <i class="mdi mdi-truck text-lg text-primary"></i>
                                <input type="text" name="title" id="title" value="{{ $item->name }}"
                                       placeholder="{{ __('Title') }}"
                                       class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Cost')}}</label>
                            <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                                <i class="mdi mdi-cash text-lg text-success"></i>
                                <input type="number" id="cost" name="cost" placeholder="{{ __('Cost') }}"
                                       value="{{ optional($item->options)->cost }}"
                                       class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                            </div>
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="flex items-center gap-3 my-6">
                        <div class="flex-1 h-px bg-main"></div>
                        <span class="text-[10px] font-bold tracking-widest text-muted uppercase">{{__('Settings')}}</span>
                        <div class="flex-1 h-px bg-main"></div>
                    </div>

                    {{-- Section: Settings --}}
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Tax Status')}}</label>
                                <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                                    <i class="mdi mdi-cash-multiple text-lg text-warning"></i>
                                    <select name="tax_status" id="tax_status"
                                            class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                                        @foreach ($all_tax_status as $key => $tax_status)
                                            <option value="{{ $key }}" @if(optional($item->options)->tax_status == $key) selected @endif>{{ $tax_status }}</option>
                                        @endforeach
                                    </select>
                                    <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Status')}}</label>
                                <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                                    <i class="mdi mdi-toggle-switch text-lg text-success"></i>
                                    <select name="status" id="status"
                                            class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                                        @foreach ($all_publish_status as $key => $status)
                                            <option value="{{ $key }}" @if(optional($item->options)->status == $key) selected @endif>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                    <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Setting Preset')}}</label>
                            <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                                <i class="mdi mdi-cog text-lg text-primary"></i>
                                <select name="setting_preset" id="setting_preset"
                                        class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                                    @foreach ($all_setting_presets as $key => $value)
                                        <option value="{{ $key }}" @if(optional($item->options)->setting_preset == $key) selected @endif>{{ $value }}</option>
                                    @endforeach
                                </select>
                                <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                            </div>
                        </div>

                        {{-- Conditional: Minimum Order Amount --}}
                        <div id="min_order_wrap" class="{{ optional($item->options)->setting_preset != 'none' ? '' : 'hidden' }}">
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Minimum Order Amount')}}</label>
                            <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                                <i class="mdi mdi-currency-usd text-lg text-info"></i>
                                <input type="number" id="minimum_order_amount" name="minimum_order_amount"
                                       placeholder="{{ __('Minimum Order Amount') }}"
                                       value="{{ optional($item->options)->minimum_order_amount }}"
                                       class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                            </div>
                        </div>

                        {{-- Conditional: Coupon Code --}}
                        <div id="coupon_wrap" class="{{ in_array(optional($item->options)->setting_preset, ['min_order_or_coupon', 'min_order_and_coupon']) ? '' : 'hidden' }}">
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Coupon Code')}}</label>
                            <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                                <i class="mdi mdi-ticket-percent text-lg text-danger"></i>
                                <select name="coupon_code" id="coupon_code"
                                        class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                                    <option value="">{{__('Select coupon code')}}</option>
                                    @foreach($all_coupons as $coupon)
                                        <option value="{{ $coupon->code }}" {{ $coupon->code === optional($item->options)->coupon ? "selected" : "" }}>{{ $coupon->code }}</option>
                                    @endforeach
                                </select>
                                <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                            </div>
                        </div>
                    </div>

                    <button type="submit"
                            class="mt-6 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                        <i class="mdi mdi-content-save text-base"></i> {{__('Update Shipping Method')}}
                    </button>
                </form>
            </div>
            @endcan
        </div>
    </div>

@endsection

@section('scripts')
    <script>
    (function ($) {
        "use strict";
        $(document).ready(function () {
            $('#setting_preset').on('change', function () {
                let presets = ['min_order', 'min_order_or_coupon', 'min_order_and_coupon'];
                let selected_value = $(this).val();
                let couponOptions = ['min_order_or_coupon', 'min_order_and_coupon'];

                if (presets.includes(selected_value)) {
                    $('#min_order_wrap').removeClass('hidden');
                    if (couponOptions.includes(selected_value)) {
                        $('#coupon_wrap').removeClass('hidden');
                    } else {
                        $('#coupon_wrap').addClass('hidden');
                    }
                } else {
                    $('#min_order_wrap').addClass('hidden');
                    $('#coupon_wrap').addClass('hidden');
                }
            });
        });
    })(jQuery);
    </script>
@endsection
