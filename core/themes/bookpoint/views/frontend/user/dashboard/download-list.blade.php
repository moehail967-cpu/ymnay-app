@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Downloads') }} @endsection

@section('section')

<div class="bp-dash-section-title">
    <i class="las la-download"></i> {{ __('My Downloads') }}
</div>

@if($download_list->isNotEmpty())
<div class="bp-dash-box">
    <div style="overflow-x:auto;">
        <table class="bp-dash-table">
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
                    <td class="bp-fw-bold">{{ $prod?->name ?? __('Product Unavailable') }}</td>
                    <td class="bp-muted">{{ $dl->created_at->format('d M Y') }}</td>
                    <td class="bp-muted">{{ $dl->download_count ?? 0 }}</td>
                    <td>
                        @if($prod)
                        <a href="{{ theme_user_download_url($prod->slug) }}" class="bp-btn bp-btn-green bp-btn-sm">
                            <i class="las la-download"></i> {{ __('Download') }}
                        </a>
                        @else
                        <span class="bp-muted" style="font-size:12px;">{{ __('Unavailable') }}</span>
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
<div class="bp-dash-box bp-dash-empty">
    <i class="las la-file-download"></i>
    <p>{{ __('No downloads available yet.') }}</p>
</div>
@endif

@endsection
