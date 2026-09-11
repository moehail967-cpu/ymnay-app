@extends('tenant.admin.admin-master')
@section('title') {{__('Theme Manage')}} @endsection

@section('content')

    <x-landlord-error-msg/>
    <x-landlord-flash-msg/>

    @php
        $selected_theme = tenant()->theme_slug;
        $all_theme = getAllThemeDataForAdmin();
        $svgPlaceholder = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="600" height="400" viewBox="0 0 600 400"><rect fill="%23f1f5f9" width="600" height="400"/><text fill="%2394a3b8" font-family="sans-serif" font-size="18" text-anchor="middle" x="300" y="210">No Screenshot</text></svg>';
    @endphp

    {{-- Page Header --}}
    <div class="bg-surface rounded-xl shadow-main border border-main mb-6">
        <div class="px-4 sm:px-6 py-4 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary to-emerald-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                    <i class="mdi mdi-palette-outline text-white text-lg"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-dark font-urbanist">{{__('Theme Manage')}}</h3>
                    <p class="text-xs text-muted">{{__('Choose and activate a theme for your storefront')}}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Theme Grid --}}
    <div class="theme-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
        @foreach($all_theme as $theme)
            @php
                $theme_slug = $theme->slug;

                $theme_data     = getIndividualThemeDetails($theme_slug);
                $theme_image    = loadScreenshot($theme_slug);
                $is_selected    = $theme_data['slug'] == $selected_theme;

                $theme_name        = get_static_option_central($theme_data['slug'].'_theme_name');
                $theme_description = get_static_option_central($theme_data['slug'].'_theme_description');
                $custom_theme_image = get_static_option_central($theme_data['slug'].'_theme_image');

                $display_name  = !empty($theme_name)        ? $theme_name        : $theme_data['name'];
                $display_desc  = !empty($theme_description) ? $theme_description : $theme_data['description'];
                $display_image = !empty($custom_theme_image) ? $custom_theme_image : $theme_image;

                // New-system metadata
                $theme_meta    = app('theme')->loadMeta($theme_slug);
                $theme_version = $theme_meta->version  ?? null;
                $theme_niche   = $theme_meta->niche    ?? null;
                $theme_price   = $theme_meta->price    ?? 0;
                $is_premium    = $theme_price > 0;
                $has_demo      = file_exists(config('theme.base_path') . '/' . $theme_slug . '/demo/data.json');
                $is_new_system = is_dir(config('theme.base_path') . '/' . $theme_slug);
            @endphp

            <div class="theme-card {{ $is_selected ? 'is-active' : '' }}"
                 data-slug="{{$theme_data['slug']}}"
                 data-name="{{$display_name}}"
                 data-description="{{$display_desc}}"
                 data-image="{{$display_image}}"
                 data-url="{{route('tenant.admin.theme.update', $theme_data['slug'])}}"
                 data-selected="{{$is_selected ? '1' : '0'}}"
                 data-version="{{$theme_version ?? ''}}"
                 data-niche="{{$theme_niche ?? ''}}"
                 data-price="{{$theme_price}}"
                 data-premium="{{$is_premium ? '1' : '0'}}"
                 data-hasdemo="{{$has_demo ? '1' : '0'}}"
                 data-newlocation="{{$is_new_system ? '1' : '0'}}">

                @if($is_selected)
                    <div class="active-ribbon"></div>
                @endif

                {{-- Image (clickable) --}}
                <div class="theme-card-img theme-card-click" style="cursor:pointer;">
                    <img src="{{$display_image}}" alt="{{$display_name}}" loading="lazy"
                         onerror="this.onerror=null;this.src='{{$svgPlaceholder}}';">
                    <div class="theme-card-gradient"></div>

                    {{-- New / Demo / Premium badges --}}
                    <div class="absolute top-2 left-2 flex flex-col gap-1 z-10">
                        @if($is_premium)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold text-white shadow-sm" style="background:linear-gradient(135deg,#f59e0b,#d97706);">
                                <i class="mdi mdi-crown text-xs"></i> Premium
                            </span>
                        @endif
                        @if($is_new_system)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold text-white bg-emerald-500 shadow-sm">
                                <i class="mdi mdi-lightning-bolt text-xs"></i> New
                            </span>
                        @endif
                        @if($has_demo)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold text-white bg-violet-500 shadow-sm">
                                <i class="mdi mdi-database-outline text-xs"></i> {{__('Demo')}}
                            </span>
                        @endif
                    </div>

                    {{-- Hover Actions --}}
                    <div class="theme-card-hover-actions">
                        <button type="button" class="hover-action-btn btn-preview theme-card-click">
                            <i class="mdi mdi-eye-outline"></i> {{__('Preview')}}
                        </button>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="theme-card-footer">
                    <div class="min-w-0 flex-1">
                        <h4 class="text-sm font-bold text-dark font-urbanist truncate">{{$display_name}}</h4>
                        <div class="flex flex-wrap gap-1.5 mt-1">
                            @if($theme_version)
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-600 border border-blue-100">
                                    <i class="mdi mdi-tag-outline text-[10px]"></i> v{{$theme_version}}
                                </span>
                            @endif
                            @if($theme_niche)
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-medium bg-amber-50 text-amber-600 border border-amber-100 capitalize">
                                    {{$theme_niche}}
                                </span>
                            @endif
                        </div>
                    </div>
                    @if($is_selected)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold text-white flex-shrink-0" style="background:linear-gradient(135deg,#10b981,#059669);">
                            <i class="mdi mdi-check-circle text-xs"></i> {{__('Selected')}}
                        </span>
                    @endif
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
                        <p class="text-xs text-muted">{{__('Preview and select this theme')}}</p>
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
                            <img id="preview_modal_image" src="" alt="" class="w-full h-auto block">
                        </div>
                    </div>

                    {{-- Info Side (2 cols) --}}
                    <div class="md:col-span-2 p-5 sm:p-6 flex flex-col">
                        <div class="flex-1 space-y-5">

                            {{-- Name + Status --}}
                            <div>
                                <h2 id="preview_modal_name" class="text-lg font-bold text-dark font-urbanist mb-2"></h2>
                                <div id="preview_modal_status_wrap" class="mb-2"></div>
                                <div id="preview_modal_meta" class="flex flex-wrap gap-1.5"></div>
                            </div>

                            {{-- Description --}}
                            <div>
                                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{__('Description')}}</label>
                                <p id="preview_modal_desc" class="text-sm text-muted leading-relaxed"></p>
                            </div>

                            {{-- Info note --}}
                            <div class="bg-info-soft rounded-xl p-3.5">
                                <div class="flex gap-2.5">
                                    <i class="mdi mdi-information-outline text-info text-lg flex-shrink-0 mt-0.5"></i>
                                    <p class="text-xs text-muted leading-relaxed">{{__('You can set theme with demo imported data or without imported data. If you set without data, you will need to configure the home page from the page builder section.')}}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Footer: Selection form --}}
                        <div class="pt-5 border-t border-main mt-5 space-y-4">
                            <div>
                                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Theme Setting Type')}}</label>
                                <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                                    <i class="mdi mdi-cog text-lg text-primary"></i>
                                    <select id="theme_setting_type"
                                            class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                                        <option value="">{{__('Select Type')}}</option>
                                        <option value="set_theme">{{__('Set without data')}}</option>
                                        <option value="set_theme_with_demo_data">{{__('Set theme with demo or old data')}}</option>
                                    </select>
                                    <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                                </div>
                            </div>

                            <button type="button" id="theme_select_btn"
                                    class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-primary hover:opacity-90 transition-all"
                                    data-slug="" data-url="">
                                <i class="mdi mdi-check-circle text-base"></i>
                                <span>{{__('Select Theme')}}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
    (function ($) {
        "use strict";

        // ── CSRF setup ────────────────────────────────────────────────────
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

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

        // Disable scroll effect for short images
        $('.theme-card-img img').on('load', function () {
            if (this.naturalHeight <= 320) {
                $(this).addClass('no-scroll');
            }
        });

        // ── PREVIEW MODAL (click card image or Preview button) ──────────
        $(document).on('click', '.theme-card-click', function (e) {
            e.stopPropagation();
            var $card    = $(this).closest('.theme-card');
            var slug        = $card.attr('data-slug');
            var name        = $card.attr('data-name');
            var desc        = $card.attr('data-description');
            var image       = $card.attr('data-image');
            var url         = $card.attr('data-url');
            var selected    = $card.attr('data-selected');
            var version     = $card.attr('data-version');
            var niche       = $card.attr('data-niche');
            var hasDemo     = $card.attr('data-hasdemo');
            var isNew       = $card.attr('data-newlocation');
            var isPremium   = $card.attr('data-premium');
            var price       = $card.attr('data-price');
            var svgFallback = '{{$svgPlaceholder}}';

            $('#preview_modal_title').text(name);
            $('#preview_modal_name').text(name);
            $('#preview_modal_desc').text(desc || '{{__("No description available.")}}');

            var $img = $('#preview_modal_image');
            $img.attr('src', image).off('error').on('error', function () {
                $(this).attr('src', svgFallback).off('error');
            });

            // Meta badges
            var metaHtml = '';
            if (isPremium === '1') {
                metaHtml += '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold text-white" style="background:linear-gradient(135deg,#f59e0b,#d97706);"><i class="mdi mdi-crown text-xs"></i> Premium — $' + price + '</span>';
            }
            if (version) {
                metaHtml += '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-blue-50 text-blue-600 border border-blue-100"><i class="mdi mdi-tag-outline text-xs"></i> v' + version + '</span>';
            }
            if (niche) {
                metaHtml += '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 text-amber-600 border border-amber-100 capitalize">' + niche + '</span>';
            }
            if (isNew === '1') {
                metaHtml += '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold text-white bg-emerald-500"><i class="mdi mdi-lightning-bolt text-xs"></i> New System</span>';
            }
            if (hasDemo === '1') {
                metaHtml += '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold text-white bg-violet-500"><i class="mdi mdi-database-outline text-xs"></i> {{__("Demo Data")}}</span>';
            }
            $('#preview_modal_meta').html(metaHtml);

            // Status badge
            var $sw = $('#preview_modal_status_wrap');
            if (selected === '1') {
                $sw.html(
                    '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold text-white" style="background:linear-gradient(135deg,#10b981,#059669);box-shadow:0 2px 8px -2px rgba(16,185,129,.4);">' +
                    '<span style="width:7px;height:7px;border-radius:50%;background:#fff;box-shadow:0 0 6px rgba(255,255,255,.8);"></span> {{__("Currently Selected")}}</span>'
                );
            } else {
                $sw.html(
                    '<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gray-100 text-gray-500 text-xs font-bold border border-gray-200">' +
                    '<span style="width:7px;height:7px;border-radius:50%;background:#cbd5e1;"></span> {{__("Not Selected")}}</span>'
                );
            }

            // Select button
            var $btn = $('#theme_select_btn');
            $btn.attr('data-slug', slug).attr('data-url', url);

            if (selected === '1') {
                $btn.attr('class', 'w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all bg-success-soft text-success border border-main cursor-default')
                    .prop('disabled', true);
                $btn.find('span').text('{{__("Currently Selected")}}');
            } else {
                $btn.attr('class', 'w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-primary hover:opacity-90 transition-all')
                    .prop('disabled', false);
                $btn.find('span').text('{{__("Select Theme")}}');
            }

            // Reset setting type
            $('#theme_setting_type').val('');

            openPreview();
        });

        $(document).on('click', '.preview-modal-close, #theme_preview_backdrop', function () {
            closePreview();
        });

        // ── SELECT THEME ──────────────────────────────────────────────────
        $(document).on('click', '#theme_select_btn', function (e) {
            e.preventDefault();
            var el   = $(this);

            if (el.prop('disabled')) return;

            var slug = el.attr('data-slug');
            var url  = el.attr('data-url');
            var theme_setting_type = $('#theme_setting_type').val();

            if (!theme_setting_type) {
                toastr.warning('{{__("Please select a theme setting type.")}}');
                return;
            }

            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    _token: '{{csrf_token()}}',
                    slug: slug,
                    theme_setting_type: theme_setting_type,
                    tenant_default_theme: slug
                },
                beforeSend: function () {
                    el.find('span').text('{{__("Applying...")}}');
                    el.css('opacity', '0.6').css('pointer-events', 'none');
                },
                success: function (data) {
                    el.css('opacity', '1').css('pointer-events', 'auto');

                    if (data.status) {
                        toastr.success(data.msg);
                    } else {
                        toastr.info(data.msg);
                    }

                    setTimeout(function () {
                        window.location.href = '{{route("tenant.admin.theme.all")}}';
                    }, 1000);
                },
                error: function (res) {
                    el.css('opacity', '1').css('pointer-events', 'auto');
                    el.find('span').text('{{__("Select Theme")}}');

                    if (res.responseJSON && res.responseJSON.errors) {
                        $.each(res.responseJSON.errors, function (key, value) {
                            toastr.error(value);
                        });
                    } else if (res.responseJSON && res.responseJSON.message) {
                        toastr.error(res.responseJSON.message);
                    } else {
                        toastr.error('{{__("Something went wrong.")}}');
                    }
                }
            });
        });

        // Escape to close
        $(document).on('keydown', function (e) {
            if (e.key === 'Escape') { closePreview(); }
        });

    })(jQuery);
    </script>
@endsection
