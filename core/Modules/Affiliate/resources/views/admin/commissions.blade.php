@extends('tenant.admin.admin-master')
@section('title') {{ __('Affiliate Commissions') }} @endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 border-b border-main flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-cash-multiple text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{ __('Commission History') }}</h3>
                <p class="text-xs text-muted">{{ __('Review and approve pending commissions') }}</p>
            </div>
        </div>
        <form class="flex items-center gap-2" method="GET">
            <select name="status" onchange="this.form.submit()"
                class="bg-secondary border border-main rounded-lg px-3 py-1.5 text-xs font-semibold text-dark outline-none focus:border-primary transition">
                <option value="">{{ __('All Statuses') }}</option>
                @foreach(['pending','approved','paid','cancelled'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            @if(request('status'))
            <a href="{{ route('tenant.admin.affiliate.commissions') }}"
               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-secondary border border-main text-xs font-semibold text-muted hover:text-dark transition">
                <i class="mdi mdi-close text-sm"></i> {{ __('Reset') }}
            </a>
            @endif
        </form>
    </div>

    <div class="tw-table-wrap">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{ __('Affiliate') }}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{ __('Code') }}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{ __('Order') }}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{ __('Order Total') }}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{ __('Rate') }}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{ __('Commission') }}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{ __('Status') }}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{ __('Date') }}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest text-right">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($commissions as $c)
                <tr class="border-b border-main hover:bg-muted transition-colors">
                    <td class="px-4 py-3.5">
                        <p class="text-sm font-semibold text-dark">{{ $c->affiliate_name }}</p>
                        <p class="text-[11px] text-muted mt-0.5">{{ $c->affiliate_email }}</p>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-secondary border border-main text-xs font-mono text-dark">{{ $c->code }}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-sm font-bold text-primary">#{{ $c->order_id }}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-sm text-dark">{{ amount_with_currency_symbol($c->order_total) }}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-sm text-muted">{{ $c->rate }}%</span>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-sm font-bold text-success">{{ amount_with_currency_symbol($c->amount) }}</span>
                    </td>
                    <td class="px-4 py-3.5">
                        @php
                            $badge = match($c->status) {
                                'approved'  => ['bg-success-soft','text-success','mdi-check-circle'],
                                'pending'   => ['bg-warning-soft','text-warning','mdi-clock-outline'],
                                'paid'      => ['bg-primary-soft','text-primary','mdi-cash-check'],
                                'cancelled' => ['bg-danger-soft','text-danger','mdi-close-circle'],
                                default     => ['bg-secondary','text-muted','mdi-help-circle'],
                            };
                        @endphp
                        <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded {{ $badge[0] }} {{ $badge[1] }} text-[9px] font-bold uppercase">
                            <i class="mdi {{ $badge[2] }} text-[9px]"></i> {{ ucfirst($c->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3.5">
                        <span class="text-xs text-muted">{{ \Carbon\Carbon::parse($c->created_at)->format('d M Y') }}</span>
                    </td>
                    <td class="px-4 py-3.5 text-right">
                        @if($c->status === 'pending')
                        <div class="flex items-center justify-end gap-1">
                            <form action="{{ route('tenant.admin.affiliate.commissions.approve', $c->id) }}" method="POST">
                                @csrf
                                <button type="submit" title="{{ __('Approve') }}"
                                    class="w-8 h-8 rounded-lg bg-success-soft flex items-center justify-center text-success hover:bg-success hover:text-white transition">
                                    <i class="mdi mdi-check text-sm"></i>
                                </button>
                            </form>
                            <form action="{{ route('tenant.admin.affiliate.commissions.reject', $c->id) }}" method="POST">
                                @csrf
                                <button type="submit" title="{{ __('Reject') }}"
                                    class="w-8 h-8 rounded-lg bg-danger-soft flex items-center justify-center text-danger hover:bg-danger hover:text-white transition">
                                    <i class="mdi mdi-close text-sm"></i>
                                </button>
                            </form>
                        </div>
                        @else
                            <span class="text-xs text-subtle">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-4 py-10 text-center">
                        <i class="mdi mdi-cash-multiple text-4xl text-subtle block mb-2"></i>
                        <p class="text-sm text-muted">{{ __('No commissions found.') }}</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($commissions->hasPages())
    <div class="px-4 py-3 border-t border-main">
        {{ $commissions->links() }}
    </div>
    @endif
</div>

@endsection
