@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Commission Payment Logs')}} @endsection

@section('style')
    <x-datatable.tw-css/>
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

{{-- Statistics Cards --}}
<div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-5">
    <div class="cm-stat-card">
        <div class="cm-stat-icon bg-[#f5f3ff] text-[#6c5ce7]">
            <i class="mdi mdi-credit-card"></i>
        </div>
        <div>
            <div class="cm-stat-label">{{__('Total Payments')}}</div>
            <div class="cm-stat-value">{{ $totalPayments }}</div>
        </div>
    </div>
    <div class="cm-stat-card">
        <div class="cm-stat-icon bg-success-soft text-success">
            <i class="mdi mdi-check-circle"></i>
        </div>
        <div>
            <div class="cm-stat-label">{{__('Paid Amount')}}</div>
            <div class="cm-stat-value text-success">{{ site_currency_symbol() }}{{ number_format($paidAmount, 2) }}</div>
        </div>
    </div>
    <div class="cm-stat-card">
        <div class="cm-stat-icon bg-warning-soft text-warning">
            <i class="mdi mdi-clock-outline"></i>
        </div>
        <div>
            <div class="cm-stat-label">{{__('Pending Amount')}}</div>
            <div class="cm-stat-value text-warning">{{ site_currency_symbol() }}{{ number_format($pendingAmount, 2) }}</div>
        </div>
    </div>
    <div class="cm-stat-card">
        <div class="cm-stat-icon bg-danger-soft text-danger">
            <i class="mdi mdi-close-circle"></i>
        </div>
        <div>
            <div class="cm-stat-label">{{__('Failed Amount')}}</div>
            <div class="cm-stat-value text-danger">{{ site_currency_symbol() }}{{ number_format($failedAmount, 2) }}</div>
        </div>
    </div>
</div>

{{-- Filter Section --}}
<details class="cm-filter-panel mb-5">
    <summary class="cm-filter-header">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="las la-filter text-primary text-sm"></i>
            </div>
            <div>
                <h4 class="text-sm font-bold text-dark">{{__('Search & Filter')}}</h4>
                <p class="text-xs text-muted">{{__('Filter payment records')}}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.commission.payment-logs') }}"
               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-muted border border-main hover:bg-muted transition">
                <i class="las la-redo-alt text-sm"></i> {{__('Reset')}}
            </a>
            <i class="las la-angle-down text-muted cm-filter-toggle-icon"></i>
        </div>
    </summary>
    <div class="cm-filter-body">
        <form method="GET" action="{{ route('admin.commission.payment-logs') }}">
            <input type="hidden" name="duration" id="duration-input" value="{{ request('duration') ?? '30' }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="lnd-label">{{__('Payment Status')}}</label>
                    <select name="payment_status" class="lnd-input">
                        <option value="">{{__('All Status')}}</option>
                        <option value="success" {{ request('payment_status') == 'success' ? 'selected' : '' }}>{{__('Success')}}</option>
                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>{{__('Pending')}}</option>
                        <option value="processing" {{ request('payment_status') == 'processing' ? 'selected' : '' }}>{{__('Processing')}}</option>
                        <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>{{__('Failed')}}</option>
                    </select>
                </div>
                <div>
                    <label class="lnd-label">{{__('Payment Gateway')}}</label>
                    <input name="payment_gateway" type="text" class="lnd-input" value="{{ request('payment_gateway') }}" placeholder="{{__('Search by Gateway')}}">
                </div>
                <div>
                    <label class="lnd-label">{{__('User')}}</label>
                    <input name="user_id" type="text" class="lnd-input" value="{{ request('user_id') }}" placeholder="{{__('Search by User ID')}}">
                </div>
                <div>
                    <label class="lnd-label">{{__('Transaction ID')}}</label>
                    <input name="transaction_id" type="text" class="lnd-input" value="{{ request('transaction_id') }}" placeholder="{{__('Search by Transaction ID')}}">
                </div>
                <div>
                    <label class="lnd-label">{{__('Date From')}}</label>
                    <input type="date" name="date_from" class="lnd-input" value="{{ request('date_from') }}">
                </div>
                <div>
                    <label class="lnd-label">{{__('Date To')}}</label>
                    <input type="date" name="date_to" class="lnd-input" value="{{ request('date_to') }}">
                </div>
                <div>
                    <label class="lnd-label">{{__('Min Amount')}}</label>
                    <input name="min_amount" type="number" step="0.01" class="lnd-input" value="{{ request('min_amount') }}" placeholder="{{__('Min Amount')}}">
                </div>
                <div>
                    <label class="lnd-label">{{__('Max Amount')}}</label>
                    <input name="max_amount" type="number" step="0.01" class="lnd-input" value="{{ request('max_amount') }}" placeholder="{{__('Max Amount')}}">
                </div>
            </div>
            <div class="mt-4">
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                    <i class="las la-search text-sm"></i> {{__('Apply Filters')}}
                </button>
            </div>
        </form>
    </div>
</details>

{{-- Payment Logs Table --}}
<div class="bg-surface rounded-xl shadow-main border border-main">
    <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
            <i class="mdi mdi-credit-card-outline text-primary text-base"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Payment Logs')}}</h3>
            <p class="text-xs text-muted">{{__('Commission payment history')}}</p>
        </div>
    </div>

    <div class="tw-table-wrap">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-main">
                    <th class="px-4 sm:px-6 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('User')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Amount')}}</th>
                    <th class="hidden md:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Gateway')}}</th>
                    <th class="hidden lg:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Transaction ID')}}</th>
                    <th class="px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Status')}}</th>
                    <th class="hidden md:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Pay Date')}}</th>
                    <th class="hidden lg:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Commissions')}}</th>
                    <th class="hidden sm:table-cell px-4 py-3 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Created')}}</th>
                    <th class="px-4 sm:px-6 py-3 text-[10px] font-bold text-muted uppercase tracking-widest text-right">{{__('Action')}}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($paymentLogs as $log)
                    <tr class="border-b border-subtle hover:bg-muted transition">
                        <td class="px-4 sm:px-6 py-3">
                            <span class="tw-pill tw-pill-info">#{{ $log->id }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm font-semibold text-dark">{{ $log->user->name ?? 'N/A' }}</div>
                            @if($log->user_id)
                                <div class="text-xs text-muted">ID: {{ $log->user_id }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm font-bold text-success">
                            {{ site_currency_symbol() }}{{ number_format($log->amount, 2) }}
                        </td>
                        <td class="hidden md:table-cell px-4 py-3">
                            <span class="tw-pill tw-pill-gray">{{ $log->payment_gateway ?? 'N/A' }}</span>
                        </td>
                        <td class="hidden lg:table-cell px-4 py-3 text-xs text-muted">
                            {{ $log->transaction_id ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-3">
                            @if($log->payment_status === 'complete')
                                <span class="tw-pill tw-pill-success">{{__('Complete')}}</span>
                            @elseif($log->payment_status === 'pending')
                                <span class="tw-pill tw-pill-warning">{{__('Pending')}}</span>
                            @elseif($log->payment_status === 'processing')
                                <span class="tw-pill tw-pill-info">{{__('Processing')}}</span>
                            @else
                                <span class="tw-pill tw-pill-danger">{{__('Failed')}}</span>
                            @endif
                        </td>
                        <td class="hidden md:table-cell px-4 py-3 text-xs text-muted">
                            {{ $log->payment_date ? $log->payment_date->format('D, d-m-y H:i') : 'N/A' }}
                        </td>
                        <td class="hidden lg:table-cell px-4 py-3">
                            <span class="tw-pill tw-pill-primary">{{ $log->commission_ids ?? [] }}</span>
                        </td>
                        <td class="hidden sm:table-cell px-4 py-3 text-xs text-muted">{{ $log->created_at->format('D, d-m-y') }}</td>
                        <td class="px-4 sm:px-6 py-3 text-right">
                            <button type="button" onclick="viewPaymentDetails({{ $log->id }})"
                                    class="tw-btn-icon tw-btn-icon-edit" title="{{__('View Details')}}">
                                <i class="mdi mdi-eye text-sm"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-4 py-8 text-center text-sm text-muted">
                            <i class="las la-inbox text-2xl block mb-1"></i>
                            {{__('No Payment Logs Found')}}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($paymentLogs->hasPages())
        <div class="px-4 sm:px-6 py-3 border-t border-main">
            {{ $paymentLogs->appends(request()->query())->links() }}
        </div>
    @endif
</div>

{{-- Payment Details Modal --}}
<div class="cm-modal-backdrop" id="paymentDetailsBackdrop"></div>
<div class="cm-modal" id="paymentDetailsModal">
    <div class="cm-modal-dialog lg">
        <div class="cm-modal-header">
            <h5 class="cm-modal-title">
                <i class="las la-info-circle text-primary"></i> {{__('Payment Details')}}
            </h5>
            <button type="button" class="cm-modal-close" onclick="closeModal('paymentDetails')">
                <i class="las la-times"></i>
            </button>
        </div>
        <div class="cm-modal-body" id="paymentDetailsContent">
            <div class="flex items-center justify-center py-8">
                <div class="cm-spinner"></div>
            </div>
        </div>
        <div class="cm-modal-footer">
            <button type="button" onclick="closeModal('paymentDetails')"
                    class="px-4 py-2 rounded-xl text-sm font-semibold text-muted border border-main hover:bg-muted transition">
                {{__('Close')}}
            </button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    <x-datatable.tw-js/>
    <script>
        /* ── Modal helpers ──────────────────────────────────── */
        function openModal(name) {
            document.getElementById(name + 'Backdrop').classList.add('active');
            document.getElementById(name + 'Modal').classList.add('active');
        }

        function closeModal(name) {
            document.getElementById(name + 'Backdrop').classList.remove('active');
            document.getElementById(name + 'Modal').classList.remove('active');
        }

        document.querySelectorAll('.cm-modal-backdrop').forEach(function(backdrop) {
            backdrop.addEventListener('click', function() {
                var id = this.id.replace('Backdrop', '');
                closeModal(id);
            });
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.cm-modal.active').forEach(function(modal) {
                    var id = modal.id.replace('Modal', '');
                    closeModal(id);
                });
            }
        });

        function viewPaymentDetails(id) {
            document.getElementById('paymentDetailsContent').innerHTML = '<div class="flex items-center justify-center py-8"><div class="cm-spinner"></div></div>';
            openModal('paymentDetails');

            axios.get("{{ route('admin.commission.payment-log-details', ':id') }}".replace(':id', id))
                .then(response => {
                    document.getElementById('paymentDetailsContent').innerHTML = response.data.html;
                })
                .catch(error => {
                    document.getElementById('paymentDetailsContent').innerHTML =
                        '<div class="text-center py-6 text-danger text-sm"><i class="las la-exclamation-triangle text-xl block mb-1"></i>Error loading details</div>';
                });
        }
    </script>
@endsection
