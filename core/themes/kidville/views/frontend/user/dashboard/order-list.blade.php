@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('My Orders') }} @endsection

@section('section')

<div style="font-size:13px;font-weight:800;color:var(--kv-red);margin-bottom:20px;display:flex;align-items:center;gap:8px;">
    <i class="las la-box"></i> {{ __('My Orders') }}
</div>

@if($order_list->isNotEmpty())
<div style="background:var(--kv-white);border:2px solid var(--kv-border);border-radius:var(--kv-radius);overflow:hidden;box-shadow:var(--kv-shadow);">
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="background:var(--kv-light);border-bottom:2px solid var(--kv-border);">
                    <th style="padding:12px 16px;text-align:left;color:var(--kv-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('Order ID') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--kv-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('Date') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--kv-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('Status') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--kv-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('Amount') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--kv-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order_list as $order)
                @php
                    $os = $order->status ?? 'pending';
                    $ps = $order->payment_status ?? 'pending';
                    $os_color = $os==='complete' ? 'var(--kv-green)' : ($os==='cancel' ? 'var(--kv-red)' : 'var(--kv-orange)');
                    $ps_color = $ps==='complete'||$ps==='success' ? 'var(--kv-green)' : ($ps==='cancel' ? 'var(--kv-red)' : 'var(--kv-orange)');
                    $os_bg    = $os==='complete' ? 'rgba(67,160,71,.1)' : ($os==='cancel' ? 'rgba(244,67,54,.1)' : 'rgba(251,140,0,.1)');
                    $ps_bg    = $ps==='complete'||$ps==='success' ? 'rgba(67,160,71,.1)' : ($ps==='cancel' ? 'rgba(244,67,54,.1)' : 'rgba(251,140,0,.1)');
                @endphp
                <tr style="border-bottom:1px solid var(--kv-border);">
                    <td style="padding:14px 16px;color:var(--kv-red);font-weight:800;">#{{ $order->id }}</td>
                    <td style="padding:14px 16px;color:var(--kv-muted);">{{ $order->created_at->format('d M Y') }}</td>
                    <td style="padding:14px 16px;">
                        <div style="margin-bottom:4px;">
                            <span style="font-size:10px;color:var(--kv-muted);display:block;margin-bottom:2px;">{{ __('Order') }}</span>
                            <span style="padding:2px 10px;border-radius:var(--kv-radius-sm);font-size:11px;font-weight:700;background:{{ $os_bg }};color:{{ $os_color }};">{{ __($os) }}</span>
                        </div>
                        <div>
                            <span style="font-size:10px;color:var(--kv-muted);display:block;margin-bottom:2px;">{{ __('Payment') }}</span>
                            <span style="padding:2px 10px;border-radius:var(--kv-radius-sm);font-size:11px;font-weight:700;background:{{ $ps_bg }};color:{{ $ps_color }};">{{ __($ps) }}</span>
                        </div>
                    </td>
                    <td style="padding:14px 16px;color:var(--kv-red);font-weight:800;">{{ amount_with_currency_symbol($order->total_amount) }}</td>
                    <td style="padding:14px 16px;">
                        <div style="display:flex;gap:8px;flex-wrap:wrap;">
                            <a href="{{ theme_user_order_detail_url($order->id) }}" class="kv-btn kv-btn-outline kv-btn-sm">
                                <i class="las la-eye"></i> {{ __('Details') }}
                            </a>
                            @if($order->status === 'pending')
                            <form class="order-cancel-form" action="{{ theme_user_cancel_order_url() }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                <button type="submit" class="cancel-btn"
                                        style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border:2px solid rgba(244,67,54,.4);border-radius:var(--kv-radius-sm);font-size:12px;font-weight:700;color:var(--kv-red);background:rgba(244,67,54,.06);cursor:pointer;transition:all .2s;"
                                        onmouseover="this.style.borderColor='var(--kv-red)'" onmouseout="this.style.borderColor='rgba(244,67,54,.4)'">
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
<div style="background:var(--kv-white);border:2px solid var(--kv-border);border-radius:var(--kv-radius);padding:48px;text-align:center;box-shadow:var(--kv-shadow);">
    <i class="las la-box" style="font-size:52px;color:var(--kv-muted);display:block;margin-bottom:12px;"></i>
    <p style="color:var(--kv-muted);font-size:14px;margin-bottom:20px;font-weight:600;">{{ __('No orders found') }}</p>
    <a href="{{ theme_shop_url() }}" class="kv-btn kv-btn-red">
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
        confirmButtonColor: '#F44336',
    }).then(function(res){ if(res.isConfirmed) form.submit(); });
});
</script>
@endsection
