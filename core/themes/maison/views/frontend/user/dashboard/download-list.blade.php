@extends(include_theme_path('user.user-master'))

@section('title') {{ __('My Downloads') }} @endsection

@section('dashboard_content')

<div class="ms-dash-section-title">
    <i class="mdi mdi-download-outline" style="margin-right:6px;color:var(--ms-olive);"></i>
    {{ __('My Downloads') }}
</div>

@if($download_list->isNotEmpty())
<div class="ms-dash-card" style="padding:0;overflow:hidden;">
    <div style="overflow-x:auto;">
        <table class="ms-table" style="width:100%;">
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
                        <strong style="color:var(--ms-dark);">{{ $prod?->name ?? __('Product Unavailable') }}</strong>
                    </td>
                    <td>{{ $dl->created_at->format('d M Y') }}</td>
                    <td>{{ $dl->download_count ?? 0 }}</td>
                    <td>
                        @if($prod)
                        <a href="{{ theme_user_download_url($prod->slug) }}"
                           style="display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:600;padding:5px 14px;background:var(--ms-dark);color:#fff;border-radius:var(--ms-radius);text-decoration:none;transition:background .2s;"
                           onmouseover="this.style.background='var(--ms-charcoal)'"
                           onmouseout="this.style.background='var(--ms-dark)'">
                            <i class="mdi mdi-download"></i> {{ __('Download') }}
                        </a>
                        @else
                        <span style="font-size:12px;color:var(--ms-muted);">{{ __('Unavailable') }}</span>
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
<div class="ms-dash-card" style="text-align:center;padding:56px 20px;">
    <i class="mdi mdi-download-off-outline" style="font-size:48px;color:var(--ms-border);display:block;margin-bottom:14px;"></i>
    <p style="color:var(--ms-muted);font-size:14px;">{{ __('No downloads available yet') }}</p>
    <a href="{{ theme_digital_shop_url() }}" class="ms-btn-dark" style="margin-top:12px;display:inline-flex;">
        {{ __('Browse Digital Products') }}
    </a>
</div>
@endif

@endsection
