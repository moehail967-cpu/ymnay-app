@extends(route_prefix().'admin.admin-master')
@section('title') {{__('Basic Settings')}} @endsection
@section('style')
@endsection

@section('content')

<x-landlord-error-msg/>
<x-landlord-flash-msg/>

<form class="forms-sample" method="post" action="{{route(route_prefix().'admin.general.basic.settings')}}">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">

        {{-- Site Information --}}
        <div class="lg:col-span-2 bg-surface rounded-xl shadow-main border border-main">
            <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-web text-primary text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Site Information')}}</h3>
                    <p class="text-xs text-muted">{{__('Basic details about your site')}}</p>
                </div>
            </div>
            <div class="px-4 sm:px-6 py-5 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Site Title')}}</label>
                        <input type="text" class="lnd-input" name="site_title" value="{{get_static_option('site_title')}}">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Site Tag Line')}}</label>
                        <input type="text" class="lnd-input" name="site_tag_line" value="{{get_static_option('site_tag_line')}}">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Footer Copyright Text')}}</label>
                    <textarea class="lnd-input" name="site_footer_copyright_text" rows="3">{{get_static_option('site_footer_copyright_text')}}</textarea>
                    <p class="text-[11px] text-muted mt-1.5">{{__('{copy} will replace by & and {year} will be replaced by current year.')}}</p>
                </div>

                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Select Timezone')}}</label>
                    @php $list = DateTimeZone::listIdentifiers(); @endphp
                    <select class="lnd-input" name="timezone" id="timezone">
                        @foreach($list as $zone)
                            <option value="{{$zone}}" @selected($zone == get_static_option('timezone'))>{{$zone}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Placeholder Image --}}
        <div class="bg-surface rounded-xl shadow-main border border-main h-fit">
            <div class="px-4 py-3.5 border-b border-main flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-success-soft flex items-center justify-center flex-shrink-0">
                    <i class="mdi mdi-image-outline text-success text-sm"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-dark font-urbanist">{{__('Placeholder Image')}}</h4>
                    <p class="text-[10px] text-muted">{{__('Default fallback image')}}</p>
                </div>
            </div>
            <div class="px-4 py-5 flex flex-col items-center">
                <x-fields.tw-media-upload name="placeholder_image" title="{{__('Placeholder Image')}}" :id="get_static_option('placeholder_image')"/>
            </div>
        </div>

    </div>

    {{-- System Toggles --}}
    <div class="bg-surface rounded-xl shadow-main border border-main mb-5">
        <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background: var(--color-info-bg, #e0f2fe);">
                <i class="mdi mdi-toggle-switch-outline text-base" style="color: var(--color-info, #0ea5e9);"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('System Preferences')}}</h3>
                <p class="text-xs text-muted">{{__('Toggle system behaviors and modes')}}</p>
            </div>
        </div>
        <div class="divide-y divide-main">

            {{-- Dark Mode --}}
            <div class="flex items-center justify-between px-4 sm:px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-secondary flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-weather-night text-sm text-muted"></i>
                    </div>
                    <div>
                        <span class="text-sm font-semibold text-dark">{{__('Dark Mode')}}</span>
                        <p class="text-[11px] text-muted">{{__('Enable dark mode for admin panel')}}</p>
                    </div>
                </div>
                <label class="dr-toggle">
                    <input type="hidden" name="dark_mode_for_admin_panel" value="">
                    <input type="checkbox" name="dark_mode_for_admin_panel" value="on"
                        @checked(!empty(get_static_option('dark_mode_for_admin_panel')))>
                    <span class="dr-toggle-track"></span>
                </label>
            </div>

            {{-- Debug Mode (landlord only) --}}
            @if(!tenant())
            <div class="flex items-center justify-between px-4 sm:px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-secondary flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-bug-outline text-sm text-muted"></i>
                    </div>
                    <div>
                        <span class="text-sm font-semibold text-dark">{{__('Debug Mode')}}</span>
                        <p class="text-[11px] text-muted">{{__('Show detailed error messages')}}</p>
                    </div>
                </div>
                <label class="dr-toggle">
                    <input type="hidden" name="debug_mode" value="">
                    <input type="checkbox" name="debug_mode" value="on"
                        @checked(env('APP_DEBUG'))>
                    <span class="dr-toggle-track"></span>
                </label>
            </div>
            @endif

            {{-- Maintenance Mode --}}
            <div class="flex items-center justify-between px-4 sm:px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: var(--color-warning-bg, #fffbeb);">
                        <i class="mdi mdi-wrench-outline text-sm" style="color: var(--color-warning, #f59e0b);"></i>
                    </div>
                    <div>
                        <span class="text-sm font-semibold text-dark">{{__('Maintenance Mode')}}</span>
                        <p class="text-[11px] text-muted">{{__('Take the site offline temporarily')}}</p>
                    </div>
                </div>
                <label class="dr-toggle">
                    <input type="hidden" name="maintenance_mode" value="">
                    <input type="checkbox" name="maintenance_mode" value="on"
                        @checked(!empty(get_static_option('maintenance_mode')))>
                    <span class="dr-toggle-track"></span>
                </label>
            </div>

            {{-- Guest Order (tenant only) --}}
            @if(tenant())
            <div class="flex items-center justify-between px-4 sm:px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-secondary flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-cart-outline text-sm text-muted"></i>
                    </div>
                    <div>
                        <span class="text-sm font-semibold text-dark">{{__('Guest Order System')}}</span>
                        <p class="text-[11px] text-muted">{{__('Allow orders without customer account')}}</p>
                    </div>
                </div>
                <label class="dr-toggle">
                    <input type="hidden" name="guest_order_system_status" value="">
                    <input type="checkbox" name="guest_order_system_status" value="on"
                        @checked(!empty(get_static_option('guest_order_system_status')))>
                    <span class="dr-toggle-track"></span>
                </label>
            </div>
            @endif

            {{-- Email Verify --}}
            <div class="flex items-center justify-between px-4 sm:px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-secondary flex items-center justify-center flex-shrink-0">
                        <i class="mdi mdi-email-check-outline text-sm text-muted"></i>
                    </div>
                    <div>
                        <span class="text-sm font-semibold text-dark">{{__('User Email Verify')}}</span>
                        <p class="text-[11px] text-muted">{{__('Require email verification on registration')}}</p>
                    </div>
                </div>
                <label class="dr-toggle">
                    <input type="hidden" name="user_email_verify_status" value="">
                    <input type="checkbox" name="user_email_verify_status" value="on"
                        @checked(!empty(get_static_option('user_email_verify_status')))>
                    <span class="dr-toggle-track"></span>
                </label>
            </div>

        </div>
    </div>

    {{-- Login & Register Page Content --}}
    <div class="bg-surface rounded-xl shadow-main border border-main mb-5">
        <div class="px-4 sm:px-6 py-4 border-b border-main rounded-t-xl flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-primary-soft flex items-center justify-center flex-shrink-0">
                <i class="mdi mdi-login text-primary text-base"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-dark font-urbanist">{{__('Login & Register Page')}}</h3>
                <p class="text-xs text-muted">{{__('Customize the text shown on the login and registration pages')}}</p>
            </div>
        </div>
        <div class="px-4 sm:px-6 py-5 space-y-5">

            {{-- Login Page --}}
            <div>
                <h4 class="text-xs font-bold text-dark uppercase tracking-widest mb-3">{{__('Login Page')}}</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Headline')}}</label>
                        <input type="text" class="lnd-input" name="login_page_headline"
                               value="{{get_static_option('login_page_headline')}}"
                               placeholder="{{__('e.g. Your Account')}}">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Subtitle / Welcome Text')}}</label>
                        <input type="text" class="lnd-input" name="login_page_subtitle"
                               value="{{get_static_option('login_page_subtitle')}}"
                               placeholder="{{__('e.g. Welcome back! Sign in to your account.')}}">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Feature 1')}}</label>
                        <input type="text" class="lnd-input" name="login_page_feature_1"
                               value="{{get_static_option('login_page_feature_1')}}"
                               placeholder="{{__('e.g. Member-only pricing')}}">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Feature 2')}}</label>
                        <input type="text" class="lnd-input" name="login_page_feature_2"
                               value="{{get_static_option('login_page_feature_2')}}"
                               placeholder="{{__('e.g. Fast delivery')}}">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Feature 3')}}</label>
                        <input type="text" class="lnd-input" name="login_page_feature_3"
                               value="{{get_static_option('login_page_feature_3')}}"
                               placeholder="{{__('e.g. Full order history')}}">
                    </div>
                </div>
            </div>

            <hr style="border-color:var(--color-border,#e5e7eb);">

            {{-- Register Page --}}
            <div>
                <h4 class="text-xs font-bold text-dark uppercase tracking-widest mb-3">{{__('Register Page')}}</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Headline')}}</label>
                        <input type="text" class="lnd-input" name="register_page_headline"
                               value="{{get_static_option('register_page_headline')}}"
                               placeholder="{{__('e.g. Create Your Account')}}">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Subtitle')}}</label>
                        <input type="text" class="lnd-input" name="register_page_subtitle"
                               value="{{get_static_option('register_page_subtitle')}}"
                               placeholder="{{__('e.g. Get access to exclusive deals and order tracking')}}">
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-2">{{__('Promo / Highlight Text')}}</label>
                    <input type="text" class="lnd-input" name="register_page_promo_text"
                           value="{{get_static_option('register_page_promo_text')}}"
                           placeholder="{{__('e.g. Members get exclusive pricing, next-day delivery, and more.')}}">
                </div>
            </div>

        </div>
    </div>

    {{-- Save --}}
    <div class="flex items-center">
        <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-semibold hover:opacity-90 transition">
            <i class="mdi mdi-content-save-outline text-base"></i> {{__('Save Changes')}}
        </button>
    </div>

</form>

<x-media-upload.tw-markup/>

@endsection

@section('scripts')
    <x-media-upload.tw-js/>
@endsection
