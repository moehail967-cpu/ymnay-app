@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Wallet Lists')}} @endsection

@section('style')
    <x-datatable.tw-css/>
    <style>.hover\:text-white:hover{color:#fff!important}</style>

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
                <i class="mdi mdi-wallet-outline text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Wallet Lists')}}</h3>
                <p class="text-xs text-muted">{{__('Manage user wallet balances and statuses')}}</p>
            </div>
        </div>
    </div>

    {{-- Info Banner --}}
    <div class="px-4 sm:px-6 py-3 border-b border-main">
        <div class="flex items-start gap-2.5 bg-blue-50 border border-blue-100 rounded-xl px-4 py-2.5">
            <i class="mdi mdi-information-outline text-info text-base mt-0.5 shrink-0"></i>
            <span class="text-[11px] text-dark leading-relaxed">
                {{__('You can activate or deactivate status from here. If status is inactive, users will not be able to use their wallet balance.')}}
            </span>
        </div>
    </div>

    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left" id="walletListsTable">
            <thead>
                <tr class="border-b border-main">
                    <th class="hidden md:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('User Details')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Wallet Balance')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Status')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Actions')}}</th>
                </tr>
            </thead>
            <tbody>
            @forelse($wallet_lists ?? [] as $data)
                <tr class="border-b border-main hover:bg-muted transition-colors">

                    {{-- ID --}}
                    <td class="hidden md:table-cell px-4 py-3.5">
                        <span class="text-[11px] font-bold text-primary">{{__('#')}} {{$data->id}}</span>
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
                                @if(optional($data->user)->mobile)
                                    <div class="tw-cell-sub">{{optional($data->user)->mobile}}</div>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- Wallet Balance --}}
                    <td class="px-4 py-3.5">
                        <span class="text-sm font-bold text-dark">{{ float_amount_with_currency_symbol($data->balance) }}</span>
                    </td>

                    {{-- Status --}}
                    <td class="px-4 py-3.5">
                        @if($data->status == 1)
                            <span class="tw-pill tw-pill-success">
                                <i class="mdi mdi-check-circle text-[10px] mr-0.5"></i> {{__('Active')}}
                            </span>
                        @else
                            <span class="tw-pill tw-pill-danger">
                                <i class="mdi mdi-close-circle text-[10px] mr-0.5"></i> {{__('Inactive')}}
                            </span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end">
                            <x-status-change-tw :url="route('landlord.admin.wallet.status',$data->id)"/>
                        </div>
                    </td>

                </tr>
            @empty
                <tr>
                    <td class="text-center py-8" colspan="5">
                        <div class="flex flex-col items-center gap-2">
                            <div class="w-12 h-12 rounded-full bg-secondary flex items-center justify-center">
                                <i class="mdi mdi-wallet-outline text-muted text-xl"></i>
                            </div>
                            <span class="text-sm text-muted">{{__('No wallet data available')}}</span>
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
