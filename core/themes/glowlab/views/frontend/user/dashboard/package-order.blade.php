@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Package Orders') }} @endsection

@section('section')

<div style="font-weight:700;color:var(--gl-dark);margin-bottom:20px;font-size:15px;display:flex;align-items:center;gap:8px;">
    <i class="mdi mdi-cube-outline" style="color:var(--gl-gold);"></i> {{ __('Package Orders') }}
</div>

@if(count($order_list) > 0)
<div style="display:flex;flex-direction:column;gap:14px;">
    @foreach($order_list as $data)
    @php
        $s = $data->status ?? 'pending';
        $s_color = match($s){ 'complete'=>'#38a169', 'cancel'=>'#c53030', 'in_progress'=>'#2b6cb0', default=>'#d97706' };
        $s_bg    = match($s){ 'complete'=>'rgba(72,187,120,.1)', 'cancel'=>'rgba(229,62,62,.1)', 'in_progress'=>'rgba(43,108,176,.1)', default=>'rgba(245,158,11,.1)' };
    @endphp
    <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:20px;box-shadow:var(--gl-shadow);">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:10px;">
            <div>
                <div style="font-weight:700;color:var(--gl-dark);font-size:15px;margin-bottom:6px;">{{ $data->package_name }}</div>
                <div style="font-size:12px;color:var(--gl-muted);display:flex;flex-wrap:wrap;gap:12px;">
                    <span><strong style="color:var(--gl-dark);">{{ __('Order ID') }}:</strong> #{{ $data->id }}</span>
                    <span><strong style="color:var(--gl-dark);">{{ __('Price') }}:</strong> {{ amount_with_currency_symbol($data->package_price) }}</span>
                    <span><strong style="color:var(--gl-dark);">{{ __('Date') }}:</strong> {{ date_format($data->created_at,'d M Y') }}</span>
                </div>
            </div>
            <span style="padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;background:{{ $s_bg }};color:{{ $s_color }};">
                {{ __($s !== 'in_progress' ? $s : 'In Progress') }}
            </span>
        </div>

        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:14px;padding-top:14px;border-top:1px solid var(--gl-border);">
            @if($data->payment_status !== 'complete' && $data->status !== 'cancel')
                <a href="{{ theme_package_order_confirm_url($data->package_id) }}"
                   style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:var(--gl-dark);color:#fff;border-radius:var(--gl-radius);font-size:12px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;text-decoration:none;transition:background .2s;"
                   onmouseover="this.style.background='var(--gl-gold)'" onmouseout="this.style.background='var(--gl-dark)'">
                    <i class="mdi mdi-credit-card"></i> {{ __('Pay Now') }}
                </a>
                <form action="{{ theme_package_order_cancel_url() }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $data->id }}">
                    <button type="submit"
                            style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border:1.5px solid #fc8181;border-radius:var(--gl-radius);background:rgba(229,62,62,.06);color:#c53030;font-size:12px;font-weight:700;cursor:pointer;transition:all .2s;"
                            onmouseover="this.style.borderColor='#c53030'" onmouseout="this.style.borderColor='#fc8181'">
                        <i class="mdi mdi-close-circle-outline"></i> {{ __('Cancel') }}
                    </button>
                </form>
            @endif
            @if($data->payment_status === 'complete')
                <form action="{{ theme_package_invoice_url() }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="id" value="{{ $data->id }}">
                    <button type="submit"
                            style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);background:transparent;color:var(--gl-dark);font-size:12px;font-weight:700;cursor:pointer;transition:all .2s;"
                            onmouseover="this.style.borderColor='var(--gl-dark)'" onmouseout="this.style.borderColor='var(--gl-border)'">
                        <i class="mdi mdi-file-pdf-box"></i> {{ __('Invoice') }}
                    </button>
                </form>
            @endif
        </div>
    </div>
    @endforeach
</div>

@else
<div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:48px;text-align:center;box-shadow:var(--gl-shadow);">
    <i class="mdi mdi-cube-outline" style="font-size:48px;color:var(--gl-border);display:block;margin-bottom:12px;"></i>
    <p style="color:var(--gl-muted);font-size:14px;margin:0;">{{ __('No package orders yet') }}</p>
</div>
@endif

@endsection
