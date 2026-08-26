<div class="space-y-5">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="lnd-label">{{__('Site Global Currency')}}</label>
            <select name="site_global_currency" class="lnd-input" id="site_global_currency">
                @foreach(script_currency_list() as $cur => $symbol)
                    <option value="{{$cur}}" @selected(get_static_option('site_global_currency') == $cur)>{{$cur.' ( '.$symbol.' )'}}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="lnd-label">{{__('Currency Symbol Position')}}</label>
            @php $all_currency_position = ['left','right']; @endphp
            <select name="site_currency_symbol_position" class="lnd-input" id="site_currency_symbol_position">
                @foreach($all_currency_position as $cur)
                    <option value="{{$cur}}" @selected(get_static_option('site_currency_symbol_position') == $cur)>{{ucwords($cur)}}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="lnd-label">{{__('Custom Currency Symbol')}}</label>
            <input type="text" class="lnd-input" name="site_custom_currency_symbol" value="{{get_static_option('site_custom_currency_symbol')}}">
            <p class="text-xs text-muted mt-1.5">{{__("If you don't have any custom symbol then leave this field empty")}}</p>
        </div>
        <div>
            <label class="lnd-label">{{__('Thousand Separator')}}</label>
            <input type="text" class="lnd-input" name="site_custom_currency_thousand_separator" value="{{get_static_option('site_custom_currency_thousand_separator') ?? ','}}">
        </div>
        <div>
            <label class="lnd-label">{{__('Decimal Separator')}}</label>
            <input type="text" class="lnd-input" name="site_custom_currency_decimal_separator" value="{{get_static_option('site_custom_currency_decimal_separator') ?? '.'}}">
        </div>
    </div>

    <div class="flex items-center justify-between">
        <div>
            <label class="lnd-label mb-0">{{__('Decimal Mode')}}</label>
            <p class="text-xs text-muted">{{__('Enable/Disable amount decimal mode')}}</p>
        </div>
        <label class="dr-toggle">
            <input type="hidden" name="currency_amount_type_status" value="">
            <input type="checkbox" name="currency_amount_type_status" value="on"
                @checked(!empty(get_static_option('currency_amount_type_status')))>
            <span class="dr-toggle-track"></span>
        </label>
    </div>

    <div>
        <label class="lnd-label">{{__('Default Payment Gateway')}}</label>
        <select name="site_default_payment_gateway" class="lnd-input">
            @php
                $all_gateways = ['cash_on_delivery','paypal','manual_payment','mollie','paytm','stripe','razorpay','flutterwave','paystack','midtranse','marcadopago','instamojo','cashfree', 'toyyibpay', 'zitopay', 'squareup', 'cinetpay', 'paytabs', 'billplz', 'payfast', 'sslcommerz'];
            @endphp
            @foreach($all_gateways as $gateway)
                <option value="{{$gateway}}" @selected(get_static_option('site_default_payment_gateway') == $gateway)>{{ucwords(str_replace('_',' ',$gateway))}}</option>
            @endforeach
        </select>
    </div>

    {{-- Exchange Rates --}}
    @php $global_currency = get_static_option('site_global_currency'); @endphp

    @php
        $exchange_currencies = ['USD', 'IDR', 'INR', 'NGN', 'ZAR', 'BRL', 'BDT'];
    @endphp

    @foreach($exchange_currencies as $exCur)
        @if($global_currency != $exCur)
            <div>
                <label class="lnd-label">{{__($global_currency.' to '.$exCur.' Exchange Rate')}}</label>
                <input type="text" class="lnd-input"
                       name="site_{{strtolower($global_currency)}}_to_{{strtolower($exCur)}}_exchange_rate"
                       value="{{get_static_option('site_'.$global_currency.'_to_'.strtolower($exCur).'_exchange_rate')}}">
                <p class="text-xs text-muted mt-1.5">{{__("Enter {$global_currency} to {$exCur} exchange rate. e.g. 1 {$global_currency} = ? {$exCur}")}}</p>
            </div>
        @endif
    @endforeach
</div>
