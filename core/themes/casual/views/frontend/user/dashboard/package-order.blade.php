@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Package Orders') }} @endsection

@section('section')

<div class="cs-dash-section-head">
    <div class="cs-dash-section-title">
        <i class="las la-cube"></i> {{ __('Package Orders') }}
    </div>
</div>

@if(count($order_list) > 0)
<div class="d-flex flex-column gap-3">
    @foreach($order_list as $data)
    @php
        $s       = $data->status ?? 'pending';
        $s_class = match($s) { 'complete' => 'success', 'cancel' => 'danger', 'in_progress' => 'info', default => 'warning' };
    @endphp
    <div class="cs-dash-box cs-dash-pkg-card">
        <div class="cs-dash-pkg-head">
            <div>
                <div class="cs-dash-pkg-name">{{ $data->package_name }}</div>
                <div class="cs-dash-pkg-meta">
                    <span><strong>{{ __('Order ID') }}:</strong> #{{ $data->id }}</span>
                    <span><strong>{{ __('Price') }}:</strong> {{ amount_with_currency_symbol($data->package_price) }}</span>
                    <span><strong>{{ __('Date') }}:</strong> {{ date_format($data->created_at, 'd M Y') }}</span>
                </div>
            </div>
            <span class="cs-dash-badge cs-dash-badge-{{ $s_class }}">
                {{ __($s !== 'in_progress' ? $s : 'In Progress') }}
            </span>
        </div>

        <div class="cs-dash-pkg-actions">
            @if($data->payment_status !== 'complete' && $data->status !== 'cancel')
                <a href="{{ theme_package_order_confirm_url($data->package_id) }}" class="cs-dash-action-btn cs-dash-action-primary">
                    <i class="las la-credit-card"></i> {{ __('Pay Now') }}
                </a>
                <form action="{{ theme_package_order_cancel_url() }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $data->id }}">
                    <button type="submit" class="cs-dash-action-btn cs-dash-action-danger">
                        <i class="las la-times-circle"></i> {{ __('Cancel') }}
                    </button>
                </form>
            @endif
            @if($data->payment_status === 'complete')
                <form action="{{ theme_package_invoice_url() }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="id" value="{{ $data->id }}">
                    <button type="submit" class="cs-dash-action-btn">
                        <i class="las la-file-pdf"></i> {{ __('Invoice') }}
                    </button>
                </form>
            @endif
        </div>
    </div>
    @endforeach
</div>

@else
<div class="cs-dash-empty">
    <i class="las la-box-open cs-dash-empty-icon"></i>
    <p class="cs-dash-empty-text">{{ __('No package orders yet.') }}</p>
</div>
@endif

@endsection
