@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Package Orders') }} @endsection

@section('section')

<div class="ar-dash-section-title">
    <i class="mdi mdi-cube-outline" style="color:var(--ar-red);"></i> {{ __('Package Orders') }}
</div>

@if(count($order_list) > 0)
<div class="d-flex flex-column gap-3">
    @foreach($order_list as $data)
    @php
        $s = $data->status ?? 'pending';
        $s_color = match($s){ 'complete'=>'#4CAF50', 'cancel'=>'var(--ar-red)', 'in_progress'=>'#29B6F6', default=>'#D4A017' };
    @endphp
    <div class="ar-dash-box" style="padding:20px;">
        <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
            <div>
                <div style="font-weight:800;font-size:16px;color:var(--ar-dark);margin-bottom:6px;font-family:var(--ar-font-head);">{{ $data->package_name }}</div>
                <div style="font-size:12px;color:var(--ar-muted);display:flex;flex-wrap:wrap;gap:12px;">
                    <span><strong style="color:var(--ar-dark);">{{ __('Order ID') }}:</strong> #{{ $data->id }}</span>
                    <span><strong style="color:var(--ar-dark);">{{ __('Price') }}:</strong> {{ amount_with_currency_symbol($data->package_price) }}</span>
                    <span><strong style="color:var(--ar-dark);">{{ __('Date') }}:</strong> {{ date_format($data->created_at,'d M Y') }}</span>
                </div>
            </div>
            <span class="ar-status-badge" style="background:rgba(248,58,38,.08);color:{{ $s_color }};">
                {{ __($s !== 'in_progress' ? $s : 'In Progress') }}
            </span>
        </div>

        <div class="d-flex gap-2 flex-wrap mt-3 pt-3" style="border-top:1px solid var(--ar-border);">
            @if($data->payment_status !== 'complete' && $data->status !== 'cancel')
                <a href="{{ theme_package_order_confirm_url($data->package_id) }}" class="ar-btn ar-btn-red ar-btn-sm">
                    <i class="mdi mdi-credit-card-outline"></i> {{ __('Pay Now') }}
                </a>
                <form action="{{ theme_package_order_cancel_url() }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $data->id }}">
                    <button type="submit" class="ar-btn ar-btn-sm" style="background:rgba(248,58,38,.1);color:var(--ar-red);border:1.5px solid var(--ar-red);">
                        <i class="mdi mdi-close-circle-outline"></i> {{ __('Cancel') }}
                    </button>
                </form>
            @endif
            @if($data->payment_status === 'complete')
                <form action="{{ theme_package_invoice_url() }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="id" value="{{ $data->id }}">
                    <button type="submit" class="ar-btn ar-btn-outline ar-btn-sm">
                        <i class="mdi mdi-file-pdf-box"></i> {{ __('Invoice') }}
                    </button>
                </form>
            @endif
        </div>
    </div>
    @endforeach
</div>

@else
<div class="ar-dash-box" style="padding:56px;text-align:center;">
    <i class="mdi mdi-cube-off-outline" style="font-size:52px;color:var(--ar-border);display:block;margin-bottom:14px;"></i>
    <p style="color:var(--ar-muted);">{{ __('No package orders yet.') }}</p>
</div>
@endif

@endsection
