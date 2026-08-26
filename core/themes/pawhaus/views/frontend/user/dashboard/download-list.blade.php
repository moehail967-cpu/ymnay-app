@extends(include_theme_path('user.user-master'))

@section('title') {{ __('Downloads') }} @endsection
@section('dash-title') {{ __('Downloads') }} @endsection

@section('dashboard-content')
<div class="ph-dash-card">
    <div class="ph-dash-card-title"><i class="las la-download"></i> {{ __('My Downloads') }}</div>
    @if(($downloads ?? collect())->isNotEmpty())
    <div class="table-responsive">
        <table class="ph-dash-table">
            <thead><tr>
                <th>{{ __('Product') }}</th>
                <th>{{ __('Order') }}</th>
                <th>{{ __('Date') }}</th>
                <th></th>
            </tr></thead>
            <tbody>
            @foreach($downloads as $dl)
            <tr>
                <td><strong>{{ $dl->product?->name ?? '—' }}</strong></td>
                <td>#{{ $dl->order_id ?? '—' }}</td>
                <td>{{ $dl->created_at?->format('d M Y') }}</td>
                <td>
                    @if($dl->download_url ?? false)
                    <a href="{{ $dl->download_url }}" class="ph-btn ph-btn-terra ph-btn-sm" target="_blank">
                        <i class="las la-download"></i> {{ __('Download') }}
                    </a>
                    @else
                    <span class="ph-badge-pill ph-badge-muted">{{ __('Unavailable') }}</span>
                    @endif
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div style="text-align:center;padding:48px;">
        <i class="las la-download" style="font-size:48px;color:var(--ph-border);display:block;margin-bottom:12px;"></i>
        <p style="color:var(--ph-muted);font-size:14px;">{{ __('No downloads available.') }}</p>
    </div>
    @endif
</div>
@endsection
