@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Payment Settings')}} @endsection

@section('style')
    <x-summernote.css/>
@endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    {{-- Card Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
            <i class="las la-credit-card text-success text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">
                @if(isset($cod) && $cod)
                    {{__('Cash On Delivery')}}
                @else
                    {{ucfirst($gateway->name ?? '')}} {{__('Gateway Settings')}}
                @endif
            </h3>
            <p class="text-xs text-muted">{{__('Configure payment method credentials and options')}}</p>
        </div>
    </div>

    {{-- Card Body --}}
    <div class="px-4 sm:px-6 py-5">
        <form action="{{route(route_prefix().'admin.payment.settings.update')}}" method="POST" enctype="multipart/form-data">
            @csrf

            @if(isset($cod) && $cod)
                {{-- ── Cash On Delivery ── --}}
                <input type="hidden" name="gateway_name" value="cash_on_delivery">
                @if(tenant())
                    <div class="flex items-center justify-between p-4 rounded-xl bg-secondary border border-subtle">
                        <div>
                            <p class="text-sm font-semibold text-dark">{{__('Enable Cash On Delivery')}}</p>
                            <p class="text-xs text-muted mt-0.5">{{__('Allow customers to pay on delivery')}}</p>
                        </div>
                        <label class="dr-toggle">
                            <input type="checkbox" name="cash_on_delivery"
                                @checked(!empty(get_static_option('cash_on_delivery')))>
                            <span class="dr-toggle-track"></span>
                        </label>
                    </div>
                @endif
            @else
                {{-- ── Regular Gateway ── --}}
                <input type="hidden" name="gateway_name" value="{{$gateway->name}}">

                @if(!empty($gateway->description))
                    <div class="flex items-start gap-3 p-4 rounded-xl bg-warning-soft border border-subtle mb-5">
                        <i class="las la-info-circle text-warning text-lg mt-0.5 flex-shrink-0"></i>
                        <p class="text-sm text-dark">{{$gateway->description}}</p>
                    </div>
                @endif

                <div class="space-y-5">
                    {{-- Enable/Disable & Test Mode Toggles --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex items-center justify-between p-4 rounded-xl bg-secondary border border-subtle">
                            <div>
                                <p class="text-sm font-semibold text-dark">{{__('Enable')}} {{ucfirst($gateway->name)}}</p>
                                <p class="text-xs text-muted mt-0.5">{{__('Activate this payment gateway')}}</p>
                            </div>
                            <label class="dr-toggle">
                                <input type="hidden" name="{{$gateway->name}}_gateway">
                                <input type="checkbox" name="{{$gateway->name}}_gateway"
                                    @checked($gateway->status === 1)>
                                <span class="dr-toggle-track"></span>
                            </label>
                        </div>

                        <div class="flex items-center justify-between p-4 rounded-xl bg-secondary border border-subtle">
                            <div>
                                <p class="text-sm font-semibold text-dark">{{__('Test Mode')}}</p>
                                <p class="text-xs text-muted mt-0.5">{{__('Use sandbox credentials')}}</p>
                            </div>
                            <label class="dr-toggle">
                                <input type="checkbox" name="{{$gateway->name}}_test_mode"
                                    @checked($gateway->test_mode === 1)>
                                <span class="dr-toggle-track"></span>
                            </label>
                        </div>
                    </div>

                    {{-- Gateway Logo --}}
                    <div>
                        <x-fields.tw-media-upload
                            name="{{$gateway->name.'_logo'}}"
                            title="{{ucfirst($gateway->name)}} {{__('Logo')}}"
                            :id="$gateway->image"/>
                    </div>

                    {{-- Paystack Callback URL --}}
                    @if($gateway->name === 'paystack')
                        <div class="p-4 rounded-xl bg-info-soft border border-subtle">
                            <p class="text-sm font-medium text-dark mb-2">
                                <i class="las la-link mr-1"></i>
                                {{__('Callback URL')}}
                            </p>
                            <p class="text-xs text-muted mb-2">{{__('Add this URL to "Settings > API Key & Webhook > Callback URL" in your Paystack dashboard')}}</p>
                            <input type="text" class="lnd-input font-mono text-xs" readonly
                                   value="{{tenant() ? route('tenant.user.frontend.paystack.ipn') : route('landlord.frontend.paystack.ipn')}}"
                                   onclick="this.select()">
                        </div>
                    @endif

                    {{-- Dynamic Credentials --}}
                    @php
                        $credentials = !empty($gateway->credentials) ? json_decode($gateway->credentials) : [];
                    @endphp

                    @if(!empty((array)$credentials))
                        <div class="space-y-4">
                            <h4 class="text-xs font-bold text-muted uppercase tracking-widest">{{__('API Credentials')}}</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($credentials as $cre_name => $cre_value)
                                    <div>
                                        <label class="lnd-label">{{ str_replace('_', ' ', ucwords($cre_name)) }}</label>
                                        <input type="text"
                                               name="{{$gateway->name.'_'.$cre_name}}"
                                               value="{{$cre_value}}"
                                               class="lnd-input">
                                        @if($gateway->name == 'paytabs' && $cre_name == 'region')
                                            <p class="text-xs text-muted mt-1">GLOBAL, ARE, EGY, SAU, OMN, JOR</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Razorpay Recurring (Landlord Only) --}}
                    @if($gateway->name === 'razorpay' && route_prefix() == 'landlord.' && !tenant())
                        <div class="space-y-4 pt-4 border-t border-main">
                            <div class="flex items-center justify-between p-4 rounded-xl bg-secondary border border-subtle">
                                <div>
                                    <p class="text-sm font-semibold text-dark">{{__('Enable Recurring Subscriptions')}}</p>
                                    <p class="text-xs text-muted mt-0.5">{{__('Automatic recurring subscriptions for new tenants. Existing tenants can opt-in from their dashboard.')}}</p>
                                </div>
                                <label class="dr-toggle">
                                    <input type="hidden" name="razorpay_recurring_enabled">
                                    <input type="checkbox" name="razorpay_recurring_enabled"
                                        @checked(get_static_option('razorpay_recurring_enabled'))>
                                    <span class="dr-toggle-track"></span>
                                </label>
                            </div>

                            <div class="p-4 rounded-xl bg-info-soft border border-subtle">
                                <p class="text-sm font-medium text-dark mb-2">
                                    <i class="las la-link mr-1"></i>
                                    {{__('Webhook Configuration')}}
                                </p>
                                <p class="text-xs text-muted mb-2">{{__('Add this webhook URL to your Razorpay Dashboard (Settings > Webhooks):')}}</p>
                                <input type="text" class="lnd-input font-mono text-xs" readonly
                                       value="{{route('landlord.frontend.razorpay.subscription.webhook')}}"
                                       onclick="this.select()">
                                <p class="text-xs text-muted mt-1.5">{{__('Select all events.')}}</p>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Save Button --}}
            <div class="pt-4 mt-5 border-t border-main">
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                    <i class="las la-save"></i> {{__('Update Changes')}}
                </button>
            </div>
        </form>
    </div>
</div>

<x-media-upload.tw-markup/>

@endsection

@section('scripts')
    <x-summernote.js/>
    <x-media-upload.tw-js/>
    <script>
        (function($){
            "use strict";
            $(document).ready(function(){
                $('.summernote').summernote({
                    height: 200,
                    codemirror: { theme: 'monokai' },
                    callbacks: {
                        onChange: function(contents, $editable) {
                            $(this).prev('input').val(contents);
                        }
                    }
                });
                if($('.summernote').length > 0){
                    $('.summernote').each(function(){
                        $(this).summernote('code', $(this).data('content'));
                    });
                }
            });
        })(jQuery);
    </script>
@endsection
