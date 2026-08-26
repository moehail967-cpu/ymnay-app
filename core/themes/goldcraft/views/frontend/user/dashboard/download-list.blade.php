@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Downloads') }} @endsection

@section('section')

<div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gc-rose);margin-bottom:20px;display:flex;align-items:center;gap:8px;">
    <i class="las la-download"></i> {{ __('My Downloads') }}
</div>

@if($download_list->isNotEmpty())
<div style="background:var(--gc-ivory);border:1px solid var(--gc-border);border-radius:var(--gc-radius);overflow:hidden;box-shadow:var(--gc-shadow);">
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;font-family:Georgia,serif;">
            <thead>
                <tr style="background:var(--gc-warm);border-bottom:1px solid var(--gc-border);">
                    <th style="padding:12px 16px;text-align:left;color:var(--gc-muted);font-size:10px;font-weight:400;text-transform:uppercase;letter-spacing:1px;">{{ __('Product') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--gc-muted);font-size:10px;font-weight:400;text-transform:uppercase;letter-spacing:1px;">{{ __('Date') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--gc-muted);font-size:10px;font-weight:400;text-transform:uppercase;letter-spacing:1px;">{{ __('Downloads') }}</th>
                    <th style="padding:12px 16px;text-align:left;color:var(--gc-muted);font-size:10px;font-weight:400;text-transform:uppercase;letter-spacing:1px;">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($download_list as $dl)
                @php $prod = $dl->product ?? null; @endphp
                <tr style="border-bottom:1px solid var(--gc-border);">
                    <td style="padding:14px 16px;color:var(--gc-dark);font-style:italic;">
                        {{ $prod?->name ?? __('Product Unavailable') }}
                    </td>
                    <td style="padding:14px 16px;color:var(--gc-muted);font-style:italic;">{{ $dl->created_at->format('d M Y') }}</td>
                    <td style="padding:14px 16px;color:var(--gc-muted);font-style:italic;">{{ $dl->download_count ?? 0 }}</td>
                    <td style="padding:14px 16px;">
                        @if($prod)
                        <a href="{{ theme_user_download_url($prod->slug) }}" class="gc-btn gc-btn-primary" style="font-size:11px;padding:6px 14px;">
                            <i class="las la-download"></i> {{ __('Download') }}
                        </a>
                        @else
                        <span style="color:var(--gc-muted);font-size:12px;font-style:italic;">{{ __('Unavailable') }}</span>
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
<div style="background:var(--gc-ivory);border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:48px;text-align:center;box-shadow:var(--gc-shadow);">
    <div style="font-size:44px;margin-bottom:12px;">📥</div>
    <p style="color:var(--gc-muted);font-size:14px;margin:0;font-style:italic;">{{ __('No downloads available yet') }}</p>
</div>
@endif

@endsection
