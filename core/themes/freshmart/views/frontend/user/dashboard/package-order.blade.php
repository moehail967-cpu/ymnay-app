@extends(theme_path('frontend.user.dashboard.user-master'))

@section('title') {{ __('Package Orders') }} @endsection
@section('page-title') {{ __('Package Orders') }} @endsection

@section('dashboard-content')
<div class="fm-dash-card">
    <div class="fm-dash-card-header">{{ __('My Package Orders') }}</div>
    <div class="fm-dash-card-body">
        @if(isset($orders) && $orders->count())
        @foreach($orders as $order)
        <div style="background:var(--fm-surface);border:1px solid var(--fm-border);border-radius:10px;padding:20px;margin-bottom:16px;">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;">
                <div>
                    <div style="font-size:16px;font-weight:800;color:var(--fm-dark);">{{ $order->package?->title ?? __('Package') }}</div>
                    <div style="font-size:12px;color:var(--fm-muted);margin-top:4px;">{{ __('Order #') }}{{ $order->id }} · {{ $order->created_at->format('d M Y') }}</div>
                </div>
                @php $pStyle = match($order->payment_status) { 'complete' => 'color:var(--fm-green);border:1px solid var(--fm-green);', 'pending' => 'color:#f39c12;border:1px solid #f39c12;', default => 'color:#e74c3c;border:1px solid #e74c3c;' }; @endphp
                <span class="fm-dash-badge" style="{{ $pStyle }}background:rgba(0,0,0,.03);">{{ ucfirst($order->payment_status) }}</span>
            </div>
            <div style="margin-top:12px;padding-top:12px;border-top:1px solid var(--fm-border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
                <span style="font-size:20px;font-weight:800;color:var(--fm-green);">{{ amount_with_currency_symbol($order->price ?? 0) }}</span>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    @if($order->payment_status === 'pending')
                        <a href="{{ theme_package_order_confirm_url($order->id) }}"
                           class="fm-btn fm-btn-green fm-btn-sm"><i class="las la-credit-card"></i> {{ __('Pay Now') }}</a>
                    @endif
                    @if(in_array($order->payment_status, ['pending','failed']))
                        <a href="{{ theme_package_order_cancel_url() }}"
                           class="fm-btn fm-btn-sm" style="border:1px solid var(--fm-border);color:var(--fm-muted);">{{ __('Cancel') }}</a>
                    @endif
                    @if($order->payment_status === 'complete')
                        <a href="{{ theme_package_invoice_url() }}"
                           target="_blank" class="fm-btn fm-btn-outline fm-btn-sm"><i class="las la-file-pdf"></i> {{ __('Invoice') }}</a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
        @else
        <div style="padding:48px;text-align:center;color:var(--fm-muted);">
            <i class="las la-cube" style="font-size:56px;display:block;margin-bottom:16px;"></i>
            <p style="font-size:16px;">{{ __('No package orders yet') }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
