@php $route_name = 'landlord'; @endphp

@extends($route_name.'.admin.admin-master')
@section('title') {{__('Trash — Deleted Tenants')}} @endsection

@section('style')
    <x-datatable.tw-css/>
    <style>.hover\:text-white:hover{color:#fff!important}</style>
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

{{-- Stats Strip --}}
<div class="grid grid-cols-3 gap-3 sm:gap-4 mb-5">
    @php
        $totalDeleted    = $trashed_users->count();
        $totalVerified   = $trashed_users->where('email_verified', 1)->count();
        $totalUnverified = $trashed_users->where('email_verified', 0)->count();
    @endphp

    <div class="stat-card bg-surface rounded-xl border border-main px-4 sm:px-5 py-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-danger-soft flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-delete-clock text-danger text-base"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-muted">{{__('Total Deleted')}}</p>
            <p class="text-lg font-bold text-dark leading-tight">{{ $totalDeleted }}</p>
        </div>
    </div>

    <div class="stat-card bg-surface rounded-xl border border-main px-4 sm:px-5 py-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-email-check text-success text-base"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-muted">{{__('Verified')}}</p>
            <p class="text-lg font-bold text-dark leading-tight">{{ $totalVerified }}</p>
        </div>
    </div>

    <div class="stat-card bg-surface rounded-xl border border-main px-4 sm:px-5 py-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-warning-soft flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-email-remove text-warning text-base"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-muted">{{__('Unverified')}}</p>
            <p class="text-lg font-bold text-dark leading-tight">{{ $totalUnverified }}</p>
        </div>
    </div>
</div>

{{-- Table Card --}}
<div class="bg-surface rounded-xl shadow-main border border-main mb-6">

    {{-- Card Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-danger-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-delete-clock text-danger text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('All Deleted Tenants')}}</h3>
                <p class="text-xs text-muted">{{__('Restore or permanently remove soft-deleted accounts')}}</p>
            </div>
        </div>
        <a href="{{route('landlord.admin.tenant')}}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary-soft border border-main text-primary text-sm font-semibold hover:bg-primary hover:text-white hover:border-primary transition whitespace-nowrap">
            <i class="mdi mdi-arrow-left text-base"></i>
            {{__('Back to Tenants')}}
        </a>
    </div>

    {{-- Warning Banner --}}
    <div class="px-4 sm:px-6 py-3 bg-danger-soft border-b border-main">
        <p class="text-xs text-danger flex items-start gap-2">
            <i class="mdi mdi-alert-circle-outline text-sm flex-shrink-0 mt-0.5"></i>
            <span>{{__('Permanently deleting a user will remove all associated data including packages, shops, and subscriptions. This action cannot be undone.')}}</span>
        </p>
    </div>

    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left" id="trashedUsersTable">
            <thead>
                <tr class="border-b border-main">
                    <th class="hidden md:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('User')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Verified')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Actions')}}</th>
                </tr>
            </thead>
            <tbody>
            @forelse($trashed_users as $user)
                @php
                    $palettes = [
                        ['bg' => 'bg-primary-soft', 'text' => 'text-primary'],
                        ['bg' => 'bg-info-soft',    'text' => 'text-info'],
                        ['bg' => 'bg-[#f3e8ff]',    'text' => 'text-[#9333ea]'],
                        ['bg' => 'bg-warning-soft', 'text' => 'text-warning'],
                        ['bg' => 'bg-danger-soft',  'text' => 'text-danger'],
                    ];
                    $pal      = $palettes[$user->id % count($palettes)];
                    $initials = collect(explode(' ', $user->name))->map(fn($n) => strtoupper($n[0] ?? ''))->take(2)->join('');
                @endphp
                <tr class="border-b border-main hover:bg-muted transition-colors">

                    {{-- ID --}}
                    <td class="hidden md:table-cell px-4 py-3.5">
                        <span class="text-[11px] font-bold text-primary"># {{$user->id}}</span>
                    </td>

                    {{-- User --}}
                    <td class="px-4 py-3.5">
                        <div class="flex items-center gap-3">
                            <span class="w-9 h-9 rounded-xl flex items-center justify-center text-[10px] font-bold flex-shrink-0 border-2 border-main {{ $pal['bg'] }} {{ $pal['text'] }} uppercase opacity-60">
                                {{ $initials }}
                            </span>
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <p class="text-sm font-semibold text-dark truncate leading-tight">{{ $user->name }}</p>
                                    <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-danger-soft text-danger text-[8px] font-bold uppercase tracking-wide">
                                        <i class="mdi mdi-delete text-[8px]"></i> {{__('Deleted')}}
                                    </span>
                                </div>
                                <p class="text-[11px] text-muted truncate mt-0.5 flex items-center gap-1">
                                    <i class="mdi mdi-at text-primary text-xs flex-shrink-0"></i>
                                    <span class="truncate max-w-[200px]">{{ $user->email }}</span>
                                </p>
                            </div>
                        </div>
                    </td>

                    {{-- Verified --}}
                    <td class="px-4 py-3.5">
                        @if($user->email_verified === 0)
                            <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-danger-soft text-danger text-[9px] font-bold uppercase">
                                <i class="mdi mdi-close-circle text-[9px]"></i> {{__('Unverified')}}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-success-soft text-success text-[9px] font-bold uppercase">
                                <i class="mdi mdi-check-circle text-[9px]"></i> {{__('Verified')}}
                            </span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end gap-2">

                            {{-- Restore --}}
                            <a href="{{route('landlord.admin.tenant.trash.restore', $user->id)}}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-success-soft border border-main text-success text-xs font-semibold hover:bg-success hover:text-white hover:border-success transition-all"
                               title="{{__('Restore')}}">
                                <i class="mdi mdi-restore text-sm"></i>
                                <span class="hidden sm:inline">{{__('Restore')}}</span>
                            </a>

                            {{-- Permanent Delete --}}
                            <button type="button"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-danger-soft border border-main text-danger text-xs font-semibold hover:bg-danger hover:text-white hover:border-danger transition-all swal_delete_button"
                                    title="{{__('Delete Permanently')}}">
                                <i class="mdi mdi-delete-forever text-sm"></i>
                                <span class="hidden sm:inline">{{__('Delete')}}</span>
                            </button>
                            <form method="post" action="{{route('landlord.admin.tenant.trash.delete', $user->id)}}" class="hidden d-none">
                                @csrf
                                <button type="submit" class="swal_form_submit_btn hidden d-none"></button>
                            </form>

                        </div>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="4">
                        <div class="flex flex-col items-center gap-3 py-14">
                            <div class="w-14 h-14 rounded-2xl bg-success-soft flex items-center justify-center">
                                <i class="mdi mdi-delete-empty text-success text-2xl"></i>
                            </div>
                            <p class="text-sm font-semibold text-dark">{{__('Trash is empty')}}</p>
                            <p class="text-xs text-muted">{{__('No deleted tenants found. All accounts are active.')}}</p>
                            <a href="{{route('landlord.admin.tenant')}}"
                               class="inline-flex items-center gap-1.5 px-4 py-2 mt-1 rounded-xl bg-primary-soft border border-main text-primary text-sm font-semibold hover:bg-primary hover:text-white hover:border-primary transition">
                                <i class="mdi mdi-arrow-left text-base"></i>
                                {{__('Back to Tenants')}}
                            </a>
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
            if ($.fn.DataTable && !$.fn.dataTable.isDataTable('#trashedUsersTable')) {
                $('#trashedUsersTable').DataTable({
                    "order": [[0, "desc"]],
                    "pageLength": 10,
                    "deferRender": true,
                    "processing": true,
                    'columnDefs': [{ 'targets': 'no-sort', 'orderable': false }],
                    'language': (typeof translatedDataTable === 'function') ? translatedDataTable() : {}
                });
            }
        });
    })(jQuery);
    </script>
@endsection
