@include('landlord.frontend.partials.header')
<link rel="stylesheet" href="{{asset('assets/new-landlord/css/auth.css')}}">

<div class="auth-canvas">
    <div class="auth-card" style="position:relative">

        {{-- Back to Home --}}
        <a href="{{url('/')}}" title="{{__('Back to Home')}}"
           style="position:absolute;top:1rem;right:1rem;display:flex;align-items:center;justify-content:center;width:2rem;height:2rem;border-radius:9999px;background:#f3f4f6;color:#374253;transition:background .2s"
           onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'">
            <i class="ti tabler-x" style="font-size:1rem;line-height:1"></i>
        </a>

        {{-- Logo --}}
        <div class="auth-logo mb-8">
            {!! render_image_markup_by_attachment_id(get_static_option('site_logo')) !!}
        </div>

        {{-- Title --}}
        <h1 class="font-urbanist font-bold text-secondary mb-1" style="font-size:1.75rem">{{__('Welcome back')}}</h1>
        <p class="text-sm mb-7" style="color:var(--heading-color, #374253)">{{__('Sign in to continue to your dashboard')}}</p>

        <x-error-msg-tw/>
        <x-flash-msg-tw/>

        <form action="" method="post" id="login_form_order_page" class="flex flex-col gap-5">
            <div class="error-wrap"></div>

            {{-- Username --}}
            <div class="flex flex-col gap-1.5">
                <label for="username" class="text-sm font-semibold text-secondary">{{__('Username')}}</label>
                <div class="auth-field relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="ti tabler-user text-gray-400"></i>
                    </div>
                    <input type="text" id="username" name="username"
                           class="w-full pl-11 pr-4 py-3 border border-borderCS rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none"
                           placeholder="{{__('Type your username')}}" required>
                </div>
            </div>

            {{-- Password --}}
            <div class="flex flex-col gap-1.5">
                <div class="flex items-center justify-between">
                    <label for="password" class="text-sm font-semibold text-secondary">{{__('Password')}}</label>
                    <a href="{{route('landlord.user.forget.password')}}" class="text-xs font-medium hover:underline" style="color:var(--main-color-one, #0C4D54)">
                        {{__('Forgot password?')}}
                    </a>
                </div>
                <div class="auth-field relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="ti tabler-lock text-gray-400"></i>
                    </div>
                    <input type="password" id="password" name="password"
                           class="w-full pl-11 pr-12 py-3 border border-borderCS rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none"
                           placeholder="{{__('Password')}}" required>
                    <button type="button" class="toggle-pass absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600" data-target="password">
                        <i class="ti tabler-eye"></i>
                    </button>
                </div>
            </div>

            {{-- Remember --}}
            <div class="flex items-center gap-2">
                <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded cursor-pointer" style="accent-color:var(--main-color-one, #0C4D54)">
                <label for="remember" class="text-sm text-secondary cursor-pointer">{{__('Keep me signed in')}}</label>
            </div>

            {{-- Submit --}}
            <button type="submit" id="login_btn" class="auth-btn">
                {{__('Sign In')}}
            </button>

            @if(moduleExists('SmsGateway') && get_static_option('otp_login_status'))
                <div class="auth-or">{{__('or')}}</div>
                <a href="{{route(route_prefix().'user.login.otp')}}" class="auth-btn-ghost">
                    <i class="ti tabler-device-mobile"></i>
                    {{__('Sign In with OTP')}}
                </a>
            @endif

            <p class="text-center text-sm" style="color:var(--body-color, #6B7280)">
                {{__("Don't have an account?")}}
                <a href="{{route(route_prefix().'user.register')}}" class="font-semibold hover:underline ml-1" style="color:var(--main-color-one, #0C4D54)">{{__('Sign up')}}</a>
            </p>

        </form>
        {{-- Demo Access Note --}}
        @if(url('/') == 'https://nazmart.net' || url('/') == 'https://nazmart.test')
        <div class="mt-8 text-center">
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-gray-50/80 rounded-xl border border-gray-100 shadow-sm">
                <div class="w-1.5 h-1.5 rounded-xl bg-emerald-400"></div>
                <span class="text-[11px] font-medium text-gray-400 uppercase tracking-wide">{{__('Demo')}}</span>
                <div class="w-px h-3 bg-gray-200"></div>
                <div class="flex items-center gap-1.5 text-xs">
                    <span class="text-gray-500">{{__('Username:')}}</span>
                    <span class="font-mono font-semibold text-gray-800">{{__("test")}}</span>
                </div>
                <div class="w-px h-3 bg-gray-200"></div>
                <div class="flex items-center gap-1.5 text-xs">
                    <span class="text-gray-500">{{__('Password:')}}</span>
                    <span class="font-mono font-semibold text-gray-800">{{__("12345678")}}</span>
                </div>
            </div>
        </div>
        @endif


    </div>
</div>

<script src="{{asset('assets/new-landlord/js/plugin.js')}}"></script>
<script src="{{asset('assets/common/js/toastr.min.js')}}"></script>
<x-custom-js.ajax-login/>
<script>
    (function($){
        "use strict";
        $(document).on('click', '.toggle-pass', function(e) {
            e.preventDefault();
            var targetId = $(this).data('target');
            var input = $('#' + targetId);
            var icon = $(this).find('i');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('tabler-eye').addClass('tabler-eye-off');
            } else {
                input.attr('type', 'password');
                icon.removeClass('tabler-eye-off').addClass('tabler-eye');
            }
        });
    })(jQuery);
</script>
</body>
</html>
