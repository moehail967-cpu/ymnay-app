@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Downloads') }} @endsection

@section('section')

<div class="bk-dash-section-title">
    <i class="mdi mdi-download-outline" style="color:var(--bk-rose);"></i> {{ __('My Downloads') }}
</div>

@if($download_list->isNotEmpty())
<div class="bk-dash-box">
    <div style="overflow-x:auto;">
        <table class="bk-dash-table">
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
                    <td style="color:var(--bk-muted);">{{ $dl->created_at->format('d M Y') }}</td>
                    <td style="color:var(--bk-muted);">{{ $dl->download_count ?? 0 }}</td>
                    <td>
                        @if($prod)
                        <a href="{{ theme_user_download_url($prod->slug) }}" class="bk-btn bk-btn-rose bk-btn-sm">
                            <i class="mdi mdi-download"></i> {{ __('Download') }}
                        </a>
                        @else
                        <span style="color:var(--bk-muted);font-size:12px;">{{ __('Unavailable') }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $download_list->links() }}</div>

@else
<div class="bk-dash-box" style="padding:56px;text-align:center;">
    <i class="mdi mdi-download-off-outline" style="font-size:52px;color:var(--bk-border);display:block;margin-bottom:14px;"></i>
    <p style="color:var(--bk-muted);">{{ __('No downloads available yet.') }}</p>
</div>
@endif

@endsection
