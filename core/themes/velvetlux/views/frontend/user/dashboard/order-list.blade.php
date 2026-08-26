@extends(theme_path('frontend.user.dashboard.user-master'))

@section('title') {{ __('My Orders') }} @endsection
@section('page-title') {{ __('My Orders') }} @endsection

@section('dashboard-content')
<div class="vl-dash-card">
    <div class="vl-dash-card-header">{{ __('My Orders') }}</div>
    <div class="vl-dash-card-body" style="padding:0;">
        @if(isset($order_list) && $order_list->count())
        <div class="table-responsive">
            <table class="vl-dash-table">
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
                @foreach($order_list as $order)
                    @php
                        $pStyle = match($order->payment_status) { 'complete' => 'color:#48BB78;border:1px solid #48BB78;', 'pending' => 'color:#D69E2E;border:1px solid #D69E2E;', default => 'color:#E53E3E;border:1px solid #E53E3E;' };
                        $oStyle = match($order->order_status ?? '') { 'complete' => 'color:#48BB78;border:1px solid #48BB78;', 'processing' => 'color:#4299E1;border:1px solid #4299E1;', 'pending' => 'color:var(--vl-champagne);border:1px solid var(--vl-champagne);', default => 'color:#E53E3E;border:1px solid #E53E3E;' };
                    @endphp
                    <tr>
                        <td style="color:var(--vl-champagne);">#{{ $order->id }}</td>
                        <td>{{ $order->created_at->format('d M Y') }}</td>
                        <td><span class="vl-dash-badge" style="{{ $pStyle }}background:rgba(0,0,0,.1);">{{ ucfirst($order->payment_status) }}</span></td>
                        <td><span class="vl-dash-badge" style="{{ $oStyle }}background:rgba(0,0,0,.1);">{{ ucfirst($order->order_status ?? 'pending') }}</span></td>
                        <td style="color:var(--vl-ivory);">{{ amount_with_currency_symbol($order->total) }}</td>
                        <td>
                            <a href="{{ theme_user_order_detail_url($order->id) }}"
                               class="vl-btn vl-btn-outline" style="padding:6px 14px;font-size:9px;letter-spacing:1px;">{{ __('Details') }}</a>
                            @if(in_array($order->payment_status, ['pending','failed']))
                                <button class="cancel-order-btn vl-btn"
                                        data-id="{{ $order->id }}"
                                        style="margin-left:4px;padding:6px 14px;font-size:9px;letter-spacing:1px;background:transparent;border:1px solid #E53E3E;color:#E53E3E;">{{ __('Cancel') }}</button>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding:16px 24px;">{{ $order_list->links() }}</div>
        @else
        <div style="padding:64px;text-align:center;color:var(--vl-muted);">
            <i class="mdi mdi-package-variant" style="font-size:56px;display:block;margin-bottom:16px;color:var(--vl-border);"></i>
            <div style="font-size:10px;letter-spacing:3px;text-transform:uppercase;margin-bottom:20px;">{{ __('No orders yet') }}</div>
            <a href="{{ theme_shop_url() }}" class="vl-btn vl-btn-primary" style="font-size:9px;letter-spacing:3px;">{{ __('Start Shopping') }}</a>
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
