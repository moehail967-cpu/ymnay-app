@php
    $route_name = 'landlord';
    $razorpayRecurring = get_static_option('razorpay_recurring_enabled');
    $totalShops = count($all_tenants ?? []);
@endphp

@extends($route_name.'.admin.admin-master')
@section('title') {{__('All Shops')}} @endsection

@section('style')
    <x-datatable.tw-css/>
<style>.hover\:text-white:hover{color:#fff!important}</style>
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">

    {{-- Card Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-store text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Shops')}}</h3>
                <p class="text-xs text-muted">{{$totalShops}} {{__('total')}}</p>
            </div>
        </div>
        <a href="{{route('landlord.admin.tenant')}}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary-soft border border-main text-primary text-sm font-semibold hover:bg-primary hover:text-white hover:border-primary transition whitespace-nowrap">
            <i class="mdi mdi-arrow-left text-base"></i>
            {{__('All Users')}}
        </a>
    </div>

    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left" id="allShopsTable">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 sm:px-6 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Shop')}}</th>
                    <th class="hidden md:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Package')}}</th>
                    @if($razorpayRecurring)
                        <th class="hidden lg:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Status')}}</th>
                    @endif
                    <th class="px-4 sm:px-6 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right w-28">{{__('Actions')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($all_tenants as $tenant)
                @php
                    $central = '.'.env('CENTRAL_DOMAIN');
                    if(!empty($tenant->custom_domain?->custom_domain) && $tenant->custom_domain?->custom_domain_status == 'connected'){
                        $url = tenant_url_with_protocol($tenant->custom_domain->custom_domain);
                    } else {
                        $url = tenant_url_with_protocol($tenant->id . $central);
                    }
                    $hash_token = hash_hmac('sha512', $tenant?->user?->username.'_'.$tenant->id, $tenant->unique_key);
                    $paymentLog = \App\Models\PaymentLogs::where('tenant_id', $tenant->id)->first();
                    $subscriptionStatus = $paymentLog->subscription_status ?? null;
                    $domain_display = $tenant->id . '.' . env('CENTRAL_DOMAIN');
                @endphp
                <tr class="border-b border-main hover:bg-muted transition-colors">

                    {{-- Shop: domain + owner --}}
                    <td class="px-4 sm:px-6 py-4">
                        <a href="{{$url}}" target="_blank" class="text-sm font-semibold text-dark hover:text-primary transition leading-tight inline-flex items-center gap-1.5">
                            {{$domain_display}}
                            <i class="mdi mdi-open-in-new text-[10px] text-muted"></i>
                        </a>
                        <p class="text-[11px] text-muted mt-0.5">{{$tenant?->user?->name ?? '---'}}</p>
                    </td>

                    {{-- Package --}}
                    <td class="hidden md:table-cell px-4 py-4">
                        <span class="text-sm text-dark">{{$tenant?->payment_log?->package_name ?? '---'}}</span>
                    </td>

                    {{-- Subscription Status --}}
                    @if($razorpayRecurring)
                        <td class="hidden lg:table-cell px-4 py-4">
                            @if($paymentLog->razorpay_subscription_id ?? '')
                                @php
                                    $sm = [
                                        'paused'     => ['bg-warning-soft text-warning', 'mdi-pause-circle',   __('Paused')],
                                        'cancelling' => ['bg-info-soft text-info',       'mdi-progress-clock', __('Cancelling')],
                                        'cancelled'  => ['bg-danger-soft text-danger',   'mdi-close-circle',   __('Cancelled')],
                                        'active'     => ['bg-success-soft text-success', 'mdi-check-circle',   __('Active')],
                                        'pending'    => ['bg-success-soft text-success', 'mdi-check-circle',   __('Active')],
                                    ];
                                    $st = $sm[$subscriptionStatus] ?? ['bg-secondary text-muted', 'mdi-help-circle', __('N/A')];
                                @endphp
                                <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded bg-{{-- already in class --}}{{$st[0]}} text-[9px] font-bold uppercase">
                                    <i class="mdi {{$st[1]}} text-xs"></i>{{$st[2]}}
                                </span>
                            @else
                                <span class="text-xs text-muted">---</span>
                            @endif
                        </td>
                    @endif

                    {{-- Actions --}}
                    <td class="px-4 sm:px-6 py-4">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{$url}}" target="_blank" title="{{__('Visit')}}"
                               class="w-8 h-8 rounded-lg bg-primary-soft border border-main flex items-center justify-center hover:bg-primary hover:text-white hover:border-primary transition-all">
                                <i class="mdi mdi-eye-outline text-sm"></i>
                            </a>

                            <div class="row-action-wrap">
                                <button type="button" onclick="toggleRowMenu(this)"
                                        class="w-8 h-8 rounded-lg bg-secondary border border-main flex items-center justify-center text-muted hover:bg-primary-soft hover:text-primary hover:border-primary transition-all">
                                    <i class="mdi mdi-dots-vertical text-sm"></i>
                                </button>

                                <div class="row-action-menu hidden">
                                    <a href="{{$url}}" target="_blank">
                                        <span class="action-icon bg-primary-soft"><i class="mdi mdi-open-in-new text-primary"></i></span>
                                        {{__('Visit Shop')}}
                                    </a>

                                    @can('users-direct-login')
                                        <a href="{{$url.'/token-login/'.$hash_token}}" target="_blank">
                                            <span class="action-icon bg-[#f3e8ff]"><i class="mdi mdi-login text-[#9333ea]"></i></span>
                                            {{__('Login as Admin')}}
                                        </a>
                                    @endcan

                                    @if($razorpayRecurring)
                                        <div class="menu-divider"></div>
                                        @if($tenant->razorpay_subscription_id)
                                            <a href="{{route('landlord.admin.tenant.recurring.settings', $tenant->id)}}">
                                                <span class="action-icon bg-info-soft"><i class="mdi mdi-cog text-info"></i></span>
                                                {{__('Subscription Settings')}}
                                            </a>
                                            @if($subscriptionStatus == 'paused')
                                                <button type="button" class="action-item subscription-action-btn" data-action="resume" data-tenant-id="{{$tenant->id}}" data-tenant-name="{{$tenant->id}}">
                                                    <span class="action-icon bg-success-soft"><i class="mdi mdi-play text-success"></i></span>
                                                    {{__('Resume')}}
                                                </button>
                                            @elseif($subscriptionStatus != 'cancelling' && $subscriptionStatus != 'cancelled')
                                                <button type="button" class="action-item subscription-action-btn" data-action="pause" data-tenant-id="{{$tenant->id}}" data-tenant-name="{{$tenant->id}}">
                                                    <span class="action-icon bg-warning-soft"><i class="mdi mdi-pause text-warning"></i></span>
                                                    {{__('Pause')}}
                                                </button>
                                            @endif
                                            @if($subscriptionStatus != 'cancelling' && $subscriptionStatus != 'cancelled')
                                                <button type="button" class="action-item action-danger subscription-action-btn" data-action="cancel" data-tenant-id="{{$tenant->id}}" data-tenant-name="{{$tenant->id}}">
                                                    <span class="action-icon bg-danger-soft"><i class="mdi mdi-cancel text-danger"></i></span>
                                                    {{__('Cancel')}}
                                                </button>
                                            @endif
                                        @else
                                            <a href="{{route('landlord.admin.tenant.recurring.settings', $tenant->id)}}">
                                                <span class="action-icon bg-info-soft"><i class="mdi mdi-autorenew text-info"></i></span>
                                                {{__('Recurring')}}
                                            </a>
                                        @endif
                                    @endif

                                    <div class="menu-divider"></div>
                                    <button type="button" class="action-item action-danger swal_delete_button">
                                        <span class="action-icon bg-danger-soft"><i class="mdi mdi-delete-outline text-danger"></i></span>
                                        {{__('Delete')}}
                                    </button>
                                    <form method="post" action="{{route(route_prefix().'admin.tenant.domain.delete', $tenant->id)}}" class="hidden d-none">
                                        @csrf
                                        <button type="submit" class="swal_form_submit_btn hidden d-none"></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>

                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

</div>

@endsection

@section('scripts')
    <x-datatable.tw-js/>

    <script>
    (function ($) {
        "use strict";

        window.toggleRowMenu = function (btn) {
            var menu = btn.nextElementSibling;
            var isHidden = menu.classList.contains('hidden');
            document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
            if (isHidden) {
                var rect = btn.getBoundingClientRect();
                menu.style.right = (window.innerWidth - rect.right) + 'px';
                menu.style.left = 'auto';
                if ((window.innerHeight - rect.bottom) >= 200 || (window.innerHeight - rect.bottom) >= rect.top) {
                    menu.style.top = (rect.bottom + 4) + 'px'; menu.style.bottom = 'auto';
                } else {
                    menu.style.bottom = (window.innerHeight - rect.top + 4) + 'px'; menu.style.top = 'auto';
                }
                menu.classList.remove('hidden');
            }
        };

        document.addEventListener('click', function (e) {
            if (!e.target.closest('.row-action-wrap')) document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
        });
        window.addEventListener('scroll', function (e) {
            if (e.target && e.target.closest && e.target.closest('.row-action-menu')) return;
            document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
        }, true);

        // close on .tw-table-wrap horizontal scroll
        document.querySelectorAll('.tw-table-wrap').forEach(function (wrap) {
            wrap.addEventListener('scroll', function () {
                document.querySelectorAll('.row-action-menu').forEach(function (m) {
                    m.classList.add('hidden');
                });
            });
        });

        $(document).ready(function () {
            if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#allShopsTable')) {
                $('#allShopsTable').DataTable({
                    "order": [[0, "asc"]],
                    "pageLength": 10,
                    "deferRender": true,
                    "processing": true,
                    'columnDefs': [{ 'targets': 'no-sort', "orderable": false }],
                    'language': (typeof translatedDataTable === 'function') ? translatedDataTable() : {}
                });
            }

            $(document).on('click', '.subscription-action-btn', function (e) {
                e.preventDefault();
                document.querySelectorAll('.row-action-menu').forEach(function (m) { m.classList.add('hidden'); });
                var action = $(this).data('action'), tenantId = $(this).data('tenant-id'), tenantName = $(this).data('tenant-name');
                var routes = {
                    pause:  '{{ route("landlord.admin.tenant.recurring.pause", ":id") }}'.replace(':id', tenantId),
                    resume: '{{ route("landlord.admin.tenant.recurring.resume", ":id") }}'.replace(':id', tenantId),
                    cancel: '{{ route("landlord.admin.tenant.recurring.cancel", ":id") }}'.replace(':id', tenantId)
                };
                var labels = { pause: '{{__("Pause")}}', resume: '{{__("Resume")}}', cancel: '{{__("Cancel")}}' };
                var msgs = {
                    pause:  '{{__("Are you sure you want to pause this subscription?")}}',
                    resume: '{{__("Are you sure you want to resume this subscription?")}}',
                    cancel: '{{__("Are you sure you want to cancel this subscription?  It will remain active until the end of the billing cycle.")}}'
                };
                Swal.fire({
                    title: labels[action] + ' {{__("Subscription")}}?',
                    text: msgs[action] + ' ({{__("Shop")}}: ' + tenantName + ')',
                    icon: 'warning', showCancelButton: true,
                    confirmButtonColor: action === 'cancel' ? '#d33' : '#2d6a4f',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '{{__("Yes")}}, ' + labels[action],
                    cancelButtonText: '{{__("Cancel")}}'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        Swal.fire({ title: '{{__("Processing...")}}', allowOutsideClick: false, didOpen: function () { Swal.showLoading(); } });
                        $.ajax({
                            url: routes[action], type: 'POST', data: { _token: '{{ csrf_token() }}' },
                            success: function (r) { Swal.fire({ icon: 'success', title: '{{__("Success")}}', text: r.message }).then(function () { location.reload(); }); },
                            error: function (x) { Swal.fire({ icon: 'error', title: '{{__("Error")}}', text: x.responseJSON?.message || '{{__("An error occurred")}}' }); }
                        });
                    }
                });
            });
        });
    })(jQuery);
    </script>
@endsection
