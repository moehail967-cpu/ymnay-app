@extends(theme_path('frontend.user.dashboard.user-master'))

@section('title') {{ __('Package Orders') }} @endsection
@section('dash-title') {{ __('Package Orders') }} @endsection

@section('dashboard-content')
<div class="el-dash-card">
    <div class="el-dash-card-title"><i class="las la-box"></i> {{ __('Package Orders') }}</div>
    @if(($package_orders ?? collect())->isNotEmpty())
    <div class="table-responsive">
        <table class="el-dash-table">
            <thead><tr>
                <th>{{ __('#') }}</th>
                <th>{{ __('Package') }}</th>
                <th>{{ __('Amount') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Date') }}</th>
                <th></th>
            </tr></thead>
            <tbody>
            @foreach($package_orders as $po)
            <tr>
                <td>#{{ $po->id }}</td>
                <td>{{ $po->package?->name ?? '—' }}</td>
                <td>{{ amount_with_currency_symbol($po->amount ?? 0) }}</td>
                <td>
                    <span class="el-badge el-badge-{{ $po->status === 'complete' ? 'success' : ($po->status === 'pending' ? 'warning' : 'muted') }}">
                        {{ ucfirst($po->status) }}
                    </span>
                </td>
                <td>{{ $po->created_at?->format('d M Y') }}</td>
                <td class="d-flex gap-2">
                    @if(($po->status ?? '') !== 'complete')
                    <a href="{{ theme_package_order_confirm_url($po->id) }}" class="el-btn el-btn-primary el-btn-sm"
                       onclick="return confirm('{{ __("Confirm this order?") }}')">
                        <i class="las la-check"></i>
                    </a>
                    @endif
                    <a href="{{ theme_package_invoice_url() }}" class="el-btn el-btn-ghost el-btn-sm" target="_blank">
                        <i class="las la-file-invoice"></i>
                    </a>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div style="text-align:center;padding:48px;">
        <i class="las la-box" style="font-size:48px;color:#e0e0e0;display:block;margin-bottom:12px;"></i>
        <p style="color:#888;font-size:14px;">{{ __('No package orders found.') }}</p>
    </div>
    @endif
</div>
@endsection
