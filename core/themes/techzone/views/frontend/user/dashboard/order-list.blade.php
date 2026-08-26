@extends(include_theme_path('user.dashboard.user-master'))

@section('title') {{ __('My Orders') }} @endsection

@section('dashboard_content')

<div style="font-size:18px;font-weight:700;color:#fff;margin-bottom:20px;display:flex;align-items:center;gap:8px;">
    <i class="mdi mdi-package-variant-closed" style="color:var(--tz-blue);"></i> {{ __('My Orders') }}
</div>

@if(isset($order_list) && $order_list->isNotEmpty())
<div class="tz-dash-card" style="padding:0;overflow:hidden;">
    <div style="overflow-x:auto;">
        <table class="tz-dash-table">
            <thead>
                <tr>
                    <th>{{ __('Order #') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Order Status') }}</th>
                    <th>{{ __('Payment') }}</th>
                    <th>{{ __('Total') }}</th>
                    <th>{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order_list as $order)
                @php
                    $os = $order->status ?? 'pending';
                    $ps = $order->payment_status ?? 'pending';
                    $os_class = match(true){ in_array($os,['complete','success'])=>'tz-dash-badge-success', $os==='cancel'=>'tz-dash-badge-danger', default=>'tz-dash-badge-warning' };
                    $ps_class = match(true){ in_array($ps,['complete','success'])=>'tz-dash-badge-success', $ps==='cancel'=>'tz-dash-badge-danger', default=>'tz-dash-badge-warning' };
                @endphp
                <tr>
                    <td><strong style="color:#fff;">#{{ $order->id }}</strong></td>
                    <td>{{ $order->created_at->format('d M Y') }}</td>
                    <td><span class="tz-dash-badge {{ $os_class }}">{{ __($os) }}</span></td>
                    <td><span class="tz-dash-badge {{ $ps_class }}">{{ __($ps) }}</span></td>
                    <td><strong style="color:var(--tz-blue);">{{ amount_with_currency_symbol($order->total_amount) }}</strong></td>
                    <td>
                        <div style="display:flex;gap:8px;flex-wrap:wrap;">
                            <a href="{{ theme_user_order_detail_url($order->id) }}"
                               style="display:inline-flex;align-items:center;gap:4px;background:var(--tz-blue);color:#fff;padding:5px 12px;border-radius:var(--tz-radius-sm);font-size:12px;font-weight:600;text-decoration:none;font-family:var(--tz-font);transition:background .2s;"
                               onmouseover="this.style.background='var(--tz-blue-deep)'" onmouseout="this.style.background='var(--tz-blue)'">
                                <i class="mdi mdi-eye-outline"></i> {{ __('View') }}
                            </a>
                            @if($order->status === 'pending')
                            <form class="tz-order-cancel-form" action="{{ theme_user_cancel_order_url() }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                <button type="button" class="tz-cancel-btn"
                                        style="display:inline-flex;align-items:center;gap:4px;background:transparent;color:#ef4444;border:1px solid rgba(239,68,68,.4);padding:5px 12px;border-radius:var(--tz-radius-sm);font-size:12px;font-weight:600;cursor:pointer;font-family:var(--tz-font);transition:all .2s;"
                                        onmouseover="this.style.background='rgba(239,68,68,.12)'" onmouseout="this.style.background='transparent'">
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
<div class="tz-dash-card tz-dash-card-body" style="text-align:center;padding:60px 20px;">
    <i class="mdi mdi-package-variant" style="font-size:52px;color:var(--tz-border);display:block;margin-bottom:12px;"></i>
    <p style="color:var(--tz-muted);font-size:14px;margin-bottom:20px;">{{ __('No orders found') }}</p>
    <a href="{{ theme_shop_url() }}"
       style="display:inline-flex;align-items:center;gap:8px;background:var(--tz-blue);color:#fff;padding:10px 20px;border-radius:var(--tz-radius-sm);font-size:13px;font-weight:700;text-decoration:none;font-family:var(--tz-font);">
        <i class="mdi mdi-storefront-outline"></i> {{ __('Start Shopping') }}
    </a>
</div>
@endif

@endsection

@section('dashboard-scripts')
<script>
$(document).on('click', '.tz-cancel-btn', function(e){
    e.preventDefault();
    var form = $(this).closest('form');
    Swal.fire({
        title: '{{ __("Cancel this order?") }}',
        text: '{{ __("This action cannot be undone.") }}',
        showCancelButton: true,
        confirmButtonText: '{{ __("Yes, Cancel") }}',
        confirmButtonColor: '#0066FF',
        background: '#1C2128',
        color: '#CDD9E5',
    }).then(function(res){ if(res.isConfirmed) form.submit(); });
});
</script>
@endsection
