@extends(theme_path('frontend.user.dashboard.user-master'))

@section('title') {{ __('Downloads') }} @endsection
@section('page-title') {{ __('Downloads') }} @endsection

@section('dashboard-content')
<div class="vl-dash-card">
    <div class="vl-dash-card-header">{{ __('My Downloads') }}</div>
    <div class="vl-dash-card-body" style="padding:0;">
        @if(isset($orders) && $orders->count())
        <div class="table-responsive">
            <table class="vl-dash-table">
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
                            <td style="color:var(--vl-ivory);">{{ $item->name ?? $item?->product?->name }}</td>
                            <td style="color:var(--vl-champagne);">#{{ $order->id }}</td>
                            <td style="color:var(--vl-muted);">{{ $item->download_count ?? 0 }}</td>
                            <td>
                                <a href="{{ theme_user_download_url($item->id) }}"
                                   class="vl-btn vl-btn-outline" style="padding:6px 14px;font-size:9px;letter-spacing:1px;">
                                    <i class="mdi mdi-cloud-download-outline"></i> {{ __('Download') }}
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
        <div style="padding:64px;text-align:center;color:var(--vl-muted);">
            <i class="mdi mdi-cloud-download-outline" style="font-size:56px;display:block;margin-bottom:16px;color:var(--vl-border);"></i>
            <div style="font-size:10px;letter-spacing:3px;text-transform:uppercase;">{{ __('No downloads yet') }}</div>
        </div>
        @endif
    </div>
</div>
@endsection
