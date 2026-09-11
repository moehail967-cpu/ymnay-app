@extends(include_theme_path('user.dashboard.user-master'))

@section('title') {{ __('Downloads') }} @endsection
@section('page-title') {{ __('Downloads') }} @endsection

@section('dashboard-content')

{!! theme_error_msg() !!}
{!! theme_flash_msg() !!}

<div class="tr-dash-card">
    <div class="tr-dash-card-header">
        <span><i class="mdi mdi-download-circle-outline" style="margin-right:6px;"></i>{{ __('My Downloads') }}</span>
    </div>
    <div class="tr-dash-card-body" style="padding:0;">
        @if(isset($download_items) && (is_array($download_items) ? count($download_items) : $download_items->count()))
        <div class="table-responsive">
            <table class="tr-dash-table">
                <thead>
                    <tr>
                        <th>{{ __('Product') }}</th>
                        <th>{{ __('License Key') }}</th>
                        <th>{{ __('Expiry') }}</th>
                        <th>{{ __('Download') }}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($download_items as $item)
                <tr>
                    <td style="font-weight:600;color:var(--tr-bark);">
                        {{ $item->product_name ?? $item?->product?->name ?? __('Digital Product') }}
                    </td>
                    <td>
                        @if($item->license_key ?? null)
                        <code style="background:var(--tr-cream);padding:3px 8px;border-radius:4px;font-size:12px;color:var(--tr-stone);border:1px solid var(--tr-border);">
                            {{ $item->license_key }}
                        </code>
                        @else
                        <span style="color:var(--tr-stone);font-size:12px;">—</span>
                        @endif
                    </td>
                    <td style="color:var(--tr-stone);font-size:13px;">
                        @if($item->expire_date ?? null)
                            {{ \Carbon\Carbon::parse($item->expire_date)->format('d M Y') }}
                        @else
                            <span>{{ __('Lifetime') }}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ theme_user_download_url($item->id) }}"
                           class="tr-btn tr-btn-primary tr-btn-sm">
                            <i class="mdi mdi-download"></i> {{ __('Download') }}
                        </a>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="padding:60px;text-align:center;color:var(--tr-stone);">
            <i class="mdi mdi-cloud-download-outline" style="font-size:56px;display:block;margin-bottom:16px;color:var(--tr-border);"></i>
            <p style="font-size:16px;margin-bottom:16px;">{{ __('No downloads yet') }}</p>
            <a href="{{ theme_shop_url() }}" class="tr-btn tr-btn-primary">
                <i class="mdi mdi-storefront-outline"></i> {{ __('Browse Products') }}
            </a>
        </div>
        @endif
    </div>
</div>

@endsection
