<div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-5">
    {{-- Total Commission --}}
    <div class="cm-stat-card">
        <div class="cm-stat-icon bg-[#f5f3ff] text-[#6c5ce7]">
            <i class="las la-coins"></i>
        </div>
        <div>
            <div class="cm-stat-label">{{__('Total Commission')}}</div>
            <div class="cm-stat-value">{{ site_currency_symbol() }}{{ number_format($statistics['total_commission'] ?? 0, 2) }}</div>
        </div>
    </div>

    {{-- Paid Commission --}}
    <div class="cm-stat-card">
        <div class="cm-stat-icon bg-success-soft text-success">
            <i class="las la-check-circle"></i>
        </div>
        <div>
            <div class="cm-stat-label">{{__('Paid Commission')}}</div>
            <div class="cm-stat-value text-success">{{ site_currency_symbol() }}{{ number_format($statistics['paid_commission'] ?? 0, 2) }}</div>
        </div>
    </div>

    {{-- Pending Commission --}}
    <div class="cm-stat-card">
        <div class="cm-stat-icon bg-warning-soft text-warning">
            <i class="las la-clock"></i>
        </div>
        <div>
            <div class="cm-stat-label">{{__('Pending Commission')}}</div>
            <div class="cm-stat-value text-warning">{{ site_currency_symbol() }}{{ number_format($statistics['pending_commission'] ?? 0, 2) }}</div>
        </div>
    </div>

    {{-- Unpaid Commission --}}
    <div class="cm-stat-card">
        <div class="cm-stat-icon bg-danger-soft text-danger">
            <i class="las la-exclamation-circle"></i>
        </div>
        <div>
            <div class="cm-stat-label">{{__('Unpaid Commission')}}</div>
            <div class="cm-stat-value text-danger">{{ site_currency_symbol() }}{{ number_format($statistics['unpaid_commission'] ?? 0, 2) }}</div>
        </div>
    </div>
</div>
