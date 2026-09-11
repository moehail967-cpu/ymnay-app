@extends(include_theme_path('user.dashboard.user-master'))

@section('title') {{ __('Package Orders') }} @endsection

@section('dashboard_content')

<div style="font-size:18px;font-weight:700;color:#fff;margin-bottom:20px;display:flex;align-items:center;gap:8px;">
    <i class="mdi mdi-cube-outline" style="color:var(--tz-blue);"></i> {{ __('Package Orders') }}
</div>

@if(isset($order_list) && count($order_list) > 0)
<div style="display:flex;flex-direction:column;gap:14px;">
    @foreach($order_list as $data)
    @php
        $s = $data->status ?? 'pending';
        $s_class = match($s){ 'complete'=>'tz-dash-badge-success', 'cancel'=>'tz-dash-badge-danger', 'in_progress'=>'tz-dash-badge-info', default=>'tz-dash-badge-warning' };
    @endphp
    <div class="tz-dash-card tz-dash-card-body">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:10px;">
            <div>
                <div style="font-size:17px;font-weight:700;color:#fff;margin-bottom:8px;">{{ $data->package_name }}</div>
                <div style="font-size:13px;color:var(--tz-muted);display:flex;flex-wrap:wrap;gap:16px;">
                    <span><span style="color:var(--tz-text);">{{ __('Order ID') }}:</span> #{{ $data->id }}</span>
                    <span><span style="color:var(--tz-text);">{{ __('Price') }}:</span> <strong style="color:var(--tz-blue);">{{ amount_with_currency_symbol($data->package_price) }}</strong></span>
                    <span><span style="color:var(--tz-text);">{{ __('Date') }}:</span> {{ date_format($data->created_at, 'd M Y') }}</span>
                </div>
            </div>
            <span class="tz-dash-badge {{ $s_class }}">{{ __($s !== 'in_progress' ? $s : 'In Progress') }}</span>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:16px;padding-top:16px;border-top:1px solid var(--tz-border);">
            @if($data->payment_status !== 'complete' && $data->status !== 'cancel')
                <a href="{{ theme_package_order_confirm_url($data->package_id) }}"
                   style="display:inline-flex;align-items:center;gap:6px;background:var(--tz-blue);color:#fff;padding:8px 16px;border-radius:var(--tz-radius-sm);font-size:12px;font-weight:700;text-decoration:none;font-family:var(--tz-font);">
                    <i class="mdi mdi-credit-card-outline"></i> {{ __('Pay Now') }}
                </a>
                <form action="{{ theme_package_order_cancel_url() }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $data->id }}">
                    <button type="submit"
                            style="display:inline-flex;align-items:center;gap:6px;background:transparent;color:#ef4444;border:1px solid rgba(239,68,68,.4);padding:8px 16px;border-radius:var(--tz-radius-sm);font-size:12px;font-weight:700;cursor:pointer;font-family:var(--tz-font);">
                        <i class="mdi mdi-close-circle-outline"></i> {{ __('Cancel') }}
                    </button>
                </form>
            @endif
            @if($data->payment_status === 'complete')
                <form action="{{ theme_package_invoice_url() }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="id" value="{{ $data->id }}">
                    <button type="submit"
                            style="display:inline-flex;align-items:center;gap:6px;background:var(--tz-surface);color:var(--tz-text);border:1px solid var(--tz-border);padding:8px 16px;border-radius:var(--tz-radius-sm);font-size:12px;font-weight:700;cursor:pointer;font-family:var(--tz-font);">
                        <i class="mdi mdi-file-pdf-box"></i> {{ __('Invoice') }}
                    </button>
                </form>
            @endif
        </div>
    </div>
    @endforeach
</div>
@else
<div class="tz-dash-card tz-dash-card-body" style="text-align:center;padding:60px 20px;">
    <i class="mdi mdi-cube-off-outline" style="font-size:52px;color:var(--tz-border);display:block;margin-bottom:12px;"></i>
    <p style="color:var(--tz-muted);font-size:14px;">{{ __('No package orders yet') }}</p>
</div>
@endif

@endsection
