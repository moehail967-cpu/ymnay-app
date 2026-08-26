@extends('landlord.admin.admin-master')
@section('title') {{__('Add New Plugin')}} @endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

{{-- Hero Banner --}}
<div class="featured-card mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
        <div class="flex items-center gap-4 flex-1 min-w-0">
            <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-cloud-upload-outline text-2xl text-white/80"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest text-white/40">{{__('Plugin Manager')}}</p>
                <p class="text-xl font-extrabold text-white leading-tight mt-0.5">{{__('Upload New Plugin')}}</p>
            </div>
        </div>
        <a href="{{route('landlord.plugin.manage.all')}}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-white/15 backdrop-blur-sm text-white text-sm font-semibold hover:bg-white/25 transition-all border border-white/10 self-start sm:self-center">
            <i class="mdi mdi-arrow-left text-base"></i> {{__('Back to Plugins')}}
        </a>
    </div>
</div>

<div class="max-w-2xl">
    <div class="bg-surface rounded-2xl overflow-hidden" style="box-shadow: var(--shadow-main); border: 1px solid var(--color-border-main);">
        <form action="{{route('landlord.plugin.manage.new')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="p-6 sm:p-8">
                {{-- Upload Zone --}}
                <label for="plugin_file_input" id="upload_zone"
                       class="group relative flex flex-col items-center justify-center gap-4 p-10 sm:p-12 rounded-2xl cursor-pointer transition-all duration-300"
                       style="border: 2px dashed var(--color-border-hover); background: var(--color-bg-secondary);">
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300"
                         style="background: var(--color-primary-soft);">
                        <i class="mdi mdi-cloud-upload-outline text-3xl text-brand"></i>
                    </div>
                    <div class="text-center">
                        <p class="text-sm font-bold text-dark">{{__('Click to select your plugin file')}}</p>
                        <p class="text-xs mt-1.5" style="color: var(--color-text-muted);">
                            {{__('Accepts')}} <span class="font-semibold text-brand">.zip</span> {{__('files only')}}
                        </p>
                    </div>
                    <span id="selected_file_name"
                          class="hidden text-xs font-bold px-4 py-1.5 rounded-full"
                          style="background: var(--color-success-bg); color: var(--color-success);">
                    </span>
                    <input type="file" id="plugin_file_input" name="plugin_file" accept=".zip" class="hidden">
                </label>

                <p class="text-[11px] mt-4 leading-relaxed" style="color: var(--color-text-muted);">
                    <i class="mdi mdi-information-outline text-brand mr-0.5"></i>
                    {{__('If a plugin with the same name already exists, uploading will override the existing files.')}}
                </p>
            </div>

            <div class="px-6 sm:px-8 py-4 flex items-center justify-end gap-3"
                 style="border-top: 1px solid var(--color-border-main); background: var(--color-bg-secondary);">
                <a href="{{route('landlord.plugin.manage.all')}}"
                   class="px-5 py-2.5 rounded-full text-sm font-semibold transition-all"
                   style="border: 1px solid var(--color-border-main); color: var(--color-text-muted);">
                    {{__('Cancel')}}
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full text-white text-sm font-semibold hover:opacity-90 transition-all"
                        style="background: var(--color-primary); box-shadow: 0 4px 12px rgba(26,92,78,0.25);">
                    <i class="mdi mdi-upload text-base"></i> {{__('Upload Plugin')}}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
(function ($) {
    "use strict";

    var zone = document.getElementById('upload_zone');

    $('#plugin_file_input').on('change', function () {
        var name = this.files.length ? this.files[0].name : '';
        var span = $('#selected_file_name');
        if (name) {
            span.text(name).removeClass('hidden');
            zone.style.borderColor = 'var(--color-primary)';
            zone.style.background = 'var(--color-primary-soft)';
        } else {
            span.addClass('hidden').text('');
            zone.style.borderColor = 'var(--color-border-hover)';
            zone.style.background = 'var(--color-bg-secondary)';
        }
    });

})(jQuery);
</script>
@endsection
