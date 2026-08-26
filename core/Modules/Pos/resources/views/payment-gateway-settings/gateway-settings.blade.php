@extends('tenant.admin.admin-master')
@section('title')
    {{__('POS Settings')}}
@endsection

@php
    $e_wallet_credential = json_decode(get_static_option("pos_e_wallet_credential"));
    $e_wallet_image_id = is_array($e_wallet_credential?->e_wallet_image)
        ? ($e_wallet_credential->e_wallet_image[0] ?? null)
        : ($e_wallet_credential?->e_wallet_image ?? null);
@endphp

@section('content')

<x-flash-msg-tw/>
<x-error-msg-tw/>

<div class="space-y-6">

    {{-- Default Tax Location --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-8">
            <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-warning-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-map-marker-outline text-warning text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Default Tax Location')}}</h3>
                        <p class="text-xs text-muted">{{__('Set the default location used to calculate POS tax')}}</p>
                    </div>
                </div>
                <div class="p-4 sm:p-6">
                    <form action="{{ route('tenant.admin.pos.tax-settings') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                            <div>
                                <label for="country" class="block text-[10px] font-bold text-muted uppercase tracking-widest mb-1.5">{{__('Country')}}</label>
                                <select class="w-full bg-surface border border-main rounded-xl px-3.5 py-2.5 text-sm text-dark outline-none focus:border-primary transition" name="country" id="country">
                                    <option value="">{{__('Select country')}}</option>
                                    @foreach($countries ?? [] as $country)
                                        <option value="{{$country->id}}" @selected($country->id == get_static_option('pos_tax_country'))>{{$country->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="state" class="block text-[10px] font-bold text-muted uppercase tracking-widest mb-1.5">{{__('State')}}</label>
                                <select class="w-full bg-surface border border-main rounded-xl px-3.5 py-2.5 text-sm text-dark outline-none focus:border-primary transition" name="state" id="state">
                                    @foreach($states ?? [] as $state)
                                        <option value="{{$state->id}}" @selected($state->id == get_static_option('pos_tax_state'))>{{$state->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="city" class="block text-[10px] font-bold text-muted uppercase tracking-widest mb-1.5">{{__('City')}}</label>
                                <select class="w-full bg-surface border border-main rounded-xl px-3.5 py-2.5 text-sm text-dark outline-none focus:border-primary transition" name="city" id="city">
                                    @foreach($cities ?? [] as $city)
                                        <option value="{{$city->id}}" @selected($city->id == get_static_option('pos_tax_city'))>{{$city->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="postal_code" class="block text-[10px] font-bold text-muted uppercase tracking-widest mb-1.5">{{__('Postal Code')}}</label>
                                <input class="w-full bg-surface border border-main rounded-xl px-3.5 py-2.5 text-sm text-dark placeholder-subtle outline-none focus:border-primary transition"
                                       name="postal_code" id="postal_code"
                                       value="{{ get_static_option('pos_tax_postal_code') }}"
                                       placeholder="{{__('Enter postal code')}}">
                            </div>
                        </div>

                        <div class="hidden tax_class_row mt-4">
                            <label for="tax_class" class="block text-[10px] font-bold text-muted uppercase tracking-widest mb-1.5">{{__('Tax Class')}}</label>
                            <select class="w-full bg-surface border border-main rounded-xl px-3.5 py-2.5 text-sm text-dark outline-none focus:border-primary transition" name="tax_class" id="tax_class"></select>
                        </div>

                        <div class="flex justify-end mt-5">
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition shadow-sm">
                                <i class="mdi mdi-content-save-outline text-base"></i> {{__('Save Changes')}}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="lg:col-span-4">
            <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden lg:sticky lg:top-[80px]">
                <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-info-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-information-outline text-info text-base"></i>
                    </div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Tax Info')}}</h3>
                </div>
                <div class="p-4 sm:p-6 space-y-3">
                    <div class="flex items-start gap-2.5 text-xs text-muted">
                        <i class="mdi mdi-chevron-right text-primary text-sm mt-0.5 flex-shrink-0"></i>
                        <span>{{__('Select country, state and city to apply the correct tax rate automatically.')}}</span>
                    </div>
                    <div class="flex items-start gap-2.5 text-xs text-muted">
                        <i class="mdi mdi-chevron-right text-primary text-sm mt-0.5 flex-shrink-0"></i>
                        <span>{{__('Tax classes load dynamically after location is selected.')}}</span>
                    </div>
                    <div class="flex items-start gap-2.5 text-xs text-muted">
                        <i class="mdi mdi-chevron-right text-primary text-sm mt-0.5 flex-shrink-0"></i>
                        <span>{{__('This setting applies to all new POS orders as the default tax.')}}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Methods --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-8">
            <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-credit-card-multiple-outline text-primary text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Payment Methods')}}</h3>
                        <p class="text-xs text-muted">{{__('Enable or configure accepted POS payment methods')}}</p>
                    </div>
                </div>
                <div class="p-4 sm:p-6">
                    <form action="{{ route('tenant.admin.pos.payment-gateway-settings') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- Tab Navigation --}}
                        <div class="flex items-center gap-1.5 mb-6 border-b border-main pb-4">
                            <button type="button" class="pos-tab-btn active flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold transition" data-target="cash-panel">
                                <i class="mdi mdi-cash text-base"></i> {{__('Cash')}}
                            </button>
                            <button type="button" class="pos-tab-btn flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold transition" data-target="card-panel">
                                <i class="mdi mdi-credit-card-outline text-base"></i> {{__('Card')}}
                            </button>
                            <button type="button" class="pos-tab-btn flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold transition" data-target="ewallet-panel">
                                <i class="mdi mdi-wallet-outline text-base"></i> {{__('E-Wallet')}}
                            </button>
                        </div>

                        {{-- Cash Panel --}}
                        <div class="pos-tab-panel" id="cash-panel">
                            <div class="flex items-center justify-between p-4 rounded-xl border border-main bg-secondary">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-success-soft flex items-center justify-center flex-shrink-0">
                                        <i class="mdi mdi-cash text-success text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-dark">{{__('Cash Payment')}}</p>
                                        <p class="text-xs text-muted mt-0.5">{{__('Accept cash payments at the counter')}}</p>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                                    <input type="hidden" name="pos_payment_gateway_enable" value="0">
                                    <input type="checkbox" name="pos_payment_gateway_enable" value="1" class="sr-only peer"
                                           @if(get_static_option('pos_payment_gateway_enable') == 1) checked @endif>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                </label>
                            </div>
                        </div>

                        {{-- Card Panel --}}
                        <div class="pos-tab-panel hidden" id="card-panel">
                            <div class="flex items-center justify-between p-4 rounded-xl border border-main bg-secondary">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-info-soft flex items-center justify-center flex-shrink-0">
                                        <i class="mdi mdi-credit-card-outline text-info text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-dark">{{__('Card Payment')}}</p>
                                        <p class="text-xs text-muted mt-0.5">{{__('Accept debit and credit card payments')}}</p>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                                    <input type="hidden" name="pos_card_payment_gateway_enable" value="0">
                                    <input type="checkbox" name="pos_card_payment_gateway_enable" value="1" class="sr-only peer"
                                           @if(get_static_option('pos_card_payment_gateway_enable') == 1) checked @endif>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                </label>
                            </div>
                        </div>

                        {{-- E-Wallet Panel --}}
                        <div class="pos-tab-panel hidden" id="ewallet-panel">
                            <div class="space-y-5">
                                <div class="flex items-center justify-between p-4 rounded-xl border border-main bg-secondary">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-warning-soft flex items-center justify-center flex-shrink-0">
                                            <i class="mdi mdi-wallet-outline text-warning text-xl"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-dark">{{__('E-Wallet Payment')}}</p>
                                            <p class="text-xs text-muted mt-0.5">{{__('Accept digital wallet payments')}}</p>
                                        </div>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                                        <input type="hidden" name="pos_e_wallet_payment_gateway_enable" value="0">
                                        <input type="checkbox" name="pos_e_wallet_payment_gateway_enable" value="1" class="sr-only peer"
                                               @if(get_static_option('pos_e_wallet_payment_gateway_enable') == 1) checked @endif>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                    </label>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-muted uppercase tracking-widest mb-1.5">{{__('Wallet Name')}}</label>
                                    <input type="text" name="e_wallet_name"
                                           value="{{ $e_wallet_credential?->e_wallet_name }}"
                                           class="w-full bg-surface border border-main rounded-xl px-3.5 py-2.5 text-sm text-dark placeholder-subtle outline-none focus:border-primary transition"
                                           placeholder="{{__('e.g. PayPal, Apple Pay, GCash')}}">
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-muted uppercase tracking-widest mb-1.5">{{__('Wallet Logo / QR Code')}}</label>
                                    <x-fields.tw-media-upload name="e_wallet_image" id="{{ $e_wallet_image_id }}" dimentions="512 x 512 px"/>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end mt-6">
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition shadow-sm">
                                <i class="mdi mdi-content-save-outline text-base"></i> {{__('Save Changes')}}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="lg:col-span-4">
            <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden lg:sticky lg:top-[80px]">
                <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-info-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-information-outline text-info text-base"></i>
                    </div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Payment Info')}}</h3>
                </div>
                <div class="p-4 sm:p-6 space-y-3">
                    <div class="flex items-start gap-2.5 text-xs text-muted">
                        <i class="mdi mdi-chevron-right text-primary text-sm mt-0.5 flex-shrink-0"></i>
                        <span>{{__('Enable the payment methods you want to accept in POS.')}}</span>
                    </div>
                    <div class="flex items-start gap-2.5 text-xs text-muted">
                        <i class="mdi mdi-chevron-right text-primary text-sm mt-0.5 flex-shrink-0"></i>
                        <span>{{__('E-Wallet supports a custom QR code or logo displayed at checkout.')}}</span>
                    </div>
                    <div class="flex items-start gap-2.5 text-xs text-muted">
                        <i class="mdi mdi-chevron-right text-primary text-sm mt-0.5 flex-shrink-0"></i>
                        <span>{{__('Multiple payment methods can be enabled at the same time.')}}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<x-media-upload.tw-markup/>

@endsection

@section('scripts')
<x-media-upload.tw-js/>
<script>
(function ($) {
    'use strict';

    // Tab switching
    $(document).on('click', '.pos-tab-btn', function () {
        let target = $(this).data('target');
        $('.pos-tab-btn').removeClass('active bg-primary text-white').addClass('text-muted');
        $(this).addClass('active bg-primary text-white').removeClass('text-muted');
        $('.pos-tab-panel').addClass('hidden');
        $('#' + target).removeClass('hidden');
    });

    $(document).ready(function () {
        $('.pos-tab-btn.active').addClass('bg-primary text-white');
        $('.pos-tab-btn:not(.active)').addClass('text-muted');
    });

    // Country → State cascade
    $(document).on('change', 'select[name=country]', function () {
        let country_id = $(this).val();
        if (country_id === "") {
            $("#state").html(""); $("#city").html("");
            toastr.error("{{__('Country field is required.')}}");
            return;
        }
        $.ajax({
            url: "{{ route('tenant.admin.tax-module.country.state.info.ajax') }}",
            type: "GET", data: {id: country_id},
            success: function (data) { $("#city").html(""); $("#state").html(data); },
            error: function () { toastr.error("{{__('Failed to load states.')}}"); }
        });
    });

    // State → City cascade
    $(document).on('change', 'select[name=state]', function () {
        let state_id = $(this).val();
        $.ajax({
            url: "{{ route('tenant.admin.tax-module.state.city.info.ajax') }}",
            type: "GET", data: {id: state_id},
            success: function (data) { $("#city").html(data); },
            error: function () { toastr.error("{{__('Failed to load cities.')}}"); }
        });
    });

})(jQuery);
</script>
@endsection
