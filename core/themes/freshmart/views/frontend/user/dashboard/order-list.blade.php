@extends(theme_path('frontend.user.dashboard.user-master'))

@section('title') {{ __('My Orders') }} @endsection
@section('page-title') {{ __('My Orders') }} @endsection

@section('dashboard-content')
<div class="fm-dash-card">
    <div class="fm-dash-card-header">{{ __('My Orders') }}</div>
    <div class="fm-dash-card-body" style="padding:0;">
        @if(isset($orders) && $orders->count())
        <div class="table-responsive">
            <table class="fm-dash-table">
                <thead>
                    <tr>
                        <th>{{ __('Order #') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Payment') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Total') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach($orders as $order)
                    @php
                        $pStyle = match($order->payment_status) { 'complete' => 'background:rgba(39,174,96,.15);color:var(--fm-green);', 'pending' => 'background:rgba(243,156,18,.1);color:#f39c12;', default => 'background:rgba(231,76,60,.1);color:#e74c3c;' };
                        $oStyle = match($order->order_status ?? '') { 'complete' => 'background:rgba(39,174,96,.15);color:var(--fm-green);', 'processing' => 'background:rgba(52,152,219,.1);color:#3498db;', 'pending' => 'background:rgba(155,89,182,.1);color:#9b59b6;', default => 'background:rgba(231,76,60,.1);color:#e74c3c;' };
                    @endphp
                    <tr>
                        <td style="font-weight:700;color:var(--fm-green);">#{{ $order->id }}</td>
                        <td>{{ $order->created_at->format('d M Y') }}</td>
                        <td><span class="fm-dash-badge" style="{{ $pStyle }}">{{ ucfirst($order->payment_status) }}</span></td>
                        <td><span class="fm-dash-badge" style="{{ $oStyle }}">{{ ucfirst($order->order_status ?? 'pending') }}</span></td>
                        <td style="font-weight:700;">{{ amount_with_currency_symbol($order->total) }}</td>
                        <td>
                            <a href="{{ theme_user_order_detail_url($order->id) }}"
                               class="fm-btn fm-btn-outline fm-btn-sm">{{ __('Details') }}</a>
                            @if(in_array($order->payment_status, ['pending','failed']))
                                <button class="fm-btn fm-btn-sm cancel-order-btn"
                                        data-id="{{ $order->id }}"
                                        style="margin-left:4px;background:rgba(231,76,60,.1);color:#e74c3c;border:1px solid #e74c3c;">{{ __('Cancel') }}</button>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding:16px 20px;">{{ $orders->links() }}</div>
        @else
        <div style="padding:60px;text-align:center;color:var(--fm-muted);">
            <i class="las la-box-open" style="font-size:56px;display:block;margin-bottom:16px;"></i>
            <p style="font-size:16px;margin-bottom:16px;">{{ __('No orders yet') }}</p>
            <a href="{{ theme_shop_url() }}" class="fm-btn fm-btn-green">{{ __('Start Shopping') }}</a>
        </div>
        @endif
    </div>
</div>
@endsection

@section('dashboard-scripts')
<script>
$(document).on('click', '.cancel-order-btn', function () {
    var id = $(this).data('id');
    if (!confirm('{{ __("Cancel this order?") }}')) return;
    $.ajax({
        url: '{{ theme_user_cancel_order_url() }}',
        type: 'GET', data: { id: id },
        success: function (d) { d.type === 'success' ? (toastr.success(d.msg), setTimeout(() => location.reload(), 500)) : toastr.error(d.msg); },
        error: function () { toastr.error('{{ __("An error occurred") }}'); }
    });
});
</script>
@endsection
