@if(tenant())
    <div class="flex items-center justify-between mb-5">
        <div>
            <label class="lnd-label mb-0">{{__('Cash On Delivery')}}</label>
            <p class="text-xs text-muted">{{__('Enable Cash On Delivery')}}</p>
        </div>
        <label class="dr-toggle">
            <input type="hidden" name="cash_on_delivery" value="">
            <input type="checkbox" name="cash_on_delivery" value="on"
                @checked(!empty(get_static_option('cash_on_delivery')))>
            <span class="dr-toggle-track"></span>
        </label>
    </div>
@endif

<div class="space-y-3">
    @foreach($all_gateway as $gateway)
        <details class="bg-secondary border border-main rounded-xl group">
            <summary class="flex items-center justify-between px-4 py-3 cursor-pointer select-none">
                <span class="text-sm font-semibold text-dark">{{$gateway->name}}</span>
                <i class="las la-angle-down text-muted text-sm transition-transform group-open:rotate-180"></i>
            </summary>
            <div class="px-4 pb-4 pt-2 border-t border-main space-y-4">
                @if(!empty($gateway->description))
                    <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 flex items-start gap-2">
                        <i class="las la-info-circle text-amber-500 text-lg mt-0.5 flex-shrink-0"></i>
                        <p class="text-xs text-amber-700">{{$gateway->description}}</p>
                    </div>
                @endif

                <div class="flex items-center justify-between">
                    <label class="lnd-label mb-0">{{__('Enable/Disable '. ucfirst($gateway->name))}}</label>
                    <label class="dr-toggle">
                        <input type="hidden" name="{{$gateway->name}}_gateway" value="">
                        <input type="checkbox" name="{{$gateway->name}}_gateway"
                               @checked($gateway->status === 1)>
                        <span class="dr-toggle-track"></span>
                    </label>
                </div>

                <div class="flex items-center justify-between">
                    <label class="lnd-label mb-0">{{sprintf(__('Test Mode %s'), ucfirst($gateway->name))}}</label>
                    <label class="dr-toggle">
                        <input type="checkbox" name="{{$gateway->name}}_test_mode"
                               @checked($gateway->test_mode === 1)>
                        <span class="dr-toggle-track"></span>
                    </label>
                </div>

                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{ sprintf(__('%s Logo'), ucfirst($gateway->name))}}</label>
                    <x-fields.tw-media-upload name="{{$gateway->name.'_logo'}}" :id="$gateway->image"/>
                </div>

                @if($gateway->name === "paystack")
                    <div>
                        <p class="text-xs text-muted mb-1.5">{{__('Put below URL to "Settings > API Key & Webhook > Callback URL" in your Paystack panel')}}</p>
                        <input type="text" class="lnd-input text-xs" readonly
                               value="{{tenant() ? route('tenant.user.frontend.paystack.ipn') : route('landlord.frontend.paystack.ipn')}}">
                    </div>
                @endif

                @php
                    $credentials = !empty($gateway->credentials) ? json_decode($gateway->credentials) : [];
                @endphp
                @foreach($credentials as $cre_name => $cre_value)
                    <div>
                        <label class="lnd-label">{{ str_replace('_', ' ', ucwords($cre_name)) }}</label>
                        <input type="text" name="{{$gateway->name.'_'.$cre_name}}" value="{{$cre_value}}" class="lnd-input">
                        @if($gateway->name == 'paytabs' && $cre_name == 'region')
                            <p class="text-xs text-muted mt-1">GLOBAL, ARE, EGY, SAU, OMN, JOR</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </details>
    @endforeach
</div>
