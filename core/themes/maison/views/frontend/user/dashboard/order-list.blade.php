@extends(include_theme_path('user.user-master'))

@section('title') {{ __('My Orders') }} @endsection

@section('dashboard_content')

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:20px;">
    <div class="ms-dash-section-title" style="margin:0;">
        <i class="mdi mdi-package-variant-closed" style="margin-right:6px;color:var(--ms-olive);"></i>
        {{ __('My Orders') }}
    </div>
</div>

@if($order_list->isNotEmpty())
<div class="ms-dash-card" style="padding:0;overflow:hidden;">
    <div style="overflow-x:auto;">
        <table class="ms-table" style="width:100%;">
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
                    $os_class = match(true){ in_array($os,['complete','success'])=>'ms-badge-success', $os==='cancel'=>'ms-badge-muted', default=>'ms-badge-warning' };
                    $ps_class = match(true){ in_array($ps,['complete','success'])=>'ms-badge-success', $ps==='cancel'=>'ms-badge-muted', default=>'ms-badge-warning' };
                @endphp
                <tr>
                    <td><strong>#{{ $order->id }}</strong></td>
                    <td>{{ $order->created_at->format('d M Y') }}</td>
                    <td><span class="ms-badge {{ $os_class }}">{{ __($os) }}</span></td>
                    <td><span class="ms-badge {{ $ps_class }}">{{ __($ps) }}</span></td>
                    <td>{{ amount_with_currency_symbol($order->total_amount) }}</td>
                    <td>
                        <div style="display:flex;gap:8px;flex-wrap:wrap;">
                            <a href="{{ theme_user_order_detail_url($order->id) }}"
                               style="display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:600;color:var(--ms-linen-d);text-decoration:none;padding:5px 12px;border:1px solid var(--ms-border);border-radius:var(--ms-radius);transition:all .2s;"
                               onmouseover="this.style.borderColor='var(--ms-linen)'"
                               onmouseout="this.style.borderColor='var(--ms-border)'">
                                <i class="mdi mdi-eye-outline"></i> {{ __('View') }}
                            </a>
                            @if($order->status === 'pending')
                            <form class="order-cancel-form" action="{{ theme_user_cancel_order_url() }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                <button type="submit" class="cancel-btn"
                                        style="display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:600;color:#c0725a;text-decoration:none;padding:5px 12px;border:1px solid rgba(192,114,90,.3);border-radius:var(--ms-radius);transition:all .2s;background:transparent;cursor:pointer;"
                                        onmouseover="this.style.borderColor='#c0725a'"
                                        onmouseout="this.style.borderColor='rgba(192,114,90,.3)'">
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
<div class="ms-dash-card" style="text-align:center;padding:56px 20px;">
    <i class="mdi mdi-package-variant" style="font-size:48px;color:var(--ms-border);display:block;margin-bottom:14px;"></i>
    <p style="color:var(--ms-muted);font-size:14px;margin-bottom:20px;">{{ __('No orders found') }}</p>
    <a href="{{ theme_shop_url() }}" class="ms-btn-dark">{{ __('Start Shopping') }}</a>
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
        text: '{{ __("This action cannot be undone.") }}',
        showCancelButton: true,
        confirmButtonText: '{{ __("Yes, Cancel") }}',
        confirmButtonColor: '#c0725a',
    }).then(function(res){ if(res.isConfirmed) form.submit(); });
});
</script>
@endsection
