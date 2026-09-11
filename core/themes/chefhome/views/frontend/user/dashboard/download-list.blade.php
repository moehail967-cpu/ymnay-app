@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Downloads') }} @endsection

@section('section')

<div style="font-weight:800;color:var(--ch-dark);margin-bottom:20px;font-size:15px;">
    <i class="las la-download" style="color:var(--ch-red);"></i> {{ __('My Downloads') }}
</div>

@if($download_list->isNotEmpty())
<div class="ch-dash-card">
    <div style="overflow-x:auto;">
        <table class="ch-dash-table">
            <thead>
                <tr>
                    <th>{{ __('Product') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Downloads') }}</th>
                    <th>{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($download_list as $dl)
                @php $prod = $dl->product ?? null; @endphp
                <tr>
                    <td style="font-weight:600;">{{ $prod?->name ?? __('Product Unavailable') }}</td>
                    <td style="color:var(--ch-muted);">{{ $dl->created_at->format('d M Y') }}</td>
                    <td style="color:var(--ch-muted);">{{ $dl->download_count ?? 0 }}</td>
                    <td>
                        @if($prod)
                        <a href="{{ theme_user_download_url($prod->slug) }}" class="ch-btn ch-btn-primary ch-btn-sm">
                            <i class="las la-download"></i> {{ __('Download') }}
                        </a>
                        @else
                        <span style="color:var(--ch-muted);font-size:12px;">{{ __('Unavailable') }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="ch-pagination mt-4">
    {{ $download_list->links() }}
</div>

@else
<div class="ch-dash-card">
    <div style="padding:48px;text-align:center;">
        <i class="las la-cloud-download-alt" style="font-size:48px;color:var(--ch-border);display:block;margin-bottom:12px;"></i>
        <p style="color:var(--ch-muted);">{{ __('No downloads available yet') }}</p>
    </div>
</div>
@endif

@endsection
