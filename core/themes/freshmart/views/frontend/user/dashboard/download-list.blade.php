@extends(theme_path('frontend.user.dashboard.user-master'))

@section('title') {{ __('Downloads') }} @endsection
@section('page-title') {{ __('Downloads') }} @endsection

@section('dashboard-content')
<div class="fm-dash-card">
    <div class="fm-dash-card-header">{{ __('My Downloads') }}</div>
    <div class="fm-dash-card-body" style="padding:0;">
        @if(isset($orders) && $orders->count())
        <div class="table-responsive">
            <table class="fm-dash-table">
                <thead>
                    <tr>
                        <th>{{ __('Product') }}</th>
                        <th>{{ __('Order #') }}</th>
                        <th>{{ __('Downloads') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach($orders as $order)
                    @foreach($order->products ?? [] as $item)
                        @if($item?->product?->file)
                        <tr>
                            <td style="font-weight:600;color:var(--fm-dark);">{{ $item->name ?? $item?->product?->name }}</td>
                            <td style="color:var(--fm-green);font-weight:700;">#{{ $order->id }}</td>
                            <td>{{ $item->download_count ?? 0 }}</td>
                            <td>
                                <a href="{{ theme_user_download_url($item->id) }}"
                                   class="fm-btn fm-btn-outline fm-btn-sm">
                                    <i class="las la-download"></i> {{ __('Download') }}
                                </a>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="padding:60px;text-align:center;color:var(--fm-muted);">
            <i class="las la-cloud-download-alt" style="font-size:56px;display:block;margin-bottom:16px;"></i>
            <p style="font-size:16px;">{{ __('No downloads yet') }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
