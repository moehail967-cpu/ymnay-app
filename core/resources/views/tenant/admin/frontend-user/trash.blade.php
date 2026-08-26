@extends('tenant.admin.admin-master')
@section('title') {{__('All Deleted Users')}} @endsection

@section('style')
    <x-datatable.tw-css/>
<style>.hover\:text-white:hover{color:#fff!important}</style>
@endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

{{-- Table Card --}}
<div class="bg-surface rounded-xl shadow-main border border-main mb-6">

    {{-- Card Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-danger-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-delete-clock text-danger text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Deleted Users')}}</h3>
                <p class="text-xs text-muted">{{__('Trashed user accounts — restore or permanently delete')}}</p>
            </div>
        </div>
        <a href="{{route('tenant.admin.user')}}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-secondary border border-main text-dark text-sm font-semibold hover:bg-primary-soft hover:text-primary hover:border-primary transition whitespace-nowrap">
            <i class="mdi mdi-arrow-left text-base"></i>
            {{__('Back')}}
        </a>
    </div>

    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-main">
                    <th class="hidden md:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest w-14">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('User')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Username')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Verified')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest no-sort text-right">{{__('Actions')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($all_users as $user)
                @php
                    $palettes = [
                        ['bg' => 'bg-primary-soft',  'text' => 'text-primary'],
                        ['bg' => 'bg-info-soft',     'text' => 'text-info'],
                        ['bg' => 'bg-[#f3e8ff]',     'text' => 'text-[#9333ea]'],
                        ['bg' => 'bg-warning-soft',  'text' => 'text-warning'],
                        ['bg' => 'bg-danger-soft',   'text' => 'text-danger'],
                    ];
                    $pal      = $palettes[$user->id % count($palettes)];
                    $initials = collect(explode(' ', $user->name))->map(fn($n) => strtoupper($n[0] ?? ''))->take(2)->join('');
                @endphp
                <tr class="border-b border-main hover:bg-muted transition-colors">
                    {{-- ID --}}
                    <td class="hidden md:table-cell px-4 py-3.5">
                        <span class="text-[11px] font-bold text-primary">{{__('#')}} {{$user->id}}</span>
                    </td>

                    {{-- User --}}
                    <td class="px-4 py-3.5">
                        <div class="flex items-center gap-3">
                            <span class="w-9 h-9 rounded-xl flex items-center justify-center text-[10px] font-bold flex-shrink-0 border-2 border-main {{ $pal['bg'] }} {{ $pal['text'] }} uppercase">
                                {{$initials}}
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-dark truncate leading-tight">{{$user->name}}</p>
                                <p class="text-[11px] text-muted truncate mt-0.5 flex items-center gap-1">
                                    <i class="mdi mdi-at text-primary text-xs flex-shrink-0"></i>
                                    <span class="truncate max-w-[180px]">{{$user->email}}</span>
                                </p>
                            </div>
                        </div>
                    </td>

                    {{-- Username --}}
                    <td class="px-4 py-3.5">
                        <span class="text-sm font-medium text-dark">{{$user->username}}</span>
                    </td>

                    {{-- Verified --}}
                    <td class="px-4 py-3.5">
                        @if($user->email_verified === 0)
                            <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-danger-soft text-danger text-[9px] font-bold uppercase">
                                <i class="mdi mdi-close-circle text-[9px]"></i>{{__('Unverified')}}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-success-soft text-success text-[9px] font-bold uppercase">
                                <i class="mdi mdi-check-circle text-[9px]"></i>{{__('Verified')}}
                            </span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{route('tenant.admin.user.trash.restore', $user->id)}}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-success-soft border border-main text-success text-xs font-semibold hover:bg-success hover:text-white hover:border-success transition-all">
                                <i class="mdi mdi-backup-restore text-sm"></i>
                                {{__('Restore')}}
                            </a>

                            <a href="javascript:void(0)" class="swal_delete_button inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-danger-soft border border-main text-danger text-xs font-semibold hover:bg-danger hover:text-white hover:border-danger transition-all">
                                <i class="mdi mdi-trash-can-outline text-sm"></i>
                                {{__('Delete')}}
                            </a>
                            <form action="{{route('tenant.admin.user.trash.delete', $user->id)}}" method="post" class="hidden">
                                @csrf
                                <button type="submit" class="swal_form_submit_btn"></button>
                            </form>
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
@endsection
