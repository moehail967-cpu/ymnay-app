@extends('tenant.admin.admin-master')

@section('title')
    {{ __('Order Successful - Domain Reseller Plugin') }}
@endsection

@section('style')
    <link rel="stylesheet" href="{{global_asset('assets/common/css/loader.css')}}">
@endsection

@section('content')

<div class="dr-loader-overlay" id="drLoader">
    <div class="dr-loader-spinner"></div>
    <p class="dr-loader-text">{{__('Processing...')}}</p>
</div>

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

<div class="dr-status-card shadow-main">
    <div class="dr-status-icon success">
        <i class="las la-check-circle"></i>
    </div>

    <h2 class="text-xl font-bold text-success mb-1">
        {{$order_details->purchase_count > 1 ? __('The order is renewed successfully') : __('The order is successful')}}
    </h2>
    <p class="text-sm text-muted mb-5">{{$order_details->domain .' - '. $order_details->period . ' Year'}}</p>

    @php
        $statusText = '\Modules\DomainReseller\Http\Enums\StatusEnum::getText';
        $colors = [0 => 'tw-pill-danger', 1 => 'tw-pill-success'];
    @endphp

    <div class="inline-flex items-center gap-4 bg-secondary border border-main rounded-xl px-6 py-4 mb-5">
        <div class="text-center">
            <p class="text-xs text-muted uppercase tracking-wider font-semibold mb-1">{{__('Payment Status')}}</p>
            <span class="tw-pill {{$colors[$order_details->payment_status]}}">
                {{$statusText($order_details->payment_status, true)}}
            </span>
        </div>
        <div class="w-px h-8 bg-main"></div>
        <div class="text-center">
            <p class="text-xs text-muted uppercase tracking-wider font-semibold mb-1">{{__('Domain Status')}}</p>
            <span class="tw-pill {{$colors[$order_details->status]}}">
                {{$statusText($order_details->status)}}
            </span>
        </div>
    </div>

    <div class="bg-secondary border border-main rounded-xl px-6 py-4 mb-5">
        <div class="flex items-center justify-between">
            <p class="text-sm font-bold text-dark">{{$order_details->domain .' - '. $order_details->period . ' Year'}}</p>
            @php
                $custom_domain = \App\Models\CustomDomain::where(['custom_domain' => $order_details->domain, 'custom_domain_status' => 'connected'])->exists();
            @endphp

            @if(!$custom_domain)
                <button class="activate-btn inline-flex items-center gap-1.5 px-5 py-2 rounded-lg bg-success text-white text-sm font-semibold hover:opacity-90 transition"
                        data-href="{{route('tenant.admin.domain-reseller.list.domain.activate')}}"
                        data-id="{{$order_details->id}}">
                    <i class="las la-bolt"></i> {{__('Activate')}}
                    <span class="cm-spinner hidden"></span>
                </button>
            @else
                <span class="tw-pill tw-pill-success">{{__('Already Activated')}}</span>
            @endif
        </div>
        <p class="text-xs text-muted mt-2">{{__('Now you can activate your newly purchased domain as your custom domain')}}</p>
    </div>
</div>

@endsection

@section('scripts')
    <script>
        (function ($) {
            "use strict";

            let unloadFlag = false;

            $(document).on('click', '.activate-btn', function (e) {
                e.preventDefault();
                unloadFlag = true;

                let el = $(this);
                let domain_id = el.attr('data-id');
                let url = el.attr('data-href');
                el.find('.cm-spinner').removeClass('hidden');
                el.attr('disabled', true);

                $.ajax({
                    type: 'GET',
                    url: `${url}?id=${domain_id}`,
                    beforeSend: function () { showLoader(); },
                    success: function (response) {
                        if (response.status) {
                            hideLoader();
                            el.find('.cm-spinner').addClass('hidden');
                            toastr.success(response.msg);
                            unloadFlag = false;
                            setTimeout(() => location.reload(), 2000);
                        }
                    },
                    error: function (response) {
                        hideLoader();
                        el.find('.cm-spinner').addClass('hidden');
                        el.attr('disabled', false);
                        toastr.error(response.msg || '{{__("Something went wrong")}}');
                        unloadFlag = false;
                    }
                });
            });

            $(window).bind('beforeunload', function () {
                if (unloadFlag) {
                    return `{{__('If you leave, the action will be terminated. Are you sure?')}}`;
                }
            });

            const textSteps = ['{{__("Setting Up DNS")}}', '{{__("Configuring Custom Domain")}}', '{{__("Getting Ready")}}'];
            let stepIndex = 0, timer = '';

            function animateText(running) {
                if (running) {
                    timer = setTimeout(function () {
                        $('#drLoader .dr-loader-text').text(textSteps[stepIndex] + '..');
                        if (stepIndex < textSteps.length - 1) animateText(true);
                        stepIndex++;
                    }, 1000);
                } else {
                    stepIndex = 0;
                    clearTimeout(timer);
                }
            }

            const showLoader = () => { $('#drLoader').addClass('active'); animateText(true); };
            const hideLoader = () => { $('#drLoader').removeClass('active'); animateText(false); };
        })(jQuery);
    </script>
@endsection
