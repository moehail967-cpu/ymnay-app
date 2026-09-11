@extends('tenant.admin.admin-master')

@section('title') {{ __('Product Feeds — URLs') }} @endsection
@section('page-title') {{ __('Product Feed URLs') }} @endsection

@section('content')
<div class="col-12">

    <div class="row g-4 mb-4">
        {{-- Google Merchant Feed --}}
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="mdi mdi-google fs-5 text-primary"></i>
                    <strong>{{ __('Google Merchant Center') }}</strong>
                </div>
                <div class="card-body">
                    @if($googleUrl)
                    <label class="form-label small text-muted">{{ __('Feed URL') }}</label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control form-control-sm font-monospace" id="google-feed-url" value="{{ $googleUrl }}" readonly>
                        <button class="btn btn-outline-secondary btn-sm" onclick="copyFeedUrl('google-feed-url')">
                            <i class="mdi mdi-content-copy"></i>
                        </button>
                    </div>
                    @if($googleCache)
                    <span class="text-muted small" id="google-generated-at">
                        <i class="mdi mdi-clock-outline me-1"></i>{{ __('Last generated') }}: {{ $googleCache->generated_at?->diffForHumans() }}
                    </span>
                    @else
                    <span class="text-warning small"><i class="mdi mdi-alert-outline me-1"></i>{{ __('Not yet generated.') }}</span>
                    @endif
                    @else
                    <p class="text-muted small">{{ __('Enable Google Merchant feed in Settings first.') }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Facebook Catalog Feed --}}
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="mdi mdi-facebook fs-5" style="color:#1877f2"></i>
                    <strong>{{ __('Facebook / Meta Product Catalog') }}</strong>
                </div>
                <div class="card-body">
                    @if($facebookUrl)
                    <label class="form-label small text-muted">{{ __('Feed URL') }}</label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control form-control-sm font-monospace" id="facebook-feed-url" value="{{ $facebookUrl }}" readonly>
                        <button class="btn btn-outline-secondary btn-sm" onclick="copyFeedUrl('facebook-feed-url')">
                            <i class="mdi mdi-content-copy"></i>
                        </button>
                    </div>
                    @if($facebookCache)
                    <span class="text-muted small" id="facebook-generated-at">
                        <i class="mdi mdi-clock-outline me-1"></i>{{ __('Last generated') }}: {{ $facebookCache->generated_at?->diffForHumans() }}
                    </span>
                    @else
                    <span class="text-warning small"><i class="mdi mdi-alert-outline me-1"></i>{{ __('Not yet generated.') }}</span>
                    @endif
                    @else
                    <p class="text-muted small">{{ __('Enable Facebook Catalog feed in Settings first.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Regenerate button --}}
    <button id="fs-regenerate-btn" class="btn btn-primary">
        <i class="mdi mdi-refresh me-1"></i> {{ __('Regenerate Now') }}
    </button>
    <span id="fs-regen-status" class="ms-3 small text-muted"></span>

</div>
@endsection

@section('scripts')
<script>
function copyFeedUrl(id) {
    var el = document.getElementById(id);
    navigator.clipboard.writeText(el.value).then(function() {
        toastr.success('{{ __('Feed URL copied to clipboard') }}');
    });
}

document.getElementById('fs-regenerate-btn').addEventListener('click', function() {
    var btn    = this;
    var status = document.getElementById('fs-regen-status');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> {{ __('Regenerating...') }}';
    status.textContent = '';

    fetch('{{ route('tenant.admin.feed-sync.regenerate') }}', {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'}
    })
    .then(r => r.json())
    .then(function(data) {
        btn.disabled = false;
        btn.innerHTML = '<i class="mdi mdi-refresh me-1"></i> {{ __('Regenerate Now') }}';

        if (data.google_generated_at) {
            var el = document.getElementById('google-generated-at');
            if (el) el.innerHTML = '<i class="mdi mdi-clock-outline me-1"></i>{{ __('Last generated') }}: ' + data.google_generated_at;
        }
        if (data.facebook_generated_at) {
            var el = document.getElementById('facebook-generated-at');
            if (el) el.innerHTML = '<i class="mdi mdi-clock-outline me-1"></i>{{ __('Last generated') }}: ' + data.facebook_generated_at;
        }

        toastr.success('{{ __('Feeds regenerated successfully.') }}');
    })
    .catch(function() {
        btn.disabled = false;
        btn.innerHTML = '<i class="mdi mdi-refresh me-1"></i> {{ __('Regenerate Now') }}';
        toastr.error('{{ __('Regeneration failed. Please try again.') }}');
    });
});
</script>
@endsection
