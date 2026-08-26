@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('My Orders') }} @endsection

@section('section')

<div class="ch-dash-card-header" style="background:var(--ch-warm);border:1px solid var(--ch-border);border-radius:var(--ch-radius);margin-bottom:20px;">
    <span><i class="las la-shopping-bag" style="color:var(--ch-red);margin-right:6px;"></i> {{ __('My Orders') }}</span>
</div>

@if($order_list->isNotEmpty())
<div class="ch-dash-card">
    <div style="overflow-x:auto;">
        <table class="ch-dash-table">
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
                    $os_bg  = $os==='complete' ? 'rgba(39,174,96,.12)'  : ($os==='cancel' ? 'rgba(192,57,43,.12)'  : 'rgba(243,156,18,.12)');
                    $os_clr = $os==='complete' ? 'var(--ch-green)'      : ($os==='cancel' ? 'var(--ch-red)'         : 'var(--ch-amber)');
                    $ps_bg  = $ps==='complete'||$ps==='success' ? 'rgba(39,174,96,.12)'  : ($ps==='cancel' ? 'rgba(192,57,43,.12)'  : 'rgba(243,156,18,.12)');
                    $ps_clr = $ps==='complete'||$ps==='success' ? 'var(--ch-green)'      : ($ps==='cancel' ? 'var(--ch-red)'         : 'var(--ch-amber)');
                @endphp
                <tr>
                    <td style="font-weight:700;">#{{ $order->id }}</td>
                    <td style="color:var(--ch-muted);">{{ $order->created_at->format('d M Y') }}</td>
                    <td>
                        <div style="margin-bottom:4px;">
                            <span style="font-size:10px;color:var(--ch-muted);display:block;">{{ __('Order') }}</span>
                            <span class="ch-dash-badge" style="background:{{ $os_bg }};color:{{ $os_clr }};">{{ __($os) }}</span>
                        </div>
                        <div>
                            <span style="font-size:10px;color:var(--ch-muted);display:block;">{{ __('Payment') }}</span>
                            <span class="ch-dash-badge" style="background:{{ $ps_bg }};color:{{ $ps_clr }};">{{ __($ps) }}</span>
                        </div>
                    </td>
                    <td style="font-weight:700;">{{ amount_with_currency_symbol($order->total_amount) }}</td>
                    <td>
                        <div style="display:flex;gap:8px;flex-wrap:wrap;">
                            <a href="{{ theme_user_order_detail_url($order->id) }}" class="ch-btn ch-btn-outline ch-btn-sm">
                                <i class="las la-eye"></i> {{ __('Details') }}
                            </a>
                            @if($order->status === 'pending')
                            <form class="order-cancel-form" action="{{ theme_user_cancel_order_url() }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                <button type="submit" class="ch-btn ch-btn-sm" style="background:rgba(192,57,43,.1);color:var(--ch-red);border:1px solid var(--ch-border);padding:6px 12px;cursor:pointer;">
                                    <i class="las la-times-circle"></i> {{ __('Cancel') }}
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="ch-pagination mt-4">
    {{ $order_list->links() }}
</div>

@else
<div class="ch-dash-card">
    <div style="padding:48px;text-align:center;">
        <i class="las la-shopping-bag" style="font-size:48px;color:var(--ch-border);display:block;margin-bottom:12px;"></i>
        <p style="color:var(--ch-muted);">{{ __('No orders found') }}</p>
        <a href="{{ theme_shop_url() }}" class="ch-btn ch-btn-primary">{{ __('Start Shopping') }}</a>
    </div>
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
        confirmButtonColor: '#C0392B',
    }).then(function(res){ if(res.isConfirmed) form.submit(); });
});
</script>
@endsection
