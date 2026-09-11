@extends(theme_path('frontend.user.dashboard.user-master'))

@section('title') {{ __('Downloads') }} @endsection
@section('page-title') {{ __('Downloads') }} @endsection

@section('dashboard-content')
<div class="fp-dash-card">
    <div class="fp-dash-card-header">{{ __('My Downloads') }}</div>
    <div class="fp-dash-card-body" style="padding:0;">
        @if(isset($orders) && $orders->count())
        <div class="table-responsive">
            <table class="fp-dash-table">
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
                            <td style="font-family:var(--fp-font-head);color:var(--fp-text);">{{ $item->name ?? $item?->product?->name }}</td>
                            <td style="color:var(--fp-green);font-family:var(--fp-font-head);">#{{ $order->id }}</td>
                            <td>{{ $item->download_count ?? 0 }}</td>
                            <td>
                                <a href="{{ theme_user_download_url($item->slug ?? $item->id) }}"
                                   class="fp-btn fp-btn-outline fp-btn-sm">
                                    <i class="mdi mdi-download"></i> {{ __('Download') }}
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
            <div style="padding:60px;text-align:center;color:var(--fp-muted);">
                <i class="mdi mdi-download-off" style="font-size:64px;color:var(--fp-border);display:block;margin-bottom:16px;"></i>
                <p style="font-family:var(--fp-font-head);font-size:18px;text-transform:uppercase;letter-spacing:1px;">{{ __('No downloads yet') }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
