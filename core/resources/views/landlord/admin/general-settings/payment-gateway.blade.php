@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Payment Settings')}} @endsection

@section('style')
    <x-summernote.css/>
@endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

<form action="{{route(route_prefix().'admin.general.payment.settings')}}" method="POST" enctype="multipart/form-data">
    @csrf

    {{-- Currency Settings Card --}}
    <div class="bg-surface rounded-xl shadow-main border border-main mb-5">
        <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-currency-usd text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Currency Settings')}}</h3>
                <p class="text-xs text-muted">{{__('Configure your site currency and formatting')}}</p>
            </div>
        </div>
        <div class="px-4 sm:px-6 py-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Site Global Currency')}}</label>
                    <select name="site_global_currency" class="lnd-input" id="site_global_currency">
                        @foreach(script_currency_list() as $cur => $symbol)
                            <option value="{{$cur}}" @selected(get_static_option('site_global_currency') == $cur)>{{$cur.' ( '.$symbol.' )'}}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Currency Symbol Position')}}</label>
                    @php $all_currency_position = ['left','right']; @endphp
                    <select name="site_currency_symbol_position" class="lnd-input" id="site_currency_symbol_position">
                        @foreach($all_currency_position as $cur)
                            <option value="{{$cur}}" @selected(get_static_option('site_currency_symbol_position') == $cur)>{{ucwords($cur)}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Custom Currency Symbol')}}</label>
                    <input type="text" class="lnd-input" name="site_custom_currency_symbol" value="{{get_static_option('site_custom_currency_symbol')}}">
                    <p class="text-[11px] text-muted mt-1.5">{{__("Leave empty if no custom symbol needed")}}</p>
                </div>
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Thousand Separator')}}</label>
                    <input type="text" class="lnd-input" name="site_custom_currency_thousand_separator" value="{{get_static_option('site_custom_currency_thousand_separator') ?? ','}}">
                </div>
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Decimal Separator')}}</label>
                    <input type="text" class="lnd-input" name="site_custom_currency_decimal_separator" value="{{get_static_option('site_custom_currency_decimal_separator') ?? '.'}}">
                </div>
            </div>

            <div class="flex items-center justify-between px-4 py-3 rounded-xl bg-secondary border border-main">
                <div>
                    <span class="text-sm font-semibold text-dark">{{__('Decimal Mode')}}</span>
                    <p class="text-[11px] text-muted">{{__('Enable/Disable amount decimal mode')}}</p>
                </div>
                <label class="dr-toggle">
                    <input type="hidden" name="currency_amount_type_status" value="">
                    <input type="checkbox" name="currency_amount_type_status" value="on"
                        @checked(!empty(get_static_option('currency_amount_type_status')))>
                    <span class="dr-toggle-track"></span>
                </label>
            </div>
        </div>
    </div>

    {{-- Default Gateway Card --}}
    <div class="bg-surface rounded-xl shadow-main border border-main mb-5">
        <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-credit-card-outline text-success text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Default Payment Gateway')}}</h3>
                <p class="text-xs text-muted">{{__('Select the default payment method for your site')}}</p>
            </div>
        </div>
        <div class="px-4 sm:px-6 py-5">
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Payment Gateway')}}</label>
            <select name="site_default_payment_gateway" class="lnd-input">
                @php
                    $all_gateways = ['cash_on_delivery','paypal','manual_payment','mollie','paytm','stripe','razorpay','flutterwave','paystack','midtranse','marcadopago','instamojo','cashfree', 'toyyibpay', 'zitopay', 'squareup', 'cinetpay', 'paytabs', 'billplz', 'payfast', 'sslcommerz'];
                @endphp
                @foreach($all_gateways as $gateway)
                    <option value="{{$gateway}}" @selected(get_static_option('site_default_payment_gateway') == $gateway)>{{ucwords(str_replace('_',' ',$gateway))}}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Exchange Rates Card --}}
    @php
        $global_currency = get_static_option('site_global_currency');
        $exchange_currencies = ['USD', 'IDR', 'INR', 'NGN', 'ZAR', 'BRL', 'BDT'];
        $has_exchange = false;
        foreach($exchange_currencies as $exCur) { if($global_currency != $exCur) { $has_exchange = true; break; } }
    @endphp

    @if($has_exchange)
    <div class="bg-surface rounded-xl shadow-main border border-main mb-5">
        <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background: var(--color-info-bg, #e0f2fe);">
                <i class="mdi mdi-swap-horizontal text-base" style="color: var(--color-info, #0ea5e9);"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Exchange Rates')}}</h3>
                <p class="text-xs text-muted">{{__('Set conversion rates from')}} <strong class="text-primary">{{$global_currency}}</strong></p>
            </div>
        </div>
        <div class="px-4 sm:px-6 py-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($exchange_currencies as $exCur)
                    @if($global_currency != $exCur)
                        <div class="px-4 py-3.5 rounded-xl bg-secondary border border-main">
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">
                                <span class="text-primary">{{$global_currency}}</span> → {{$exCur}}
                            </label>
                            <input type="text" class="lnd-input"
                                   name="site_{{strtolower($global_currency)}}_to_{{strtolower($exCur)}}_exchange_rate"
                                   value="{{get_static_option('site_'.$global_currency.'_to_'.strtolower($exCur).'_exchange_rate')}}"
                                   placeholder="1 {{$global_currency}} = ? {{$exCur}}">
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Save Button --}}
    <div class="flex items-center">
        <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
            <i class="mdi mdi-content-save-outline text-base"></i> {{__('Update Changes')}}
        </button>
    </div>

</form>

<x-media-upload.tw-markup/>

@endsection

@section('scripts')
    <x-summernote.js/>
    <x-media-upload.tw-js/>
    <script>
        (function($){
            "use strict";
            $(document).ready(function ($) {
                $('.summernote').summernote({
                    height: 200,
                    codemirror: {
                        theme: 'monokai'
                    },
                    callbacks: {
                        onChange: function(contents, $editable) {
                            $(this).prev('input').val(contents);
                        }
                    }
                });
                if($('.summernote').length > 0){
                    $('.summernote').each(function(index,value){
                        $(this).summernote('code', $(this).data('content'));
                    });
                }
            });
        })(jQuery);
    </script>
@endsection
