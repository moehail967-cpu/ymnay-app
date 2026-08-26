@extends(theme_path('frontend.user.dashboard.user-master'))

@section('title') {{ __('Package Orders') }} @endsection
@section('page-title') {{ __('Package Orders') }} @endsection

@section('dashboard-content')
<div class="vl-dash-card">
    <div class="vl-dash-card-header">{{ __('My Package Orders') }}</div>
    <div class="vl-dash-card-body">
        @if(isset($orders) && $orders->count())
        @foreach($orders as $order)
        <div style="background:var(--vl-surface);border:1px solid var(--vl-border);padding:24px;margin-bottom:16px;">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;">
                <div>
                    <div style="font-family:'Cormorant Garamond',serif;font-size:18px;font-weight:300;color:var(--vl-ivory);letter-spacing:.5px;">{{ $order->package?->title ?? __('Package') }}</div>
                    <div style="font-size:11px;color:var(--vl-muted);margin-top:4px;letter-spacing:.5px;">{{ __('Order #') }}{{ $order->id }} · {{ $order->created_at->format('d M Y') }}</div>
                </div>
                @php $pStyle = match($order->payment_status) { 'complete' => 'color:#48BB78;border:1px solid #48BB78;', 'pending' => 'color:#D69E2E;border:1px solid #D69E2E;', default => 'color:#E53E3E;border:1px solid #E53E3E;' }; @endphp
                <span class="vl-dash-badge" style="{{ $pStyle }}background:rgba(0,0,0,.1);">{{ ucfirst($order->payment_status) }}</span>
            </div>
            <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--vl-border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
                <span style="font-family:'Cormorant Garamond',serif;font-size:20px;color:var(--vl-champagne);">{{ amount_with_currency_symbol($order->price ?? 0) }}</span>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    @if($order->payment_status === 'pending')
                        <a href="{{ theme_package_order_confirm_url($order->id) }}"
                           class="vl-btn vl-btn-primary" style="padding:8px 16px;font-size:9px;letter-spacing:2px;">
                            <i class="mdi mdi-credit-card-outline"></i> {{ __('Pay Now') }}
                        </a>
                    @endif
                    @if(in_array($order->payment_status, ['pending','failed']))
                        <a href="{{ theme_package_order_cancel_url() }}"
                           class="vl-btn vl-btn-outline" style="padding:8px 16px;font-size:9px;letter-spacing:2px;">{{ __('Cancel') }}</a>
                    @endif
                    @if($order->payment_status === 'complete')
                        <a href="{{ theme_package_invoice_url() }}"
                           target="_blank" class="vl-btn vl-btn-outline" style="padding:8px 16px;font-size:9px;letter-spacing:2px;">
                            <i class="mdi mdi-file-pdf-outline"></i> {{ __('Invoice') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
        @else
        <div style="padding:48px;text-align:center;color:var(--vl-muted);">
            <i class="mdi mdi-cube-outline" style="font-size:56px;display:block;margin-bottom:16px;color:var(--vl-border);"></i>
            <div style="font-size:10px;letter-spacing:3px;text-transform:uppercase;">{{ __('No package orders yet') }}</div>
        </div>
        @endif
    </div>
</div>
@endsection
