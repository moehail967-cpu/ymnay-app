@extends('tenant.admin.admin-master')

@section('title') {{ __('Product Feeds — Settings') }} @endsection
@section('page-title') {{ __('Product Feeds — Settings') }} @endsection

@section('content')
<div class="col-12">

    {{-- Setup Guide --}}
    <div class="card mb-4 border-primary border-opacity-25" x-data="{ open: false }">
        <div class="card-header d-flex align-items-center gap-2 cursor-pointer bg-primary bg-opacity-10" @click="open = !open">
            <i class="mdi mdi-book-open-variant text-primary"></i>
            <strong class="text-primary">{{ __('Setup Guide') }}</strong>
            <span class="text-muted small ms-2">{{ __('Click to expand') }}</span>
            <i class="mdi ms-auto text-primary" :class="open ? 'mdi-chevron-up' : 'mdi-chevron-down'"></i>
        </div>
        <div class="card-body" x-show="open" x-transition>
            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="fw-bold"><i class="mdi mdi-google text-primary me-1"></i> Google Merchant Center</h6>
                    <ol class="small text-muted ps-3 mb-0">
                        <li>Go to <strong>merchants.google.com</strong> → Products → Feeds</li>
                        <li>Click <strong>+</strong> to add a new feed → choose <em>Scheduled fetch</em></li>
                        <li>Set the fetch URL to your <strong>Google Feed URL</strong> (copy from Feed URLs page)</li>
                        <li>Set the fetch frequency to <em>Daily</em></li>
                        <li>Google will crawl the URL automatically on schedule</li>
                    </ol>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold"><i class="mdi mdi-facebook me-1" style="color:#1877f2"></i> Facebook / Meta Product Catalog</h6>
                    <ol class="small text-muted ps-3 mb-0">
                        <li>Go to <strong>business.facebook.com</strong> → Commerce Manager</li>
                        <li>Open your catalog → Data Sources → Add Items → Use Data Feed</li>
                        <li>Choose <em>Scheduled feed</em> and paste your <strong>Facebook Feed URL</strong></li>
                        <li>Set schedule to <em>Daily</em> and save</li>
                        <li>Facebook will crawl and sync your products automatically</li>
                    </ol>
                </div>
                <div class="col-12">
                    <div class="alert alert-info mb-0 py-2 small">
                        <i class="mdi mdi-information-outline me-1"></i>
                        <strong>{{ __('Feed URLs are secret.') }}</strong> {{ __('Each store gets a unique token in its URL — do not share it publicly. Go to') }} <a href="{{ route('tenant.admin.feed-sync.urls') }}">{{ __('Feed URLs') }}</a> {{ __('to copy your URLs.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible mb-4">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form method="POST" action="{{ route('tenant.admin.feed-sync.settings.save') }}">
        @csrf

        {{-- ── Google Merchant Center ──────────────────────── --}}
        <div class="card mb-4" x-data="{ open: true }">
            <div class="card-header d-flex align-items-center gap-2 cursor-pointer" @click="open = !open">
                <i class="mdi mdi-google fs-5 text-primary"></i>
                <strong>{{ __('Google Merchant Center') }}</strong>
                <i class="mdi ms-auto" :class="open ? 'mdi-chevron-up' : 'mdi-chevron-down'"></i>
            </div>
            <div class="card-body" x-show="open" x-transition>
                <div class="row g-3">
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="google_merchant_enabled" id="google_merchant_enabled" {{ ($options['google_merchant_enabled'] ?? '0') === '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="google_merchant_enabled">{{ __('Enable Google Merchant Feed') }}</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('Shipping Country') }}</label>
                        <input type="text" class="form-control" name="google_shipping_country" value="{{ $options['google_shipping_country'] ?? 'US' }}" placeholder="{{ __('US') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('Shipping Price') }}</label>
                        <input type="number" class="form-control" step="0.01" min="0" name="google_shipping_price" value="{{ $options['google_shipping_price'] ?? '0.00' }}">
                        <div class="form-text">{{ __('Use 0 for free shipping') }}</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('Product Condition') }}</label>
                        <select class="form-select" name="google_condition">
                            @foreach(['new'=>'New','used'=>'Used','refurbished'=>'Refurbished'] as $val => $label)
                            <option value="{{ $val }}" {{ ($options['google_condition'] ?? 'new') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Brand Source') }}</label>
                        <select class="form-select" name="google_brand_field" id="google_brand_field">
                            @foreach(['product_brand'=>'Product Brand','store_name'=>'Store Name','custom'=>'Custom Value'] as $val => $label)
                            <option value="{{ $val }}" {{ ($options['google_brand_field'] ?? 'product_brand') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6" id="custom-brand-wrap" style="{{ ($options['google_brand_field'] ?? '') !== 'custom' ? 'display:none' : '' }}">
                        <label class="form-label">{{ __('Custom Brand Value') }}</label>
                        <input type="text" class="form-control" name="google_custom_brand" value="{{ $options['google_custom_brand'] ?? '' }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Facebook Catalog ────────────────────────────── --}}
        <div class="card mb-4" x-data="{ open: true }">
            <div class="card-header d-flex align-items-center gap-2 cursor-pointer" @click="open = !open">
                <i class="mdi mdi-facebook fs-5" style="color:#1877f2"></i>
                <strong>{{ __('Facebook / Meta Product Catalog') }}</strong>
                <i class="mdi ms-auto" :class="open ? 'mdi-chevron-up' : 'mdi-chevron-down'"></i>
            </div>
            <div class="card-body" x-show="open" x-transition>
                <div class="row g-3">
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="facebook_catalog_enabled" {{ ($options['facebook_catalog_enabled'] ?? '0') === '1' ? 'checked' : '' }}>
                            <label class="form-check-label">{{ __('Enable Facebook Catalog Feed') }}</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="facebook_include_sale_price" {{ ($options['facebook_include_sale_price'] ?? '0') === '1' ? 'checked' : '' }}>
                            <label class="form-check-label">{{ __('Include Sale Price') }}</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Default Availability') }}</label>
                        <select class="form-select" name="facebook_availability">
                            @foreach(['in stock'=>'In Stock','preorder'=>'Preorder','available for order'=>'Available for Order'] as $val => $label)
                            <option value="{{ $val }}" {{ ($options['facebook_availability'] ?? 'in stock') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary px-4">
            <i class="mdi mdi-content-save me-1"></i> {{ __('Save Settings') }}
        </button>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('google_brand_field').addEventListener('change', function() {
    document.getElementById('custom-brand-wrap').style.display = this.value === 'custom' ? '' : 'none';
});
</script>
@endsection
