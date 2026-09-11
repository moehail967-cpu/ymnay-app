@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('My Orders') }} @endsection

@section('section')

<div class="ar-dash-section-title">
    <i class="mdi mdi-package-variant-closed" style="color:var(--ar-red);"></i> {{ __('My Orders') }}
</div>

@if($order_list->isNotEmpty())
<div class="ar-dash-box">
    <div style="overflow-x:auto;">
        <table class="ar-dash-table">
            <thead>
                <tr>
                    <th>{{ __('Order ID') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Amount') }}</th>
                    <th>{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order_list as $order)
                @php
                    $os = $order->status ?? 'pending';
                    $ps = $order->payment_status ?? 'pending';
                    $os_ok = in_array($os, ['complete', 'success']);
                    $ps_ok = in_array($ps, ['complete', 'success']);
                @endphp
                <tr>
                    <td style="font-weight:700;">#{{ $order->id }}</td>
                    <td style="color:var(--ar-muted);">{{ $order->created_at->format('d M Y') }}</td>
                    <td>
                        <span class="ar-status-badge" style="
                            background:{{ $os_ok ? 'rgba(76,175,80,.12)' : ($os==='cancel'?'rgba(248,58,38,.12)':'rgba(255,193,7,.12)') }};
                            color:{{ $os_ok ? '#4CAF50' : ($os==='cancel'?'var(--ar-red)':'#D4A017') }};">
                            {{ __($os) }}
                        </span>
                        <span class="ar-status-badge ms-1" style="
                            background:{{ $ps_ok ? 'rgba(76,175,80,.12)' : ($ps==='cancel'?'rgba(248,58,38,.12)':'rgba(255,193,7,.12)') }};
                            color:{{ $ps_ok ? '#4CAF50' : ($ps==='cancel'?'var(--ar-red)':'#D4A017') }};">
                            {{ __($ps) }}
                        </span>
                    </td>
                    <td style="font-weight:700;color:var(--ar-red);">{{ amount_with_currency_symbol($order->total_amount) }}</td>
                    <td>
                        <a href="{{ theme_user_package_orders_url() }}/{{ $order->id }}" class="ar-btn ar-btn-outline ar-btn-sm">
                            <i class="mdi mdi-eye-outline"></i> {{ __('Details') }}
                        </a>
                        @if($order->status === 'pending')
                        <form class="order-cancel-form d-inline" action="{{ theme_user_cancel_order_url() }}" method="POST">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                            <button type="submit" class="cancel-btn ar-btn ar-btn-sm" style="background:rgba(248,58,38,.1);color:var(--ar-red);border:1.5px solid var(--ar-red);">
                                <i class="mdi mdi-close-circle-outline"></i> {{ __('Cancel') }}
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $order_list->links() }}
</div>

@else
<div class="ar-dash-box" style="padding:56px;text-align:center;">
    <i class="mdi mdi-package-variant" style="font-size:52px;color:var(--ar-border);display:block;margin-bottom:14px;"></i>
    <p style="color:var(--ar-muted);margin-bottom:20px;">{{ __('No orders found.') }}</p>
    <a href="{{ theme_shop_url() }}" class="ar-btn ar-btn-red">{{ __('Start Shopping') }}</a>
</div>
@endif

@endsection

@section('dashboard-scripts')
<script>
$(document).on('click', '.cancel-btn', function(e){
    e.preventDefault();
    var form = $(this).closest('form');
    Swal.fire({
        title: '{{ __("Cancel this order?") }}',
        text: '{{ __("This cannot be undone.") }}',
        showCancelButton: true,
        confirmButtonText: '{{ __("Yes, Cancel") }}',
        confirmButtonColor: '#F83A26',
    }).then(function(res){ if(res.isConfirmed) form.submit(); });
});
</script>
@endsection
