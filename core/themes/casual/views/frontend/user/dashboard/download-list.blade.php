@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Downloads') }} @endsection

@section('section')

<div class="cs-dash-section-head">
    <div class="cs-dash-section-title">
        <i class="las la-download"></i> {{ __('My Downloads') }}
    </div>
</div>

@if($download_list->isNotEmpty())
<div class="cs-dash-box">
    <div class="cs-dash-table-wrap">
        <table class="cs-dash-table">
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
                    <td class="cs-dash-td-bold">{{ $prod?->name ?? __('Product Unavailable') }}</td>
                    <td class="cs-dash-td-muted">{{ $dl->created_at->format('d M Y') }}</td>
                    <td class="cs-dash-td-muted">{{ $dl->download_count ?? 0 }}</td>
                    <td>
                        @if($prod)
                        <a href="{{ theme_user_download_url($prod->slug) }}" class="cs-dash-action-btn cs-dash-action-primary">
                            <i class="las la-download"></i> {{ __('Download') }}
                        </a>
                        @else
                        <span class="cs-dash-td-muted">{{ __('Unavailable') }}</span>
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
<div class="cs-dash-empty">
    <i class="las la-cloud-download-alt cs-dash-empty-icon"></i>
    <p class="cs-dash-empty-text">{{ __('No downloads available yet.') }}</p>
</div>
@endif

@endsection
