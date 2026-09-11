@extends('landlord.admin.admin-master')
@section('title') {{__('Price Plan Settings')}} @endsection

@section('style')
    <link rel="stylesheet" href="{{global_asset('assets/common/css/select2.min.css')}}">
@endsection

@section('content')

<x-landlord-flash-msg/>
<x-landlord-error-msg/>

<form action="{{route('landlord.admin.price.plan.settings')}}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- Main Content (Left) --}}
        <div class="lg:col-span-9 space-y-6">

            {{-- General Settings Card --}}
            <div class="bg-surface rounded-xl shadow-main border border-main">
                <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-cog-outline text-primary text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Price Plan Settings')}}</h3>
                        <p class="text-xs text-muted">{{__('Configure pricing plan defaults and options')}}</p>
                    </div>
                </div>

                <div class="p-4 sm:p-6 space-y-5">

                    {{-- Expiration Mail Days --}}
                    @php
                        $fileds = [
                            1 => __('One Day'),
                            2 => __('Two Day'),
                            3 => __('Three Day'),
                            4 => __('Four Day'),
                            5 => __('Five Day'),
                            6 => __('Six Day'),
                            7 => __('Seven Day')
                        ];
                    @endphp
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">
                            {{__('Expiration Mail Alert Days')}}
                        </label>
                        <select name="package_expire_notify_mail_days[]" class="form-control expiration_dates w-full"
                                multiple="multiple">
                            @foreach($fileds as $key => $field)
                                @php
                                    $package_expire_notify_mail_days = get_static_option('package_expire_notify_mail_days');
                                    $decoded = json_decode($package_expire_notify_mail_days) ?? [];
                                @endphp
                                <option value="{{$key}}"
                                    @foreach($decoded as $day)
                                        {{$day == $key ? 'selected' : ''}}
                                    @endforeach
                                >{{__($field)}}</option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-muted mt-1.5">{{__('Select how many days earlier expiration mail alert will be sent')}}</p>
                    </div>

                    {{-- Renew Date Calculation --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Renew Date Calculation Method')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                            <i class="mdi mdi-calendar-sync text-lg text-primary"></i>
                            <select name="renew_date_calculation" id="renew_date_calculation" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                                <option value="append_to_old" {{ get_static_option('renew_date_calculation') == 'append_to_old' ? 'selected' : '' }}>{{ __('Append to Old Expire Date') }}</option>
                                <option value="start_from_today" {{ get_static_option('renew_date_calculation') == 'start_from_today' ? 'selected' : '' }}>{{ __('Start from Today') }}</option>
                            </select>
                            <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                        </div>
                    </div>

                    {{-- Default Theme --}}
                    <div>
                        @php
                            $themes = getAllThemeData();
                        @endphp
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Default Theme')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                            <i class="mdi mdi-palette-outline text-lg text-primary"></i>
                            <select name="default_theme" id="default-theme" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                                @foreach($themes as $theme)
                                    @php
                                        $custom_theme_name = get_static_option_central($theme->slug.'_theme_name');
                                    @endphp
                                    <option value="{{$theme->slug}}" {{get_static_option('default_theme') == $theme->slug ? 'selected' : ''}}>
                                        {{ !empty($custom_theme_name) ? $custom_theme_name : $theme->name }}
                                    </option>
                                @endforeach
                            </select>
                            <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                        </div>
                    </div>

                    {{-- Default Language --}}
                    <div>
                        @php
                            $languages = [
                                'en_GB' => 'English (UK)',
                                'ar' => 'العربية',
                                'hi_IN' => 'हिन्दी',
                                'tr_TR' => 'Türkçe',
                                'it_IT' => 'Italiano',
                                'pt_PT' => 'Português',
                                'pt_BR' => 'Português do Brasil',
                                'pt_AO' => 'Português de Angola'
                            ];
                        @endphp
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Default Languages')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-1 focus-within:border-primary transition">
                            <i class="mdi mdi-translate text-lg text-primary"></i>
                            <select name="default_language" id="default-language" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0 appearance-none cursor-pointer">
                                @foreach($languages as $index => $language)
                                    <option value="{{$index}}" {{get_static_option_central('default_language') == $index ? 'selected' : ''}}>{{ $language }}</option>
                                @endforeach
                            </select>
                            <i class="mdi mdi-chevron-down text-base text-primary pointer-events-none"></i>
                        </div>
                    </div>

                    {{-- Zero Plan Limit --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Zero Price Plan Purchase Limit (Free Plan)')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-numeric-0-box-outline text-lg text-primary"></i>
                            <input type="number" name="zero_plan_limit" id="zero-plan-limit"
                                   placeholder="{{__('Example: 1')}}"
                                   value="{{get_static_option('zero_plan_limit')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                        <p class="text-[11px] text-muted mt-1.5">{{__('Purchase limitation for a price plan which price is zero')}}</p>
                    </div>

                    {{-- Tenant Admin Username --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Tenant Admin Default Username')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-account-outline text-lg text-primary"></i>
                            <input type="text" name="tenant_admin_default_username"
                                   value="{{get_static_option_central('tenant_admin_default_username')}}"
                                   placeholder="{{__('Default username')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>

                    {{-- Tenant Admin Password --}}
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Tenant Admin Default Password')}}</label>
                        <div class="flex items-center gap-2.5 bg-secondary border border-main rounded-xl px-4 py-2.5 focus-within:border-primary transition">
                            <i class="mdi mdi-lock-outline text-lg text-primary"></i>
                            <input type="text" name="tenant_admin_default_password"
                                   value="{{get_static_option_central('tenant_admin_default_password')}}"
                                   placeholder="{{__('Default password')}}"
                                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0">
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Sidebar (Right) --}}
        <div class="lg:col-span-3">
            <div class="bg-surface rounded-xl shadow-main border border-main overflow-hidden sticky top-4 space-y-0">

                {{-- Default Logo Section --}}
                <div class="px-4 py-4 border-b border-main">
                    <h4 class="text-xs font-bold text-dark uppercase tracking-widest">{{__('Default Logo')}}</h4>
                </div>

                <div class="p-4 space-y-5">
                    <div id="drag-drop-form">
                        <div id="preview" class="flex gap-3 items-start">
                            <div class="tw-upload-area" id="uploadfile">
                                <span class="px-2">
                                    <i class="mdi mdi-cloud-upload-outline text-2xl text-primary block mb-1"></i>
                                    {{__('Drag & Drop or Click')}}
                                </span>
                            </div>
                        </div>

                        <input type="hidden" name="has_file" id="has-file" value="{{get_static_option_central('plan_default_logo') ? 'yes' : 'no'}}">
                        <input style="display:none;" name="file_input" id="file-input" type="file" multiple>
                    </div>
                    <p class="text-[11px] text-muted">{{__('Upload default logo for plans')}}</p>

                    {{-- Submit --}}
                    <button type="submit" id="update"
                            class="w-full inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
                        <i class="mdi mdi-content-save-outline text-base"></i> {{__('Update Changes')}}
                    </button>
                </div>
            </div>
        </div>

    </div>

</form>

@endsection

@section('scripts')
    <script src="{{global_asset('assets/common/js/select2.min.js')}}"></script>
    <script>
    (function ($) {
        "use strict";

        $(document).ready(function () {
            $('.expiration_dates').select2({
                placeholder: '{{ __("Select days") }}',
                allowClear: true,
                dropdownParent: $('body')
            });
        });

        // ── Drag & Drop Upload ───────────────────────────────────────
        $(function () {
            // Drag over
            $('.tw-upload-area').on('dragover', function (e) {
                e.stopPropagation();
                e.preventDefault();
                $(this).css('border-color', 'var(--color-primary, #6366f1)');
            });

            // Open file selector on div click
            $("#uploadfile").on('click', function () {
                $("#file-input").click();
            });

            // Drop files on upload area
            $('.tw-upload-area').on('drop', function (e) {
                e.stopPropagation();
                e.preventDefault();
                e.dataTransfer = e.originalEvent.dataTransfer;
                $(this).css('border-color', '');
                document.querySelector('#file-input').files = e.originalEvent.dataTransfer.files;
                $('#file-input').trigger('change');
            });

            // Trigger previewImages on file input value change
            $('#file-input').on('change', function () {
                previewImages();
            });
        });

        function previewImages() {
            var file_length = 1;
            var preview = document.querySelector('#preview');
            if (document.querySelector('#file-input').files) {
                [].forEach.call(document.querySelector('#file-input').files, readAndPreview);
            }

            function readAndPreview(file) {
                if (!/\.(jpe?g|png|gif|webp)$/i.test(file.name)) {
                    return toastr.error(file.name + " is not an image");
                }

                var reader = new FileReader();
                reader.addEventListener("load", function () {
                    var image = $(
                        '<div class="tw-upload-preview">' +
                        '<img src="' + this.result + '" alt="">' +
                        '<button type="button" class="tw-preview-remove">&times;</button>' +
                        '</div>'
                    );

                    if ($("#preview img").length < file_length) {
                        $('#preview').append(image);
                    }

                    $('.tw-preview-remove').click(function () {
                        $(this).parent('.tw-upload-preview').remove();
                    });
                });

                reader.readAsDataURL(file);
            }
        }

        // ── Existing Image Preview ───────────────────────────────────
        var prevImage = `{{get_static_option_central('plan_default_logo')}}`;
        if (prevImage !== '') {
            var fullPath = `{{global_asset('assets/landlord/uploads/default-uploads')}}`;
            fullPath += '/' + prevImage;

            var image = $(
                '<div class="tw-upload-preview">' +
                '<img src="' + fullPath + '" alt="">' +
                '<button type="button" class="tw-preview-remove">&times;</button>' +
                '</div>'
            );

            $('#preview').append(image);

            $('.tw-preview-remove').click(function () {
                $(this).parent('.tw-upload-preview').remove();
                $('#has-file').val('no');
            });
        }

    })(jQuery);
    </script>
@endsection
