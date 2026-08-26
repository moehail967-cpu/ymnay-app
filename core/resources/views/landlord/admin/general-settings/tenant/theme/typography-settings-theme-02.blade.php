<div class="col">
    <h4 class="text-sm font-bold text-dark font-urbanist mb-4">{{__('Theme Two')}}</h4>

    <div class="space-y-5">
        <div>
            <label class="lnd-label">{{__('Font Family')}}</label>
            <select class="form-control nice-select wide body_font_family" name="body_font_family_{{$suffix}}" id="body_font_family" data-theme="{{$suffix}}">
                @foreach($google_fonts as $font_family => $font_variant)
                    <option value="{{$font_family}}" @selected($font_family == get_static_option('body_font_family_'.$suffix))>{{$font_family}}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="lnd-label">{{__('Font Variant')}}</label>
            @php
                $font_family_selected = get_static_option('body_font_family_'.$suffix) ?? get_static_option('body_font_family_'.$suffix);
                $get_font_family_variants = property_exists($google_fonts, $font_family_selected) ? (array) $google_fonts->$font_family_selected : ['variants' => array('regular')];
            @endphp
            <select class="form-control wide body_font_variant_{{$suffix}}" multiple id="body_font_variant" name="body_font_variant_{{$suffix}}[]" size="5">
                @foreach($get_font_family_variants['variants'] as $variant)
                    @php
                        $selected_variant = !empty(get_static_option('body_font_variant_'.$suffix)) ? unserialize(get_static_option('body_font_variant_'.$suffix)) : [];
                    @endphp
                    <option value="{{$variant}}" @selected(in_array($variant, $selected_variant))>{{str_replace(['0,','1,'],['','i'],$variant)}}</option>
                @endforeach
            </select>
        </div>

        <div class="pt-4 border-t border-main">
            <h4 class="text-sm font-bold text-dark font-urbanist mb-4">{{__('Heading Typography Settings')}}</h4>

            <div class="flex items-center justify-between mb-5">
                <div>
                    <label class="lnd-label mb-0">{{__('Heading Font')}}</label>
                    <p class="text-xs text-muted">{{__('Use different font family for heading tags (h1-h6)')}}</p>
                </div>
                <label class="dr-toggle">
                    <input type="checkbox" name="heading_font_{{$suffix}}" class="heading_font"
                        @checked(!empty(get_static_option('heading_font_'.$suffix)))
                        id="heading_font" data-theme="{{$suffix}}">
                    <span class="dr-toggle-track"></span>
                </label>
            </div>

            <div class="space-y-5">
                <div>
                    <label class="lnd-label">{{__('Font Family')}}</label>
                    <select class="form-control nice-select wide heading_font_family" name="heading_font_family_{{$suffix}}" id="heading_font_family" data-theme="{{$suffix}}">
                        @foreach($google_fonts as $font_family => $font_variant)
                            <option value="{{$font_family}}" @selected($font_family == get_static_option('heading_font_family_'.$suffix))>{{$font_family}}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="lnd-label">{{__('Font Variant')}}</label>
                    @php
                        $font_family_selected = get_static_option('heading_font_family_'.$suffix) ?? '';
                        $get_font_family_variants = property_exists($google_fonts, $font_family_selected) ? (array) $google_fonts->$font_family_selected : ['variants' => array('regular')];
                    @endphp
                    <select class="form-control wide heading_font_variant_{{$suffix}}" multiple name="heading_font_variant_{{$suffix}}[]" id="heading_font_variant" size="5">
                        @foreach($get_font_family_variants['variants'] as $variant)
                            @php
                                $selected_variant = !empty(get_static_option('heading_font_variant_'.$suffix)) ? unserialize(get_static_option('heading_font_variant_'.$suffix)) : [];
                            @endphp
                            <option value="{{$variant}}" @selected(in_array($variant, $selected_variant))>{{str_replace(['0,','1,'],['','i'],$variant)}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
