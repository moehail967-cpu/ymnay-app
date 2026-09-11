@extends(include_theme_path('user.dashboard.user-master'))

@section('title') {{ __('My Downloads') }} @endsection

@section('dashboard_content')

<div style="font-size:18px;font-weight:700;color:#fff;margin-bottom:20px;display:flex;align-items:center;gap:8px;">
    <i class="mdi mdi-download-outline" style="color:var(--tz-blue);"></i> {{ __('My Downloads') }}
</div>

@if(isset($download_list) && $download_list->isNotEmpty())
<div class="tz-dash-card" style="padding:0;overflow:hidden;">
    <div style="overflow-x:auto;">
        <table class="tz-dash-table">
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
                    <td><strong style="color:#fff;">{{ $prod?->name ?? __('Product Unavailable') }}</strong></td>
                    <td>{{ $dl->created_at->format('d M Y') }}</td>
                    <td>{{ $dl->download_count ?? 0 }}</td>
                    <td>
                        @if($prod)
                        <a href="{{ theme_user_download_url($prod->slug) }}"
                           style="display:inline-flex;align-items:center;gap:4px;background:var(--tz-blue);color:#fff;padding:5px 12px;border-radius:var(--tz-radius-sm);font-size:12px;font-weight:600;text-decoration:none;font-family:var(--tz-font);transition:background .2s;"
                           onmouseover="this.style.background='var(--tz-blue-deep)'" onmouseout="this.style.background='var(--tz-blue)'">
                            <i class="mdi mdi-download"></i> {{ __('Download') }}
                        </a>
                        @else
                        <span style="font-size:12px;color:var(--tz-muted);">{{ __('Unavailable') }}</span>
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
<div class="tz-dash-card tz-dash-card-body" style="text-align:center;padding:60px 20px;">
    <i class="mdi mdi-download-off-outline" style="font-size:52px;color:var(--tz-border);display:block;margin-bottom:12px;"></i>
    <p style="color:var(--tz-muted);font-size:14px;margin-bottom:20px;">{{ __('No downloads available yet') }}</p>
    <a href="{{ theme_shop_url() }}"
       style="display:inline-flex;align-items:center;gap:8px;background:var(--tz-blue);color:#fff;padding:10px 20px;border-radius:var(--tz-radius-sm);font-size:13px;font-weight:700;text-decoration:none;font-family:var(--tz-font);">
        <i class="mdi mdi-storefront-outline"></i> {{ __('Browse Products') }}
    </a>
</div>
@endif

@endsection
