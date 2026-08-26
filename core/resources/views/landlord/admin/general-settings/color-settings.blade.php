@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Color Settings')}} @endsection

@section('style')
    <x-colorpicker.css/>
@endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

{{-- Hero Banner --}}
<div class="featured-card rounded-2xl p-6 sm:p-8 mb-6">
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-white/15 backdrop-blur-sm flex items-center justify-center">
                <i class="las la-palette text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-white font-urbanist">{{__('Color Settings')}}</h2>
                <p class="text-sm text-white/70 mt-0.5">{{__('Customize your site color palette for a unique brand identity')}}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <div class="flex -space-x-1.5">
                <span class="w-5 h-5 rounded-full border-2 border-white/30" style="background:#F04751"></span>
                <span class="w-5 h-5 rounded-full border-2 border-white/30" style="background:#FF805D"></span>
                <span class="w-5 h-5 rounded-full border-2 border-white/30" style="background:#599A8D"></span>
                <span class="w-5 h-5 rounded-full border-2 border-white/30" style="background:#1E88E5"></span>
                <span class="w-5 h-5 rounded-full border-2 border-white/30" style="background:#FABE50"></span>
            </div>
            <span class="text-xs text-white/50 ml-1">{{__('Active Palette')}}</span>
        </div>
    </div>
</div>

<form class="forms-sample" method="post" action="{{route(route_prefix().'admin.general.color.settings')}}">
    @csrf

    @if(is_null(tenant()))
        <div class="space-y-5">

            {{-- Primary Colors --}}
            <div class="color-section-card">
                <div class="color-section-header">
                    <div class="color-section-icon" style="background:var(--color-primary-soft);color:var(--color-primary)">
                        <i class="las la-tint"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-dark font-urbanist">{{__('Primary Colors')}}</h4>
                        <p class="text-[11px] text-muted">{{__('Main brand colors used across the site')}}</p>
                    </div>
                </div>
                <div class="color-section-body">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <x-colorpicker.input value="{{get_static_option('main_color_one','#0D4D54')}}" name="main_color_one" label="{{__('Main Color One')}}"/>
                        <x-colorpicker.input value="{{get_static_option('main_color_two','#93E720')}}" name="main_color_two" label="{{__('Main Color Two')}}"/>
                        <x-colorpicker.input value="{{get_static_option('main_color_three','#599A8D')}}" name="main_color_three" label="{{__('Main Color Three')}}"/>
                        <x-colorpicker.input value="{{get_static_option('main_color_four','#1E88E5')}}" name="main_color_four" label="{{__('Main Color Four')}}"/>
                    </div>
                </div>
            </div>

            {{-- Secondary Colors --}}
            <div class="color-section-card">
                <div class="color-section-header">
                    <div class="color-section-icon" style="background:var(--color-warning-bg);color:var(--color-warning)">
                        <i class="las la-swatchbook"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-dark font-urbanist">{{__('Secondary Colors')}}</h4>
                        <p class="text-[11px] text-muted">{{__('Accent and supporting color tones')}}</p>
                    </div>
                </div>
                <div class="color-section-body">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <x-colorpicker.input value="{{get_static_option('secondary_color','#FFFFFF')}}" name="secondary_color" label="{{__('Secondary Color One')}}"/>
                        <x-colorpicker.input value="{{get_static_option('secondary_color_two','#373F53')}}" name="secondary_color_two" label="{{__('Secondary Color Two')}}"/>
                    </div>
                </div>
            </div>

            {{-- Section Backgrounds --}}
            <div class="color-section-card">
                <div class="color-section-header">
                    <div class="color-section-icon" style="background:var(--color-info-bg);color:var(--color-info)">
                        <i class="las la-layer-group"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-dark font-urbanist">{{__('Section Backgrounds')}}</h4>
                        <p class="text-[11px] text-muted">{{__('Background colors for different page sections')}}</p>
                    </div>
                </div>
                <div class="color-section-body">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        <x-colorpicker.input value="{{get_static_option('section_bg_1','#FFFFFF')}}" name="section_bg_1" label="{{__('Background One')}}"/>
                        <x-colorpicker.input value="{{get_static_option('section_bg_2','#FFF6EE')}}" name="section_bg_2" label="{{__('Background Two')}}"/>
                        <x-colorpicker.input value="{{get_static_option('section_bg_3','#F4F8FB')}}" name="section_bg_3" label="{{__('Background Three')}}"/>
                        <x-colorpicker.input value="{{get_static_option('section_bg_4','#F2F3FB')}}" name="section_bg_4" label="{{__('Background Four')}}"/>
                        <x-colorpicker.input value="{{get_static_option('section_bg_5','#F9F5F2')}}" name="section_bg_5" label="{{__('Background Five')}}"/>
                        <x-colorpicker.input value="{{get_static_option('section_bg_6','#E5EFF8')}}" name="section_bg_6" label="{{__('Background Six')}}"/>
                    </div>
                </div>
            </div>

            {{-- Text & Accent Colors --}}
            <div class="color-section-card">
                <div class="color-section-header">
                    <div class="color-section-icon" style="background:var(--color-success-bg);color:var(--color-success)">
                        <i class="las la-font"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-dark font-urbanist">{{__('Text & Accent Colors')}}</h4>
                        <p class="text-[11px] text-muted">{{__('Typography and highlight color definitions')}}</p>
                    </div>
                </div>
                <div class="color-section-body">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        <x-colorpicker.input value="{{get_static_option('heading_color','#000000')}}" name="heading_color" label="{{__('Heading Color')}}"/>
                        <x-colorpicker.input value="{{get_static_option('body_color','#666666')}}" name="body_color" label="{{__('Body Color')}}"/>
                        <x-colorpicker.input value="{{get_static_option('light_color','#FFFFFF')}}" name="light_color" label="{{__('Light Color')}}"/>
                        <x-colorpicker.input value="{{get_static_option('extra_light_color','#888888')}}" name="extra_light_color" label="{{__('Extra Light Color')}}"/>
                        <x-colorpicker.input value="{{get_static_option('review_color','#FABE50')}}" name="review_color" label="{{__('Review Color')}}"/>
                        <x-colorpicker.input value="{{get_static_option('new_color','#5AB27E')}}" name="new_color" label="{{__('New Badge Color')}}"/>
                    </div>
                </div>
            </div>

            {{-- Admin Panel Colors --}}
            <div class="color-section-card">
                <div class="color-section-header">
                    <div class="color-section-icon" style="background:var(--color-primary-soft);color:var(--color-primary)">
                        <i class="las la-sliders-h"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-dark font-urbanist">{{__('Admin Panel Colors')}}</h4>
                        <p class="text-[11px] text-muted">{{__('Brand & UI colors for the landlord admin panel')}}</p>
                    </div>
                </div>
                <div class="color-section-body">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        <x-colorpicker.input value="{{get_static_option('admin_primary_color','#1A4A3E')}}" name="admin_primary_color" label="{{__('Primary Color')}}"/>
                        <x-colorpicker.input value="{{get_static_option('admin_primary_hover_color','#E6F7EF')}}" name="admin_primary_hover_color" label="{{__('Primary Hover')}}"/>
                        <x-colorpicker.input value="{{get_static_option('admin_primary_soft_color','#F1FFFB')}}" name="admin_primary_soft_color" label="{{__('Primary Soft (Badge BG)')}}"/>
                        <x-colorpicker.input value="{{get_static_option('admin_primary_soft_hover_color','#D8F1ED')}}" name="admin_primary_soft_hover_color" label="{{__('Primary Soft Hover')}}"/>
                        <x-colorpicker.input value="{{get_static_option('admin_bg_base','#EEF1F3')}}" name="admin_bg_base" label="{{__('Page Background')}}"/>
                        <x-colorpicker.input value="{{get_static_option('admin_surface_bg','#FFFFFF')}}" name="admin_surface_bg" label="{{__('Card / Surface Background')}}"/>
                        <x-colorpicker.input value="{{get_static_option('admin_sidebar_bg','#FFFFFF')}}" name="admin_sidebar_bg" label="{{__('Sidebar Background')}}"/>
                        <x-colorpicker.input value="{{get_static_option('admin_secondary_bg','#EEF1F3')}}" name="admin_secondary_bg" label="{{__('Secondary Background')}}"/>
                        <x-colorpicker.input value="{{get_static_option('admin_muted_bg','#EDF9F9')}}" name="admin_muted_bg" label="{{__('Muted Background')}}"/>
                        <x-colorpicker.input value="{{get_static_option('admin_brand_text_color','#21B777')}}" name="admin_brand_text_color" label="{{__('Brand Text Color')}}"/>
                        <x-colorpicker.input value="{{get_static_option('admin_border_color','#F1F7F7')}}" name="admin_border_color" label="{{__('Border Color')}}"/>
                        <x-colorpicker.input value="{{get_static_option('admin_border_subtle_color','#F8F8F8')}}" name="admin_border_subtle_color" label="{{__('Border Subtle')}}"/>
                        <x-colorpicker.input value="{{get_static_option('admin_border_hover_color','#D8F1ED')}}" name="admin_border_hover_color" label="{{__('Border Hover')}}"/>
                        <x-colorpicker.input value="{{get_static_option('admin_sidebar_hover_color','#F8FAFC')}}" name="admin_sidebar_hover_color" label="{{__('Sidebar Hover')}}"/>
                    </div>
                </div>
            </div>

            {{-- Admin Text Colors --}}
            <div class="color-section-card">
                <div class="color-section-header">
                    <div class="color-section-icon" style="background:var(--color-info-bg);color:var(--color-info)">
                        <i class="las la-font"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-dark font-urbanist">{{__('Admin Text Colors')}}</h4>
                        <p class="text-[11px] text-muted">{{__('Typography colors for the admin panel')}}</p>
                    </div>
                </div>
                <div class="color-section-body">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <x-colorpicker.input value="{{get_static_option('admin_text_main_color','#000000')}}" name="admin_text_main_color" label="{{__('Main Text')}}"/>
                        <x-colorpicker.input value="{{get_static_option('admin_text_dark_color','#000000')}}" name="admin_text_dark_color" label="{{__('Dark Text')}}"/>
                        <x-colorpicker.input value="{{get_static_option('admin_text_muted_color','#64748B')}}" name="admin_text_muted_color" label="{{__('Muted Text')}}"/>
                        <x-colorpicker.input value="{{get_static_option('admin_text_subtle_color','#64748B')}}" name="admin_text_subtle_color" label="{{__('Subtle Text')}}"/>
                    </div>
                </div>
            </div>

        </div>
    @endif

    @if(tenant())
        <div class="color-section-card">
            <div class="color-section-header">
                <div class="color-section-icon" style="background:var(--color-primary-soft);color:var(--color-primary)">
                    <i class="las la-tint"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-dark font-urbanist">{{__('Theme Colors')}}</h4>
                    <p class="text-[11px] text-muted">{{__('Customize tenant theme color scheme')}}</p>
                </div>
            </div>
            <div class="color-section-body">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @include('landlord.admin.general-settings.tenant.theme.color-settings')
                </div>
            </div>
        </div>
    @endif

    {{-- Save Button --}}
    <div class="mt-6 flex items-center gap-3">
        <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-white text-sm font-semibold transition-all duration-200 hover:shadow-lg"
                style="background:var(--color-primary);box-shadow:0 4px 14px -3px rgba(26,92,78,0.3)">
            <i class="las la-save text-base"></i> {{__('Save Changes')}}
        </button>
        <button type="reset"
                class="inline-flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-medium transition border"
                style="border-color:var(--color-border-main);color:var(--color-text-muted);background:var(--color-bg-surface)">
            <i class="las la-undo-alt text-base"></i> {{__('Reset')}}
        </button>
    </div>
</form>

@endsection

@section('scripts')
    <x-colorpicker.js/>
@endsection
