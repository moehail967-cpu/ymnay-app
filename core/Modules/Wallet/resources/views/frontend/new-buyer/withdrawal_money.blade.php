@extends('landlord.frontend.dashboard.master')

@section('page-title')
    {{ __('Withdraw History') }}
@endsection

@section('title')
    {{ __('Withdraw History') }}
@endsection

@php
    $totalWithdrawn = $withdraw_request_logs->where('status', 'complete')->sum('amount');
    $pendingWithdrawals = $withdraw_request_logs->where('status', 'pending')->sum('amount');
    $completedWithdrawals = $withdraw_request_logs->where('status', 'complete')->sum('amount');
@endphp

@section('section')
    <!-- Main Content -->
    <div class="col-span-full lg:col-span-9">
        <!-- Top Header -->
        <header class="bg-[#F8FAFB] lg:sticky top-[78px] z-30 border-b rounded-t-3xl">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between px-6 py-3 gap-4">
                <div class="flex items-center">
                    <button id="menuBtn"
                            class="block mr-3 lg:hidden text-gray-600 hover:text-teal-600 focus:outline-none">
                        <i class="icon-base ti tabler-menu-2 icon-28px"></i>
                    </button>
                    <div class="">
                        <h1 class="text-lg font-medium text-secondary">{{ __('Withdraw History') }}</h1>
                        <p class="text-xs lg:text-sm text-sub2Title mt-1">{{ __('Track and manage your withdrawal transactions') }}</p>
                    </div>
                </div>
                <div class="pb-2">
                    <a href="{{ route('landlord.user.wallet.new_withdrawal') }}"
                       class="flex items-center justify-center px-6 py-3 rounded-xl gap-2 text-base font-normal text-white transition-opacity hover:opacity-90 bg-primary">
                        <i class="icon-base ti tabler-plus"></i>
                        {{ __('New Withdrawal') }}
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="p-6">
            <div class="flex flex-col flex-wrap sm:flex-row gap-4 w-full">

                <!-- Card 1: Total Withdrawn -->
                <div class="flex-1 bg-white rounded-2xl border border-borderCS px-5 py-4">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                             style="background-color: #e8f0f5;">
                            <i class="ti tabler-currency-dollar text-lg text-sectionC icon-22px"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm text-sub2Title font-normal">{{ __('Total Withdrawn') }}</span>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-xl font-bold text-secondary">{{ float_amount_with_currency_symbol($totalWithdrawn) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Pending Withdrawals -->
                <div class="flex-1 bg-white rounded-2xl border border-borderCS px-5 py-4">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                             style="background-color: #e8f0f5;">
                            <i class="ti tabler-clock text-lg text-sectionC icon-22px"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm text-sub2Title font-normal">{{ __('Pending Withdrawals') }}</span>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-xl font-bold text-secondary">{{ float_amount_with_currency_symbol($pendingWithdrawals) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Completed Withdrawals -->
                <div class="flex-1 bg-white rounded-2xl border border-borderCS px-5 py-4">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                             style="background-color: #e8f0f5;">
                            <i class="ti tabler-progress-check text-lg text-sectionC icon-22px"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm text-sub2Title font-normal">{{ __('Completed Withdrawals') }}</span>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-xl font-bold text-secondary">{{ float_amount_with_currency_symbol($completedWithdrawals) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Withdraw History Table -->
            <div class="flex flex-col gap-4 mt-6">
                <div class="bg-white rounded-2xl border border-borderCS overflow-hidden">
                    <div class="px-6 pt-5 pb-4">
                        <h2 class="text-lg font-medium text-secondary">{{ __('Withdrawal History') }}</h2>
                        <p class="text-base text-sub2Title mt-0.5">{{ __('Track your withdrawal requests and their status.') }}</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                            <tr class="border-t border-b border-borderCS bg-[#F8FAFB]">
                                <th class="text-left text-sm font-medium text-secondary px-6 py-3">{{ __('ID') }}</th>
                                <th class="text-left text-base font-medium text-secondary px-4 py-3">{{ __('Amount') }}</th>
                                <th class="text-left text-base font-medium text-secondary px-4 py-3">{{ __('Status') }}</th>
                                <th class="text-left text-base font-medium text-secondary px-4 py-3">{{ __('Method') }}</th>
                                <th class="text-left text-base font-medium text-secondary px-4 py-3">{{ __('Date') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($withdraw_request_logs as $log)
                                <tr class="border-b border-borderCS">
                                    <td class="px-6 py-5 text-sm text-gray-600">#{{ $log->id }}</td>
                                    <td class="px-4 py-5 text-sm text-gray-700">{{ float_amount_with_currency_symbol($log->amount) }}</td>
                                    <td class="px-4 py-5">
                                        @if($log->status == 'complete')
                                            <span class="inline-block border border-green-400 text-green-500 text-xs font-medium px-4 py-1 rounded-full bg-white">
                                                {{ __('Complete') }}
                                            </span>
                                        @elseif($log->status == 'pending')
                                            <span class="inline-block border border-yellow-400 text-yellow-500 text-xs font-medium px-4 py-1 rounded-full bg-white">
                                                {{ __('Pending') }}
                                            </span>
                                        @elseif($log->status == 'cancel')
                                            <span class="inline-block border border-red-400 text-red-500 text-xs font-medium px-4 py-1 rounded-full bg-white">
                                                {{ __('Cancelled') }}
                                            </span>
                                        @else
                                            <span class="inline-block border border-blue-400 text-blue-500 text-xs font-medium px-4 py-1 rounded-full bg-white">
                                                {{ ucfirst($log->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-5 text-sm text-gray-700">{{ $log->payment_method ?? __('N/A') }}</td>
                                    <td class="px-4 py-5 text-sm text-gray-700">{{ $log->created_at->format('d-m-Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                                        {{ __('No withdrawal requests found') }}
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="{{ $withdraw_request_logs->hasPages() ? 'p-6' : '' }}">
                {!! $withdraw_request_logs->links('vendor.pagination.tailwind') !!}
            </div>

        </main>
    </div>
@endsection

@section('scripts')
    @parent
    <script src="{{ asset('assets/new-landlord/js/active_page.js') }}"></script>
@endsection
