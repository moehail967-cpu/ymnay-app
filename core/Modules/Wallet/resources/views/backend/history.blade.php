@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Wallet History')}} @endsection

@section('style')
    <x-datatable.tw-css/>
    <link rel="stylesheet" href="{{ global_asset('assets/new-landlord/admin/css/components/wallet.css') }}">
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

{{-- Table Card --}}
<div class="bg-surface rounded-xl shadow-main border border-main mb-6">

    {{-- Card Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-history text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Wallet History')}}</h3>
                <p class="text-xs text-muted">{{__('All users wallet transaction history')}}</p>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left" id="walletHistoryTable">
            <thead>
                <tr class="border-b border-main">
                    <th class="hidden md:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('User Details')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Payment Gateway')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Payment Status')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Amount')}}</th>
                    <th class="hidden sm:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort">{{__('Attachment')}}</th>
                </tr>
            </thead>
            <tbody>
            @forelse($wallet_history_lists as $key => $data)
                <tr class="border-b border-main hover:bg-muted transition-colors">

                    {{-- ID --}}
                    <td class="hidden md:table-cell px-4 py-3.5">
                        <span class="text-[11px] font-bold text-primary">{{__('#')}} {{$loop->iteration}}</span>
                    </td>

                    {{-- User Details --}}
                    <td class="px-4 py-3.5">
                        <div class="tw-cell-user">
                            <div class="tw-avatar-initials">
                                {{ strtoupper(substr(optional($data->user)->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <div class="tw-cell-name">{{optional($data->user)->name}}</div>
                                <div class="tw-cell-sub">{{optional($data->user)->email}}</div>
                                @if(optional($data->user)->username)
                                    <div class="tw-cell-sub">@ {{optional($data->user)->username}}</div>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- Payment Gateway --}}
                    <td class="px-4 py-3.5">
                        @php
                            $gateway = str_replace('_', ' ', $data->payment_gateway);
                        @endphp
                        <span class="tw-pill tw-pill-info">
                            <i class="mdi mdi-credit-card-outline text-[10px] mr-0.5"></i>
                            {{ ucwords($gateway) }}
                        </span>
                    </td>

                    {{-- Payment Status --}}
                    <td class="px-4 py-3.5">
                        <div class="flex items-center gap-2 flex-wrap">
                            @if($data->payment_status === 'complete')
                                <span class="tw-pill tw-pill-success">
                                    <i class="mdi mdi-check-circle text-[10px] mr-0.5"></i> {{__('Complete')}}
                                </span>
                            @else
                                <span class="tw-pill tw-pill-warning">
                                    <i class="mdi mdi-clock-outline text-[10px] mr-0.5"></i> {{__('Pending')}}
                                </span>
                            @endif

                            @if($data->payment_status == 'pending')
                                <x-status-change-tw :url="route('landlord.admin.wallet.history.status', $data->id)"/>
                            @endif
                        </div>
                    </td>

                    {{-- Amount --}}
                    <td class="px-4 py-3.5">
                        <span class="text-sm font-bold text-dark">{{ float_amount_with_currency_symbol($data->amount) }}</span>
                    </td>

                    {{-- Manual Payment Image --}}
                    <td class="hidden sm:table-cell px-4 py-3.5">
                        @if($data->manual_payment_image)
                            <div class="wallet-attachment-wrap">
                                <img class="wallet-attachment"
                                     src="{{ asset('assets/landlord/uploads/deposit_payment_attachments/'.$data->manual_payment_image) }}"
                                     alt="{{__('Payment Attachment')}}">
                            </div>
                        @else
                            <span class="text-xs text-muted italic">{{__('N/A')}}</span>
                        @endif
                    </td>

                </tr>
            @empty
                <tr>
                    <td class="text-center py-8" colspan="6">
                        <div class="flex flex-col items-center gap-2">
                            <div class="w-12 h-12 rounded-full bg-secondary flex items-center justify-center">
                                <i class="mdi mdi-history text-muted text-xl"></i>
                            </div>
                            <span class="text-sm text-muted">{{__('No wallet history available')}}</span>
                        </div>
                    </td>
                </tr>
            @endforelse
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
        $(document).ready(function () {
            $(document).on('click', '.swal_status_change', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: '{{__("Are you sure to change status?")}}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#1a5c4e',
                    cancelButtonColor: '#D2042D',
                    confirmButtonText: '{{__("Yes, change it!")}}',
                    cancelButtonText: '{{__("Cancel")}}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(this).next().find('.swal_form_submit_btn').trigger('click');
                    }
                });
            });
        });
    })(jQuery);
    </script>
@endsection
