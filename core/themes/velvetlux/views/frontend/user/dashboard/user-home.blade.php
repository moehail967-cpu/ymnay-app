@extends(theme_path('frontend.user.dashboard.user-master'))

@section('title') {{ __('Dashboard') }} @endsection
@section('page-title') {{ __('Dashboard') }} @endsection

@section('dashboard-content')

{{-- Stats --}}
<div class="row g-3 mb-4">
    @foreach([['mdi mdi-package-variant-closed',__('Orders'),$total_order??0],['mdi mdi-heart-outline',__('Wishlist'),$total_wishlist??0],['mdi mdi-cloud-download-outline',__('Downloads'),$total_download??0],['mdi mdi-lifebuoy',__('Tickets'),$total_ticket??0]] as [$icon,$label,$value])
    <div class="col-6 col-md-3">
        <div class="vl-dash-stat">
            <div class="vl-dash-stat-icon">
                <i class="{{ $icon }}"></i>
            </div>
            <div>
                <div class="vl-dash-stat-value">{{ $value }}</div>
                <div class="vl-dash-stat-label">{{ $label }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Recent Orders --}}
<div class="vl-dash-card">
    <div class="vl-dash-card-header">
        {{ __('Recent Orders') }}
        <a href="{{ route('tenant.user.dashboard.package.order') }}"
           style="font-size:10px;color:var(--vl-champagne);text-decoration:none;letter-spacing:2px;">{{ __('View All') }}</a>
    </div>
    <div class="vl-dash-card-body" style="padding:0;">
        @if(isset($recent_orders) && $recent_orders->count())
        <div class="table-responsive">
            <table class="vl-dash-table">
                <thead>
                    <tr>
                        <th>{{ __('Order #') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Total') }}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($recent_orders as $order)
                    <tr>
                        <td>
                            <a href="{{ theme_user_order_detail_url($order->id) }}"
                               style="color:var(--vl-champagne);font-weight:400;">#{{ $order->id }}</a>
                        </td>
                        <td>{{ $order->created_at->format('d M Y') }}</td>
                        <td>
                            @php $sc = match($order->payment_status) { 'complete' => 'color:#48BB78;border:1px solid #48BB78;', 'pending' => 'color:#D69E2E;border:1px solid #D69E2E;', default => 'color:#E53E3E;border:1px solid #E53E3E;' }; @endphp
                            <span class="vl-dash-badge" style="{{ $sc }}background:rgba(0,0,0,.1);">{{ ucfirst($order->payment_status) }}</span>
                        </td>
                        <td style="color:var(--vl-champagne);">{{ amount_with_currency_symbol($order->total) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="padding:48px;text-align:center;color:var(--vl-muted);">
            <i class="mdi mdi-package-variant" style="font-size:48px;display:block;margin-bottom:12px;color:var(--vl-border);"></i>
            <div style="font-size:10px;letter-spacing:3px;text-transform:uppercase;">{{ __('No orders yet.') }}</div>
        </div>
        @endif
    </div>
</div>

{!! do_action('nazmart:user_dashboard_home', auth()->user()) !!}
@endsection
