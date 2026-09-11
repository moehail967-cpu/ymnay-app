@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('My Orders') }} @endsection

@section('section')

<div class="cs-dash-section-head">
    <div class="cs-dash-section-title">
        <i class="las la-shopping-bag"></i> {{ __('My Orders') }}
    </div>
</div>

@if($order_list->isNotEmpty())
<div class="cs-dash-box">
    <div class="cs-dash-table-wrap">
        <table class="cs-dash-table">
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
                    $os    = $order->status ?? 'pending';
                    $ps    = $order->payment_status ?? 'pending';
                    $os_ok = in_array($os, ['complete', 'success']);
                    $ps_ok = in_array($ps, ['complete', 'success']);
                @endphp
                <tr>
                    <td class="cs-dash-td-bold">#{{ $order->id }}</td>
                    <td class="cs-dash-td-muted">{{ $order->created_at->format('d M Y') }}</td>
                    <td>
                        <span class="cs-dash-badge cs-dash-badge-{{ $os_ok ? 'success' : ($os === 'cancel' ? 'danger' : 'warning') }}">
                            {{ __($os) }}
                        </span>
                        <span class="cs-dash-badge cs-dash-badge-{{ $ps_ok ? 'success' : ($ps === 'cancel' ? 'danger' : 'warning') }} ms-1">
                            {{ __($ps) }}
                        </span>
                    </td>
                    <td class="cs-dash-td-price">{{ amount_with_currency_symbol($order->total_amount) }}</td>
                    <td>
                        <a href="{{ theme_user_package_orders_url() }}/{{ $order->id }}" class="cs-dash-action-btn">
                            <i class="las la-eye"></i> {{ __('Details') }}
                        </a>
                        @if($order->status === 'pending')
                        <form class="order-cancel-form d-inline" action="{{ theme_user_cancel_order_url() }}" method="POST">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                            <button type="submit" class="cs-dash-action-btn cs-dash-action-danger cancel-btn">
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
<div class="cs-dash-empty">
    <i class="las la-box-open cs-dash-empty-icon"></i>
    <p class="cs-dash-empty-text">{{ __('No orders found.') }}</p>
    <a href="{{ theme_shop_url() }}" class="cs-dash-empty-btn">{{ __('Start Shopping') }}</a>
</div>
@endif

@endsection

@section('dashboard-scripts')
<script>
$(document).on('click', '.cancel-btn', function (e) {
    e.preventDefault();
    var form = $(this).closest('form');
    Swal.fire({
        title: '{{ __("Cancel this order?") }}',
        text: '{{ __("This cannot be undone.") }}',
        showCancelButton: true,
        confirmButtonText: '{{ __("Yes, Cancel") }}',
        confirmButtonColor: '#F83A26',
    }).then(function (res) { if (res.isConfirmed) form.submit(); });
});
</script>
@endsection
