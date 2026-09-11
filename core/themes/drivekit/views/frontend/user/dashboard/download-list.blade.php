@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Downloads') }} @endsection

@section('section')

<div style="font-weight:800;color:var(--dk-white);margin-bottom:20px;font-size:15px;text-transform:uppercase;letter-spacing:.5px;">
    <i class="mdi mdi-download-outline" style="color:var(--dk-red);"></i> {{ __('My Downloads') }}
</div>

@if($download_list->isNotEmpty())
<div style="background:var(--dk-surface);border:1px solid var(--dk-border);border-radius:var(--dk-radius);overflow:hidden;">
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="background:var(--dk-panel);border-bottom:1px solid var(--dk-border);">
                    <th style="padding:12px 16px;text-align:left;color:var(--dk-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('Product') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--dk-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('Date') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--dk-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('Downloads') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--dk-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($download_list as $dl)
                @php $prod = $dl->product ?? null; @endphp
                <tr style="border-bottom:1px solid var(--dk-border);">
                    <td style="padding:14px 16px;color:var(--dk-white);font-weight:600;">
                        {{ $prod?->name ?? __('Product Unavailable') }}
                    </td>
                    <td style="padding:14px 16px;color:var(--dk-silver);">{{ $dl->created_at->format('d M Y') }}</td>
                    <td style="padding:14px 16px;color:var(--dk-silver);">{{ $dl->download_count ?? 0 }}</td>
                    <td style="padding:14px 16px;">
                        @if($prod)
                        <a href="{{ theme_user_download_url($prod->slug) }}" class="dk-btn dk-btn-red dk-btn-sm">
                            <i class="mdi mdi-download"></i> {{ __('Download') }}
                        </a>
                        @else
                        <span style="color:var(--dk-muted);font-size:12px;">{{ __('Unavailable') }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="dk-pagination mt-4">
    {{ $download_list->links() }}
</div>

@else
<div style="background:var(--dk-surface);border:1px solid var(--dk-border);border-radius:var(--dk-radius);padding:48px;text-align:center;">
    <i class="mdi mdi-download-off-outline" style="font-size:48px;color:var(--dk-border);display:block;margin-bottom:12px;"></i>
    <p style="color:var(--dk-silver);">{{ __('No downloads available yet') }}</p>
</div>
@endif

@endsection
