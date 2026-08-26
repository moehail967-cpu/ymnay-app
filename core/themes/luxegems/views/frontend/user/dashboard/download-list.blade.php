@extends(include_theme_path('user.user-master'))
@section('dash-title') {{ __('Downloads') }} @endsection

@section('dash-content')
<div class="lg-dash-card">
    <div class="lg-dash-card-title">{{ __('My Downloads') }}</div>
    <div style="overflow-x:auto;">
        <table class="lg-dash-table">
            <thead>
                <tr>
                    <th>{{ __('Product') }}</th>
                    <th>{{ __('Order') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($downloads ?? [] as $dl)
                <tr>
                    <td>{{ $dl->product?->name ?? '—' }}</td>
                    <td>#{{ $dl->order_id }}</td>
                    <td>{{ $dl->created_at?->format('d M Y') }}</td>
                    <td>
                        <a href="{{ $dl->download_url ?? '#' }}" class="lg-dash-btn lg-dash-btn-gold" style="font-size:9px;padding:6px 14px;">
                            <i class="las la-download"></i> {{ __('Download') }}
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;padding:32px;color:var(--lx-muted);">{{ __('No downloads available.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
