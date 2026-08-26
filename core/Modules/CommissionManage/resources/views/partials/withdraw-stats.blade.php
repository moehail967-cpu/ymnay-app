<div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-4">
    {{-- Total Requests --}}
    <div class="cm-stat-card">
        <div class="cm-stat-icon bg-[#f5f3ff] text-[#6c5ce7]">
            <i class="las la-file-invoice-dollar"></i>
        </div>
        <div>
            <div class="cm-stat-label">{{__('Total Requests')}}</div>
            <div class="cm-stat-value">{{ $statistics['total_requests'] ?? 0 }}</div>
        </div>
    </div>

    {{-- Pending Requests --}}
    <div class="cm-stat-card">
        <div class="cm-stat-icon bg-warning-soft text-warning">
            <i class="las la-clock"></i>
        </div>
        <div>
            <div class="cm-stat-label">{{__('Pending Requests')}}</div>
            <div class="cm-stat-value text-warning">{{ $statistics['pending_requests'] ?? 0 }}</div>
        </div>
    </div>

    {{-- Approved Requests --}}
    <div class="cm-stat-card">
        <div class="cm-stat-icon bg-success-soft text-success">
            <i class="las la-check-circle"></i>
        </div>
        <div>
            <div class="cm-stat-label">{{__('Approved Requests')}}</div>
            <div class="cm-stat-value text-success">{{ $statistics['approved_requests'] ?? 0 }}</div>
        </div>
    </div>

    {{-- Rejected Requests --}}
    <div class="cm-stat-card">
        <div class="cm-stat-icon bg-danger-soft text-danger">
            <i class="las la-times-circle"></i>
        </div>
        <div>
            <div class="cm-stat-label">{{__('Rejected Requests')}}</div>
            <div class="cm-stat-value text-danger">{{ $statistics['rejected_requests'] ?? 0 }}</div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
    {{-- Pending Amount --}}
    <div class="cm-stat-featured cm-stat-card">
        <div class="cm-stat-icon" style="background: rgba(255,255,255,0.1); color: #fde68a;">
            <i class="las la-hourglass-half"></i>
        </div>
        <div>
            <div class="cm-stat-label">{{__('Pending Amount')}}</div>
            <div class="cm-stat-value">{{ site_currency_symbol() }}{{ number_format($statistics['pending_amount'] ?? 0, 2) }}</div>
        </div>
    </div>

    {{-- Approved Amount --}}
    <div class="cm-stat-featured cm-stat-card">
        <div class="cm-stat-icon" style="background: rgba(255,255,255,0.1); color: #a7f3d0;">
            <i class="las la-money-check-alt"></i>
        </div>
        <div>
            <div class="cm-stat-label">{{__('Approved Amount')}}</div>
            <div class="cm-stat-value">{{ site_currency_symbol() }}{{ number_format($statistics['approved_amount'] ?? 0, 2) }}</div>
        </div>
    </div>
</div>
