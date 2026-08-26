@extends(include_theme_path('user.user-master'))

@section('title') {{ __('Package Orders') }} @endsection

@section('dashboard_content')

<div class="ms-dash-section-title">
    <i class="mdi mdi-cube-outline" style="margin-right:6px;color:var(--ms-olive);"></i>
    {{ __('Package Orders') }}
</div>

@if(count($order_list) > 0)
<div style="display:flex;flex-direction:column;gap:14px;">
    @foreach($order_list as $data)
    @php
        $s = $data->status ?? 'pending';
        $s_class = match($s){ 'complete'=>'ms-badge-success', 'cancel'=>'ms-badge-muted', 'in_progress'=>'ms-badge-info', default=>'ms-badge-warning' };
    @endphp
    <div class="ms-dash-card">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:10px;">
            <div>
                <div style="font-size:16px;font-weight:600;color:var(--ms-dark);margin-bottom:6px;">{{ $data->package_name }}</div>
                <div style="font-size:12px;color:var(--ms-muted);display:flex;flex-wrap:wrap;gap:14px;">
                    <span><strong style="color:var(--ms-charcoal);">{{ __('Order ID') }}:</strong> #{{ $data->id }}</span>
                    <span><strong style="color:var(--ms-charcoal);">{{ __('Price') }}:</strong> {{ amount_with_currency_symbol($data->package_price) }}</span>
                    <span><strong style="color:var(--ms-charcoal);">{{ __('Date') }}:</strong> {{ date_format($data->created_at, 'd M Y') }}</span>
                </div>
            </div>
            <span class="ms-badge {{ $s_class }}">
                {{ __($s !== 'in_progress' ? $s : 'In Progress') }}
            </span>
        </div>

        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:14px;padding-top:14px;border-top:1px solid var(--ms-border);">
            @if($data->payment_status !== 'complete' && $data->status !== 'cancel')
                <a href="{{ theme_package_order_confirm_url($data->package_id) }}" class="ms-btn-dark" style="font-size:12px;padding:7px 16px;">
                    <i class="mdi mdi-credit-card-outline" style="margin-right:4px;"></i> {{ __('Pay Now') }}
                </a>
                <form action="{{ theme_package_order_cancel_url() }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $data->id }}">
                    <button type="submit" class="ms-btn-border" style="font-size:12px;padding:7px 16px;color:#c0725a;border-color:rgba(192,114,90,.3);"
                            onmouseover="this.style.borderColor='#c0725a'"
                            onmouseout="this.style.borderColor='rgba(192,114,90,.3)'">
                        <i class="mdi mdi-close-circle-outline" style="margin-right:4px;"></i> {{ __('Cancel') }}
                    </button>
                </form>
            @endif
            @if($data->payment_status === 'complete')
                <form action="{{ theme_package_invoice_url() }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="id" value="{{ $data->id }}">
                    <button type="submit" class="ms-btn-border" style="font-size:12px;padding:7px 16px;">
                        <i class="mdi mdi-file-pdf-box" style="margin-right:4px;"></i> {{ __('Invoice') }}
                    </button>
                </form>
            @endif
        </div>
    </div>
    @endforeach
</div>
@else
<div class="ms-dash-card" style="text-align:center;padding:56px 20px;">
    <i class="mdi mdi-cube-off-outline" style="font-size:48px;color:var(--ms-border);display:block;margin-bottom:14px;"></i>
    <p style="color:var(--ms-muted);font-size:14px;">{{ __('No package orders yet') }}</p>
</div>
@endif

@endsection
