@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Theme Marketplace')}} @endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

{{-- Page Header --}}
<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary to-violet-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                <i class="mdi mdi-store-outline text-white text-lg"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-dark font-urbanist">{{__('Theme Marketplace')}}</h3>
                <p class="text-xs text-muted">{{__('Upload, manage, and price themes for your tenants')}}</p>
            </div>
        </div>
        <button onclick="document.getElementById('upload-modal').classList.remove('hidden')"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-primary/90 transition">
            <i class="mdi mdi-upload text-base"></i>
            {{__('Upload Theme')}}
        </button>
    </div>
</div>

{{-- Installed Themes --}}
<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-6 py-4 border-b border-main">
        <h4 class="font-semibold text-dark">{{__('Installed Themes')}} <span class="ml-2 text-xs font-normal text-muted">({{ $installed->count() }})</span></h4>
    </div>
    <div class="p-6">
        @if($installed->isEmpty())
            <p class="text-muted text-sm text-center py-6">{{__('No themes installed yet. Upload a theme zip to get started.')}}</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($installed as $theme)
                <div class="border border-main rounded-xl p-4 flex flex-col gap-3">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="font-semibold text-dark text-sm">{{ $theme->name }}</p>
                            <p class="text-xs text-muted">v{{ $theme->installed_version }}</p>
                        </div>
                        @if($theme->update_available)
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">{{__('Update available')}}</span>
                        @else
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">{{__('Up to date')}}</span>
                        @endif
                    </div>

                    <p class="text-xs text-muted line-clamp-2">{{ $theme->description }}</p>

                    <div class="flex flex-wrap gap-2 text-xs">
                        @if($theme->niche)
                            <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-600 border border-blue-100">{{ $theme->niche }}</span>
                        @endif
                        <span class="px-2 py-0.5 rounded-full {{ $theme->is_free ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-violet-50 text-violet-600 border border-violet-100' }}">
                            {{ $theme->is_free ? __('Free') : amount_with_currency_symbol($theme->price) }}
                        </span>
                    </div>

                    <div class="flex gap-2 mt-auto pt-2 border-t border-main">
                        <button onclick="openPricingModal('{{ $theme->slug }}', {{ $theme->price }}, '{{ $theme->license_type }}')"
                                class="flex-1 text-center text-xs font-medium py-1.5 rounded-lg border border-main text-muted hover:border-primary hover:text-primary transition">
                            <i class="mdi mdi-tag-outline"></i> {{__('Pricing')}}
                        </button>
                        @if($theme->update_available)
                        <label for="update-zip-{{ $theme->slug }}"
                               class="flex-1 text-center text-xs font-medium py-1.5 rounded-lg border border-amber-300 text-amber-700 hover:bg-amber-50 transition cursor-pointer">
                            <i class="mdi mdi-arrow-up-circle-outline"></i> {{__('Update')}}
                        </label>
                        <input id="update-zip-{{ $theme->slug }}" type="file" accept=".zip" class="hidden"
                               onchange="submitUpdate('{{ $theme->slug }}', this)">
                        @endif
                        <button onclick="confirmDeactivate('{{ $theme->slug }}')"
                                class="px-2 py-1.5 rounded-lg border border-red-200 text-red-500 hover:bg-red-50 transition text-xs">
                            <i class="mdi mdi-close-circle-outline"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- Upload Modal --}}
<div id="upload-modal" class="hidden fixed inset-0 z-[800] flex items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-surface rounded-2xl shadow-xl border border-main w-full max-w-md mx-4">
        <div class="flex items-center justify-between px-6 py-4 border-b border-main">
            <h5 class="font-bold text-dark">{{__('Upload New Theme')}}</h5>
            <button onclick="document.getElementById('upload-modal').classList.add('hidden')" class="text-muted hover:text-dark">
                <i class="mdi mdi-close text-xl"></i>
            </button>
        </div>
        <form action="{{ route('landlord.admin.theme.marketplace.upload') }}" method="POST" enctype="multipart/form-data" id="upload-form">
            @csrf
            <div class="p-6 flex flex-col gap-4">
                <div>
                    <label class="block text-xs font-semibold text-dark mb-1">{{__('Theme Zip File')}} <span class="text-red-500">*</span></label>
                    <input type="file" name="theme_zip" accept=".zip" required
                           class="w-full text-sm border border-main rounded-xl px-3 py-2 focus:ring-2 focus:ring-primary/30 focus:border-primary outline-none transition">
                    <p class="text-xs text-muted mt-1">{{__('Max size: 50MB. Zip must contain theme.json at root.')}}</p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-dark mb-1">{{__('Price')}}</label>
                        <input type="number" name="price" value="0" min="0" step="0.01"
                               class="w-full text-sm border border-main rounded-xl px-3 py-2 focus:ring-2 focus:ring-primary/30 focus:border-primary outline-none transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-dark mb-1">{{__('License')}}</label>
                        <select name="license_type" class="w-full text-sm border border-main rounded-xl px-3 py-2 focus:ring-2 focus:ring-primary/30 focus:border-primary outline-none transition">
                            <option value="free">{{__('Free')}}</option>
                            <option value="single">{{__('Single use')}}</option>
                            <option value="unlimited">{{__('Unlimited')}}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-main flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('upload-modal').classList.add('hidden')"
                        class="px-4 py-2 rounded-xl border border-main text-sm text-muted hover:border-dark hover:text-dark transition">
                    {{__('Cancel')}}
                </button>
                <button type="submit" id="upload-btn"
                        class="px-5 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-primary/90 transition">
                    <i class="mdi mdi-upload mr-1"></i> {{__('Install')}}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Pricing Modal --}}
<div id="pricing-modal" class="hidden fixed inset-0 z-[800] flex items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-surface rounded-2xl shadow-xl border border-main w-full max-w-sm mx-4">
        <div class="flex items-center justify-between px-6 py-4 border-b border-main">
            <h5 class="font-bold text-dark">{{__('Update Pricing')}}</h5>
            <button onclick="document.getElementById('pricing-modal').classList.add('hidden')" class="text-muted hover:text-dark">
                <i class="mdi mdi-close text-xl"></i>
            </button>
        </div>
        <form id="pricing-form" method="POST">
            @csrf
            <div class="p-6 flex flex-col gap-4">
                <div>
                    <label class="block text-xs font-semibold text-dark mb-1">{{__('Price')}}</label>
                    <input type="number" name="price" id="pricing-price" min="0" step="0.01"
                           class="w-full text-sm border border-main rounded-xl px-3 py-2 focus:ring-2 focus:ring-primary/30 focus:border-primary outline-none transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-dark mb-1">{{__('License Type')}}</label>
                    <select name="license_type" id="pricing-license" class="w-full text-sm border border-main rounded-xl px-3 py-2 outline-none transition">
                        <option value="free">{{__('Free')}}</option>
                        <option value="single">{{__('Single use')}}</option>
                        <option value="unlimited">{{__('Unlimited')}}</option>
                    </select>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-main flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('pricing-modal').classList.add('hidden')"
                        class="px-4 py-2 rounded-xl border border-main text-sm text-muted hover:text-dark transition">
                    {{__('Cancel')}}
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-primary/90 transition">
                    {{__('Save')}}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
function openPricingModal(slug, price, license) {
    document.getElementById('pricing-price').value = price;
    document.getElementById('pricing-license').value = license;
    document.getElementById('pricing-form').action = `/admin-home/theme-marketplace/pricing/${slug}`;
    document.getElementById('pricing-modal').classList.remove('hidden');
}

function confirmDeactivate(slug) {
    Swal.fire({
        title: '{{ __("Deactivate Theme?") }}',
        text: '{{ __("Tenants using this theme will retain their current look but won\'t be able to select it again.") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '{{ __("Deactivate") }}',
        confirmButtonColor: '#ef4444',
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin-home/theme-marketplace/${slug}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            }).then(r => r.json()).then(data => {
                if (data.status) {
                    toastr.success(data.msg);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    toastr.error(data.msg);
                }
            });
        }
    });
}

function submitUpdate(slug, input) {
    if (!input.files[0]) return;

    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('theme_zip', input.files[0]);

    toastr.info('{{ __("Uploading update...") }}');

    fetch(`/admin-home/theme-marketplace/update/${slug}`, {
        method: 'POST',
        body: formData,
    }).then(r => r.json()).then(data => {
        if (data.status) {
            toastr.success(data.msg);
            setTimeout(() => location.reload(), 1500);
        } else {
            toastr.error(data.msg);
        }
    });
}

// Show loading state on upload
document.getElementById('upload-form').addEventListener('submit', function() {
    document.getElementById('upload-btn').disabled = true;
    document.getElementById('upload-btn').innerHTML = '<i class="mdi mdi-loading mdi-spin mr-1"></i> {{ __("Installing...") }}';
});
</script>
@endsection
