@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('My Orders') }} @endsection

@section('section')

<div style="font-weight:700;color:var(--gl-dark);margin-bottom:20px;font-size:15px;display:flex;align-items:center;gap:8px;">
    <i class="mdi mdi-package-variant-closed" style="color:var(--gl-gold);"></i> {{ __('My Orders') }}
</div>

@if($order_list->isNotEmpty())
<div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);overflow:hidden;box-shadow:var(--gl-shadow);">
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="background:var(--gl-gold-pale);border-bottom:1px solid var(--gl-border);">
                    <th style="padding:12px 16px;text-align:left;color:var(--gl-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('Order ID') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--gl-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('Date') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--gl-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('Status') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--gl-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('Amount') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--gl-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('Action') }}</th>
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
                <tr style="border-bottom:1px solid var(--gl-border);">
                    <td style="padding:14px 16px;color:var(--gl-dark);font-weight:700;">#{{ $order->id }}</td>
                    <td style="padding:14px 16px;color:var(--gl-muted);">{{ $order->created_at->format('d M Y') }}</td>
                    <td style="padding:14px 16px;">
                        <div style="margin-bottom:4px;">
                            <span style="font-size:10px;color:var(--gl-muted);display:block;margin-bottom:2px;">{{ __('Order') }}</span>
                            <span style="padding:2px 10px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;background:{{ $os_bg }};color:{{ $os_color }};">{{ __($os) }}</span>
                        </div>
                        <div>
                            <span style="font-size:10px;color:var(--gl-muted);display:block;margin-bottom:2px;">{{ __('Payment') }}</span>
                            <span style="padding:2px 10px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;background:{{ $ps_bg }};color:{{ $ps_color }};">{{ __($ps) }}</span>
                        </div>
                    </td>
                    <td style="padding:14px 16px;color:var(--gl-dark);font-weight:700;">{{ amount_with_currency_symbol($order->total_amount) }}</td>
                    <td style="padding:14px 16px;">
                        <div style="display:flex;gap:8px;flex-wrap:wrap;">
                            <a href="{{ theme_user_order_detail_url($order->id) }}"
                               style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);font-size:11px;font-weight:600;color:var(--gl-dark);text-decoration:none;transition:border-color .2s;"
                               onmouseover="this.style.borderColor='var(--gl-gold)'" onmouseout="this.style.borderColor='var(--gl-border)'">
                                <i class="mdi mdi-eye-outline"></i> {{ __('Details') }}
                            </a>
                            @if($order->status === 'pending')
                            <form class="order-cancel-form" action="{{ theme_user_cancel_order_url() }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                <button type="submit" class="cancel-btn"
                                        style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border:1.5px solid #fc8181;border-radius:var(--gl-radius);font-size:11px;font-weight:600;color:#c53030;background:rgba(229,62,62,.06);cursor:pointer;transition:all .2s;"
                                        onmouseover="this.style.borderColor='#c53030'" onmouseout="this.style.borderColor='#fc8181'">
                                    <i class="mdi mdi-close-circle-outline"></i> {{ __('Cancel') }}
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
<div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:48px;text-align:center;box-shadow:var(--gl-shadow);">
    <i class="mdi mdi-package-variant" style="font-size:48px;color:var(--gl-border);display:block;margin-bottom:12px;"></i>
    <p style="color:var(--gl-muted);font-size:14px;margin-bottom:20px;">{{ __('No orders found') }}</p>
    <a href="{{ theme_shop_url() }}"
       style="display:inline-flex;align-items:center;gap:6px;padding:10px 24px;background:var(--gl-dark);color:#fff;border-radius:var(--gl-radius);font-size:12px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;text-decoration:none;transition:background .2s;"
       onmouseover="this.style.background='var(--gl-gold)'" onmouseout="this.style.background='var(--gl-dark)'">
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
        confirmButtonColor: '#B8965A',
    }).then(function(res){ if(res.isConfirmed) form.submit(); });
});
</script>
@endsection
