@extends('tenant.admin.admin-master')
@section('title')
    {{__('PWA Settings')}}
@endsection

@section('content')

<x-flash-msg-tw/>
<x-error-msg-tw/>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    {{-- Main Form --}}
    <div class="lg:col-span-8">
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-cellphone-link text-primary text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Progressive Web App (PWA)')}}</h3>
                    <p class="text-xs text-muted">{{__('Configure the PWA manifest for your POS storefront')}}</p>
                </div>
            </div>

            <div class="p-4 sm:p-6">
                <form action="{{ route('tenant.admin.pos.pwa-settings') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="space-y-5">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Name --}}
                            <div>
                                <label for="name" class="block text-[10px] font-bold text-muted uppercase tracking-widest mb-1.5">{{__('App Name')}}</label>
                                <input class="w-full bg-surface border border-main rounded-xl px-3.5 py-2.5 text-sm text-dark placeholder-subtle outline-none focus:border-primary transition"
                                       id="name" type="text" name="name" value="{{ $manifest_data->name ?? '' }}"
                                       placeholder="{{__('My POS App')}}">
                            </div>

                            {{-- Display Mode --}}
                            <div>
                                @php $display_mode = ['fullscreen' => 'Fullscreen', 'standalone' => 'Standalone']; @endphp
                                <label for="display_mode" class="block text-[10px] font-bold text-muted uppercase tracking-widest mb-1.5">{{__('Display Mode')}}</label>
                                <select class="w-full bg-surface border border-main rounded-xl px-3.5 py-2.5 text-sm text-dark outline-none focus:border-primary transition"
                                        name="display_mode" id="display_mode">
                                    @foreach($display_mode as $value => $label)
                                        <option value="{{ $value }}" @selected(!empty($manifest_data) && $manifest_data->display == $value)>{{ __($label) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Description --}}
                        <div>
                            <label for="description" class="block text-[10px] font-bold text-muted uppercase tracking-widest mb-1.5">{{__('Description')}}</label>
                            <textarea class="w-full bg-surface border border-main rounded-xl px-3.5 py-2.5 text-sm text-dark placeholder-subtle outline-none focus:border-primary transition resize-none"
                                      id="description" name="description" rows="2"
                                      placeholder="{{__('A short description of your app')}}">{{ $manifest_data->description ?? '' }}</textarea>
                        </div>

                        {{-- Theme Color + Icon Upload --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Theme Color --}}
                            <div>
                                <label class="block text-[10px] font-bold text-muted uppercase tracking-widest mb-1.5">{{__('Theme Color')}}</label>
                                <div class="flex items-center gap-3 p-3 bg-secondary border border-main rounded-xl">
                                    <input class="w-10 h-10 rounded-lg border border-main cursor-pointer p-0.5 flex-shrink-0 bg-transparent"
                                           type="color" value="{{ $manifest_data->theme_color ?? '#1a5c4e' }}" id="colorPicker" name="color">
                                    <div>
                                        <p class="text-xs font-bold text-dark" id="color-hex">{{ $manifest_data->theme_color ?? '#1a5c4e' }}</p>
                                        <p class="text-[10px] text-muted mt-0.5">{{__('Hex color code')}}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- App Icon --}}
                            <div>
                                <label class="block text-[10px] font-bold text-muted uppercase tracking-widest mb-1.5">{{__('App Icon')}}</label>
                                <div class="flex items-center gap-3 p-3 bg-secondary border border-main rounded-xl">
                                    <img class="pwa-icon-preview w-12 h-12 rounded-xl object-cover border border-main flex-shrink-0"
                                         src="{{ $manifest_data ? global_asset('assets/pwa/'.$manifest_data->icons[0]->src) : placeholder_image() }}" alt="">
                                    <div class="flex-1 min-w-0">
                                        <input class="w-full text-sm text-dark outline-none bg-transparent file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-soft file:text-primary hover:file:bg-primary hover:file:text-white file:transition file:cursor-pointer"
                                               type="file" id="icon" name="icon" accept="image/*">
                                        <p class="text-[10px] text-muted mt-1">{{__('512×512 px · 1:1 ratio · Max 2 MB')}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition shadow-sm">
                            <i class="mdi mdi-content-save-outline text-base"></i> {{__('Save Changes')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Info Sidebar --}}
    <div class="lg:col-span-4">
        <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden lg:sticky lg:top-[80px]">
            <div class="px-4 sm:px-6 py-4 border-b border-main flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-info-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-information-outline text-info text-base"></i>
                </div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('PWA Info')}}</h3>
            </div>
            <div class="p-4 sm:p-6 space-y-3">
                <div class="flex items-start gap-2.5 text-xs text-muted">
                    <i class="mdi mdi-chevron-right text-primary text-sm mt-0.5 flex-shrink-0"></i>
                    <span>{{__('The app name is shown on device home screens when installed.')}}</span>
                </div>
                <div class="flex items-start gap-2.5 text-xs text-muted">
                    <i class="mdi mdi-chevron-right text-primary text-sm mt-0.5 flex-shrink-0"></i>
                    <span>{{__('Standalone mode hides the browser UI. Fullscreen uses the entire screen.')}}</span>
                </div>
                <div class="flex items-start gap-2.5 text-xs text-muted">
                    <i class="mdi mdi-chevron-right text-primary text-sm mt-0.5 flex-shrink-0"></i>
                    <span>{{__('Icon must be exactly 1:1 ratio (e.g. 512×512 px) for best results.')}}</span>
                </div>
                <div class="flex items-start gap-2.5 text-xs text-muted">
                    <i class="mdi mdi-chevron-right text-primary text-sm mt-0.5 flex-shrink-0"></i>
                    <span>{{__('Theme color controls the browser toolbar color on mobile devices.')}}</span>
                </div>
            </div>

            {{-- Current Icon Preview Card --}}
            @if(!empty($manifest_data))
            <div class="mx-4 sm:mx-6 mb-4 sm:mb-6 p-4 rounded-xl bg-secondary border border-main">
                <p class="text-[10px] font-bold text-muted uppercase tracking-widest mb-3">{{__('Current Settings')}}</p>
                <div class="flex items-center gap-3">
                    <img src="{{ global_asset('assets/pwa/'.$manifest_data->icons[0]->src) }}"
                         class="w-10 h-10 rounded-lg object-cover border border-main flex-shrink-0" alt="">
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-dark truncate">{{ $manifest_data->name ?? '—' }}</p>
                        <div class="flex items-center gap-1.5 mt-1">
                            <span class="inline-block w-3 h-3 rounded-sm flex-shrink-0"
                                  style="background-color: {{ $manifest_data->theme_color ?? '#1a5c4e' }}"></span>
                            <span class="text-[10px] text-muted font-mono">{{ $manifest_data->theme_color ?? '—' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script>
(function ($) {
    'use strict';

    $(document).on('change', '#icon', function () {
        if (this.files && this.files[0]) {
            let reader = new FileReader();
            reader.onload = function (e) {
                $('.pwa-icon-preview').attr('src', e.target.result);
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    $(document).on('input', '#colorPicker', function () {
        $('#color-hex').text($(this).val());
    });

})(jQuery);
</script>
@endsection
