@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('My Orders') }} @endsection

@section('section')

<div class="bp-dash-section-title">
    <i class="las la-shopping-bag"></i> {{ __('My Orders') }}
</div>

@if($order_list->isNotEmpty())
<div class="bp-dash-box">
    <div style="overflow-x:auto;">
        <table class="bp-dash-table">
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
                    <td class="bp-fw-bold">#{{ $order->id }}</td>
                    <td class="bp-muted">{{ $order->created_at->format('d M Y') }}</td>
                    <td>
                        <span class="bp-status-badge {{ $os_ok ? 'bp-badge-success' : ($os==='cancel' ? 'bp-badge-danger' : 'bp-badge-warn') }}">
                            {{ __($os) }}
                        </span>
                        <span class="bp-status-badge ms-1 {{ $ps_ok ? 'bp-badge-success' : ($ps==='cancel' ? 'bp-badge-danger' : 'bp-badge-warn') }}">
                            {{ __($ps) }}
                        </span>
                    </td>
                    <td class="bp-fw-bold bp-accent-text">{{ amount_with_currency_symbol($order->total_amount) }}</td>
                    <td>
                        <a href="{{ theme_user_package_orders_url() }}/{{ $order->id }}" class="bp-btn bp-btn-outline bp-btn-sm">
                            <i class="las la-eye"></i> {{ __('Details') }}
                        </a>
                        @if($order->status === 'pending')
                        <form class="order-cancel-form d-inline" action="{{ theme_user_cancel_order_url() }}" method="POST">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                            <button type="submit" class="cancel-btn bp-btn bp-btn-sm bp-btn-danger">
                                <i class="las la-times-circle"></i> {{ __('Cancel') }}
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
<div class="bp-dash-box bp-dash-empty">
    <i class="las la-shopping-bag"></i>
    <p>{{ __('No orders found.') }}</p>
    <a href="{{ theme_shop_url() }}" class="bp-btn bp-btn-green">{{ __('Start Shopping') }}</a>
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
        confirmButtonColor: '#e94560',
    }).then(function(res){ if(res.isConfirmed) form.submit(); });
});
</script>
@endsection
