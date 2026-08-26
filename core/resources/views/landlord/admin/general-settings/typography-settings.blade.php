@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Typography Settings')}} @endsection

@section('style')
    <link rel="stylesheet" href="{{global_asset('assets/landlord/admin/css/nice-select.css')}}">
@endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

<form class="forms-sample" method="post" action="{{route(route_prefix().'admin.general.typography.settings')}}">
    @csrf

    @if(is_null(tenant()))
        <div class="typo_admin grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
            {{-- Body Font --}}
            <div class="bg-surface rounded-xl shadow-main border border-main">
                <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                        <i class="las la-font text-primary text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Body Font')}}</h3>
                        <p class="text-xs text-muted">{{__('Main font used for paragraphs and body text')}}</p>
                    </div>
                </div>
                <div class="px-4 sm:px-6 py-5 space-y-5">
                    <div>
                        <label class="lnd-label">{{__('Font Family')}}</label>
                        <select class="form-control nice-select wide" name="body_font_family" id="body_font_family">
                            @foreach($google_fonts as $font_family => $font_variant)
                                <option value="{{$font_family}}" @selected($font_family == get_static_option('body_font_family'))>{{$font_family}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="lnd-label">{{__('Font Variant')}}</label>
                        @php
                            $font_family_selected = get_static_option('body_font_family') ?? get_static_option('body_font_family');
                            $get_font_family_variants = property_exists($google_fonts, $font_family_selected) ? (array) $google_fonts->$font_family_selected : ['variants' => array('regular')];
                        @endphp
                        <select class="form-control wide" multiple id="body_font_variant" name="body_font_variant[]" size="5">
                            @foreach($get_font_family_variants['variants'] as $variant)
                                @php
                                    $selected_variant = !empty(get_static_option('body_font_variant')) ? unserialize(get_static_option('body_font_variant')) : [];
                                @endphp
                                <option value="{{$variant}}" @selected(in_array($variant, $selected_variant))>{{str_replace(['0,','1,'],['','i'],$variant)}}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-muted mt-1.5">{{__('Select which font weights to load')}}</p>
                    </div>
                </div>
            </div>

            {{-- Heading Font --}}
            <div class="bg-surface rounded-xl shadow-main border border-main">
                <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-info-soft flex items-center justify-center flex-shrink-0">
                            <i class="las la-heading text-info text-base"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Heading Font')}}</h3>
                            <p class="text-xs text-muted">{{__('Font for h1-h6 heading tags')}}</p>
                        </div>
                    </div>
                    <label class="dr-toggle">
                        <input type="checkbox" name="heading_font" id="heading_font"
                            @checked(!empty(get_static_option('heading_font')))>
                        <span class="dr-toggle-track"></span>
                    </label>
                </div>
                <div class="px-4 sm:px-6 py-5 space-y-5" id="heading_font_fields">
                    <div>
                        <label class="lnd-label">{{__('Font Family')}}</label>
                        <select class="form-control nice-select wide" name="heading_font_family" id="heading_font_family">
                            @foreach($google_fonts as $font_family => $font_variant)
                                <option value="{{$font_family}}" @selected($font_family == get_static_option('heading_font_family'))>{{$font_family}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="lnd-label">{{__('Font Variant')}}</label>
                        @php
                            $font_family_selected = get_static_option('heading_font_family') ?? '';
                            $get_font_family_variants = property_exists($google_fonts, $font_family_selected) ? (array) $google_fonts->$font_family_selected : ['variants' => array('regular')];
                        @endphp
                        <select class="form-control wide" multiple name="heading_font_variant[]" id="heading_font_variant" size="5">
                            @foreach($get_font_family_variants['variants'] as $variant)
                                @php
                                    $selected_variant = !empty(get_static_option('heading_font_variant')) ? unserialize(get_static_option('heading_font_variant')) : [];
                                @endphp
                                <option value="{{$variant}}" @selected(in_array($variant, $selected_variant))>{{str_replace(['0,','1,'],['','i'],$variant)}}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-muted mt-1.5">{{__('Select which font weights to load')}}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(tenant())
        @include('landlord.admin.general-settings.tenant.theme.typography-settings')
    @endif

    <div class="flex justify-end">
        <button type="submit"
                class="inline-flex items-center gap-1.5 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
            <i class="las la-save"></i> {{__('Save Changes')}}
        </button>
    </div>
</form>

@endsection

@section('scripts')
    <script src="{{global_asset('assets/landlord/admin/js/jquery.nice-select.min.js')}}"></script>
    <script>
        (function($){
            "use strict";
            $(document).ready(function(){
                // Init nice-select
                if($('.nice-select').length > 0){
                    $('.nice-select').niceSelect();
                }

                // Toggle heading font fields
                var $headingFields = $('#heading_font_fields');
                var $headingCheck = $('#heading_font');

                function toggleHeadingFields() {
                    if ($headingCheck.prop('checked')) {
                        $headingFields.slideDown(200);
                    } else {
                        $headingFields.slideUp(200);
                    }
                }
                toggleHeadingFields();
                $headingCheck.on('change', toggleHeadingFields);

                // AJAX font variant loader for body font
                $(document).on('change', '.body_font_family', function (e) {
                    e.preventDefault();
                    var themeNum = $(this).data('theme');
                    var fontFamily = $(this).val();
                    $.ajax({
                        url: "{{route(route_prefix().'admin.general.typography.single')}}",
                        type: "POST",
                        data: {
                            _token: "{{csrf_token()}}",
                            font_family: fontFamily,
                            theme: themeNum
                        },
                        success: function (data) {
                            var theme = data.theme;
                            var variantSelector = $('.body_font_variant_' + theme);
                            variantSelector.html('');
                            $.each(data.decoded_fonts.variants, function (index, value) {
                                var nameval = value.replace('0,', '');
                                nameval = nameval.replace('1,', 'i');
                                variantSelector.append('<option value="' + value + '">' + nameval + '</option>');
                            });
                        }
                    });
                });

                // AJAX font variant loader for heading font
                $(document).on('change', '.heading_font_family', function (e) {
                    e.preventDefault();
                    var themeNum = $(this).data('theme');
                    var fontFamily = $(this).val();
                    $.ajax({
                        url: "{{route(route_prefix().'admin.general.typography.single')}}",
                        type: "POST",
                        data: {
                            _token: "{{csrf_token()}}",
                            font_family: fontFamily,
                            theme: themeNum
                        },
                        success: function (data) {
                            var theme = data.theme;
                            var variantSelector = $('.heading_font_variant_' + theme);
                            variantSelector.html('');
                            $.each(data.decoded_fonts.variants, function (index, value) {
                                var nameval = value.replace('0,', '');
                                nameval = nameval.replace('1,', 'i');
                                variantSelector.append('<option value="' + value + '">' + nameval + '</option>');
                            });
                        }
                    });
                });

                // Tenant theme toggles
                let switch_one = $('input[data-theme=theme_one]');
                let switch_two = $('input[data-theme=theme_two]');
                let switch_three = $('input[data-theme=theme_three]');

                if(switch_one.length && !switch_one.prop('checked')){
                    let theme = switch_one.data('theme');
                    $('select[name=heading_font_family_'+theme+'], .heading_font_variant_'+theme+'').parent().fadeOut();
                }
                if(switch_two.length && !switch_two.prop('checked')) {
                    let theme = switch_two.data('theme');
                    $('select[name=heading_font_family_'+theme+'], .heading_font_variant_'+theme+'').parent().fadeOut();
                }
                if(switch_three.length && !switch_three.prop('checked')) {
                    let theme = switch_three.data('theme');
                    $('select[name=heading_font_family_'+theme+'], .heading_font_variant_'+theme+'').parent().fadeOut();
                }

                $(document).on('change', 'input.heading_font', function (e) {
                    let theme = $(this).data('theme');
                    let themeName = theme.replace('heading_font_', '');
                    var dependendFields = $('select[name=heading_font_family_'+themeName+'], .heading_font_variant_'+themeName+'');
                    if (!$(this).prop('checked')) {
                        dependendFields.parent().fadeOut();
                    } else {
                        dependendFields.parent().fadeIn();
                    }
                });
            });
        }(jQuery));
    </script>
@endsection
