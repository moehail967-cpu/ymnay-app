@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Package Orders') }} @endsection

@section('section')

<div style="font-weight:800;color:var(--ch-dark);margin-bottom:20px;font-size:15px;">
    <i class="las la-box" style="color:var(--ch-red);"></i> {{ __('Package Orders') }}
</div>

@if(count($order_list) > 0)
<div style="display:flex;flex-direction:column;gap:14px;">
    @foreach($order_list as $data)
    @php
        $s = $data->status ?? 'pending';
        $s_color = match($s){ 'complete'=>'var(--ch-green)', 'cancel'=>'var(--ch-red)', 'in_progress'=>'#29B6F6', default=>'var(--ch-amber)' };
        $s_bg    = match($s){ 'complete'=>'rgba(39,174,96,.1)', 'cancel'=>'rgba(192,57,43,.1)', 'in_progress'=>'rgba(41,182,246,.1)', default=>'rgba(243,156,18,.1)' };
    @endphp
    <div class="ch-dash-card">
        <div class="ch-dash-card-body">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:10px;">
                <div>
                    <div style="font-weight:800;color:var(--ch-dark);font-size:15px;margin-bottom:6px;">{{ $data->package_name }}</div>
                    <div style="font-size:12px;color:var(--ch-muted);display:flex;flex-wrap:wrap;gap:12px;">
                        <span><strong style="color:var(--ch-dark);">{{ __('Order ID') }}:</strong> #{{ $data->id }}</span>
                        <span><strong style="color:var(--ch-dark);">{{ __('Price') }}:</strong> {{ amount_with_currency_symbol($data->package_price) }}</span>
                        <span><strong style="color:var(--ch-dark);">{{ __('Date') }}:</strong> {{ date_format($data->created_at,'d M Y') }}</span>
                    </div>
                </div>
                <span class="ch-dash-badge" style="background:{{ $s_bg }};color:{{ $s_color }};">
                    {{ __($s !== 'in_progress' ? $s : 'In Progress') }}
                </span>
            </div>

            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:14px;padding-top:14px;border-top:1px solid var(--ch-border);">
                @if($data->payment_status !== 'complete' && $data->status !== 'cancel')
                    <a href="{{ theme_package_order_confirm_url($data->package_id) }}" class="ch-btn ch-btn-primary ch-btn-sm">
                        <i class="las la-credit-card"></i> {{ __('Pay Now') }}
                    </a>
                    <form action="{{ theme_package_order_cancel_url() }}" method="POST" style="display:inline;">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $data->id }}">
                        <button type="submit" class="ch-btn ch-btn-sm" style="background:rgba(192,57,43,.08);color:var(--ch-red);border:1px solid var(--ch-border);padding:6px 12px;cursor:pointer;">
                            <i class="las la-times-circle"></i> {{ __('Cancel') }}
                        </button>
                    </form>
                @endif
                @if($data->payment_status === 'complete')
                    <form action="{{ theme_package_invoice_url() }}" method="POST" style="display:inline;">
                        @csrf
                        <input type="hidden" name="id" value="{{ $data->id }}">
                        <button type="submit" class="ch-btn ch-btn-outline ch-btn-sm">
                            <i class="las la-file-pdf"></i> {{ __('Invoice') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="ch-dash-card">
    <div style="padding:48px;text-align:center;">
        <i class="las la-box-open" style="font-size:48px;color:var(--ch-border);display:block;margin-bottom:12px;"></i>
        <p style="color:var(--ch-muted);">{{ __('No package orders yet') }}</p>
    </div>
</div>
@endif

@endsection
