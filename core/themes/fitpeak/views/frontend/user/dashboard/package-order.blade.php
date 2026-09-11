@extends(theme_path('frontend.user.dashboard.user-master'))

@section('title') {{ __('Package Orders') }} @endsection
@section('page-title') {{ __('Package Orders') }} @endsection

@section('dashboard-content')
<div class="fp-dash-card">
    <div class="fp-dash-card-header">{{ __('My Package Orders') }}</div>
    <div class="fp-dash-card-body">
        @if(isset($orders) && $orders->count())
            @foreach($orders as $order)
            <div style="background:var(--fp-card);border:1px solid var(--fp-border);border-radius:var(--fp-radius);padding:20px;margin-bottom:16px;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;">
                    <div>
                        <div style="font-family:var(--fp-font-head);font-size:18px;font-weight:800;color:var(--fp-text);text-transform:uppercase;letter-spacing:1px;">{{ $order->package?->title ?? __('Package') }}</div>
                        <div style="font-size:12px;color:var(--fp-muted);margin-top:4px;">{{ __('Order #') }}{{ $order->id }} · {{ $order->created_at->format('d M Y') }}</div>
                    </div>
                    @php $pStyle = match($order->payment_status) { 'complete' => 'color:var(--fp-green);border:1px solid var(--fp-green);', 'pending' => 'color:#ffc107;border:1px solid #ffc107;', default => 'color:#ff4d4d;border:1px solid #ff4d4d;' }; @endphp
                    <span class="fp-dash-badge" style="{{ $pStyle }}background:rgba(255,255,255,.03);">{{ ucfirst($order->payment_status) }}</span>
                </div>
                <div style="margin-top:12px;padding-top:12px;border-top:1px solid var(--fp-border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
                    <span style="font-family:var(--fp-font-head);font-size:20px;font-weight:800;color:var(--fp-green);">{{ amount_with_currency_symbol($order->price ?? 0) }}</span>
                    <div style="display:flex;gap:8px;flex-wrap:wrap;">
                        @if($order->payment_status === 'pending')
                            <a href="{{ theme_package_order_confirm_url($order->id) }}"
                               class="fp-btn fp-btn-primary fp-btn-sm"><i class="mdi mdi-credit-card-outline"></i> {{ __('Pay Now') }}</a>
                        @endif
                        @if(in_array($order->payment_status, ['pending','failed']))
                            <a href="{{ !is_null(tenant()) ? route('tenant.user.package.order.cancel', $order->id) : '#' }}"
                               class="fp-btn fp-btn-dark fp-btn-sm">{{ __('Cancel') }}</a>
                        @endif
                        @if($order->payment_status === 'complete')
                            <a href="{{ !is_null(tenant()) ? route('tenant.user.package.order.invoice', $order->id) : '#' }}"
                               target="_blank" class="fp-btn fp-btn-outline fp-btn-sm"><i class="mdi mdi-file-pdf-outline"></i> {{ __('Invoice') }}</a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div style="padding:40px;text-align:center;color:var(--fp-muted);">
                <i class="mdi mdi-cube-off-outline" style="font-size:64px;color:var(--fp-border);display:block;margin-bottom:16px;"></i>
                <p style="font-family:var(--fp-font-head);font-size:18px;text-transform:uppercase;letter-spacing:1px;">{{ __('No package orders yet') }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
