@extends('landlord.frontend.dashboard.master')

@section('page-title')
    {{ __('My Wallet') }}
@endsection

@section('title')
    {{ __('My Wallet') }}
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
            border: 2px solid red;
        }

        .payment_gateway_extra_field_information_wrap {
            display: none !important;
        }

        /* .payment_getway_image ul li img {
                                max-width: 100%;
                                height: auto;
                                margin: auto;
                                object-fit: contain;
                            } */
    </style>
@endsection

@php
    use App\Helpers\PaymentGatewayRenderHelper;
    $gateways = PaymentGatewayRenderHelper::getActiveGateways();
@endphp

@section('section')
    <!-- Deposit Modal -->
    <div id="depositModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-secondary">{{ __('Deposit to Wallet') }}</h2>
                <button onclick="closeDepositModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="ti tabler-x text-xl"></i>
                </button>
            </div>
            <div class="px-6 py-5">
                <form action="{{ route('landlord.user.wallet.deposit') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Deposit Amount') }}
                            ({{ site_currency_symbol() }})</label>
                        <input type="number" name="amount" min="10" max="5000"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-teal-200 focus:border-teal-400"
                            placeholder="{{ __('Enter amount (min 10, max 5000)') }}" required>
                    </div>

                    <div class="mb-4">
                        <label
                            class="block text-sm font-medium text-gray-700 mb-2">{{ __('Select Payment Gateway') }}</label>
                        <div class="payment-gateway-wrapper pb-3">
                            {!! \App\Helpers\PaymentGatewayRenderHelper::renderPaymentGatewayForForm() !!}
                        </div>
                    </div>

                    <div class="manual_transaction_id hidden mb-4">
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

                    <button type="submit"
                        class="w-full py-3 rounded-xl text-sm font-semibold text-white transition-opacity hover:opacity-90 bg-primary mt-2">
                        {{ __('Deposit Now') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="col-span-full lg:col-span-9">
        <!-- Top Header -->
        <header class="bg-[#F8FAFB] lg:sticky top-[78px] z-30 border-y rounded-tr-3xl">
            <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4 px-4 sm:px-6 lg:pr-8 pt-3 pb-4">
                <div class="flex items-center w-full lg:w-auto">
                    <button id="menuBtn" class="block lg:hidden text-gray-600 hover:text-teal-600 focus:outline-none p-2 rounded-lg hover:bg-gray-100 transition-colors">
                        <i class="icon-base ti tabler-menu-2 icon-24px sm:icon-28px"></i>
                    </button>
                    <div class="pl-3 lg:pl-0 flex-1 lg:flex-none">
                        <h1 class="text-lg sm:text-xl font-medium text-secondary leading-tight">{{ __('My Wallet') }}</h1>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1 line-clamp-1 sm:line-clamp-none">
                            {{ __('Manage your wallet balance and transactions') }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row w-full lg:w-auto gap-3">
                    <button onclick="openDepositModal()"
                        class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-white transition-all hover:shadow-lg active:scale-[0.98]"
                        style="background-color:#1b3f50;">
                        <i class="ti tabler-download text-base flex-shrink-0"></i>
                        <span>{{ __('Deposit') }}</span>
                    </button>

                    <a href="{{ route('landlord.user.wallet.withdraw') }}"
                        class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium text-secondary bg-white border border-gray-200 hover:bg-gray-50 transition-colors">
                        <i class="ti tabler-send text-base text-gray-600 flex-shrink-0"></i>
                        <span>{{ __('Withdraw') }}</span>
                    </a>

                    <a href="{{ route('landlord.user.pay.commission') }}"
                        class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium text-secondary bg-white border border-gray-200 hover:bg-gray-50 transition-colors">
                        <i class="ti tabler-circle-x text-base text-gray-600 flex-shrink-0"></i>
                        <span>{{ __('Pay Commissions') }}</span>
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="p-6">
            <div class="flex flex-col flex-wrap sm:flex-row gap-4 w-full">

                <!-- Card 1: Wallet Balance -->
                <div class="flex-1 bg-white rounded-2xl border border-borderCS px-5 py-4">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0"
                            style="background-color: #e8f0f5;">
                            <i class="ti tabler-wallet text-lg text-sectionC icon-22px"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm text-sub2Title font-normal">{{ __('Wallet Balance') }}</span>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-xl font-bold text-secondary">
                                    {{ float_amount_with_currency_symbol($balance->balance ?? 0) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Total Earnings -->
                <div class="flex-1 bg-white rounded-2xl border border-borderCS px-5 py-4">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0"
                            style="background-color: #e8f0f5;">
                            <i class="ti tabler-trending-up text-lg text-sectionC icon-22px"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm text-sub2Title font-normal">{{ __('Total Earnings') }}</span>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-xl font-bold text-secondary">
                                    {{ float_amount_with_currency_symbol($totalEarning) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Net Earnings -->
                <div class="flex-1 bg-white rounded-2xl border border-borderCS px-5 py-4">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0"
                            style="background-color: #e8f0f5;">
                            <i class="ti tabler-currency-dollar text-lg text-sectionC icon-22px"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm text-sub2Title font-normal">{{ __('Net Earnings') }}</span>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-xl font-bold text-secondary">
                                    {{ float_amount_with_currency_symbol($netEarning) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Charts Section (rendered dynamically by JS) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 w-full mt-6">
                <div id="earningsChartContainer"></div>
                <div id="commissionChartContainer"></div>
            </div>

            <!-- Wallet History Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-borderCS overflow-hidden w-full mt-6">
                <div class="px-6 pt-5 pb-4">
                    <h2 class="text-lg font-semibold text-secondary">{{ __('Wallet History') }}</h2>
                    <p class="text-base text-sub2Title mt-0.5">
                        {{ __('You can deposit to your wallet and withdraw your earnings.') }}
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full whitespace-nowrap">
                        <thead>
                            <tr class="border-t border-b border-borderCS bg-[#F8FAFB]">
                                <th class="text-left text-base font-medium text-secondary px-6 py-3">{{ __('ID') }}</th>
                                <th class="text-left text-base font-medium text-secondary px-4 py-3">
                                    {{ __('Payment Gateway') }}
                                </th>
                                <th class="text-left text-base font-medium text-secondary px-4 py-3">
                                    {{ __('Payment Status') }}
                                </th>
                                <th class="text-left text-base font-medium text-secondary px-4 py-3">
                                    {{ __('Deposit Amount') }}
                                </th>
                                <th class="text-left text-base font-medium text-secondary px-4 py-3">
                                    {{ __('Deposit Date') }}
                                </th>
                                <th class="text-left text-base font-medium text-secondary px-4 py-3">
                                    {{ __('Payment Image') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($wallet_histories as $history)
                                <tr class="border-b border-gray-100">
                                    <td class="px-6 py-4 text-sm text-gray-600">#{{ $history->id }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-700">
                                        {{ ucwords(str_replace('_', ' ', $history->payment_gateway ?? __('Card'))) }}
                                    </td>
                                    <td class="px-4 py-4">
                                        @if($history->payment_status == 'complete')
                                            <span
                                                class="inline-block bg-[#F0FDF4] border border-[#B9F8CF] text-[#008236] text-sm font-normal px-4 py-1 rounded-full">
                                                {{ __('Complete') }}
                                            </span>
                                        @elseif($history->payment_status == 'pending')
                                            <span
                                                class="inline-block bg-[#FFFBEB] border border-[#FDE68A] text-[#D97706] text-sm font-normal px-4 py-1 rounded-full">
                                                {{ __('Pending') }}
                                            </span>
                                        @else
                                            <span
                                                class="inline-block bg-[#FEF2F2] border border-[#FECACA] text-[#DC2626] text-sm font-normal px-4 py-1 rounded-full">
                                                {{ ucfirst($history->payment_status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-700">
                                        {{ float_amount_with_currency_symbol($history->amount) }}
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-700">{{ $history->created_at->format('d-m-Y') }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-500">
                                        @if(empty($history->manual_payment_image))
                                            <span class="flex items-center gap-1.5">
                                                <i class="ti tabler-photo text-base text-gray-400"></i> {{ __('No Image') }}
                                            </span>
                                        @else
                                            <a href="{{ asset('assets/landlord/uploads/deposit_payment_attachments/' . $history->manual_payment_image) }}"
                                                target="_blank"
                                                class="flex items-center gap-1.5 cursor-pointer hover:text-gray-800 text-gray-600">
                                                <i class="ti tabler-eye text-base text-gray-500"></i> {{ __('View Image') }}
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                                        {{ __('No wallet history found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="{{ $wallet_histories->hasPages() ? 'p-6' : '' }}">
                {!! $wallet_histories->links('vendor.pagination.tailwind') !!}
            </div>

        </main>
    </div>
@endsection

@section('scripts')
    @parent
    <script src="{{ asset('assets/new-landlord/js/chart.umd.min.js') }}"></script>
    <script>
        // Dynamic chart data (must be before external JS files)
        window.earningsLabels = @json($monthlyEarningsLabels);
        window.earningsData = @json($monthlyEarningsValues);
        window.paidCommission = {{ $paidCommission }};
        window.unpaidCommission = {{ $unpaidCommission }};
        window.totalCommission = {{ $totalCommission }};
        window.currencySymbol = '{{ site_currency_symbol() }}';
    </script>
    <script src="{{ asset('assets/new-landlord/js/walet.js')}}?v={{ time() }}"></script>
    <script src="{{ asset('assets/new-landlord/js/donought_chart.js')}}?v={{ time() }}"></script>
    <script src="{{ asset('assets/new-landlord/js/active_page.js') }}"></script>
    <script>
        // Deposit Modal
        function openDepositModal() {
            document.getElementById('depositModal').classList.remove('hidden');
        }
        function closeDepositModal() {
            document.getElementById('depositModal').classList.add('hidden');
        }
        document.getElementById('depositModal').addEventListener('click', function (e) {
            if (e.target === this) closeDepositModal();
        });

        // Payment Gateway Selection for Deposit
        document.addEventListener('DOMContentLoaded', function () {
            const paymentList = document.querySelectorAll('.payment_getway_image ul li');
            paymentList.forEach(function (item) {
                item.addEventListener('click', function () {
                    paymentList.forEach(li => li.classList.remove('selected'));
                    this.classList.add('selected');
                    const gateway = this.dataset.gateway;
                    const hiddenInput = document.querySelector('input[name="selected_payment_gateway"]');
                    if (hiddenInput) hiddenInput.value = gateway;

                    const manualFields = document.querySelector('.manual_transaction_id');
                    if (manualFields) {
                        if (gateway === 'manual_payment') {
                            manualFields.classList.remove('hidden');
                        } else {
                            manualFields.classList.add('hidden');
                        }
                    }
                });
            });
        });
    </script>
@endsection
