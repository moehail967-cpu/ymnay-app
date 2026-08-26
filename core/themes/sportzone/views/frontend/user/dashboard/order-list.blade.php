@extends(include_theme_path('user.dashboard.user-master'))

@section('title') {{ __('My Orders') }} @endsection

@section('dashboard_content')

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:20px;">
    <div class="sz-dash-section-title" style="margin-bottom:0;padding-bottom:0;">
        <i class="mdi mdi-package-variant-closed" style="margin-right:8px;color:var(--sz-red);"></i>
        {{ __('My Orders') }}
    </div>
</div>

@if(isset($order_list) && $order_list->isNotEmpty())
<div class="sz-dash-card" style="padding:0;overflow:hidden;">
    <div style="overflow-x:auto;">
        <table class="sz-dash-table">
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
                    $os_class = match(true){ in_array($os,['complete','success'])=>'sz-dash-badge-success', $os==='cancel'=>'sz-dash-badge-danger', default=>'sz-dash-badge-warning' };
                    $ps_class = match(true){ in_array($ps,['complete','success'])=>'sz-dash-badge-success', $ps==='cancel'=>'sz-dash-badge-danger', default=>'sz-dash-badge-warning' };
                @endphp
                <tr>
                    <td><strong style="color:var(--sz-dark);">#{{ $order->id }}</strong></td>
                    <td style="color:var(--sz-muted);">{{ $order->created_at->format('d M Y') }}</td>
                    <td><span class="sz-dash-badge {{ $os_class }}">{{ __($os) }}</span></td>
                    <td><span class="sz-dash-badge {{ $ps_class }}">{{ __($ps) }}</span></td>
                    <td><strong>{{ amount_with_currency_symbol($order->total_amount) }}</strong></td>
                    <td>
                        <div style="display:flex;gap:8px;flex-wrap:wrap;">
                            <a href="{{ theme_user_order_detail_url($order->id) }}"
                               class="sz-dash-btn sz-dash-btn-navy"
                               style="padding:6px 14px;font-size:11px;">
                                <i class="mdi mdi-eye-outline"></i> {{ __('View') }}
                            </a>
                            @if($order->status === 'pending')
                            <form class="sz-order-cancel-form" action="{{ theme_user_cancel_order_url() }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                <button type="button" class="sz-cancel-btn sz-dash-btn sz-dash-btn-outline"
                                        style="padding:6px 14px;font-size:11px;color:var(--sz-red);border-color:rgba(198,40,40,.3);">
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

<div style="margin-top:20px;">
    {{ $order_list->links() }}
</div>

@else
<div class="sz-dash-card" style="text-align:center;padding:60px 20px;">
    <i class="mdi mdi-package-variant" style="font-size:56px;color:var(--sz-border);display:block;margin-bottom:14px;"></i>
    <p style="color:var(--sz-muted);font-size:14px;font-family:var(--sz-font-body);margin-bottom:20px;">{{ __('No orders found') }}</p>
    <a href="{{ theme_shop_url() }}" class="sz-dash-btn sz-dash-btn-red">
        <i class="mdi mdi-storefront-outline"></i> {{ __('Start Shopping') }}
    </a>
</div>
@endif

@endsection

@section('dashboard-scripts')
<script>
$(document).on('click', '.sz-cancel-btn', function(e){
    e.preventDefault();
    var form = $(this).closest('form');
    Swal.fire({
        title: '{{ __("Cancel this order?") }}',
        text: '{{ __("This action cannot be undone.") }}',
        showCancelButton: true,
        confirmButtonText: '{{ __("Yes, Cancel") }}',
        confirmButtonColor: '#C62828',
    }).then(function(res){ if(res.isConfirmed) form.submit(); });
});
</script>
@endsection
