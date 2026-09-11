@extends(route_prefix().'admin.admin-master')
@section('title') {{__('All Themes')}} @endsection

@section('style')
    <style>.hover\:text-white:hover{color:#fff!important}</style>
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

{{-- Page Header --}}
<div class="bg-surface rounded-xl shadow-main border border-main mb-6">
    <div class="px-4 sm:px-6 py-4 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary to-emerald-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                <i class="mdi mdi-palette-outline text-white text-lg"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-dark font-urbanist">{{__('All Themes')}}</h3>
                <p class="text-xs text-muted">{{__('Manage and customize your storefront themes')}}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{route('landlord.admin.theme.marketplace')}}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-white text-sm font-semibold hover:bg-primary/90 transition whitespace-nowrap">
                <i class="mdi mdi-store-outline text-base"></i>
                {{__('Marketplace')}}
            </a>
            <a href="{{route('landlord.admin.theme.settings')}}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-main text-sm font-semibold text-brand hover:border-primary hover:text-primary hover:bg-primary-soft transition whitespace-nowrap">
                <i class="mdi mdi-cog-outline text-base"></i>
                {{__('Settings')}}
            </a>
        </div>
    </div>
</div>

{{-- Theme Grid --}}
<div class="theme-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
    @foreach(getAllThemeDataForAdmin() as $theme)
        @php
            $theme_slug    = $theme->slug;
            $theme_data    = getIndividualThemeDetails($theme_slug);
            $theme_image   = loadScreenshot($theme_slug);
            $is_active     = $theme->status == true;
            $theme_version = $theme->version ?? null;
            $theme_niche   = $theme->niche   ?? null;
            $has_demo      = file_exists(config('theme.base_path') . '/' . $theme_slug . '/demo/data.json');
            $is_new_system = is_dir(config('theme.base_path') . '/' . $theme_slug);

            $theme_name        = get_static_option_central($theme_data['slug'].'_theme_name');
            $theme_description = get_static_option_central($theme_data['slug'].'_theme_description');
            $theme_url         = get_static_option_central($theme_data['slug'].'_theme_url');
            $custom_theme_id   = get_static_option_central($theme_data['slug'].'_theme_image_id');
            $custom_theme_image= get_static_option_central($theme_data['slug'].'_theme_image');

            $display_name  = !empty($theme_name)        ? $theme_name        : $theme_data['name'];
            $display_desc  = !empty($theme_description) ? $theme_description : ($theme_data['description'] ?? '');
            $display_image = !empty($custom_theme_image) ? $custom_theme_image : $theme_image;
        @endphp

        <div class="theme-card {{ $is_active ? 'is-active' : '' }}" data-slug="{{$theme_slug}}">

            @if($is_active)
                <div class="active-ribbon"></div>
            @endif

            {{-- Image --}}
            <div class="theme-card-img">
                <img src="{{$display_image}}" alt="{{$display_name}}" loading="lazy"
                     onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'800\' height=\'500\'%3E%3Crect fill=\'%23f1f5f9\' width=\'800\' height=\'500\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' font-size=\'24\' fill=\'%2394a3b8\' font-family=\'sans-serif\'%3ENo Screenshot%3C/text%3E%3C/svg%3E'">
                <div class="theme-card-gradient"></div>

                {{-- Badges --}}
                <div class="absolute top-2.5 left-2.5 flex flex-col gap-1.5 z-10">
                    @if($is_new_system)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-500 text-white shadow-sm">
                            <i class="mdi mdi-lightning-bolt text-xs"></i> New
                        </span>
                    @endif
                    @if($has_demo)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-violet-500 text-white shadow-sm">
                            <i class="mdi mdi-database-outline text-xs"></i> Demo
                        </span>
                    @endif
                </div>

                {{-- Hover Actions --}}
                <div class="theme-card-hover-actions">
                    <button type="button"
                            class="hover-action-btn btn-preview theme-preview-btn"
                            data-slug="{{$theme_data['slug']}}"
                            data-name="{{$display_name}}"
                            data-description="{{$display_desc}}"
                            data-image="{{$display_image}}"
                            data-image_id="{{$custom_theme_id}}"
                            data-url="{{!empty($theme_url) ? $theme_url : ''}}"
                            data-status="{{$is_active ? 'active' : 'inactive'}}"
                            data-version="{{$theme_version}}"
                            data-niche="{{$theme_niche}}"
                            data-hasdemo="{{$has_demo ? '1' : '0'}}"
                            data-newlocation="{{$is_new_system ? '1' : '0'}}">
                        <i class="mdi mdi-eye-outline"></i> {{__('Preview')}}
                    </button>
                    <button type="button"
                            class="hover-action-btn btn-edit theme-edit-btn"
                            data-slug="{{$theme_data['slug']}}"
                            data-name="{{$display_name}}"
                            data-description="{{$display_desc}}"
                            data-image="{{$display_image}}"
                            data-image_id="{{$custom_theme_id}}"
                            data-url="{{!empty($theme_url) ? $theme_url : ''}}">
                        <i class="mdi mdi-pencil-outline"></i> {{__('Edit')}}
                    </button>
                </div>
            </div>

            {{-- Footer --}}
            <div class="theme-card-footer">
                <div class="min-w-0">
                    <h4 class="text-sm font-bold text-dark font-urbanist truncate">{{$display_name}}</h4>
                    <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                        @if($theme_version)
                            <span class="text-[10px] text-muted font-medium">v{{$theme_version}}</span>
                        @endif
                        @if($theme_niche)
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-secondary border border-main text-muted capitalize">{{$theme_niche}}</span>
                        @endif
                    </div>
                </div>
                <button type="button"
                        class="status-pill theme-toggle-btn {{ $is_active ? 'pill-active' : 'pill-inactive' }} flex-shrink-0"
                        data-slug="{{$theme_slug}}"
                        data-status="{{$is_active ? 'inactive' : 'active'}}">
                    <span class="pill-dot"></span>
                    {{$is_active ? __('Active') : __('Inactive')}}
                </button>
            </div>
        </div>
    @endforeach
</div>

{{-- ═══════════════════════════════════════════════════════════════════ --}}
{{-- PREVIEW MODAL                                                      --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
<div id="theme_preview_backdrop" class="cm-modal-backdrop"></div>
<div id="theme_preview_modal" class="cm-modal">
    <div class="cm-modal-dialog xl max-h-[92vh] flex flex-col">

        {{-- Header --}}
        <div class="cm-modal-header">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary to-emerald-600 flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-palette-swatch-outline text-white text-lg"></i>
                </div>
                <div>
                    <h3 id="preview_modal_title" class="text-sm font-bold text-dark font-urbanist">{{__('Theme Preview')}}</h3>
                    <p class="text-xs text-muted">{{__('Theme details and screenshot')}}</p>
                </div>
            </div>
            <button type="button" class="cm-modal-close preview-modal-close">
                <i class="mdi mdi-close text-lg"></i>
            </button>
        </div>

        {{-- Body --}}
        <div class="flex-1 overflow-y-auto">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-0">

                {{-- Image Side (3 cols) --}}
                <div class="md:col-span-3 bg-secondary border-r border-main p-4 sm:p-5">
                    <div class="rounded-xl overflow-y-auto border border-main shadow-sm bg-surface" style="max-height: 520px;">
                        <img id="preview_modal_image" src="" alt="" class="w-full h-auto block"
                             onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'800\' height=\'500\'%3E%3Crect fill=\'%23f1f5f9\' width=\'800\' height=\'500\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' font-size=\'24\' fill=\'%2394a3b8\' font-family=\'sans-serif\'%3ENo Screenshot%3C/text%3E%3C/svg%3E'">
                    </div>
                </div>

                {{-- Info Side (2 cols) --}}
                <div class="md:col-span-2 p-5 sm:p-6 flex flex-col">
                    <div class="flex-1 space-y-5">

                        {{-- Name + Status --}}
                        <div>
                            <h2 id="preview_modal_name" class="text-lg font-bold text-dark font-urbanist mb-2"></h2>
                            <div id="preview_modal_status_wrap"></div>
                        </div>

                        {{-- Meta row: version / niche / badges --}}
                        <div id="preview_modal_meta" class="flex flex-wrap gap-2"></div>

                        {{-- Description --}}
                        <div>
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Description')}}</label>
                            <p id="preview_modal_desc" class="text-sm text-muted leading-relaxed"></p>
                        </div>

                        {{-- Demo URL --}}
                        <div id="preview_modal_url_wrap" class="hidden">
                            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Demo URL')}}</label>
                            <a id="preview_modal_url" href="#" target="_blank"
                               class="inline-flex items-center gap-1.5 text-sm text-primary font-semibold hover:underline break-all">
                                <i class="mdi mdi-open-in-new text-sm flex-shrink-0"></i>
                                <span></span>
                            </a>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="flex flex-col gap-2.5 pt-5 border-t border-main mt-5">
                        <button type="button" id="preview_toggle_btn"
                                class="theme-toggle-btn w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all"
                                data-slug="" data-status="">
                            <i class="mdi mdi-toggle-switch text-base"></i>
                            <span></span>
                        </button>
                        <button type="button" id="preview_edit_btn"
                                class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl border border-main text-sm font-semibold text-dark hover:border-primary hover:text-primary hover:bg-primary-soft transition"
                                data-slug="" data-name="" data-description="" data-url="" data-image="" data-image_id="">
                            <i class="mdi mdi-pencil-outline text-base"></i>
                            {{__('Edit Details')}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════ --}}
{{-- EDIT MODAL                                                         --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
<div id="theme_edit_backdrop" class="cm-modal-backdrop"></div>
<div id="theme_edit_modal" class="cm-modal">
    <div class="cm-modal-dialog" style="max-width: 640px;">

        {{-- Header --}}
        <div class="cm-modal-header" style="background: var(--color-bg-secondary);">
            <div class="cm-modal-title">
                <div class="w-9 h-9 rounded-lg bg-warning-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-pencil-outline text-warning text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Edit Theme Details')}}</h3>
                    <p class="text-xs text-muted font-normal">{{__('Update name, description and image')}}</p>
                </div>
            </div>
            <button type="button" class="cm-modal-close edit-modal-close">
                <i class="mdi mdi-close text-lg"></i>
            </button>
        </div>

        {{-- Body --}}
        <form id="theme_edit_form" action="{{route('landlord.admin.theme.update')}}" method="POST">
            @csrf
            <input type="hidden" name="theme_slug" value="">

            <div class="cm-modal-body space-y-5">

                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Theme Name')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <i class="mdi mdi-format-title text-lg text-primary"></i>
                        <input type="text" name="theme_name" placeholder="{{__('Enter theme name')}}"
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Description')}}</label>
                    <div class="bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <textarea name="theme_description" rows="4"
                                  class="w-full bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0 resize-y"
                                  placeholder="{{__('Theme description...')}}"></textarea>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Theme URL')}}</label>
                    <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                        <i class="mdi mdi-link-variant text-lg text-primary"></i>
                        <input type="text" name="theme_url" placeholder="{{__('https://...')}}"
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Theme Image')}}</label>
                    <x-fields.tw-media-upload name="theme_image" dimentions="1920x1200"/>
                </div>
            </div>

            <div class="cm-modal-footer" style="background: var(--color-bg-secondary);">
                <button type="button" class="edit-modal-close px-5 py-2.5 text-sm font-medium text-dark bg-surface border border-main rounded-xl hover:bg-muted transition">
                    {{__('Cancel')}}
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white bg-primary rounded-xl hover:opacity-90 transition">
                    <i class="mdi mdi-content-save-outline text-base"></i>
                    {{__('Save Changes')}}
                </button>
            </div>
        </form>
    </div>
</div>

<x-media-upload.tw-markup/>
@endsection

@section('scripts')
    <x-media-upload.tw-js/>

    <script>
    (function ($) {
        "use strict";

        // ── Modal helpers ──────────────────────────────────────────────────
        function openModal(backdropId, modalId) {
            $('#' + backdropId).addClass('active');
            $('#' + modalId).addClass('active');
            $('body').css('overflow', 'hidden');
        }
        function closeModal(backdropId, modalId) {
            $('#' + backdropId).removeClass('active');
            $('#' + modalId).removeClass('active');
            $('body').css('overflow', '');
        }
        function openPreview()  { openModal('theme_preview_backdrop', 'theme_preview_modal'); }
        function closePreview() { closeModal('theme_preview_backdrop', 'theme_preview_modal'); }
        function openEdit()     { openModal('theme_edit_backdrop', 'theme_edit_modal'); }
        function closeEdit()    { closeModal('theme_edit_backdrop', 'theme_edit_modal'); }

        $('.theme-card-img img').on('load', function () {
            if (this.naturalHeight <= 320) $(this).addClass('no-scroll');
        });

        // ── PREVIEW MODAL ─────────────────────────────────────────────────
        $(document).on('click', '.theme-preview-btn', function () {
            var el      = $(this);
            var slug    = el.data('slug');
            var name    = el.data('name');
            var desc    = el.data('description');
            var image   = el.data('image');
            var imgId   = el.data('image_id');
            var url     = el.data('url');
            var status  = el.data('status');
            var version = el.data('version');
            var niche   = el.data('niche');
            var hasDemo = el.data('hasdemo');
            var isNew   = el.data('newlocation');

            $('#preview_modal_title').text(name);
            $('#preview_modal_name').text(name);
            $('#preview_modal_desc').text(desc || '{{__("No description available.")}}');
            $('#preview_modal_image').attr('src', image);

            // Status badge
            var $sw = $('#preview_modal_status_wrap');
            if (status === 'active') {
                $sw.html('<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold text-white" style="background:linear-gradient(135deg,#10b981,#059669);box-shadow:0 2px 8px -2px rgba(16,185,129,.4);"><span style="width:7px;height:7px;border-radius:50%;background:#fff;"></span> {{__("Active")}}</span>');
            } else {
                $sw.html('<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gray-100 text-gray-500 text-xs font-bold border border-gray-200"><span style="width:7px;height:7px;border-radius:50%;background:#cbd5e1;"></span> {{__("Inactive")}}</span>');
            }

            // Meta badges
            var $meta = $('#preview_modal_meta');
            $meta.html('');
            if (version) $meta.append('<span class="px-2 py-0.5 rounded-md text-[11px] font-mono font-semibold bg-secondary border border-main text-muted">v' + version + '</span>');
            if (niche)   $meta.append('<span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 border border-blue-100 text-blue-600 capitalize">' + niche + '</span>');
            if (isNew == '1') $meta.append('<span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 border border-emerald-200 text-emerald-600"><i class="mdi mdi-lightning-bolt"></i> New System</span>');
            if (hasDemo == '1') $meta.append('<span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-violet-50 border border-violet-200 text-violet-600"><i class="mdi mdi-database-outline"></i> Demo Data</span>');

            // Toggle button
            var $tb = $('#preview_toggle_btn');
            if (status === 'active') {
                $tb.attr('class','theme-toggle-btn w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all bg-red-50 text-red-600 border border-red-200 hover:bg-red-600 hover:text-white hover:border-red-600')
                   .attr('data-slug', slug).attr('data-status', 'inactive');
                $tb.find('span').text('{{__("Deactivate Theme")}}');
                $tb.find('i').attr('class','mdi mdi-toggle-switch-off-outline text-base');
            } else {
                $tb.attr('class','theme-toggle-btn w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all text-white hover:opacity-90')
                   .css('background','linear-gradient(135deg,#10b981,#059669)')
                   .attr('data-slug', slug).attr('data-status', 'active');
                $tb.find('span').text('{{__("Activate Theme")}}');
                $tb.find('i').attr('class','mdi mdi-toggle-switch text-base');
            }

            // URL
            if (url && url.length > 0) {
                $('#preview_modal_url_wrap').removeClass('hidden');
                $('#preview_modal_url').attr('href', url).find('span').text(url);
            } else {
                $('#preview_modal_url_wrap').addClass('hidden');
            }

            $('#preview_edit_btn').attr('data-slug', slug).attr('data-name', name)
                .attr('data-description', desc).attr('data-url', url)
                .attr('data-image', image).attr('data-image_id', imgId);

            openPreview();
        });

        $(document).on('click', '.preview-modal-close, #theme_preview_backdrop', closePreview);
        $(document).on('click', '#preview_edit_btn', function () {
            closePreview();
            populateEditModal($(this));
            openEdit();
        });

        // ── EDIT MODAL ────────────────────────────────────────────────────
        function populateEditModal($t) {
            var $f = $('#theme_edit_form');
            $f.find('input[name="theme_slug"]').val($t.attr('data-slug'));
            $f.find('input[name="theme_name"]').val($t.attr('data-name') || '');
            $f.find('textarea[name="theme_description"]').val($t.attr('data-description') || '');
            $f.find('input[name="theme_url"]').val($t.attr('data-url') || '');

            var imageId = $t.attr('data-image_id');
            var image   = $t.attr('data-image');
            var $w      = $f.find('.tw-media-upload-wrapper');
            $w.find('.tw-media-id-input').val(imageId || '');

            if (imageId && image) {
                $w.find('.tw-img-wrap').html(
                    '<div class="tw-attachment-preview relative inline-block">' +
                    '<img src="' + image + '" alt="" class="tw-preview-img w-24 h-24 rounded-lg object-cover border border-gray-200" />' +
                    '<button type="button" class="tw-rmv-btn absolute -top-2 -right-2 w-5 h-5 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-xs leading-none transition">&times;</button>' +
                    '</div>'
                );
                $w.find('.tw-media-open-btn').html('<i class="ti tabler-photo text-base"></i> {{__("Change Image")}}');
            } else {
                $w.find('.tw-img-wrap').html('<div class="tw-attachment-preview"></div>');
                $w.find('.tw-media-open-btn').html('<i class="ti tabler-photo text-base"></i> {{__("Upload Image")}}');
            }
        }

        $(document).on('click', '.theme-edit-btn', function () {
            populateEditModal($(this));
            openEdit();
        });
        $(document).on('click', '.edit-modal-close, #theme_edit_backdrop', closeEdit);

        // ── TOGGLE STATUS ─────────────────────────────────────────────────
        $(document).on('click', '.theme-toggle-btn', function (e) {
            e.preventDefault();
            var el = $(this), slug = el.attr('data-slug'), status = el.attr('data-status');

            Swal.fire({
                title: '{{__("Are you sure?")}}',
                text: '{{__("Changing theme status may affect the page builder add-ons and frontend design.")}}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: '{{__("Yes, confirm!")}}',
                cancelButtonText: '{{__("Cancel")}}',
            }).then(function (result) {
                if (!result.isConfirmed) return;

                $.ajax({
                    type: 'POST',
                    url: '{{route("landlord.admin.theme.status.update")}}',
                    data: { _token: '{{csrf_token()}}', slug: slug },
                    beforeSend: function () { el.css('opacity','0.5').css('pointer-events','none'); },
                    success: function (data) {
                        el.css('opacity','1').css('pointer-events','auto');
                        var $card    = $('.theme-card[data-slug="' + slug + '"]');
                        var $pills   = $card.find('.theme-toggle-btn');
                        var $preview = $('.theme-preview-btn[data-slug="' + slug + '"]');

                        if (data.status === true) {
                            $card.addClass('is-active');
                            if (!$card.find('.active-ribbon').length) $card.prepend('<div class="active-ribbon"></div>');
                            $pills.attr('class','status-pill theme-toggle-btn pill-active')
                                  .attr('data-status','inactive')
                                  .html('<span class="pill-dot"></span> {{__("Active")}}');
                            $preview.attr('data-status','active');
                            Swal.fire({icon:'success',title:'{{__("Activated!")}}',text:data.msg,timer:1500,showConfirmButton:false});
                        } else {
                            $card.removeClass('is-active');
                            $card.find('.active-ribbon').remove();
                            $pills.attr('class','status-pill theme-toggle-btn pill-inactive')
                                  .attr('data-status','active')
                                  .html('<span class="pill-dot"></span> {{__("Inactive")}}');
                            $preview.attr('data-status','inactive');
                            Swal.fire({icon:'info',title:'{{__("Deactivated!")}}',text:data.msg,timer:1500,showConfirmButton:false});
                        }
                        closePreview();
                    },
                    error: function () {
                        el.css('opacity','1').css('pointer-events','auto');
                        Swal.fire({icon:'error',title:'{{__("Error")}}',text:'{{__("Something went wrong.")}}'});
                    }
                });
            });
        });

        $(document).on('keydown', function (e) {
            if (e.key === 'Escape') { closePreview(); closeEdit(); }
        });

    })(jQuery);
    </script>
@endsection
