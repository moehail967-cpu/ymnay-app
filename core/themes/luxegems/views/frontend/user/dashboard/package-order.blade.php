@extends(include_theme_path('user.user-master'))
@section('dash-title') {{ __('Package Orders') }} @endsection

@section('dash-content')
<div class="lg-dash-card">
    <div class="lg-dash-card-title">{{ __('Package Order History') }}</div>
    <div style="overflow-x:auto;">
        <table class="lg-dash-table">
            <thead>
                <tr>
                    <th>{{ __('#') }}</th>
                    <th>{{ __('Package') }}</th>
                    <th>{{ __('Amount') }}</th>
                    <th>{{ __('Payment') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($package_orders ?? [] as $po)
                <tr>
                    <td>#{{ $po->id }}</td>
                    <td>{{ $po->package?->title ?? '—' }}</td>
                    <td class="lx-price" style="font-size:13px;">{{ amount_with_currency_symbol($po->amount ?? 0) }}</td>
                    <td style="font-size:11px;color:var(--lx-muted);">{{ ucfirst($po->payment_gateway ?? '—') }}</td>
                    <td>
                        <span class="lg-dash-badge lg-dash-badge-{{ strtolower($po->status ?? 'pending') }}">
                            {{ ucfirst($po->status ?? 'pending') }}
                        </span>
                    </td>
                    <td>{{ $po->created_at?->format('d M Y') }}</td>
                    <td>
                        @if(($po->status ?? '') === 'pending')
                        <a href="{{ theme_package_order_confirm_url($po->id) }}" class="lg-dash-btn lg-dash-btn-gold" style="font-size:9px;padding:6px 12px;">
                            {{ __('Confirm') }}
                        </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:32px;color:var(--lx-muted);">{{ __('No package orders found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
