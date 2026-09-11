@extends('tenant.admin.admin-master')

@section('title')
    {{ __('Renew Domain - Domain Reseller Plugin') }}
@endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

@php
    $full_domain = $order_details->domain;
    $extension = last(explode('.', $full_domain));
    $fee_title = __(get_static_option_central('domain_reseller_additional_fee_title') ?? 'Platform Fee');
    $fee_amount = get_static_option_central('domain_reseller_additional_charge') ?? 0;
@endphp

<form action="{{route('tenant.admin.domain-reseller.renew', wrap_random_number($order_details->id))}}" method="POST">
    @csrf

    <div class="space-y-6">
        {{-- Domain Info --}}
        <div class="bg-surface rounded-xl shadow-main border border-main">
            <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-info-soft flex items-center justify-center flex-shrink-0">
                    <i class="las la-sync text-info text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Renew Domain')}}</h3>
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
                            <p class="text-xs text-warning mt-1">
                                <i class="las la-clock"></i>
                                {{__('Previous Expire Date:')}} {{$order_details->expire_at?->format('d-M-Y')}}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="price text-lg font-extrabold text-success">${{$order_details->domain_price}}</p>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-4">
                    <p class="renew-expire-text text-sm text-muted">
                        <span class="font-semibold text-dark">{{__('1 Year')}}</span>
                        <span class="text-xs">{{__('Expires on').' '.now()->addYear()->format('F Y')}}</span>
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

        {{-- Order Summary --}}
        <div class="dr-order-summary">
            <h3 class="dr-order-title">{{__('Order Summary')}}</h3>

            <div class="dr-order-row">
                <span class="dr-order-label">{{__('Subtotal')}}</span>
                <span class="dr-order-value subtotal">(USD) ${{$order_details->domain_price}}</span>
            </div>

            <div class="dr-order-row">
                <span class="dr-order-label">{{$fee_title}}</span>
                <span class="dr-order-value fee">(USD) ${{$fee_amount}}</span>
            </div>

            <div class="dr-order-row dr-order-total">
                <span class="dr-order-label font-bold text-dark">{{__('Total')}}</span>
                <span class="dr-order-value total">(USD) ${{$order_details->domain_price + $fee_amount}}</span>
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
</form>

@endsection

@section('scripts')
    <script>
        (function ($) {
            "use strict";

            $(document).on('click', '.payment-gateway-wrapper ul li', function () {
                let el = $(this);
                $('.payment-gateway-wrapper ul li').removeClass('selected');
                el.addClass('selected');
                $('input[name=selected_payment_gateway]').val(el.attr('data-gateway'));
            });

            $(document).on('click', '.pay-button', function () {
                $(this).find('.cm-spinner').removeClass('hidden');
            });

            const domainData = {
                domain: `{{$order_details->domain}}`,
                price: {{$order_details->domain_price}},
                fee: {{$fee_amount}},
                currency: 'USD',
                period: 1
            };

            $(document).on('change', 'select[name=period]', function () {
                domainData.period = $(this).val();
                finalRender();
            });

            function renewExpire() {
                let el = $('.renew-expire-text');
                let renew = $('input[name=auto_renew]').is(':checked');
                let label = renew ? '{{__("Renews on")}}' : '{{__("Expires on")}}';
                el.html(`<span class="font-semibold text-dark">${domainData.period} {{__('Year')}}</span> <span class="text-xs">${label} ${getDate()}</span>`);
            }

            function getDate() {
                const months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
                const d = new Date();
                return `${months[d.getMonth()]} ${d.getFullYear() + parseInt(domainData.period)}`;
            }

            function getNewPrice() {
                let sym = '$';
                let price = domainData.price * domainData.period;
                let total = price + Number(domainData.fee);

                $('.price').html(sym + price);
                $('.subtotal').html(`(${domainData.currency}) ${sym}${price}`);
                $('.total').html(`(${domainData.currency}) ${sym}${total}`);
            }

            function finalRender() { renewExpire(); getNewPrice(); }
            finalRender();
        })(jQuery);
    </script>
@endsection
