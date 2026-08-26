@include('landlord.frontend.partials.header')
<link rel="stylesheet" href="{{asset('assets/new-landlord/css/auth.css')}}">

<div class="auth-canvas">
    <div class="auth-card">

        {{-- Logo --}}
        <div class="auth-logo mb-8">
            {!! render_image_markup_by_attachment_id(get_static_option('site_logo')) !!}
        </div>

        {{-- Icon badge --}}
        <div class="auth-icon-badge">
            <i class="ti tabler-lock-open" style="font-size:1.5rem; color:#0C4D54"></i>
        </div>

        {{-- Title --}}
        <h1 class="font-urbanist font-bold text-secondary text-center mb-1" style="font-size:1.625rem">{{__('Forgot your password?')}}</h1>
        <p class="text-sm text-center mb-7" style="color:#374253">
            {{__("Enter your username and we'll send a reset link to your email.")}}
        </p>

        <x-error-msg-tw/>
        <x-flash-msg-tw/>

        <form action="{{route('landlord.user.forget.password')}}" method="post" class="flex flex-col gap-5">
            @csrf

            <div class="flex flex-col gap-1.5">
                <label class="text-sm font-semibold text-secondary">{{__('Username')}}</label>
                <div class="auth-field relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="ti tabler-user text-gray-400"></i>
                    </div>
                    <input type="text" name="username" placeholder="{{__('Type your username')}}"
                           class="w-full pl-11 pr-4 py-3 border border-borderCS rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none" required />
                </div>
            </div>

            <button type="submit" id="send" class="auth-btn">
                <i class="ti tabler-send" style="font-size:0.95rem"></i>
                {{__('Send Reset Link')}}
            </button>

            <p class="text-center text-sm" style="color:#6B7280">
                {{__('Remember your password?')}}
                <a href="{{route('landlord.user.login')}}" class="font-semibold hover:underline ml-1" style="color:#0C4D54">{{__('Back to Sign In')}}</a>
            </p>
        </form>

    </div>
</div>

<script src="{{asset('assets/new-landlord/js/plugin.js')}}"></script>
<script src="{{asset('assets/common/js/toastr.min.js')}}"></script>
<script>
    (function($){
        "use strict";
        $(document).ready(function () {
            <x-btn.custom :id="'send'" :title="__('Sending')"/>
        });
    })(jQuery);
</script>
</body>
</html>
