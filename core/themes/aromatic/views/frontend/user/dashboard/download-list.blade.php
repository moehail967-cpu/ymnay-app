@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Downloads') }} @endsection

@section('section')

<div class="ar-dash-section-title">
    <i class="mdi mdi-download-outline" style="color:var(--ar-red);"></i> {{ __('My Downloads') }}
</div>

@if($download_list->isNotEmpty())
<div class="ar-dash-box">
    <div style="overflow-x:auto;">
        <table class="ar-dash-table">
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
                    <td style="color:var(--ar-muted);">{{ $dl->created_at->format('d M Y') }}</td>
                    <td style="color:var(--ar-muted);">{{ $dl->download_count ?? 0 }}</td>
                    <td>
                        @if($prod)
                        <a href="{{ theme_user_download_url($prod->slug) }}" class="ar-btn ar-btn-red ar-btn-sm">
                            <i class="mdi mdi-download"></i> {{ __('Download') }}
                        </a>
                        @else
                        <span style="color:var(--ar-muted);font-size:12px;">{{ __('Unavailable') }}</span>
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
<div class="ar-dash-box" style="padding:56px;text-align:center;">
    <i class="mdi mdi-download-off-outline" style="font-size:52px;color:var(--ar-border);display:block;margin-bottom:14px;"></i>
    <p style="color:var(--ar-muted);">{{ __('No downloads available yet.') }}</p>
</div>
@endif

@endsection
