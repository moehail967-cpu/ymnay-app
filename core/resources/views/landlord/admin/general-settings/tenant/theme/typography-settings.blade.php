@php
    $theme = getSelectedThemeData();
    $theme_name = $theme->name;
    $suffix = getSelectedThemeSlug();
@endphp

<div class="typo_admin grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">

    {{-- Body Font --}}
    <div class="bg-surface rounded-xl shadow-main border border-main">
        <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="las la-font text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Body Font')}} — {{__($theme_name)}}</h3>
                <p class="text-xs text-muted">{{__('Main font used for paragraphs and body text')}}</p>
            </div>
        </div>
        <div class="px-4 sm:px-6 py-5 space-y-5">
            <div>
                <label class="lnd-label">{{__('Font Family')}}</label>
                <select class="form-control nice-select wide body_font_family" name="body_font_family_{{$suffix}}" id="body_font_family_{{$suffix}}" data-theme="{{$suffix}}">
                    @foreach($google_fonts as $font_family => $font_variant)
                        <option value="{{$font_family}}" @selected($font_family == get_static_option('body_font_family_'.$suffix))>{{$font_family}}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="lnd-label">{{__('Font Variant')}}</label>
                @php
                    $body_font_selected = get_static_option('body_font_family_'.$suffix) ?? '';
                    $body_font_variants = property_exists($google_fonts, $body_font_selected) ? (array) $google_fonts->$body_font_selected : ['variants' => ['regular']];
                    $body_selected_variant = !empty(get_static_option('body_font_variant_'.$suffix)) ? unserialize(get_static_option('body_font_variant_'.$suffix)) : [];
                @endphp
                <div class="body_font_variant_{{$suffix}} flex flex-wrap gap-2 p-3 border border-main rounded-lg bg-white">
                    @foreach($body_font_variants['variants'] as $variant)
                        @php $label = str_replace(['0,','1,'],['','i'],$variant); @endphp
                        <label class="flex items-center gap-1.5 text-xs text-dark cursor-pointer select-none">
                            <input type="checkbox" name="body_font_variant_{{$suffix}}[]" value="{{$variant}}"
                                class="rounded border-main accent-primary"
                                @checked(in_array($variant, $body_selected_variant))>
                            {{$label}}
                        </label>
                    @endforeach
                </div>
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
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Heading Font')}} — {{__($theme_name)}}</h3>
                    <p class="text-xs text-muted">{{__('Font for h1-h6 heading tags')}}</p>
                </div>
            </div>
            <label class="dr-toggle">
                <input type="checkbox" name="heading_font_{{$suffix}}" class="heading_font"
                    @checked(!empty(get_static_option('heading_font_'.$suffix)))
                    id="heading_font_{{$suffix}}" data-theme="{{$suffix}}">
                <span class="dr-toggle-track"></span>
            </label>
        </div>
        <div class="px-4 sm:px-6 py-5 space-y-5">
            <div>
                <label class="lnd-label">{{__('Font Family')}}</label>
                <select class="form-control nice-select wide heading_font_family" name="heading_font_family_{{$suffix}}" id="heading_font_family_{{$suffix}}" data-theme="{{$suffix}}">
                    @foreach($google_fonts as $font_family => $font_variant)
                        <option value="{{$font_family}}" @selected($font_family == get_static_option('heading_font_family_'.$suffix))>{{$font_family}}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="lnd-label">{{__('Font Variant')}}</label>
                @php
                    $heading_font_selected = get_static_option('heading_font_family_'.$suffix) ?? '';
                    $heading_font_variants = property_exists($google_fonts, $heading_font_selected) ? (array) $google_fonts->$heading_font_selected : ['variants' => ['regular']];
                    $heading_selected_variant = !empty(get_static_option('heading_font_variant_'.$suffix)) ? unserialize(get_static_option('heading_font_variant_'.$suffix)) : [];
                @endphp
                <div class="heading_font_variant_{{$suffix}} flex flex-wrap gap-2 p-3 border border-main rounded-lg bg-white">
                    @foreach($heading_font_variants['variants'] as $variant)
                        @php $label = str_replace(['0,','1,'],['','i'],$variant); @endphp
                        <label class="flex items-center gap-1.5 text-xs text-dark cursor-pointer select-none">
                            <input type="checkbox" name="heading_font_variant_{{$suffix}}[]" value="{{$variant}}"
                                class="rounded border-main accent-primary"
                                @checked(in_array($variant, $heading_selected_variant))>
                            {{$label}}
                        </label>
                    @endforeach
                </div>
                <p class="text-xs text-muted mt-1.5">{{__('Select which font weights to load')}}</p>
            </div>
        </div>
    </div>

</div>
