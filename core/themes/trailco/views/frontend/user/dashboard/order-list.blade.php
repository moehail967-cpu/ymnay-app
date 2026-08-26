@extends(include_theme_path('user.dashboard.user-master'))

@section('title') {{ __('My Orders') }} @endsection
@section('page-title') {{ __('My Orders') }} @endsection

@section('dashboard-content')

{!! theme_error_msg() !!}
{!! theme_flash_msg() !!}

<div class="tr-dash-card">
    <div class="tr-dash-card-header">
        <span><i class="mdi mdi-package-variant-closed" style="margin-right:6px;"></i>{{ __('My Orders') }}</span>
    </div>
    <div class="tr-dash-card-body" style="padding:0;">
        @if(isset($orders) && $orders->count())
        <div class="table-responsive">
            <table class="tr-dash-table">
                <thead>
                    <tr>
                        <th>{{ __('Order #') }}</th>
                        <th>{{ __('Items') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Total') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($orders as $order)
                    @php
                        $pStyle = match($order->payment_status) {
                            'complete'   => 'background:rgba(92,122,62,.15);color:var(--tr-olive);',
                            'pending'    => 'background:rgba(212,169,106,.2);color:#9a7a30;',
                            default      => 'background:rgba(194,91,42,.15);color:var(--tr-terra);'
                        };
                        $oStyle = match($order->order_status ?? '') {
                            'complete'   => 'background:rgba(92,122,62,.15);color:var(--tr-olive);',
                            'processing' => 'background:rgba(92,122,62,.08);color:var(--tr-olive);',
                            'pending'    => 'background:rgba(212,169,106,.2);color:#9a7a30;',
                            default      => 'background:rgba(194,91,42,.15);color:var(--tr-terra);'
                        };
                    @endphp
                    <tr>
                        <td style="font-weight:700;color:var(--tr-bark);">#{{ $order->id }}</td>
                        <td style="color:var(--tr-stone);">{{ $order->products?->count() ?? 0 }}</td>
                        <td style="color:var(--tr-stone);">{{ $order->created_at->format('d M Y') }}</td>
                        <td>
                            <span class="tr-dash-badge" style="{{ $pStyle }}">{{ ucfirst($order->payment_status) }}</span>
                            <span class="tr-dash-badge" style="{{ $oStyle }}margin-top:3px;">{{ ucfirst($order->order_status ?? 'pending') }}</span>
                        </td>
                        <td style="font-weight:700;color:var(--tr-bark);">{{ amount_with_currency_symbol($order->total) }}</td>
                        <td>
                            <a href="{{ theme_user_order_detail_url($order->id) }}"
                               class="tr-btn tr-btn-outline tr-btn-sm" style="margin-bottom:4px;">
                                <i class="mdi mdi-eye-outline"></i> {{ __('Details') }}
                            </a>
                            @if(in_array($order->payment_status, ['pending', 'failed']))
                            <a href="{{ theme_user_cancel_order_url($order->id) }}"
                               class="tr-btn tr-btn-sm tr-swal-cancel"
                               data-route="{{ theme_user_cancel_order_url($order->id) }}"
                               style="background:rgba(194,91,42,.12);color:var(--tr-terra);border:1px solid var(--tr-terra);">
                                <i class="mdi mdi-close-circle-outline"></i> {{ __('Cancel') }}
                            </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding:16px 20px;">{{ $orders->links() }}</div>
        @else
        <div style="padding:60px;text-align:center;color:var(--tr-stone);">
            <i class="mdi mdi-package-variant" style="font-size:56px;display:block;margin-bottom:16px;color:var(--tr-border);"></i>
            <p style="font-size:16px;margin-bottom:16px;">{{ __('No orders yet') }}</p>
            <a href="{{ theme_shop_url() }}" class="tr-btn tr-btn-primary">
                <i class="mdi mdi-storefront-outline"></i> {{ __('Start Shopping') }}
            </a>
        </div>
        @endif
    </div>
</div>

@endsection

@section('dashboard-scripts')
<script>
$(document).on('click', '.tr-swal-cancel', function (e) {
    e.preventDefault();
    var url = $(this).data('route');
    if (!confirm('{{ __("Are you sure you want to cancel this order?") }}')) return;
    window.location.href = url;
});
</script>
@endsection
