@extends('tenant.admin.admin-master')
@section('title') {{ __('Affiliate Programme') }} @endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

{{-- Stats --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-surface rounded-xl border border-main px-5 py-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-account-group text-primary text-base"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-muted">{{ __('Total Affiliates') }}</p>
            <p class="text-lg font-bold text-dark leading-tight">{{ $affiliates->total() }}</p>
        </div>
    </div>
    <div class="bg-surface rounded-xl border border-main px-5 py-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-check-circle text-success text-base"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-muted">{{ __('Active') }}</p>
            <p class="text-lg font-bold text-dark leading-tight">{{ $affiliates->where('status','active')->count() }}</p>
        </div>
    </div>
    <div class="bg-surface rounded-xl border border-main px-5 py-4 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-warning-soft flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-pause-circle text-warning text-base"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-muted">{{ __('Paused') }}</p>
            <p class="text-lg font-bold text-dark leading-tight">{{ $affiliates->where('status','paused')->count() }}</p>
        </div>
    </div>
</div>

{{-- Table Card --}}
<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 border-b border-main flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-account-network-outline text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{ __('All Affiliates') }}</h3>
                <p class="text-xs text-muted">{{ __('Manage affiliate accounts and their status') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('tenant.admin.affiliate.commissions') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary-soft text-primary text-xs font-semibold hover:bg-primary hover:text-white transition">
                <i class="mdi mdi-cash-multiple text-sm"></i> {{ __('Commissions') }}
            </a>
            <a href="{{ route('tenant.admin.affiliate.payouts') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-secondary border border-main text-dark text-xs font-semibold hover:border-primary hover:text-primary transition">
                <i class="mdi mdi-cash-check text-sm"></i> {{ __('Payouts') }}
            </a>
            <a href="{{ route('tenant.admin.affiliate.settings') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-secondary border border-main text-dark text-xs font-semibold hover:border-primary hover:text-primary transition">
                <i class="mdi mdi-cog-outline text-sm"></i> {{ __('Settings') }}
            </a>
        </div>
    </div>

    <div class="tw-table-wrap">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{ __('Affiliate') }}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{ __('Code') }}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{ __('Balance') }}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{ __('Total Earned') }}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{ __('Status') }}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{ __('Joined') }}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest text-right">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($affiliates as $row)
                <tr class="border-b border-main hover:bg-muted transition-colors">
                    <td class="px-4 py-3.5">
                        <p class="text-sm font-semibold text-dark">{{ $row->user_name }}</p>
                        <p class="text-[11px] text-muted mt-0.5">{{ $row->user_email }}</p>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-secondary border border-main text-xs font-mono text-dark">{{ $row->code }}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-sm font-semibold text-dark">{{ amount_with_currency_symbol($row->balance) }}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-sm text-muted">{{ amount_with_currency_symbol($row->total_earned) }}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        @if($row->status === 'active')
                            <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-success-soft text-success text-[9px] font-bold uppercase">
                                <i class="mdi mdi-check-circle text-[9px]"></i> {{ __('Active') }}
                            </span>
                        @elseif($row->status === 'paused')
                            <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-warning-soft text-warning text-[9px] font-bold uppercase">
                                <i class="mdi mdi-pause-circle text-[9px]"></i> {{ __('Paused') }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-danger-soft text-danger text-[9px] font-bold uppercase">
                                <i class="mdi mdi-close-circle text-[9px]"></i> {{ $row->status }}
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-xs text-muted">{{ \Carbon\Carbon::parse($row->created_at)->format('d M Y') }}</span>
                    </td>
                    <td class="px-4 py-3.5 text-right">
                        <form action="{{ route('tenant.admin.affiliate.affiliates.toggle', $row->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold transition
                                    {{ $row->status === 'active'
                                        ? 'bg-warning-soft text-warning hover:bg-warning hover:text-white'
                                        : 'bg-success-soft text-success hover:bg-success hover:text-white' }}">
                                <i class="mdi {{ $row->status === 'active' ? 'mdi-pause' : 'mdi-play' }} text-sm"></i>
                                {{ $row->status === 'active' ? __('Pause') : __('Activate') }}
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-10 text-center">
                        <i class="mdi mdi-account-group-outline text-4xl text-subtle block mb-2"></i>
                        <p class="text-sm text-muted">{{ __('No affiliates yet.') }}</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($affiliates->hasPages())
    <div class="px-4 py-3 border-t border-main">
        {{ $affiliates->links() }}
    </div>
    @endif
</div>

@endsection
