@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Package Orders') }} @endsection

@section('section')

<div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gc-rose);margin-bottom:20px;display:flex;align-items:center;gap:8px;">
    <i class="las la-cube"></i> {{ __('Package Orders') }}
</div>

@if(count($order_list) > 0)
<div style="display:flex;flex-direction:column;gap:14px;">
    @foreach($order_list as $data)
    @php
        $s = $data->status ?? 'pending';
        $s_color = match($s){ 'complete'=>'#38a169', 'cancel'=>'#c53030', 'in_progress'=>'#2b6cb0', default=>'#d97706' };
        $s_bg    = match($s){ 'complete'=>'rgba(72,187,120,.1)', 'cancel'=>'rgba(229,62,62,.1)', 'in_progress'=>'rgba(43,108,176,.1)', default=>'rgba(245,158,11,.1)' };
    @endphp
    <div style="background:var(--gc-ivory);border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:20px;box-shadow:var(--gc-shadow);">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:10px;">
            <div style="font-family:Georgia,serif;">
                <div style="color:var(--gc-dark);font-size:15px;font-style:italic;margin-bottom:6px;">{{ $data->package_name }}</div>
                <div style="font-size:12px;color:var(--gc-muted);display:flex;flex-wrap:wrap;gap:12px;font-style:italic;">
                    <span><span style="color:var(--gc-dark);">{{ __('Order ID') }}:</span> #{{ $data->id }}</span>
                    <span><span style="color:var(--gc-dark);">{{ __('Price') }}:</span> {{ amount_with_currency_symbol($data->package_price) }}</span>
                    <span><span style="color:var(--gc-dark);">{{ __('Date') }}:</span> {{ date_format($data->created_at,'d M Y') }}</span>
                </div>
            </div>
            <span style="padding:4px 12px;border-radius:20px;font-size:11px;text-transform:uppercase;background:{{ $s_bg }};color:{{ $s_color }};">
                {{ __($s !== 'in_progress' ? $s : 'In Progress') }}
            </span>
        </div>

        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:14px;padding-top:14px;border-top:1px solid var(--gc-border);">
            @if($data->payment_status !== 'complete' && $data->status !== 'cancel')
                <a href="{{ theme_package_order_confirm_url($data->package_id) }}" class="gc-btn gc-btn-primary" style="font-size:12px;">
                    <i class="las la-credit-card"></i> {{ __('Pay Now') }}
                </a>
                <form action="{{ theme_package_order_cancel_url() }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $data->id }}">
                    <button type="submit"
                            style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border:1.5px solid #fc8181;border-radius:var(--gc-radius);background:rgba(229,62,62,.06);color:#c53030;font-size:12px;cursor:pointer;transition:all .2s;font-family:Georgia,serif;"
                            onmouseover="this.style.borderColor='#c53030'" onmouseout="this.style.borderColor='#fc8181'">
                        <i class="las la-times-circle"></i> {{ __('Cancel') }}
                    </button>
                </form>
            @endif
            @if($data->payment_status === 'complete')
                <form action="{{ theme_package_invoice_url() }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="id" value="{{ $data->id }}">
                    <button type="submit" class="gc-btn gc-btn-ghost" style="font-size:12px;">
                        <i class="las la-file-pdf"></i> {{ __('Invoice') }}
                    </button>
                </form>
            @endif
        </div>
    </div>
    @endforeach
</div>

@else
<div style="background:var(--gc-ivory);border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:48px;text-align:center;box-shadow:var(--gc-shadow);">
    <div style="font-size:44px;margin-bottom:12px;"><i class="las la-box"></i></div>
    <p style="color:var(--gc-muted);font-size:14px;margin:0;font-style:italic;">{{ __('No package orders yet') }}</p>
</div>
@endif

@endsection
