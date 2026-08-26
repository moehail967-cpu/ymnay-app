@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Package Orders') }} @endsection

@section('section')

<div style="font-weight:800;color:var(--dk-white);margin-bottom:20px;font-size:15px;text-transform:uppercase;letter-spacing:.5px;">
    <i class="mdi mdi-cube-outline" style="color:var(--dk-red);"></i> {{ __('Package Orders') }}
</div>

@if(count($order_list) > 0)
<div style="display:flex;flex-direction:column;gap:14px;">
    @foreach($order_list as $data)
    @php
        $s = $data->status ?? 'pending';
        $s_color = match($s){ 'complete'=>'#4CAF50', 'cancel'=>'var(--dk-red)', 'in_progress'=>'#29B6F6', default=>'#FFC107' };
    @endphp
    <div style="background:var(--dk-surface);border:1px solid var(--dk-border);border-radius:var(--dk-radius);padding:20px;">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:10px;">
            <div>
                <div style="font-weight:800;color:var(--dk-white);font-size:15px;margin-bottom:6px;">{{ $data->package_name }}</div>
                <div style="font-size:12px;color:var(--dk-silver);display:flex;flex-wrap:wrap;gap:12px;">
                    <span><strong style="color:var(--dk-white);">{{ __('Order ID') }}:</strong> #{{ $data->id }}</span>
                    <span><strong style="color:var(--dk-white);">{{ __('Price') }}:</strong> {{ amount_with_currency_symbol($data->package_price) }}</span>
                    <span><strong style="color:var(--dk-white);">{{ __('Date') }}:</strong> {{ date_format($data->created_at,'d M Y') }}</span>
                </div>
            </div>
            <div>
                <span style="padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;background:rgba(255,255,255,.06);color:{{ $s_color }};">
                    {{ __($s !== 'in_progress' ? $s : 'In Progress') }}
                </span>
            </div>
        </div>

        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:14px;padding-top:14px;border-top:1px solid var(--dk-border);">
            @if($data->payment_status !== 'complete' && $data->status !== 'cancel')
                <a href="{{ theme_package_order_confirm_url($data->package_id) }}" class="dk-btn dk-btn-red dk-btn-sm">
                    <i class="mdi mdi-credit-card-outline"></i> {{ __('Pay Now') }}
                </a>
                <form action="{{ theme_package_order_cancel_url() }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $data->id }}">
                    <button type="submit" class="dk-btn dk-btn-sm" style="background:rgba(229,48,48,.12);color:var(--dk-red);border:1px solid var(--dk-red);padding:6px 12px;font-size:12px;border-radius:var(--dk-radius);cursor:pointer;font-weight:700;">
                        <i class="mdi mdi-close-circle-outline"></i> {{ __('Cancel') }}
                    </button>
                </form>
            @endif
            @if($data->payment_status === 'complete')
                <form action="{{ theme_package_invoice_url() }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="id" value="{{ $data->id }}">
                    <button type="submit" class="dk-btn dk-btn-ghost dk-btn-sm">
                        <i class="mdi mdi-file-pdf-box"></i> {{ __('Invoice') }}
                    </button>
                </form>
            @endif
        </div>
    </div>
    @endforeach
</div>
@else
<div style="background:var(--dk-surface);border:1px solid var(--dk-border);border-radius:var(--dk-radius);padding:48px;text-align:center;">
    <i class="mdi mdi-cube-off-outline" style="font-size:48px;color:var(--dk-border);display:block;margin-bottom:12px;"></i>
    <p style="color:var(--dk-silver);">{{ __('No package orders yet') }}</p>
</div>
@endif

@endsection
