<div class="bg-surface rounded-xl shadow-main border border-main">
    {{-- Card Header --}}
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
            <i class="las la-money-bill-wave text-primary text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Withdraw Requests')}}</h3>
            <p class="text-xs text-muted">{{__('All withdraw request records')}}</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="tw-table-wrap">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 sm:px-6 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Tenant')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Amount')}}</th>
                    <th class="hidden sm:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Method')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Status')}}</th>
                    <th class="hidden md:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Created')}}</th>
                    <th class="px-4 sm:px-6 py-3 text-[10px] font-bold text-muted uppercase tracking-widest text-right">{{__('Action')}}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($withdraws as $withdraw)
                    @php
                        $tenant_name = App\Models\User::where('id', $withdraw->tenant_id)->first();
                    @endphp
                    <tr class="border-b border-subtle hover:bg-muted transition">
                        <td class="px-4 sm:px-6 py-3 text-sm font-semibold text-dark">#{{ $withdraw->id }}</td>
                        <td class="px-4 py-3">
                            <div class="text-sm font-semibold text-dark">{{ $tenant_name->name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm font-bold text-success">
                            {{ site_currency_symbol() }}{{ number_format($withdraw->amount, 2) }}
                        </td>
                        <td class="hidden sm:table-cell px-4 py-3">
                            <span class="tw-pill tw-pill-info">{{ ucfirst(str_replace('_', ' ', $withdraw->payment_method)) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @if($withdraw->status === 'complete')
                                <span class="tw-pill tw-pill-success">{{__('Approved')}}</span>
                            @elseif($withdraw->status === 'pending')
                                <span class="tw-pill tw-pill-warning">{{__('Pending')}}</span>
                            @else
                                <span class="tw-pill tw-pill-danger">{{__('Rejected')}}</span>
                            @endif
                        </td>
                        <td class="hidden md:table-cell px-4 py-3 text-xs text-muted">{{ $withdraw->created_at->format('D, d-m-y') }}</td>
                        <td class="px-4 sm:px-6 py-3 text-right">
                            <div class="inline-flex items-center gap-1">
                                <button type="button" onclick="viewWithdrawDetails({{ $withdraw->id }})"
                                        class="tw-btn-icon tw-btn-icon-edit" title="{{__('View Details')}}">
                                    <i class="mdi mdi-eye text-sm"></i>
                                </button>
                                @if($withdraw->status === 'pending')
                                    <button type="button" onclick="showApproveModal({{ $withdraw->id }})"
                                            class="tw-btn-icon" style="color:#0d9488;background:#f0fdfa;border-color:#99f6e4;" title="{{__('Approve')}}">
                                        <i class="mdi mdi-check-circle-outline text-sm"></i>
                                    </button>
                                    <button type="button" onclick="showRejectModal({{ $withdraw->id }})"
                                            class="tw-btn-icon tw-btn-icon-danger" title="{{__('Reject')}}">
                                        <i class="mdi mdi-close-circle-outline text-sm"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-sm text-muted">
                            <i class="las la-inbox text-2xl block mb-1"></i>
                            {{__('No Withdraw Requests Found')}}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($withdraws->hasPages())
        <div class="px-4 sm:px-6 py-3 border-t border-main">
            {{ $withdraws->appends(request()->query())->links() }}
        </div>
    @endif
</div>
