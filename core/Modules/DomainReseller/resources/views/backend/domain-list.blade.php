@extends(route_prefix().'admin.admin-master')

@section('title')
    {{ __('Domain List - Domain Reseller Plugin') }}
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

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
                <i class="las la-globe-americas text-success text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Purchased Domains')}}</h3>
                <p class="text-xs text-muted">{{__('All your purchased domains are listed below')}}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{route(route_prefix().'admin.domain-reseller.index')}}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-main text-xs font-semibold text-muted hover:text-dark hover:border-sidebar-hover transition">
                <i class="las la-arrow-left"></i> {{__('Back')}}
            </a>
            <a href="{{route(route_prefix().'admin.domain-reseller.list.domain.failed')}}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-main text-xs font-semibold text-danger hover:bg-danger-soft transition">
                <i class="las la-exclamation-circle"></i>
                {{tenant() ? __('Pending Purchases') : __('Failed Purchases')}}
            </a>
        </div>
    </div>

    <div class="px-4 sm:px-6 py-5">
        <div class="tw-table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>{{__('#')}}</th>
                        <th>{{__('Tenant')}}</th>
                        <th>{{__('Email')}}</th>
                        <th>{{__('Domain')}}</th>
                        <th>{{__('Period')}}</th>
                        <th>{{__('Price')}}</th>
                        <th>{{__('Expire Date')}}</th>
                        @if(!!tenant())
                            <th>{{__('Action')}}</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($domain_list ?? [] as $index => $item)
                        @php
                            $custom_domain = \App\Models\CustomDomain::where(['custom_domain' => $item->domain, 'custom_domain_status' => 'connected'])->first();
                            $date = \Carbon\Carbon::parse($item->expire_at ?? null);
                            $isExpired = now() > $date;
                        @endphp
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$item->paymentable_tenant?->id}}</td>
                            <td>{{$item->email}}</td>
                            <td><span class="font-semibold text-dark">{{$item->domain}}</span></td>
                            <td>{{$item->period}} {{__('Year')}}</td>
                            <td>{{amount_with_currency_symbol($item->domain_price + $item->extra_fee)}}</td>
                            <td>
                                <p class="text-sm {{$isExpired ? 'text-danger' : 'text-dark'}} font-medium">{{$date->format('D, d F Y')}}</p>
                                <p class="text-xs {{$isExpired ? 'text-danger' : 'text-muted'}}">
                                    {{!$isExpired ? $date->diffForHumans(['parts' => 2]) : __('Expired')}}
                                </p>
                            </td>
                            @if(!!tenant())
                                <td>
                                    @if(!$isExpired)
                                        @if($custom_domain)
                                            <span class="tw-pill tw-pill-success">{{__('Activated')}}</span>
                                        @else
                                            <button class="activate-btn inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-success text-white text-xs font-semibold hover:opacity-90 transition"
                                                    data-href="{{route('tenant.admin.domain-reseller.list.domain.activate')}}"
                                                    data-id="{{$item->id}}">
                                                {{__('Activate')}}
                                                <span class="cm-spinner hidden"></span>
                                            </button>
                                        @endif
                                    @else
                                        <a class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-info text-white text-xs font-semibold hover:opacity-90 transition"
                                           href="{{route('tenant.admin.domain-reseller.renew', wrap_random_number($item->id))}}">
                                            {{__('Renew Domain')}}
                                        </a>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-8">{{__('No domains found')}}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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

            const textSteps = ['{{__("Setting Up DNS")}}', '{{__("Configuring Custom Domain")}}'];
            let stepIndex = 0;
            let timer = '';

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

            const showLoader = () => {
                $('#drLoader').addClass('active');
                animateText(true);
            };

            const hideLoader = () => {
                $('#drLoader').removeClass('active');
                animateText(false);
            };
        })(jQuery);
    </script>
@endsection
