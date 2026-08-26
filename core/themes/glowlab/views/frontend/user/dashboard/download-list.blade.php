@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Downloads') }} @endsection

@section('section')

<div style="font-weight:700;color:var(--gl-dark);margin-bottom:20px;font-size:15px;display:flex;align-items:center;gap:8px;">
    <i class="mdi mdi-download-outline" style="color:var(--gl-gold);"></i> {{ __('My Downloads') }}
</div>

@if($download_list->isNotEmpty())
<div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);overflow:hidden;box-shadow:var(--gl-shadow);">
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="background:var(--gl-gold-pale);border-bottom:1px solid var(--gl-border);">
                    <th style="padding:12px 16px;text-align:left;color:var(--gl-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('Product') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--gl-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('Date') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--gl-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('Downloads') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--gl-muted);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($download_list as $dl)
                @php $prod = $dl->product ?? null; @endphp
                <tr style="border-bottom:1px solid var(--gl-border);">
                    <td style="padding:14px 16px;color:var(--gl-dark);font-weight:600;">
                        {{ $prod?->name ?? __('Product Unavailable') }}
                    </td>
                    <td style="padding:14px 16px;color:var(--gl-muted);">{{ $dl->created_at->format('d M Y') }}</td>
                    <td style="padding:14px 16px;color:var(--gl-muted);">{{ $dl->download_count ?? 0 }}</td>
                    <td style="padding:14px 16px;">
                        @if($prod)
                        <a href="{{ theme_user_download_url($prod->slug) }}"
                           style="display:inline-flex;align-items:center;gap:4px;padding:6px 14px;background:var(--gl-dark);color:#fff;border-radius:var(--gl-radius);font-size:11px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;text-decoration:none;transition:background .2s;"
                           onmouseover="this.style.background='var(--gl-gold)'" onmouseout="this.style.background='var(--gl-dark)'">
                            <i class="mdi mdi-download"></i> {{ __('Download') }}
                        </a>
                        @else
                        <span style="color:var(--gl-muted);font-size:12px;">{{ __('Unavailable') }}</span>
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
<div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:48px;text-align:center;box-shadow:var(--gl-shadow);">
    <i class="mdi mdi-download-outline" style="font-size:48px;color:var(--gl-border);display:block;margin-bottom:12px;"></i>
    <p style="color:var(--gl-muted);font-size:14px;margin:0;">{{ __('No downloads available yet') }}</p>
</div>
@endif

@endsection
