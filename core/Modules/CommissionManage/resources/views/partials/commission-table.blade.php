<div class="bg-surface rounded-xl shadow-main border border-main">
    {{-- Card Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
            <i class="las la-coins text-primary text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Commission History')}}</h3>
            <p class="text-xs text-muted">{{__('All commission records')}}</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 sm:px-6 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('User')}}</th>
                    <th class="hidden md:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Order UID')}}</th>
                    <th class="hidden lg:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Gateway')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Order Total')}}</th>
                    <th class="hidden sm:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Rate')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Commission')}}</th>
                    <th class="hidden md:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Pay Status')}}</th>
                    <th class="hidden sm:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Status')}}</th>
                    <th class="hidden lg:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Created')}}</th>
                    <th class="px-4 sm:px-6 py-3 text-[10px] font-bold text-muted uppercase tracking-widest text-right">{{__('Action')}}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($commissions as $commission)
                    <tr class="border-b border-subtle hover:bg-muted transition">
                        <td class="px-4 sm:px-6 py-3 text-sm font-semibold text-dark">#{{ $commission->id }}</td>
                        <td class="px-4 py-3">
                            <div class="text-sm font-semibold text-dark">{{ $commission->user->name ?? '' }}</div>
                            @if($commission->tenant_id)
                                <div class="text-xs text-muted">{{ $commission->tenant_id }}</div>
                            @endif
                        </td>
                        <td class="hidden md:table-cell px-4 py-3">
                            <span class="tw-pill tw-pill-info">{{ $commission->uid }}</span>
                        </td>
                        <td class="hidden lg:table-cell px-4 py-3 text-sm text-dark">{{ $commission->payment_gateway ?? '' }}</td>
                        <td class="px-4 py-3 text-sm font-semibold text-dark">{{ site_currency_symbol() }}{{ number_format($commission->order_total, 2) }}</td>
                        <td class="hidden sm:table-cell px-4 py-3">
                            <span class="tw-pill tw-pill-gray">{{ $commission->commission_rate }}%</span>
                        </td>
                        <td class="px-4 py-3 text-sm font-bold text-success">{{ site_currency_symbol() }}{{ number_format($commission->commission_amount, 2) }}</td>
                        <td class="hidden md:table-cell px-4 py-3">
                            @if($commission->payment_status === 'success')
                                <span class="tw-pill tw-pill-success">{{__('Success')}}</span>
                            @elseif($commission->payment_status === 'pending')
                                <span class="tw-pill tw-pill-warning">{{__('Pending')}}</span>
                            @else
                                <span class="tw-pill tw-pill-danger">{{__('Cancel')}}</span>
                            @endif
                        </td>
                        <td class="hidden sm:table-cell px-4 py-3">
                            @if($commission->status === 'complete')
                                <span class="tw-pill tw-pill-solid-success">{{__('Complete')}}</span>
                            @else
                                <span class="tw-pill tw-pill-gray">{{__('Pending')}}</span>
                            @endif
                        </td>
                        <td class="hidden lg:table-cell px-4 py-3 text-xs text-muted">{{ $commission->created_at->format('D, d-m-y') }}</td>
                        <td class="px-4 sm:px-6 py-3 text-right">
                            <div class="inline-flex items-center gap-1">
                                <button type="button" onclick="viewDetails({{ $commission->id }})"
                                        class="tw-btn-icon tw-btn-icon-edit" title="{{__('View Details')}}">
                                    <i class="mdi mdi-eye text-sm"></i>
                                </button>
                                @if($commission->status !== 'complete')
                                    <button type="button" onclick="markAsPaid({{ $commission->id }})"
                                            class="tw-btn-icon" style="color:#0d9488;background:#f0fdfa;border-color:#99f6e4;" title="{{__('Mark as Paid')}}">
                                        <i class="mdi mdi-check-circle text-sm"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="px-4 py-8 text-center text-sm text-muted">
                            <i class="las la-inbox text-2xl block mb-1"></i>
                            {{__('No Commission Records Found')}}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($commissions->hasPages())
        <div class="px-4 sm:px-6 py-3 border-t border-main">
            {{ $commissions->appends(request()->query())->links() }}
        </div>
    @endif
</div>
