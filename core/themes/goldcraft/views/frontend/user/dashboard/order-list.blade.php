@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('My Orders') }} @endsection

@section('section')

<div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gc-rose);margin-bottom:20px;display:flex;align-items:center;gap:8px;">
    <i class="las la-box"></i> {{ __('My Orders') }}
</div>

@if($order_list->isNotEmpty())
<div style="background:var(--gc-ivory);border:1px solid var(--gc-border);border-radius:var(--gc-radius);overflow:hidden;box-shadow:var(--gc-shadow);">
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;font-family:Georgia,serif;">
            <thead>
                <tr style="background:var(--gc-warm);border-bottom:1px solid var(--gc-border);">
                    <th style="padding:12px 16px;text-align:left;color:var(--gc-muted);font-size:10px;font-weight:400;text-transform:uppercase;letter-spacing:1px;">{{ __('Order ID') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--gc-muted);font-size:10px;font-weight:400;text-transform:uppercase;letter-spacing:1px;">{{ __('Date') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--gc-muted);font-size:10px;font-weight:400;text-transform:uppercase;letter-spacing:1px;">{{ __('Status') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--gc-muted);font-size:10px;font-weight:400;text-transform:uppercase;letter-spacing:1px;">{{ __('Amount') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--gc-muted);font-size:10px;font-weight:400;text-transform:uppercase;letter-spacing:1px;">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order_list as $order)
                @php
                    $os = $order->status ?? 'pending';
                    $ps = $order->payment_status ?? 'pending';
                    $os_color = $os==='complete' ? '#38a169' : ($os==='cancel' ? '#c53030' : '#d97706');
                    $ps_color = $ps==='complete'||$ps==='success' ? '#38a169' : ($ps==='cancel' ? '#c53030' : '#d97706');
                    $os_bg    = $os==='complete' ? 'rgba(72,187,120,.1)' : ($os==='cancel' ? 'rgba(229,62,62,.1)' : 'rgba(245,158,11,.1)');
                    $ps_bg    = $ps==='complete'||$ps==='success' ? 'rgba(72,187,120,.1)' : ($ps==='cancel' ? 'rgba(229,62,62,.1)' : 'rgba(245,158,11,.1)');
                @endphp
                <tr style="border-bottom:1px solid var(--gc-border);">
                    <td style="padding:14px 16px;color:var(--gc-rose);font-style:italic;">#{{ $order->id }}</td>
                    <td style="padding:14px 16px;color:var(--gc-muted);font-style:italic;">{{ $order->created_at->format('d M Y') }}</td>
                    <td style="padding:14px 16px;">
                        <div style="margin-bottom:4px;">
                            <span style="font-size:10px;color:var(--gc-muted);display:block;margin-bottom:2px;">{{ __('Order') }}</span>
                            <span style="padding:2px 10px;border-radius:20px;font-size:11px;font-weight:400;text-transform:uppercase;background:{{ $os_bg }};color:{{ $os_color }};">{{ __($os) }}</span>
                        </div>
                        <div>
                            <span style="font-size:10px;color:var(--gc-muted);display:block;margin-bottom:2px;">{{ __('Payment') }}</span>
                            <span style="padding:2px 10px;border-radius:20px;font-size:11px;font-weight:400;text-transform:uppercase;background:{{ $ps_bg }};color:{{ $ps_color }};">{{ __($ps) }}</span>
                        </div>
                    </td>
                    <td style="padding:14px 16px;color:var(--gc-rose);font-style:italic;">{{ amount_with_currency_symbol($order->total_amount) }}</td>
                    <td style="padding:14px 16px;">
                        <div style="display:flex;gap:8px;flex-wrap:wrap;">
                            <a href="{{ theme_user_order_detail_url($order->id) }}" class="gc-btn gc-btn-ghost" style="font-size:11px;padding:6px 12px;">
                                <i class="las la-eye"></i> {{ __('Details') }}
                            </a>
                            @if($order->status === 'pending')
                            <form class="order-cancel-form" action="{{ theme_user_cancel_order_url() }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                <button type="submit" class="cancel-btn"
                                        style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border:1.5px solid #fc8181;border-radius:var(--gc-radius);font-size:11px;color:#c53030;background:rgba(229,62,62,.06);cursor:pointer;transition:all .2s;font-family:Georgia,serif;"
                                        onmouseover="this.style.borderColor='#c53030'" onmouseout="this.style.borderColor='#fc8181'">
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
<div style="margin-top:16px;">{{ $order_list->links() }}</div>

@else
<div style="background:var(--gc-ivory);border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:48px;text-align:center;box-shadow:var(--gc-shadow);">
    <div style="font-size:44px;margin-bottom:12px;"><i class="las la-box"></i></div>
    <p style="color:var(--gc-muted);font-size:14px;margin-bottom:20px;font-style:italic;">{{ __('No orders found') }}</p>
    <a href="{{ theme_shop_url() }}" class="gc-btn gc-btn-primary">
        {{ __('Start Shopping') }}
    </a>
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
        confirmButtonColor: '#C9806A',
    }).then(function(res){ if(res.isConfirmed) form.submit(); });
});
</script>
@endsection
