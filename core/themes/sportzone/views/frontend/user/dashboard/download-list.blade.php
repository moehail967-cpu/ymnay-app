@extends(include_theme_path('user.dashboard.user-master'))

@section('title') {{ __('My Downloads') }} @endsection

@section('dashboard_content')

<div class="sz-dash-section-title">
    <i class="mdi mdi-download-outline" style="margin-right:8px;color:var(--sz-red);"></i>
    {{ __('My Downloads') }}
</div>

@if(isset($download_list) && $download_list->isNotEmpty())
<div class="sz-dash-card" style="padding:0;overflow:hidden;">
    <div style="overflow-x:auto;">
        <table class="sz-dash-table">
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
                    <td>
                        <strong style="color:var(--sz-dark);">{{ $prod?->name ?? __('Product Unavailable') }}</strong>
                    </td>
                    <td style="color:var(--sz-muted);">{{ $dl->created_at->format('d M Y') }}</td>
                    <td style="color:var(--sz-muted);">{{ $dl->download_count ?? 0 }}</td>
                    <td>
                        @if($prod)
                        <a href="{{ theme_user_download_url($prod->slug) }}"
                           class="sz-dash-btn sz-dash-btn-red"
                           style="padding:6px 16px;font-size:11px;">
                            <i class="mdi mdi-download"></i> {{ __('Download') }}
                        </a>
                        @else
                        <span style="font-size:12px;color:var(--sz-muted);">{{ __('Unavailable') }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top:20px;">
    {{ $download_list->links() }}
</div>

@else
<div class="sz-dash-card" style="text-align:center;padding:60px 20px;">
    <i class="mdi mdi-download-off-outline" style="font-size:56px;color:var(--sz-border);display:block;margin-bottom:14px;"></i>
    <p style="color:var(--sz-muted);font-size:14px;font-family:var(--sz-font-body);margin-bottom:20px;">{{ __('No downloads available yet') }}</p>
    <a href="{{ theme_shop_url() }}" class="sz-dash-btn sz-dash-btn-red">
        <i class="mdi mdi-storefront-outline"></i> {{ __('Browse Products') }}
    </a>
</div>
@endif

@endsection
