@extends('tenant.admin.admin-master')

@section('title')
    {{ __('Domain Reseller Plugin') }}
@endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

@php
    $full_domain = $data['data']['domain'];
    $exploded = explode('.', $full_domain);
    $extension = last($exploded);
    $fee_title = __(get_static_option_central('domain_reseller_additional_fee_title') ?? 'Platform Fee');
    $fee_amount = get_static_option_central('domain_reseller_additional_charge') ?? 0;
@endphp

<form action="{{route('tenant.admin.domain-reseller.checkout')}}" method="POST">
    @csrf

    <div class="grid grid-cols-12 gap-6">
        {{-- Left Column: Domain + Contact Forms --}}
        <div class="col-span-12 lg:col-span-8 space-y-5">

            {{-- Domain Info --}}
            <div class="bg-surface rounded-xl shadow-main border border-main">
                <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                        <i class="las la-globe text-primary text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Checkout')}}</h3>
                        <p class="text-xs text-muted">{{__('Fill up the form to proceed')}}</p>
                    </div>
                </div>
                <div class="px-4 sm:px-6 py-5">
                    <div class="dr-domain-bar">
                        <div class="flex items-center gap-3">
                            <div class="dr-www">WWW</div>
                            <div>
                                <h4 class="text-base font-bold text-dark">{{$full_domain}}</h4>
                                <p class="text-xs text-muted">.{{strtoupper($extension)}} {{__('Domain Registration')}}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="price text-lg font-extrabold text-success">{{$data['data']['currency'] === 'USD' ? '$' : $data['data']['currency']}}{{$data['data']['price']}}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-4">
                        <p class="renew-expire-text text-sm text-muted">
                            <span class="font-semibold text-dark">{{__('1 Year')}}</span>
                            <span class="text-xs">{{__('Expires on')}}</span>
                        </p>
                        <div>
                            <label class="lnd-label text-xs mb-1">{{__('Select Period')}}</label>
                            <select name="period" id="period" class="lnd-input !py-1.5 !text-sm">
                                <option value="1">{{__('1 year validity')}}</option>
                                <option value="2">{{__('2 year validity')}}</option>
                                <option value="3">{{__('3 year validity')}}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contact Admin --}}
            <div class="bg-surface rounded-xl shadow-main border border-main">
                <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl">
                    <h4 class="text-sm font-bold text-dark">{{__('Contact Admin')}}</h4>
                </div>
                <div class="px-4 sm:px-6 py-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="lnd-label">{{__('First name')}} <span class="text-danger">*</span></label>
                            <input type="text" class="lnd-input" name="nameFirst" value="{{old('nameFirst')}}" placeholder="{{__('Write first name')}}">
                        </div>
                        <div>
                            <label class="lnd-label">{{__('Last name')}} <span class="text-danger">*</span></label>
                            <input type="text" class="lnd-input" name="nameLast" value="{{old('nameLast')}}" placeholder="{{__('Write last name')}}">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="lnd-label">{{__('Middle name')}}</label>
                        <input type="text" class="lnd-input" name="nameMiddle" value="{{old('nameMiddle')}}" placeholder="{{__('Write middle name')}}">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                        <div>
                            <label class="lnd-label">{{__('Email')}} <span class="text-danger">*</span></label>
                            <input type="email" class="lnd-input" name="email" value="{{old('email')}}" placeholder="{{__('Write email address')}}">
                        </div>
                        <div>
                            <label class="lnd-label">{{__('Phone')}} <span class="text-danger">*</span></label>
                            <input type="text" class="lnd-input" name="phone" value="{{old('phone')}}" placeholder="{{__('Example +1.12345 67890')}}">
                            <p class="text-xs text-muted mt-1">{{__('Separate with dot (.)')}}</p>
                        </div>
                        <div>
                            <label class="lnd-label">{{__('Fax')}}</label>
                            <input type="text" class="lnd-input" name="fax" value="{{old('fax')}}" placeholder="{{__('Example +1.12345 67890')}}">
                            <p class="text-xs text-muted mt-1">{{__('Separate with dot (.)')}}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="lnd-label">{{__('Job Title')}}</label>
                            <input type="text" class="lnd-input" name="jobTitle" value="{{old('jobTitle')}}" placeholder="{{__('Write job title')}}">
                        </div>
                        <div>
                            <label class="lnd-label">{{__('Organization')}}</label>
                            <input type="text" class="lnd-input" name="organization" value="{{old('organization')}}" placeholder="{{__('Write organization name')}}">
                        </div>
                    </div>

                    <div class="mt-5 pt-4 border-t border-main">
                        <h5 class="text-xs font-bold text-dark uppercase tracking-wider mb-3">{{__('Address')}}</h5>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div>
                                <label class="lnd-label">{{__('Country')}} <span class="text-danger">*</span></label>
                                <select class="lnd-input country" name="country" id="country">
                                    <option value="">{{__('Select a country')}}</option>
                                    @foreach($data['countries'] ?? [] as $country)
                                        <option value="{{$country->countryKey}}">{{$country->label}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="lnd-label">{{__('State')}} <span class="text-danger">*</span></label>
                                <select class="lnd-input state" name="state" id="state"></select>
                            </div>
                            <div>
                                <label class="lnd-label">{{__('City')}} <span class="text-danger">*</span></label>
                                <input type="text" class="lnd-input" name="city" id="city" value="{{old('city')}}" placeholder="{{__('Write city name')}}">
                            </div>
                            <div>
                                <label class="lnd-label">{{__('Postal Code')}} <span class="text-danger">*</span></label>
                                <input type="text" class="lnd-input" name="postalCode" id="postalCode" value="{{old('postalCode')}}" placeholder="{{__('Write postal code')}}">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="lnd-label">{{__('Address One')}} <span class="text-danger">*</span></label>
                            <textarea name="address1" class="lnd-input" id="address1" rows="3">{{old('address1')}}</textarea>
                        </div>
                        <div class="mt-4">
                            <label class="lnd-label">{{__('Address Two')}}</label>
                            <textarea name="address2" class="lnd-input" id="address2" rows="3">{{old('address2')}}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Additional Contact Sections --}}
            <x-domainreseller::domain-checkout-form title="{{ __('Contact Billing') }}" key="contact_billing" :countries="$data['countries']"/>
            <x-domainreseller::domain-checkout-form title="{{ __('Contact Registrant') }}" key="contact_registrant" :countries="$data['countries']"/>
            <x-domainreseller::domain-checkout-form title="{{ __('Contact Tech') }}" key="contact_tech" :countries="$data['countries']"/>
        </div>

        {{-- Right Column: Order Summary --}}
        <div class="col-span-12 lg:col-span-4">
            <div class="dr-order-summary sticky top-4">
                <h3 class="dr-order-title">{{__('Order Summary')}}</h3>

                <div class="dr-order-row">
                    <span class="dr-order-label">{{__('Subtotal')}}</span>
                    <span class="dr-order-value subtotal">(USD) $00.00</span>
                </div>

                <div class="dr-order-row">
                    <span class="dr-order-label">{{$fee_title}}</span>
                    <span class="dr-order-value fee">(USD) ${{$fee_amount}}</span>
                </div>

                <div class="dr-order-row dr-order-total">
                    <span class="dr-order-label font-bold text-dark">{{__('Total')}}</span>
                    <span class="dr-order-value total">(USD) ${{$fee_amount}}</span>
                </div>

                <div class="mt-4">
                    <p class="text-xs font-semibold text-muted uppercase tracking-wider mb-2">{{__('Payment Method')}}</p>
                    <div class="payment-gateways">
                        {!! \App\Helpers\PaymentGatewayRenderHelper::renderPaymentGatewayForForm(['manual_payment']) !!}
                    </div>
                </div>

                <button type="submit" class="dr-pay-btn pay-button">
                    {{__("I'm Ready to Pay")}}
                    <span class="cm-spinner hidden"></span>
                </button>
            </div>
        </div>
    </div>
</form>

@endsection

@section('scripts')
    <script>
        (function ($) {
            "use strict";

            // Countdown for duplicate payment error
            $(document).ready(function () {
                let counterText = $('.alert.alert-danger p');
                if (counterText.text().includes('1 minutes')) {
                    let counter = 60;
                    let interval = setInterval(function () {
                        counter--;
                        if (counter <= 0) {
                            clearInterval(interval);
                            counterText.parent().slideUp();
                        } else {
                            counterText.text("{{__('Can not proceed duplicate payment, wait for next')}} " + counter + " {{__('seconds')}}");
                        }
                    }, 1000);
                }
            });

            // Payment gateway selection
            $(document).on('click', '.payment-gateway-wrapper ul li', function () {
                let el = $(this);
                $('.payment-gateway-wrapper ul li').removeClass('selected');
                el.addClass('selected');
                $('input[name=selected_payment_gateway]').val(el.attr('data-gateway'));
            });

            // Expand / collapse contact sections
            $(document).on('click', '.expand-collapse-btn', function (e) {
                e.preventDefault();
                $(this).closest('.contact-parent-wrapper').find('.contact-form-wrapper').toggleClass('hidden');
            });

            // Same as contact admin
            $('.contact-parent-wrapper').find('input, textarea, select').attr('disabled', true);
            $('.same-contact-admin').attr('disabled', false);

            $(document).on('click', '.same-contact-admin', function () {
                let el = $(this);
                let parent = el.closest('.contact-parent-wrapper');

                if (el.is(':checked')) {
                    parent.find('input, textarea, select').attr('disabled', true);
                    el.attr('disabled', false);
                    parent.find('.contact-form-wrapper').addClass('hidden');
                    el.siblings('.was_unchecked').val("");
                } else {
                    parent.find('input, textarea, select').attr('disabled', false);
                    parent.find('.contact-form-wrapper').removeClass('hidden');
                    el.siblings('.was_unchecked').val("yes");
                }
            });

            // Pay button loader
            $(document).on('click', '.pay-button', function () {
                $(this).find('.cm-spinner').removeClass('hidden');
            });

            // Country → State dropdown
            $(document).on('change', 'select.country', function () {
                let el = $(this);
                let selected = el.val();
                let stateEl = el.closest('.grid').find('.state');

                $.ajax({
                    url: `{{route('tenant.admin.domain-reseller.states')}}?countryKey=${selected}`,
                    type: 'GET',
                    beforeSend: function () {
                        stateEl.html(`<option value=''>{{__('Loading...')}}</option>`);
                    },
                    success: function (response) {
                        if (response.status && response.states.states.length > 0) {
                            let opts = `<option value=''>{{__('Select a state')}}</option>`;
                            response.states.states.forEach((v) => {
                                opts += `<option value='${v.stateKey}'>${v.label}</option>`;
                            });

                            if (stateEl.prop('tagName') === 'INPUT') {
                                stateEl.replaceWith(`<select class="lnd-input state" name="state" id="state">${opts}</select>`);
                            } else {
                                stateEl.html(opts);
                            }
                        } else {
                            stateEl.replaceWith(`<input class="lnd-input state" name="state" id="state" placeholder="{{__('Write state name')}}">`);
                        }
                    },
                    error: function () {
                        stateEl.html(`<option value=''>{{__('Something went wrong')}}</option>`);
                    }
                });
            });

            // Price calculation
            const domainData = {
                domain: `{{$data['data']['domain']}}`,
                price: {{round($data['data']['price'], 2)}},
                fee: {{round($fee_amount, 2)}},
                currency: `{{$data['data']['currency']}}`,
                renew: false,
                period: 1
            };

            $(document).on('change', 'select[name=period]', function () {
                domainData.period = $(this).val();
                finalRender();
            });

            function renewExpire() {
                let el = $('.renew-expire-text');
                domainData.renew = $('input[name=auto_renew]').is(':checked');
                let label = domainData.renew ? '{{__("Renews on")}}' : '{{__("Expires on")}}';
                el.html(`<span class="font-semibold text-dark">${domainData.period} {{__('Year')}}</span> <span class="text-xs">${label} ${getDate()}</span>`);
            }

            function getDate() {
                const months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
                const d = new Date();
                return `${months[d.getMonth()]} ${d.getFullYear() + Number(domainData.period)}`;
            }

            function getNewPrice() {
                let sym = domainData.currency === 'USD' ? '$' : domainData.currency;
                let price = domainData.price * domainData.period;
                let total = price + Number(domainData.fee);

                $('.price').html(sym + price);
                $('.subtotal').html(`(${domainData.currency}) ${sym}${price}`);
                $('.total').html(`(${domainData.currency}) ${sym}${total.toFixed(2)}`);
            }

            function finalRender() { renewExpire(); getNewPrice(); }
            finalRender();

            // Restore old form state
            const oldVals = {
                contact_billing_was_unchecked: `{{old('contact_billing_was_unchecked')}}`,
                contact_registrant_was_unchecked: `{{old('contact_registrant_was_unchecked')}}`,
                contact_tech_was_unchecked: `{{old('contact_tech_was_unchecked')}}`
            };

            for (const key in oldVals) {
                if (oldVals[key] !== '') {
                    let el = $(`input[name=${key}]`);
                    el.siblings('.same-contact-admin').attr('checked', false);
                    el.closest('.contact-parent-wrapper').find('.contact-form-wrapper').removeClass('hidden');
                    el.closest('.contact-parent-wrapper').find('.contact-form-wrapper').find('input,select,textarea').attr('disabled', false);
                }
            }
        })(jQuery);
    </script>
@endsection
