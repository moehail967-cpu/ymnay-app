@extends(include_theme_path('user.dashboard.user-master'))

@section('title') {{ __('Package Orders') }} @endsection

@section('dashboard_content')

<div class="sz-dash-section-title">
    <i class="mdi mdi-cube-outline" style="margin-right:8px;color:var(--sz-red);"></i>
    {{ __('Package Orders') }}
</div>

@if(isset($order_list) && count($order_list) > 0)
<div style="display:flex;flex-direction:column;gap:14px;">
    @foreach($order_list as $data)
    @php
        $s = $data->status ?? 'pending';
        $s_class = match($s){
            'complete'    => 'sz-dash-badge-success',
            'cancel'      => 'sz-dash-badge-danger',
            'in_progress' => 'sz-dash-badge-info',
            default       => 'sz-dash-badge-warning',
        };
    @endphp
    <div class="sz-dash-card">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:10px;">
            <div>
                <div style="font-family:var(--sz-font-head);font-size:17px;font-weight:700;color:var(--sz-dark);text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">
                    {{ $data->package_name }}
                </div>
                <div style="font-size:13px;color:var(--sz-muted);display:flex;flex-wrap:wrap;gap:16px;font-family:var(--sz-font-body);">
                    <span>
                        <strong style="font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1px;color:var(--sz-dark);">{{ __('Order ID') }}:</strong>
                        #{{ $data->id }}
                    </span>
                    <span>
                        <strong style="font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1px;color:var(--sz-dark);">{{ __('Price') }}:</strong>
                        {{ amount_with_currency_symbol($data->package_price) }}
                    </span>
                    <span>
                        <strong style="font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1px;color:var(--sz-dark);">{{ __('Date') }}:</strong>
                        {{ date_format($data->created_at, 'd M Y') }}
                    </span>
                </div>
            </div>
            <span class="sz-dash-badge {{ $s_class }}">
                {{ __($s !== 'in_progress' ? $s : 'In Progress') }}
            </span>
        </div>

        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:16px;padding-top:16px;border-top:1px solid var(--sz-border);">
            @if($data->payment_status !== 'complete' && $data->status !== 'cancel')
                <a href="{{ theme_package_order_confirm_url($data->package_id) }}" class="sz-dash-btn sz-dash-btn-red" style="padding:7px 18px;font-size:11px;">
                    <i class="mdi mdi-credit-card-outline"></i> {{ __('Pay Now') }}
                </a>
                <form action="{{ theme_package_order_cancel_url() }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $data->id }}">
                    <button type="submit" class="sz-dash-btn sz-dash-btn-outline" style="padding:7px 18px;font-size:11px;color:var(--sz-red);border-color:rgba(198,40,40,.3);">
                        <i class="mdi mdi-close-circle-outline"></i> {{ __('Cancel') }}
                    </button>
                </form>
            @endif
            @if($data->payment_status === 'complete')
                <form action="{{ theme_package_invoice_url() }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="id" value="{{ $data->id }}">
                    <button type="submit" class="sz-dash-btn sz-dash-btn-navy" style="padding:7px 18px;font-size:11px;">
                        <i class="mdi mdi-file-pdf-box"></i> {{ __('Invoice') }}
                    </button>
                </form>
            @endif
        </div>
    </div>
    @endforeach
</div>
@else
<div class="sz-dash-card" style="text-align:center;padding:60px 20px;">
    <i class="mdi mdi-cube-off-outline" style="font-size:56px;color:var(--sz-border);display:block;margin-bottom:14px;"></i>
    <p style="color:var(--sz-muted);font-size:14px;font-family:var(--sz-font-body);">{{ __('No package orders yet') }}</p>
</div>
@endif

@endsection
