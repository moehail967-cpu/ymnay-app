@extends('landlord.frontend.dashboard.master')

@section('page-title')
    {{ __('Commission Payment') }}
@endsection

@section('title')
    {{ __('Commission Payment') }}
@endsection

@section('style')
    <style>
        .payment_getway_image ul {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .payment_getway_image ul li {
            width: calc(100% / 5 - 8px);
            transition: 0.3s;
            border: 2px solid transparent;
            cursor: pointer;
            box-sizing: border-box;
            display: flex;
            align-items: center;
            border-color: #ddd;
            overflow: hidden;
            height: 42px;
        }

        .payment_getway_image ul li:is(:hover, .selected) {
            border: 2px solid #0C4D54;
        }

        .payment_gateway_extra_field_information_wrap {
            display: none !important;
        }
    </style>
@endsection

@php
    use App\Helpers\PaymentGatewayRenderHelper;
    $totalCommissionAmount = $commission_history->sum('commission_amount');
    $completedAmount = $commission_history->where('status', 'complete')->sum('commission_amount');
    $pendingAmount = $commission_history->where('status', 'pending')->sum('commission_amount');
    $gateways = PaymentGatewayRenderHelper::getActiveGateways();
@endphp

@section('section')
    <!-- Pay Commission Modal -->
    <div id="payCommissionModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-secondary">{{ __('Pay Commission') }}</h2>
                <button onclick="closePayCommissionModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="ti tabler-x text-xl"></i>
                </button>
            </div>
            <div class="px-6 py-5">
                <p class="text-sm font-semibold text-gray-700 mb-4">
                    {{ __('Total Amount to Pay:') }}
                    <strong class="text-secondary">{{ amount_with_currency_symbol($unpaidCommission) }}</strong>
                </p>

                <form id="commissionPaymentForm" method="POST" action="{{ route('landlord.tenant.commission.pay', 'all') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="amount" value="{{ $unpaidCommission }}">

                    @if($showWallet)
                        <div class="mb-4">
                            <label class="flex items-center gap-3 cursor-pointer border border-gray-200 rounded-xl px-4 py-3 hover:bg-gray-50 transition">
                                <input class="w-4 h-4 accent-teal-600" type="radio" name="payment_gateway" value="wallet" id="walletOption">
                                <span class="text-sm text-gray-700">
                                    {{ __('Pay using Wallet Balance') }} –
                                    <strong>{{ amount_with_currency_symbol($walletBalance) }}</strong>
                                </span>
                            </label>
                        </div>
                        <div class="flex items-center gap-3 mb-4">
                            <hr class="flex-1 border-gray-200">
                            <span class="text-xs text-gray-400">{{ __('Or choose another method') }}</span>
                            <hr class="flex-1 border-gray-200">
                        </div>
                    @endif

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Select Payment Gateway') }}</label>
                        <div class="payment-gateway-wrapper pb-3">
                            {!! PaymentGatewayRenderHelper::renderPaymentGatewayForForm($gateways) !!}
                        </div>
                    </div>

                    <div class="commission_manual_transaction_id hidden mb-4">
                        @php
                            $payment_gateways_manual = \App\Models\PaymentGateway::where(['status' => \App\Enums\StatusEnums::PUBLISH, 'name' => 'manual_payment'])->first();
                        @endphp
                        @if(!empty($payment_gateways_manual))
                            <p class="bg-blue-50 border border-blue-200 text-blue-700 text-sm rounded-xl px-4 py-3 mb-3">
                                {{ json_decode($payment_gateways_manual->credentials)->description ?? '' }}
                            </p>
                        @endif
                        <input type="text" name="transaction_id"
                               class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-teal-200 focus:border-teal-400 mb-2"
                               placeholder="{{ __('Transaction ID') }}">
                        <input type="file" name="manual_payment_image" accept="image/*,.pdf"
                               class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm outline-none">
                    </div>

                    <button type="submit" id="commissionPayBtn"
                            class="w-full py-3 rounded-xl text-sm font-semibold text-white transition-opacity hover:opacity-90 bg-primary mt-2">
                        {{ __('Confirm Payment') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="col-span-full lg:col-span-9">
        <!-- Top Header -->
        <header class="bg-[#F8FAFB] lg:sticky top-[78px] z-30 border-b rounded-t-3xl">
            <div class="flex items-center justify-between px-6 py-3.5">
                <div class="flex items-center">
                    <button id="menuBtn"
                            class="block mr-3 lg:hidden text-gray-600 hover:text-teal-600 focus:outline-none">
                        <i class="icon-base ti tabler-menu-2 icon-28px"></i>
                    </button>
                    <div class="">
                        <h1 class="text-lg font-medium text-secondary">{{ __('Commission Payment History') }}</h1>
                        <p class="text-xs lg:text-sm text-sub2Title mt-1">{{ __('Track and manage your commission payments') }}</p>
                    </div>
                </div>
                <div id="btnWithDrawal" class="flex justify-end pb-2">
                    @if($unpaidCommission > 0)
                        <button onclick="openPayCommissionModal()"
                                class="px-6 py-3 rounded-xl flex items-center gap-2 text-base font-normal text-white transition-opacity hover:opacity-90 bg-primary">
                            <i class="icon-base ti tabler-credit-card"></i>
                            {{ __('Pay All Commissions') }}
                        </button>
                    @endif
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 w-full">

                <!-- Card 1: Total Commission -->
                <div class="bg-white rounded-2xl border border-borderCS px-5 py-4">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                             style="background-color: #e8f0f5;">
                            <i class="ti tabler-ticket text-lg text-sectionC icon-22px"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm text-sub2Title font-normal">{{ __('Total Commission') }}</span>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-xl font-medium text-secondary">{{ float_amount_with_currency_symbol($totalCommissionAmount) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Completed -->
                <div class="bg-white rounded-2xl border border-borderCS px-5 py-4">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                             style="background-color: #e8f0f5;">
                            <i class="ti tabler-circle-check text-lg text-sectionC icon-22px"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm text-sub2Title font-normal">{{ __('Completed') }}</span>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-xl font-medium text-secondary">{{ float_amount_with_currency_symbol($completedAmount) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Pending -->
                <div class="bg-white rounded-2xl border border-borderCS px-5 py-4">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                             style="background-color: #e8f0f5;">
                            <i class="ti tabler-clock text-lg text-sectionC icon-22px"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm text-sub2Title font-normal">{{ __('Pending') }}</span>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-xl font-medium text-secondary">{{ float_amount_with_currency_symbol($pendingAmount) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Total Payments -->
                <div class="bg-white rounded-2xl border border-borderCS px-5 py-4">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                             style="background-color: #e8f0f5;">
                            <i class="ti tabler-list-numbers text-lg text-sectionC icon-22px"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm text-sub2Title font-normal">{{ __('Total Payments') }}</span>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-xl font-medium text-secondary">{{ $commission_payment_logs->total() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="flex flex-col gap-4 mt-6">

                <!-- Header Card -->
                <div class="bg-white rounded-2xl border border-borderCS px-5 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-medium text-secondary">{{ __('Payment History') }}</h2>
                            <p class="text-sm text-sub2Title mt-0.5">{{ $commission_payment_logs->total() }} {{ __('total payments') }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <!-- Search -->
                            <form method="GET" action="" class="flex items-center gap-2" id="commissionSearchForm">
                                @if(request('status'))
                                    <input type="hidden" name="status" value="{{ request('status') }}">
                                @endif
                                <div class="flex items-center gap-2 border border-borderCS rounded-xl px-3 py-2 bg-white w-full sm:w-64 focus-within:ring-2 focus-within:ring-teal-200 focus-within:border-teal-400 transition">
                                    <i class="ti tabler-search text-sub2Title text-base flex-shrink-0"></i>
                                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search order ID or method...') }}"
                                        class="text-sm text-sub2Title placeholder-sub2Title outline-none bg-transparent w-full" />
                                </div>
                            </form>
                            <!-- Status Filter -->
                            <form method="GET" action="" id="commissionStatusFilter">
                                @if(request('search'))
                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                @endif
                                <select name="status" onchange="document.getElementById('commissionStatusFilter').submit()"
                                        class="border border-borderCS rounded-xl px-3 py-2.5 text-sm text-sub2Title bg-white outline-none cursor-pointer hover:bg-gray-50 transition">
                                    <option value="">{{ __('All Status') }}</option>
                                    <option value="complete" {{ request('status') == 'complete' ? 'selected' : '' }}>{{ __('Complete') }}</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                </select>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Table Card -->
                <div class="bg-white rounded-2xl border border-borderCS overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full whitespace-nowrap">
                            <thead>
                            <tr class="border-b bg-[#F8FAFB] border-borderCS">
                                <th class="text-left border-r border-borderCS text-sm font-semibold text-gray-700 px-6 py-4">{{ __('ID') }}</th>
                                <th class="text-left border-r border-borderCS text-sm font-semibold text-gray-700 px-4 py-4">{{ __('Order ID') }}</th>
                                <th class="text-left border-r border-borderCS text-sm font-semibold text-gray-700 px-4 py-4">{{ __('Amount') }}</th>
                                <th class="text-left border-r border-borderCS text-sm font-semibold text-gray-700 px-4 py-4">{{ __('Status') }}</th>
                                <th class="text-left border-r border-borderCS text-sm font-semibold text-gray-700 px-4 py-4">{{ __('Method') }}</th>
                                <th class="text-left text-sm font-semibold text-gray-700 px-4 py-4">{{ __('Date') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($commission_payment_logs as $log)
                                <tr class="border-b border-borderCS">
                                    <td class="px-6 py-5 text-sm text-gray-600">#{{ $log->id }}</td>
                                    <td class="px-4 py-5 text-sm text-gray-700">{{ $log->order_id ?? __('N/A') }}</td>
                                    <td class="px-4 py-5 text-sm text-gray-700">{{ float_amount_with_currency_symbol($log->amount) }}</td>
                                    <td class="px-4 py-5">
                                        @if($log->payment_status == 'complete')
                                            <span class="inline-block border border-green-400 text-green-500 text-xs font-medium px-4 py-1 rounded-full bg-white">
                                                {{ __('Complete') }}
                                            </span>
                                        @elseif($log->payment_status == 'pending')
                                            <span class="inline-block border border-yellow-400 text-yellow-500 text-xs font-medium px-4 py-1 rounded-full bg-white">
                                                {{ __('Pending') }}
                                            </span>
                                        @else
                                            <span class="inline-block border border-blue-400 text-blue-500 text-xs font-medium px-4 py-1 rounded-full bg-white">
                                                {{ ucfirst($log->payment_status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-5 text-sm text-gray-700">{{ ucwords(str_replace('_', ' ', $log->payment_gateway ?? __('N/A'))) }}</td>
                                    <td class="px-4 py-5 text-sm text-gray-700">{{ $log->created_at->format('d-m-Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                                        {{ __('No commission payment logs found') }}
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Pagination -->
            <div class="{{ $commission_payment_logs->hasPages() ? 'p-6' : '' }}">
                {{ $commission_payment_logs->links('vendor.pagination.tailwind') }}
            </div>

        </main>
    </div>
@endsection

@section('scripts')
    @parent
    <script src="{{ asset('assets/new-landlord/js/active_page.js') }}"></script>
    <script>
        // Pay Commission Modal open/close
        function openPayCommissionModal() {
            document.getElementById('payCommissionModal').classList.remove('hidden');
        }
        function closePayCommissionModal() {
            document.getElementById('payCommissionModal').classList.add('hidden');
        }
        // Close modal on backdrop click
        document.getElementById('payCommissionModal').addEventListener('click', function(e) {
            if (e.target === this) closePayCommissionModal();
        });

        // Payment Gateway Selection for Commission
        document.addEventListener('DOMContentLoaded', function() {
            const paymentList = document.querySelectorAll('.payment-gateway-wrapper .payment-list li, .payment-gateway-wrapper li[data-gateway]');
            paymentList.forEach(function(item) {
                item.addEventListener('click', function() {
                    // Uncheck wallet radio when selecting a gateway
                    const walletRadio = document.getElementById('walletOption');
                    if (walletRadio) walletRadio.checked = false;

                    paymentList.forEach(li => li.classList.remove('selected'));
                    this.classList.add('selected');
                    const gateway = this.dataset.gateway || this.dataset.value;
                    const hiddenInput = document.querySelector('.payment-gateway-wrapper input[name="selected_payment_gateway"]');
                    if (hiddenInput) hiddenInput.value = gateway;

                    const manualFields = document.querySelector('.commission_manual_transaction_id');
                    if (manualFields) {
                        if (gateway === 'manual_payment') {
                            manualFields.classList.remove('hidden');
                        } else {
                            manualFields.classList.add('hidden');
                        }
                    }
                });
            });

            // Wallet radio: deselect gateway when wallet is chosen
            const walletRadio = document.getElementById('walletOption');
            if (walletRadio) {
                walletRadio.addEventListener('change', function() {
                    if (this.checked) {
                        paymentList.forEach(li => li.classList.remove('selected'));
                        const hiddenInput = document.querySelector('.payment-gateway-wrapper input[name="selected_payment_gateway"]');
                        if (hiddenInput) hiddenInput.value = '';
                        const manualFields = document.querySelector('.commission_manual_transaction_id');
                        if (manualFields) manualFields.classList.add('hidden');
                    }
                });
            }
        });

        // Form Submit button text change
        document.getElementById('commissionPaymentForm').addEventListener('submit', function() {
            const btn = document.getElementById('commissionPayBtn');
            btn.disabled = true;
            btn.textContent = '{{ __("Processing...") }}';
        });

    </script>
@endsection
