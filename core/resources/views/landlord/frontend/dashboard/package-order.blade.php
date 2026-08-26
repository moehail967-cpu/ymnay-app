@extends('landlord.frontend.dashboard.master')

@section('page-title')
    {{__('Payment Logs')}}
@endsection

@section('title')
    {{__('Payment Logs')}}
@endsection

@section('style')
    <link rel="stylesheet" href="{{global_asset('assets/common/css/toastr.css')}}">
    <style>
        .qp-gateway-wrapper ul {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
            list-style: none;
            padding: 0;
            margin: 0;
        }
        @media (min-width: 480px) {
            .qp-gateway-wrapper ul { grid-template-columns: repeat(5, minmax(0, 1fr)); }
        }
        .qp-gateway-wrapper ul li {
            position: relative;
            cursor: pointer;
            background: #fff;
            border: 2px solid #d1d5db;
            border-radius: 8px;
            padding: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: border-color 0.2s;
            min-height: 44px;
        }
        .qp-gateway-wrapper ul li:hover { border-color: var(--sectionC, #0C4D54); }
        .qp-gateway-wrapper ul li .img-select { width: 100%; }
        .qp-gateway-wrapper ul li img { width: 100%; height: 26px; object-fit: contain; }
        .qp-gateway-wrapper ul li.selected { border-color: var(--sectionC, #0C4D54); }
        .qp-gateway-wrapper ul li.selected::after {
            content: '';
            position: absolute;
            top: 3px; right: 3px;
            width: 16px; height: 16px;
            background-color: var(--sectionC, #0C4D54);
            border-radius: 50%;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='20 6 9 17 4 12'%3E%3C/polyline%3E%3C/svg%3E");
            background-size: 10px;
            background-repeat: no-repeat;
            background-position: center;
        }
    </style>
@endsection

@section('section')
    <!-- Main Content -->
    <div class="col-span-full lg:col-span-9">
        <!-- Top Header -->
        <header class="bg-[#F8FAFB] lg:sticky top-[78px] z-30 border-b rounded-t-3xl">
            <div class="flex items-center flex-wrap justify-between px-6 py-3.5">
                <div class="flex items-center">
                    <!-- Mobile Menu Button -->
                    <button id="menuBtn"
                            class="block pr-2 lg:hidden text-gray-600 hover:text-teal-600 focus:outline-none">
                        <i class="icon-base ti tabler-menu-2 icon-28px"></i>
                    </button>
                    <div class="ml-3 lg:ml-0">
                        <h1 class="text-lg font-medium text-secondary">{{__('Payment Log')}}</h1>
                        <p class="text-xs lg:text-sm text-gray-500 mt-1">{{__('Welcome back, manage your shops and orders')}}</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="p-6">
            @if(count($package_orders) > 0)
                <div class="{{ count($package_orders) > 1 ? 'space-y-4' : '' }}">
                    @foreach($package_orders as $data)
                        @php
                            $tenant = \App\Models\Tenant::where('id', $data->tenant_id)->first();
                            $paymentLog = \App\Models\PaymentLogs::where('id', $data->id)->first();
                            $subscriptionStatus = $paymentLog->subscription_status ?? null;

                            $payment = \App\Models\PaymentLogs::where(['tenant_id' => $data->id, 'status' => 'trial'])->latest()->first();
                            $start_date = date('d-m-Y', strtotime($data->created_at));
                            $renewal_at = date('d-m-Y', strtotime($data->start_date));
                            $expire_date = date('d-m-Y', strtotime($data?->tenant?->expire_date));
                            $end_date = $data?->expire_date != null ? date('d-m-Y', strtotime($data?->expire_date)) : __('Lifetime');

                            if (!empty($payment)) {
                                $end_date = date('d-m-Y', strtotime($payment->expire_date));
                            }
                            $end_date = $data?->expire_date != null ? date('d-m-Y', strtotime($data?->expire_date)) : __('Lifetime');

                            // Status badge classes
                            $statusColor = match($data->status) {
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'cancel' => 'bg-red-100 text-red-700',
                                'in_progress' => 'bg-blue-100 text-blue-700',
                                'trial' => 'bg-purple-100 text-purple-700',
                                default => 'bg-green-100 text-green-700',
                            };

                            $paymentStatusColor = match($data->payment_status) {
                                'complete' => 'bg-green-100 text-green-700',
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'cancel' => 'bg-red-100 text-red-700',
                                default => 'bg-gray-100 text-gray-700',
                            };
                        @endphp

                        <div class="bg-white border border-gray-200 rounded-2xl px-6 py-4">
                            {{-- Collapsed Header --}}
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div>
                                        <p class="font-medium text-secondary text-base">{{$data->package_name}}</p>
                                        <p class="text-sm text-gray-500">{{__('Order ID:')}} #{{$data->id}}</p>
                                    </div>
                                    <span class="{{$paymentStatusColor}} text-sm font-medium px-4 py-1.5 rounded-full capitalize">{{__($data->payment_status)}}</span>
                                </div>
                                <button class="w-9 h-9 flex items-center justify-center rounded-full border bg-primary/10 border-gray-200 text-gray-500 hover:bg-gray-50">
                                    <i class="btnShowExpanded icon-base ti tabler-chevron-down text-sectionC transition-transform duration-300"></i>
                                </button>
                            </div>

                            {{-- Expanded Details --}}
                            <div class="expanded hidden mt-4 border-t border-borderCS pt-6">
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-y-5 gap-x-6 items-start">

                                    {{-- Column 1: Order Name + Order Date --}}
                                    <div class="flex flex-col items-start gap-4">
                                        {{-- Order Name --}}
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-teal-50 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-400 mb-0.5">{{__('Order Name')}}</p>
                                                <p class="text-sm font-medium text-gray-800">{{$data->tenant_id.'.'.env('CENTRAL_DOMAIN')}}</p>
                                            </div>
                                        </div>

                                        {{-- Order Date --}}
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                                    <line x1="16" y1="2" x2="16" y2="6" stroke-linecap="round" />
                                                    <line x1="8" y1="2" x2="8" y2="6" stroke-linecap="round" />
                                                    <line x1="3" y1="10" x2="21" y2="10" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-400 mb-0.5">{{__('Order Date')}}</p>
                                                <p class="text-sm font-medium text-gray-800">{{ $start_date }}</p>
                                            </div>
                                        </div>

                                        {{-- Renewal Date (if renewed) --}}
                                        @if(($data->renew_status != 0) || ($data->is_renew != 0))
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-400 mb-0.5">{{__('Renewal Date')}}</p>
                                                    <p class="text-sm font-medium text-gray-800">{{ $renewal_at }}</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Column 2: Package Price + Expire Date --}}
                                    <div class="flex flex-col items-start gap-4">
                                        {{-- Package Price --}}
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-400 mb-0.5">{{__('Package Price')}}</p>
                                                <p class="text-sm font-medium text-gray-800">{{amount_with_currency_symbol($data->package_price)}}</p>
                                            </div>
                                        </div>

                                        {{-- Expire Date --}}
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-yellow-50 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                                    <line x1="16" y1="2" x2="16" y2="6" stroke-linecap="round" />
                                                    <line x1="8" y1="2" x2="8" y2="6" stroke-linecap="round" />
                                                    <line x1="3" y1="10" x2="21" y2="10" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-400 mb-0.5">{{__('Expire Date')}}</p>
                                                @if($data->status && $data->payment_status !== 'complete')
                                                    <p class="text-sm font-medium text-gray-800">{{ $expire_date ?? '' }}</p>
                                                @else
                                                    <p class="text-sm font-medium text-gray-800">{{ $end_date }}</p>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Renew Taken --}}
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-400 mb-0.5">{{__('Renew Taken')}}</p>
                                                @if($data->status && $data->payment_status !== 'complete')
                                                    <p class="text-sm font-medium text-gray-800">{{ $data?->tenant?->renew_status ?? 0 }}</p>
                                                @else
                                                    <p class="text-sm font-medium text-gray-800">{{ $data->renew_status ?? 0 }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Column 3: Status + Actions --}}
                                    <div class="flex flex-col gap-4">
                                        {{-- Order Status --}}
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-400 mb-0.5">{{__('Order Status')}}</p>
                                                <span class="{{$statusColor}} text-xs font-medium px-3 py-1 rounded-full capitalize">{{__(str_replace('_', ' ', $data->status))}}</span>
                                            </div>
                                        </div>

                                        {{-- Auto-Renewal Status (Razorpay) --}}
                                        @if($data->payment_status == 'complete' && $tenant && get_static_option('razorpay_recurring_enabled'))
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-400 mb-0.5">{{__('Auto-Renewal')}}</p>
                                                    @if(!$tenant->recurring_enabled || !$tenant->razorpay_subscription_id)
                                                        <span class="bg-red-100 text-red-700 text-xs font-medium px-3 py-1 rounded-full">{{__('Disabled')}}</span>
                                                    @else
                                                        @if($subscriptionStatus == 'paused')
                                                            <span class="bg-yellow-100 text-yellow-700 text-xs font-medium px-3 py-1 rounded-full">{{__('Paused')}}</span>
                                                        @elseif($subscriptionStatus == 'cancelled')
                                                            <span class="bg-blue-100 text-blue-700 text-xs font-medium px-3 py-1 rounded-full">{{__('Cancelled')}}</span>
                                                        @else
                                                            <span class="bg-green-100 text-green-700 text-xs font-medium px-3 py-1 rounded-full">{{__('Active')}}</span>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Action Buttons --}}
                                        <div class="flex flex-col gap-2 mt-1">
                                            {{-- Pay Now + Cancel (for incomplete payments) --}}
                                            @if($data->payment_status != 'complete' && $data->status != 'cancel')
                                                <button type="button"
                                                    data-log-id="{{ $data->id }}"
                                                    data-package="{{ $data->package_name }}"
                                                    data-price="{{ amount_with_currency_symbol($data->package_price) }}"
                                                    onclick="openQuickPayModal(this)"
                                                    class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-5 py-2.5 rounded-xl w-full justify-center transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    {{__('Pay Now')}}
                                                </button>
                                                <form action="{{route(route_prefix().'user.dashboard.package.order.cancel')}}" method="post">
                                                    @csrf
                                                    <input type="hidden" name="order_id" value="{{$data->id}}">
                                                    <button type="submit"
                                                            class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-5 py-2.5 rounded-xl w-full justify-center transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                        {{__('Cancel')}}
                                                    </button>
                                                </form>
                                            @endif

                                            {{-- Renew Now (for completed payments with razorpay recurring disabled) --}}
                                            @if($data->payment_status == 'complete' && $tenant && get_static_option('razorpay_recurring_enabled'))
                                                @if(!$tenant->recurring_enabled || !$tenant->razorpay_subscription_id)
                                                    <a href="{{route(route_prefix().'frontend.order.confirm', $data->package_id)}}"
                                                       class="flex items-center gap-2 bg-teal-800 hover:bg-teal-900 text-white text-sm font-medium px-5 py-2.5 rounded-xl w-full justify-center transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                        </svg>
                                                        {{__('Renew Now')}}
                                                    </a>
                                                @endif
                                            @endif

                                            {{-- Invoice (for completed payments) --}}
                                            @if($data->payment_status == 'complete')
                                                <form action="{{route(route_prefix().'frontend.package.invoice.generate')}}" method="post">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{$data->id}}">
                                                    <button type="submit"
                                                            class="flex items-center gap-2 border border-gray-200 text-gray-700 text-sm font-medium px-5 py-2.5 rounded-xl w-full justify-center hover:bg-gray-50 transition-colors">
                                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        {{__('Invoice')}}
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="{{ $package_orders->hasPages() ? 'mt-8 mb-4' : '' }}">
                    {{ $package_orders->links('vendor.pagination.tailwind') }}
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-xl px-6 py-4 text-center">
                    <p class="font-medium">{{__('No Order Found')}}</p>
                </div>
            @endif
        </main>
    </div>

    {{-- ===== QUICK-PAY MODAL ===== --}}
    <div id="quickPayModal"
         class="fixed inset-0 z-[800] hidden items-center justify-center bg-black/50 px-4"
         onclick="if(event.target===this) closeQuickPayModal()">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 relative">

            <button type="button" onclick="closeQuickPayModal()"
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <h2 class="text-xl font-semibold text-secondary mb-1">{{__('Complete Payment')}}</h2>
            <p id="qp-package-name" class="text-sm text-gray-500 mb-6"></p>

            <form id="quickPayForm"
                  action="{{ route('landlord.user.dashboard.package.order.quick.pay') }}"
                  method="POST">
                @csrf
                <input type="hidden" name="payment_log_id" id="qp-log-id" value="">
                <input type="hidden" name="payment_gateway" id="qp-gateway-value" value="">

                <div class="mb-5">
                    <label class="block text-sm font-medium text-secondary mb-3">{{__('Select Payment Method')}}</label>
                    <div class="qp-gateway-wrapper">
                        {!! (new \App\Helpers\PaymentGatewayRenderHelper())->renderPaymentGatewayForForm() !!}
                    </div>
                </div>

                <div class="flex justify-between items-center border-t border-gray-100 pt-4 mb-5">
                    <span class="text-gray-600 text-sm">{{__('Total')}}</span>
                    <span id="qp-price" class="text-secondary font-semibold text-lg"></span>
                </div>

                <button type="submit" id="qp-submit-btn"
                        class="w-full bg-green-600 hover:bg-green-700 disabled:opacity-60 disabled:cursor-not-allowed text-white font-medium py-3.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span id="qp-btn-text">{{__('Pay Now')}}</span>
                </button>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
    @parent
    <script src="{{global_asset('assets/common/js/toastr.min.js')}}"></script>
    <script>
        document.querySelectorAll('.btnShowExpanded').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const card = this.closest('.bg-white');
                const expanded = card.querySelector('.expanded');
                expanded.classList.toggle('hidden');
                this.classList.toggle('rotate-180');
            });
        });

        (function ($) {
            "use strict";

            var _defaultGw = '{{ get_static_option("site_default_payment_gateway") }}';

            // Gateway click handler — syncs to hidden input
            $(document).on('click', '#quickPayModal .qp-gateway-wrapper ul li', function () {
                $(this).addClass('selected').siblings().removeClass('selected');
                $('#qp-gateway-value').val($(this).data('gateway'));
            });

            window.openQuickPayModal = function (btn) {
                var el = $(btn);
                $('#qp-log-id').val(el.data('log-id'));
                $('#qp-package-name').text(el.data('package'));
                $('#qp-price').text(el.data('price'));
                // Reset
                $('#qp-terms').prop('checked', false);
                $('#qp-submit-btn').prop('disabled', false);
                $('#qp-btn-text').text('{{__("Pay Now")}}');
                // Pre-select default gateway
                var $defaultLi = $('#quickPayModal .qp-gateway-wrapper ul li[data-gateway="' + _defaultGw + '"]');
                if ($defaultLi.length) {
                    $defaultLi.addClass('selected').siblings().removeClass('selected');
                    $('#qp-gateway-value').val(_defaultGw);
                }
                $('#quickPayModal').removeClass('hidden').addClass('flex');
            };

            window.closeQuickPayModal = function () {
                $('#quickPayModal').removeClass('flex').addClass('hidden');
            };

            $('#quickPayForm').on('submit', function (e) {
                if (!$('#qp-gateway-value').val()) {
                    e.preventDefault();
                    toastr.error('{{__("Please select a payment method.")}}');
                    return;
                }
                $('#qp-submit-btn').prop('disabled', true);
                $('#qp-btn-text').text('{{__("Processing...")}}');
            });

        })(jQuery);
    </script>
@endsection
