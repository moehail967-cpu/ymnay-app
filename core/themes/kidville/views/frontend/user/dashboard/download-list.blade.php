@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Downloads') }} @endsection

@section('section')

<div style="font-size:13px;font-weight:800;color:var(--kv-red);margin-bottom:20px;display:flex;align-items:center;gap:8px;">
    <i class="las la-download"></i> {{ __('My Downloads') }}
</div>

@if($download_list->isNotEmpty())
<div style="background:var(--kv-white);border:2px solid var(--kv-border);border-radius:var(--kv-radius);overflow:hidden;box-shadow:var(--kv-shadow);">
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="background:var(--kv-light);border-bottom:2px solid var(--kv-border);">
                    <th style="padding:12px 16px;text-align:left;color:var(--kv-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('Product') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--kv-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('Date') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--kv-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('Downloads') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--kv-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($download_list as $dl)
                @php $prod = $dl->product ?? null; @endphp
                <tr style="border-bottom:1px solid var(--kv-border);">
                    <td style="padding:14px 16px;color:var(--kv-dark);font-weight:700;">
                        {{ $prod?->name ?? __('Product Unavailable') }}
                    </td>
                    <td style="padding:14px 16px;color:var(--kv-muted);">{{ $dl->created_at->format('d M Y') }}</td>
                    <td style="padding:14px 16px;color:var(--kv-muted);">{{ $dl->download_count ?? 0 }}</td>
                    <td style="padding:14px 16px;">
                        @if($prod)
                        <a href="{{ theme_user_download_url($prod->slug) }}" class="kv-btn kv-btn-red kv-btn-sm">
                            <i class="las la-download"></i> {{ __('Download') }}
                        </a>
                        @else
                        <span style="color:var(--kv-muted);font-size:12px;">{{ __('Unavailable') }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div style="margin-top:16px;">{{ $download_list->links() }}</div>

@else
<div style="background:var(--kv-white);border:2px solid var(--kv-border);border-radius:var(--kv-radius);padding:48px;text-align:center;box-shadow:var(--kv-shadow);">
    <i class="las la-download" style="font-size:52px;color:var(--kv-muted);display:block;margin-bottom:12px;"></i>
    <p style="color:var(--kv-muted);font-size:14px;margin:0;font-weight:600;">{{ __('No downloads available yet') }}</p>
</div>
@endif

@endsection
