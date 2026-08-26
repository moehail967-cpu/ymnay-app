@extends(include_theme_path('user.dashboard.user-master'))

@section('title') {{ __('Package Orders') }} @endsection
@section('page-title') {{ __('Package Orders') }} @endsection

@section('dashboard-content')

{!! theme_error_msg() !!}
{!! theme_flash_msg() !!}

<div class="tr-dash-card">
    <div class="tr-dash-card-header">
        <span><i class="mdi mdi-cube-outline" style="margin-right:6px;"></i>{{ __('My Package Orders') }}</span>
    </div>
    <div class="tr-dash-card-body">
        @if(isset($package_orders) && (is_array($package_orders) ? count($package_orders) : $package_orders->count()))

        @foreach($package_orders as $item)
        @php
            $pStyle = match($item->payment_status) {
                'complete' => 'color:var(--tr-olive);border:1px solid var(--tr-olive);',
                'pending'  => 'color:#9a7a30;border:1px solid #c9a84c;',
                default    => 'color:var(--tr-terra);border:1px solid var(--tr-terra);'
            };
        @endphp
        <div style="background:var(--tr-cream);border:1px solid var(--tr-border);border-radius:var(--tr-radius);padding:20px;margin-bottom:16px;">

            {{-- Header row --}}
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:14px;">
                <div>
                    <div style="font-size:16px;font-weight:800;color:var(--tr-bark);">
                        <i class="mdi mdi-cube-scan" style="color:var(--tr-olive);margin-right:6px;"></i>
                        {{ $item->package?->title ?? __('Package') }}
                    </div>
                    <div style="font-size:12px;color:var(--tr-stone);margin-top:4px;">
                        {{ __('Order #') }}{{ $item->id }} &middot; {{ $item->created_at->format('d M Y') }}
                    </div>
                </div>
                <span class="tr-dash-badge" style="{{ $pStyle }}background:rgba(0,0,0,.03);">
                    {{ ucfirst($item->payment_status) }}
                </span>
            </div>

            {{-- Date info --}}
            @if($item->payment_status === 'complete')
            <div style="display:flex;gap:24px;flex-wrap:wrap;margin-bottom:14px;">
                @if($item->start_date ?? null)
                <div style="font-size:12px;color:var(--tr-stone);">
                    <i class="mdi mdi-calendar-start" style="color:var(--tr-olive);margin-right:4px;"></i>
                    {{ __('Started:') }} <strong style="color:var(--tr-bark);">{{ \Carbon\Carbon::parse($item->start_date)->format('d M Y') }}</strong>
                </div>
                @endif
                @if($item->expire_date ?? null)
                <div style="font-size:12px;color:var(--tr-stone);">
                    <i class="mdi mdi-calendar-end" style="color:var(--tr-terra);margin-right:4px;"></i>
                    {{ __('Expires:') }} <strong style="color:var(--tr-bark);">{{ \Carbon\Carbon::parse($item->expire_date)->format('d M Y') }}</strong>
                </div>
                @endif
            </div>
            @endif

            {{-- Footer: price + actions --}}
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;padding-top:14px;border-top:1px solid var(--tr-border);">
                <span style="font-size:22px;font-weight:800;color:var(--tr-olive);">
                    {{ amount_with_currency_symbol($item->price ?? 0) }}
                </span>
                <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
                    @if($item->payment_status === 'pending')
                    <a href="{{ theme_package_order_confirm_url($item->id) }}"
                       class="tr-btn tr-btn-primary tr-btn-sm">
                        <i class="mdi mdi-credit-card-outline"></i> {{ __('Pay Now') }}
                    </a>
                    @endif

                    @if(in_array($item->payment_status, ['pending', 'failed']))
                    <a href="{{ theme_user_cancel_order_url($item->id) }}"
                       class="tr-btn tr-btn-sm tr-pkg-cancel"
                       data-route="{{ theme_user_cancel_order_url($item->id) }}"
                       style="background:rgba(194,91,42,.1);color:var(--tr-terra);border:1px solid var(--tr-terra);">
                        <i class="mdi mdi-close-circle-outline"></i> {{ __('Cancel') }}
                    </a>
                    @endif

                    @if($item->payment_status === 'complete')
                    <a href="{{ theme_package_invoice_url() }}"
                       target="_blank"
                       class="tr-btn tr-btn-outline tr-btn-sm">
                        <i class="mdi mdi-file-pdf-box"></i> {{ __('Invoice') }}
                    </a>
                    @endif
                </div>
            </div>

        </div>
        @endforeach

        @else
        <div style="padding:60px;text-align:center;color:var(--tr-stone);">
            <i class="mdi mdi-cube-off-outline" style="font-size:56px;display:block;margin-bottom:16px;color:var(--tr-border);"></i>
            <p style="font-size:16px;margin-bottom:16px;">{{ __('No package orders yet') }}</p>
        </div>
        @endif
    </div>
</div>

@endsection

@section('dashboard-scripts')
<script>
$(document).on('click', '.tr-pkg-cancel', function (e) {
    e.preventDefault();
    var url = $(this).data('route');
    if (!confirm('{{ __("Are you sure you want to cancel this package order?") }}')) return;
    window.location.href = url;
});
</script>
@endsection
