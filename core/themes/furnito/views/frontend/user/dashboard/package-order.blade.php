@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Package Orders') }} @endsection

@section('section')

<div class="fn-dash-section-title">
    <i class="las la-box"></i> {{ __('Package Orders') }}
</div>

@if(count($order_list) > 0)
<div class="d-flex flex-column gap-3">
    @foreach($order_list as $data)
    @php
        $s = $data->status ?? 'pending';
        $s_cls = match($s){ 'complete'=>'fn-badge-success', 'cancel'=>'fn-badge-danger', 'in_progress'=>'fn-badge-info', default=>'fn-badge-warn' };
    @endphp
    <div class="fn-dash-box fn-dash-box-pad">
        <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
            <div>
                <div class="fn-package-name">{{ $data->package_name }}</div>
                <div class="fn-package-meta">
                    <span><strong>{{ __('Order ID') }}:</strong> #{{ $data->id }}</span>
                    <span><strong>{{ __('Price') }}:</strong> {{ amount_with_currency_symbol($data->package_price) }}</span>
                    <span><strong>{{ __('Date') }}:</strong> {{ date_format($data->created_at,'d M Y') }}</span>
                </div>
            </div>
            <span class="fn-status-badge {{ $s_cls }}">
                {{ __($s !== 'in_progress' ? $s : 'In Progress') }}
            </span>
        </div>

        <div class="d-flex gap-2 flex-wrap mt-3 pt-3 fn-package-actions">
            @if($data->payment_status !== 'complete' && $data->status !== 'cancel')
                <a href="{{ theme_package_order_confirm_url($data->package_id) }}" class="fn-btn fn-btn-gold fn-btn-sm">
                    <i class="las la-credit-card"></i> {{ __('Pay Now') }}
                </a>
                <form action="{{ theme_package_order_cancel_url() }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $data->id }}">
                    <button type="submit" class="fn-btn fn-btn-sm fn-btn-danger">
                        <i class="las la-times-circle"></i> {{ __('Cancel') }}
                    </button>
                </form>
            @endif
            @if($data->payment_status === 'complete')
                <form action="{{ theme_package_invoice_url() }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="id" value="{{ $data->id }}">
                    <button type="submit" class="fn-btn fn-btn-outline fn-btn-sm">
                        <i class="las la-file-pdf"></i> {{ __('Invoice') }}
                    </button>
                </form>
            @endif
        </div>
    </div>
    @endforeach
</div>

@else
<div class="fn-dash-box fn-dash-empty">
    <i class="las la-box-open"></i>
    <p>{{ __('No package orders yet.') }}</p>
</div>
@endif

@endsection
