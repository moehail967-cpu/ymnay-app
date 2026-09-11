@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Package Orders') }} @endsection

@section('section')

<div style="font-size:13px;font-weight:800;color:var(--kv-red);margin-bottom:20px;display:flex;align-items:center;gap:8px;">
    <i class="las la-cube"></i> {{ __('Package Orders') }}
</div>

@if(count($order_list) > 0)
<div style="display:flex;flex-direction:column;gap:14px;">
    @foreach($order_list as $data)
    @php
        $s = $data->status ?? 'pending';
        $s_color = match($s){ 'complete'=>'var(--kv-green)', 'cancel'=>'var(--kv-red)', 'in_progress'=>'var(--kv-blue)', default=>'var(--kv-orange)' };
        $s_bg    = match($s){ 'complete'=>'rgba(67,160,71,.1)', 'cancel'=>'rgba(244,67,54,.1)', 'in_progress'=>'rgba(30,136,229,.1)', default=>'rgba(251,140,0,.1)' };
    @endphp
    <div style="background:var(--kv-white);border:2px solid var(--kv-border);border-radius:var(--kv-radius);padding:20px;box-shadow:var(--kv-shadow);">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:10px;">
            <div>
                <div style="color:var(--kv-dark);font-size:16px;font-weight:800;margin-bottom:6px;">{{ $data->package_name }}</div>
                <div style="font-size:12px;color:var(--kv-muted);display:flex;flex-wrap:wrap;gap:12px;">
                    <span><span style="color:var(--kv-dark);font-weight:700;">{{ __('Order ID') }}:</span> #{{ $data->id }}</span>
                    <span><span style="color:var(--kv-dark);font-weight:700;">{{ __('Price') }}:</span> {{ amount_with_currency_symbol($data->package_price) }}</span>
                    <span><span style="color:var(--kv-dark);font-weight:700;">{{ __('Date') }}:</span> {{ date_format($data->created_at,'d M Y') }}</span>
                </div>
            </div>
            <span style="padding:4px 14px;border-radius:var(--kv-radius-sm);font-size:11px;font-weight:700;text-transform:uppercase;background:{{ $s_bg }};color:{{ $s_color }};">
                {{ __($s !== 'in_progress' ? $s : 'In Progress') }}
            </span>
        </div>

        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:14px;padding-top:14px;border-top:1px solid var(--kv-border);">
            @if($data->payment_status !== 'complete' && $data->status !== 'cancel')
                <a href="{{ theme_package_order_confirm_url($data->package_id) }}" class="kv-btn kv-btn-red kv-btn-sm">
                    <i class="las la-credit-card"></i> {{ __('Pay Now') }}
                </a>
                <form action="{{ theme_package_order_cancel_url() }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $data->id }}">
                    <button type="submit"
                            style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border:2px solid rgba(244,67,54,.4);border-radius:var(--kv-radius-sm);background:rgba(244,67,54,.06);color:var(--kv-red);font-size:12px;font-weight:700;cursor:pointer;transition:all .2s;"
                            onmouseover="this.style.borderColor='var(--kv-red)'" onmouseout="this.style.borderColor='rgba(244,67,54,.4)'">
                        <i class="las la-times-circle"></i> {{ __('Cancel') }}
                    </button>
                </form>
            @endif
            @if($data->payment_status === 'complete')
                <form action="{{ theme_package_invoice_url() }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="id" value="{{ $data->id }}">
                    <button type="submit" class="kv-btn kv-btn-outline kv-btn-sm">
                        <i class="las la-file-pdf"></i> {{ __('Invoice') }}
                    </button>
                </form>
            @endif
        </div>
    </div>
    @endforeach
</div>

@else
<div style="background:var(--kv-white);border:2px solid var(--kv-border);border-radius:var(--kv-radius);padding:48px;text-align:center;box-shadow:var(--kv-shadow);">
    <i class="las la-box" style="font-size:52px;color:var(--kv-muted);display:block;margin-bottom:12px;"></i>
    <p style="color:var(--kv-muted);font-size:14px;margin:0;font-weight:600;">{{ __('No package orders yet') }}</p>
</div>
@endif

@endsection
