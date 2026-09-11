@extends(theme_path('frontend.user.dashboard.user-master'))

@section('title') {{ __('My Orders') }} @endsection
@section('page-title') {{ __('My Orders') }} @endsection

@section('dashboard-content')
<div class="fp-dash-card">
    <div class="fp-dash-card-header">{{ __('My Orders') }}</div>
    <div class="fp-dash-card-body" style="padding:0;">
        @if(isset($orders) && $orders->count())
        <div class="table-responsive">
            <table class="fp-dash-table">
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
                        $pStyle = match($order->payment_status) { 'complete' => 'background:rgba(0,255,65,.15);color:var(--fp-green);border:1px solid var(--fp-green);', 'pending' => 'background:rgba(255,193,7,.1);color:#ffc107;border:1px solid #ffc107;', default => 'background:rgba(255,77,77,.1);color:#ff4d4d;border:1px solid #ff4d4d;' };
                        $oStyle = match($order->order_status ?? '') { 'complete' => 'background:rgba(0,255,65,.15);color:var(--fp-green);border:1px solid var(--fp-green);', 'processing' => 'background:rgba(255,193,7,.1);color:#ffc107;border:1px solid #ffc107;', 'pending' => 'background:rgba(100,100,255,.1);color:#6d6dff;border:1px solid #6d6dff;', default => 'background:rgba(255,77,77,.1);color:#ff4d4d;border:1px solid #ff4d4d;' };
                    @endphp
                    <tr>
                        <td style="font-family:var(--fp-font-head);font-weight:700;color:var(--fp-green);">#{{ $order->id }}</td>
                        <td>{{ $order->created_at->format('d M Y') }}</td>
                        <td><span class="fp-dash-badge" style="{{ $pStyle }}">{{ ucfirst($order->payment_status) }}</span></td>
                        <td><span class="fp-dash-badge" style="{{ $oStyle }}">{{ ucfirst($order->order_status ?? 'pending') }}</span></td>
                        <td style="font-family:var(--fp-font-head);font-weight:700;">{{ amount_with_currency_symbol($order->total) }}</td>
                        <td>
                            <a href="{{ theme_user_order_detail_url($order->id) }}"
                               class="fp-btn fp-btn-dark fp-btn-sm">{{ __('Details') }}</a>
                            @if(in_array($order->payment_status, ['pending','failed']))
                                <a href="javascript:void(0)" class="fp-btn fp-btn-outline fp-btn-sm cancel-order-btn" data-id="{{ $order->id }}"
                                   style="margin-left:4px;">{{ __('Cancel') }}</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding:16px 20px;">{{ $orders->links() }}</div>
        @else
            <div style="padding:60px;text-align:center;color:var(--fp-muted);">
                <i class="mdi mdi-package-variant" style="font-size:64px;color:var(--fp-border);display:block;margin-bottom:16px;"></i>
                <p style="font-family:var(--fp-font-head);font-size:18px;text-transform:uppercase;letter-spacing:1px;">{{ __('No orders yet') }}</p>
                <a href="{{ theme_shop_url() }}" class="fp-btn fp-btn-primary" style="margin-top:12px;">{{ __('Start Shopping') }}</a>
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
        success: function (d) { if (d.type === 'success') { toastr.success(d.msg); setTimeout(() => location.reload(), 500); } else { toastr.error(d.msg); } },
        error: function () { toastr.error('{{ __("An error occurred") }}'); }
    });
});
</script>
@endsection
