@extends('tenant.admin.admin-master')

@section("title", __("Tax Module Settings"))

@section("content")

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <div class="lg:col-span-8">
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-cog-outline text-primary text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Tax Module Settings')}}</h3>
                    <p class="text-xs text-muted">{{__('Configure your store tax system')}}</p>
                </div>
            </div>

            <div class="p-4 sm:p-6">
                <form action="{{ route('tenant.admin.tax-module.settings') }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="space-y-6">

                        {{-- Tax System --}}
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase">{{__('Select Tax System')}}</label>
                                <button type="button" id="enable-info-about-tax-system" class="w-5 h-5 rounded-full bg-info-soft flex items-center justify-center text-info hover:bg-info hover:text-white transition" title="{{__('Info')}}">
                                    <i class="mdi mdi-information-outline text-xs"></i>
                                </button>
                            </div>
                            <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                                <i class="mdi mdi-calculator-variant-outline text-lg text-primary"></i>
                                <select class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer" name="tax_system" id="tax_system">
                                    <option @selected(get_static_option('tax_system') == "zone_wise_tax_system") value="zone_wise_tax_system">{{ __("Zone wise tax system") }}</option>
                                    <option @selected(get_static_option("tax_system") == "advance_tax_system") value="advance_tax_system">{{ __("Advance Tax system") }}</option>
                                </select>
                                <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                            </div>
                        </div>

                        {{-- Advanced Settings --}}
                        <div id="advance_tax_system_settings" class="space-y-5">

                            {{-- Prices with tax --}}
                            <div>
                                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Prices entered with tax')}}</label>
                                <div class="bg-secondary border border-main rounded-xl p-4 space-y-2.5">
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input name="prices_include_tax" @checked(get_static_option("prices_include_tax") == 'yes') value="yes" type="radio"
                                               class="w-4 h-4 text-primary border-gray-300 focus:ring-primary">
                                        <span class="text-sm text-dark group-hover:text-primary transition">{{__('Yes, I will enter prices inclusive of tax')}}</span>
                                    </label>
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input name="prices_include_tax" @checked(get_static_option("prices_include_tax") == 'no') value="no" type="radio"
                                               class="w-4 h-4 text-primary border-gray-300 focus:ring-primary">
                                        <span class="text-sm text-dark group-hover:text-primary transition">{{__('No, I will enter prices exclusive of tax')}}</span>
                                    </label>
                                </div>
                            </div>

                            {{-- Calculate tax based on --}}
                            <div>
                                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Calculate tax based on')}}</label>
                                <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                                    <i class="mdi mdi-account-outline text-lg text-primary"></i>
                                    <select name="calculate_tax_based_on" id="calculate_tax_based_on" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                                        <option @selected(get_static_option("calculate_tax_based_on") == "customer_account_address") value="customer_account_address">{{ __("Customer Account Address") }}</option>
                                        <option @selected(get_static_option("calculate_tax_based_on") == "customer_billing_address") value="customer_billing_address">{{ __("Customer Billing Address") }}</option>
                                    </select>
                                    <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                                </div>
                            </div>

                            {{-- Shipping tax class --}}
                            <div>
                                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Shipping tax class')}}</label>
                                <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                                    <i class="mdi mdi-truck-outline text-lg text-primary"></i>
                                    <select name="shipping_tax_class" id="shipping_tax_class" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                                        <option value="shipping_tax_class_based_on_cart_items">{{__("Shipping tax class based on cart items")}}</option>
                                        @foreach($taxClasses as $taxClass)
                                            <option @selected(get_static_option("shipping_tax_class") == $taxClass->id) value="{{ $taxClass->id }}">{{ $taxClass->name }}</option>
                                        @endforeach
                                    </select>
                                    <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                                </div>
                                <a href="{{ route('tenant.admin.tax-module.tax-class') }}" class="inline-flex items-center gap-1 text-xs text-primary hover:underline mt-1.5">
                                    <i class="mdi mdi-plus-circle-outline text-sm"></i> {{ __("Add tax class") }}
                                </a>
                            </div>

                            {{-- Rounding --}}
                            <div>
                                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Rounding')}}</label>
                                <label class="flex items-center gap-3 cursor-pointer bg-secondary border border-main rounded-xl px-4 py-3 group hover:border-primary transition">
                                    <input name="tax_round_at_subtotal" {{ get_static_option("tax_round_at_subtotal") ? "checked" : "" }} id="tax_round_at_subtotal" type="checkbox" value="1"
                                           class="w-4 h-4 rounded text-primary border-gray-300 focus:ring-primary">
                                    <span class="text-sm text-dark">{{ __("Round tax at subtotal level, instead of rounding per line") }}</span>
                                </label>
                            </div>

                            {{-- Display prices --}}
                            <div class="display_price_wrapper">
                                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Display prices in the shop')}}</label>
                                <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                                    <i class="mdi mdi-tag-outline text-lg text-primary"></i>
                                    <select id="display_price_in_the_shop" name="display_price_in_the_shop" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                                        <option @selected(get_static_option("display_price_in_the_shop") == "including") value="including">{{__("Including tax")}}</option>
                                        <option @selected(get_static_option("display_price_in_the_shop") == "exclusive") value="exclusive">{{ __("Exclusive tax") }}</option>
                                    </select>
                                    <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                                </div>
                            </div>
                        </div>

                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                            <i class="mdi mdi-content-save-outline text-base"></i> {{__('Update Tax Settings')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Info Panel --}}
    <div class="lg:col-span-4">
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden info-tax-system hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-info-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-information-outline text-info text-base"></i>
                </div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('About Advanced Tax System')}}</h3>
            </div>
            <div class="p-4 sm:p-6 space-y-3">
                <p class="text-sm text-muted leading-relaxed">{{ __("Our Advanced Tax System offers unparalleled features for seamless tax management.") }}</p>
                <p class="text-sm text-muted leading-relaxed">{{ __("With the ability to select multiple taxes across various regions, you can effortlessly comply with global regulations.") }}</p>
                <p class="text-sm text-muted leading-relaxed">{{ __("Choose to display taxes individually per product or as a subtotal at checkout, providing transparency and a streamlined customer experience.") }}</p>
                <p class="text-sm text-muted leading-relaxed">{{ __("Simplify tax calculations, enhance efficiency, and ensure compliance with ease.") }}</p>
            </div>
        </div>
    </div>
</div>

@endsection

@section("scripts")
    <script>
        (function ($) {
            "use strict";

            $(document).on("click", "#enable-info-about-tax-system", function () {
                $(".info-tax-system").toggleClass("hidden");
            });

            tax_system();
            sidebar();
            display_price_in_shop($('input[name=prices_include_tax]:checked'));

            $(document).on("change", "#tax_system", function () {
                tax_system();
                var tax_type = $(this);
                $('label.info-message').remove();
                if (tax_type.val() === 'zone_wise_tax_system') {
                    tax_type.closest('.bg-secondary').after('<label class="info-message text-xs text-primary mt-2 block">{{__("Press update, you will get country and state tax option on sidebar")}}</label>');
                }
            });

            $(document).on('change', 'input[name=prices_include_tax]', function () {
                display_price_in_shop($(this));
            });

            function tax_system() {
                var tax_system = $("#tax_system").val();
                if (tax_system === 'zone_wise_tax_system') {
                    $("#advance_tax_system_settings").fadeOut();
                    return;
                }
                $("#advance_tax_system_settings").fadeIn();
            }

            function sidebar() {
                var tax_system = $("#tax_system").val();
                var tax_manage_menu = $('#tax-manage-menu-items');
                var country_state = tax_manage_menu.find('ul').children().slice(0, 2);
                var tax_class = tax_manage_menu.find('ul').children().slice(3, 4);
                if (tax_system === 'zone_wise_tax_system') {
                    country_state.fadeIn();
                    tax_class.fadeOut();
                } else {
                    country_state.fadeOut();
                    tax_class.fadeIn();
                }
            }

            function display_price_in_shop(selector) {
                if (selector.val() === 'yes') {
                    $('.display_price_wrapper').fadeIn();
                } else {
                    $('.display_price_wrapper').hide();
                }
            }
        })(jQuery);
    </script>
@endsection
