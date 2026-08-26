@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Downloads') }} @endsection

@section('section')

<div class="fn-dash-section-title">
    <i class="las la-download"></i> {{ __('My Downloads') }}
</div>

@if($download_list->isNotEmpty())
<div class="fn-dash-box">
    <div style="overflow-x:auto;">
        <table class="fn-dash-table">
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
                    <td class="fn-fw-bold">{{ $prod?->name ?? __('Product Unavailable') }}</td>
                    <td class="fn-muted">{{ $dl->created_at->format('d M Y') }}</td>
                    <td class="fn-muted">{{ $dl->download_count ?? 0 }}</td>
                    <td>
                        @if($prod)
                        <a href="{{ theme_user_download_url($prod->slug) }}" class="fn-btn fn-btn-gold fn-btn-sm">
                            <i class="las la-download"></i> {{ __('Download') }}
                        </a>
                        @else
                        <span class="fn-muted" style="font-size:12px;">{{ __('Unavailable') }}</span>
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
<div class="fn-dash-box fn-dash-empty">
    <i class="las la-file-download"></i>
    <p>{{ __('No downloads available yet.') }}</p>
</div>
@endif

@endsection
