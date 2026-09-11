@extends(theme_path('frontend.user.dashboard.user-master'))

@section('title') {{ __('Dashboard') }} @endsection
@section('page-title') {{ __('Dashboard') }} @endsection

@section('dashboard-content')

{{-- Stats --}}
<div class="row g-3 mb-4">
    @foreach([['las la-box',__('Orders'),$total_order??0,'rgba(39,174,96,.1)'],['las la-heart',__('Wishlist'),$total_wishlist??0,'rgba(231,76,60,.1)'],['las la-download',__('Downloads'),$total_download??0,'rgba(52,152,219,.1)'],['las la-headset',__('Tickets'),$total_ticket??0,'rgba(155,89,182,.1)']] as [$icon,$label,$value,$bg])
    <div class="col-6 col-md-3">
        <div class="fm-dash-stat">
            <div class="fm-dash-stat-icon" style="background:{{ $bg }};">
                <i class="{{ $icon }}" style="color:var(--fm-green);"></i>
            </div>
            <div>
                <div class="fm-dash-stat-value">{{ $value }}</div>
                <div class="fm-dash-stat-label">{{ $label }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Recent Orders --}}
<div class="fm-dash-card">
    <div class="fm-dash-card-header">
        {{ __('Recent Orders') }}
        <a href="{{ route('tenant.user.dashboard.package.order') }}"
           style="font-size:12px;color:var(--fm-green);font-weight:600;">{{ __('View All') }}</a>
    </div>
    <div class="fm-dash-card-body" style="padding:0;">
        @if(isset($recent_orders) && $recent_orders->count())
        <div class="table-responsive">
            <table class="fm-dash-table">
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
                               style="color:var(--fm-green);font-weight:700;">#{{ $order->id }}</a>
                        </td>
                        <td>{{ $order->created_at->format('d M Y') }}</td>
                        <td>
                            @php $sc = match($order->payment_status) { 'complete' => 'background:rgba(39,174,96,.15);color:var(--fm-green);', 'pending' => 'background:rgba(243,156,18,.1);color:#f39c12;', default => 'background:rgba(231,76,60,.1);color:#e74c3c;' }; @endphp
                            <span class="fm-dash-badge" style="{{ $sc }}">{{ ucfirst($order->payment_status) }}</span>
                        </td>
                        <td style="font-weight:700;color:var(--fm-green);">{{ amount_with_currency_symbol($order->total) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="padding:48px;text-align:center;color:var(--fm-muted);">
            <i class="las la-box-open" style="font-size:48px;display:block;margin-bottom:12px;"></i>
            {{ __('No orders yet.') }}
        </div>
        @endif
    </div>
</div>

{!! do_action('nazmart:user_dashboard_home', auth()->user()) !!}
@endsection
